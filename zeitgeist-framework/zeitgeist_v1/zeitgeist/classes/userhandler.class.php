<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Userhandler class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * @version 1.0.2 - 30.09.2007
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgObjectcache::init();
 */
class zgUserhandler
{
	private static $instance = false;
	
	private $debug;
	private $messages;
	private $session;
	private $database;
	private $configuration;
	
	public $userdata;
	public $userrights;
	public $character;
	
	private $loggedIn;

	/**
	 * Class constructor
	 * 
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();
		$this->database->setDBCharset('utf8');

		$this->session = zgSession::init();
		$this->session->startSession();
		
		$this->userrights = new zgUserrights();
		$this->character = new zgUsercharacters();
		
		$this->loggedIn = false;
	}

	
	/**
	 * Initialize the singleton
	 * 
	 * @return object
	 */
	public static function init()
	{
		if (self::$instance === false)
		{
			self::$instance = new zgUserhandler();
		}

		return self::$instance;
	}
	
	
	/**
	 * Tries to establish a login for a user from the session data
	 * Only works if the user was correctly logged in while the current session is active
	 * 
	 * @return boolean
	 */
	public function establishUserSession()
	{
		$this->debug->guard();
		
		if (!$this->session->getSessionId())
		{
			$this->debug->write('Error establishing user session: could not find a session id', 'error');
			$this->messages->setMessage('Error establishing user session: could not find a session id', 'error');
			$this->debug->unguard(false);
			return false;
		}
		
		if (!$this->session->getSessionVariable('user_userid'))
		{
			$this->debug->write('Could not establish user session: user id not found in session', 'warning');
			$this->messages->setMessage('Could not establish user session: user id not found in session', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$this->character->reloadCharacterdata())
		{
			$this->debug->write('Could not reload character data: data not found in session', 'warning');
			$this->messages->setMessage('Could not reload character data: data not found in session', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if (!$this->_validateUserSession())
		{
			$this->debug->write('Could not validate the user session: session is not safe!', 'warning');
			$this->messages->setMessage('Could not validate the user session: session is not safe!', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if (!$this->_reloginFromSession())
		{
			$this->debug->write('Could not relogin the user from the session', 'warning');
			$this->messages->setMessage('Could not relogin the user from the session', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * Validates the session, trying to make sure that it is really the right user calling it 
	 * 
	 * @return boolean 
	 */
	private function _validateUserSession()
	{
		$this->debug->guard();

		if ($this->session->getBoundIP() != getenv('REMOTE_ADDR'))
		{
			$this->debug->write('Problem validating the user session: IP does not match the session', 'warning');
			$this->messages->setMessage('Problem validating the user session: IP does not match the session', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Login a user with username and password
	 * If successfull it will gather the user specific data and tie it to the session
	 * 
	 * @param string $name name of the user
	 * @param string $password supposed password of the user
	 * 
	 * @return boolean 
	 */
	public function loginUser($name, $password)
	{
		$this->debug->guard();
		
		$userTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_users');
		$sql = "SELECT * FROM " . $userTablename . " WHERE user_name = '" . $name . "' AND user_password = '". md5($password) . "'";
	
	    if ($res = $this->database->query($sql))
	    {
	        if ($this->database->numRows($res))
	        {
	            $row = $this->database->fetchArray($res);
	        	$this->session->setSessionVariable('user_userid', $row['user_id']);
	        	$this->session->setSessionVariable('user_activecharacter', $row['user_activecharacter']);
	        	$this->session->setSessionVariable('user_key', $row['user_key']);
	        	
	        	$this->userrights->loadUserrights($row['user_id']);
	        	$this->character->loadCharacterdata($row['user_activecharacter']);
	        	
	        	$this->saveUserstates();
	        	
	        	$this->loggedIn = true;
	        	
				$this->debug->unguard(true);
				return true;
	        }
	        else
	        {
				$this->debug->write('Error validating a user: user not found or password is wrong', 'error');
				$this->messages->setMessage('Error validating a user: user not found or password is wrong', 'error');
				$this->debug->unguard(false);
				return false;
	        }
	    }
	    else
	    {
			$this->debug->write('Error searching a user: could not read the user table', 'error');
			$this->messages->setMessage('Error searching a user: could not read the user table', 'error');
			$this->debug->unguard(false);
			return false;
	    }
		
		$this->debug->unguard(false);
		return false;
	}
	
	public function logoutUser()
	{
		
	}
	
	
	/**
	 * Reload all the user specific data from the session into the structures and classes
	 * 
	 * @return boolean 
	 */
	private function _reloginFromSession()
	{
		$this->debug->guard();
		
		$this->userrights->reloadUserrights();
		$this->character->reloadCharacterdata();

		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * Calls all functions to save all user specific data into the session
	 * 
	 * @return boolean 
	 */
	public function saveUserstates()
	{
		$this->debug->guard();
		
		$this->saveUserdata();
		$this->userrights->saveUserrights();
		$this->character->saveCharacterdata();

		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * Checks if the user is currently logged in
	 * 
	 * @return boolean 
	 */
	public function isLoggedIn()
	{
		$this->debug->guard();
		
		if ($this->loggedIn)
		{
			$this->debug->unguard(true);
			return true;
		}
		
		$this->debug->unguard(false);
		return false;
	}
	
	
	public function getUserdata($dataid, $dataprofile)
	{
		
	}
	
	
	public function setUserdata()
	{
		
	}
	
	public function saveUserdata()
	{
		
	}
	
	
}
?>
