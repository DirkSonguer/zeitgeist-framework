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
	
	protected $debug;
	protected $messages;
	protected $session;
	protected $database;
	protected $configuration;
	
	public $userdata;
	public $userrights;
	
	protected $loggedIn;

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

		$this->session = zgSession::init();
		$this->session->startSession();
		
		$this->userrights = new zgUserrights();
		$this->userdata = new zgUserdata();
		
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
		
		$this->loggedIn = true;
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * Validates the session, trying to make sure that it is really the right user calling it 
	 * 
	 * @return boolean 
	 */
	protected function _validateUserSession()
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
		
		if (!$this->loggedIn)
		{
			$userTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_users');
			$sql = "SELECT * FROM " . $userTablename . " WHERE user_username = '" . $name . "' AND user_password = '". md5($password) . "'";
		
		    if ($res = $this->database->query($sql))
		    {
		        if ($this->database->numRows($res))
		        {
		            $row = $this->database->fetchArray($res);
		        	$this->session->setSessionVariable('user_userid', $row['user_id']);
		        	$this->session->setSessionVariable('user_key', $row['user_key']);
		        	
		        	$this->userrights->loadUserrights($row['user_id']);
		        	$this->userdata->loadUserdata($row['user_id']);
		        	
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
		}
	    else
	    {
			$this->debug->write('Error logging in a user: user is already logged in. Cannot login user twice', 'error');
			$this->messages->setMessage('Error logging in a user: user is already logged in. Cannot login user twice', 'error');
			$this->debug->unguard(false);
			return false;
	    }		

		$this->debug->unguard(false);
		return false;
	}
	
	
	/**
	 * Log out the user if he is currently logged in
	 * 
	 * @return boolean 
	 */	
	public function logoutUser()
	{
		$this->debug->guard();
		
		if ($this->loggedIn)
		{
			$this->session->unsetSessionVariable('user_userid');
			$this->session->unsetSessionVariable('user_key');
		}
	    else
	    {
			$this->debug->write('Error logging out user: user is not logged in', 'warning');
			$this->messages->setMessage('Error logging out user: user is not logged in', 'warning');
			$this->debug->unguard(false);
			return false;
	    }
	    
	    $this->session->stopSession();
	    
		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * Reload all the user specific data from the session into the structures and classes
	 * 
	 * @return boolean 
	 */
	protected function _reloginFromSession()
	{
		$this->debug->guard();
		
		$this->userrights->reloadUserrights();
		$this->userdata->reloadUserdata();
		
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
		
		$this->userrights->saveUserrights();
		$this->userdata->saveUserdata();
		
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
	
	
	/**
	 * Returns the current UserID
	 * 
	 * @return integer 
	 */
	public function getUserID()
	{
		$this->debug->guard();
		
		if ($this->loggedIn)
		{
			$userid = $this->session->getSessionVariable('user_userid');
			
			if ($userid)
			{
				$this->debug->unguard($userid);
				return $userid;
			}
		}
		
		$this->debug->unguard(false);
		return false;
	}	
}
?>
