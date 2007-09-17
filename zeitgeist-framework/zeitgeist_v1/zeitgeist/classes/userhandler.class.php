<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Userhandler class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * @version 1.0.1 - 28.08.2007
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
	
	public $rights;
	public $userdata;

	/**
	 * Class constructor
	 * 
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->session = zgSession::init();

		$this->database = new zgDatabase();
		$this->database->connect();
		$this->database->setDBCharset('utf8');
		
		$this->rights = new zgUserrights();
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
	
	
	public function establishUserSession()
	{
		$this->debug->guard();
		
		if (!$this->session->getSessionId())
		{
			$this->debug->unguard(false);
			return false;
		}
		
		if (!$this->session->getSessionVariable('user_userid'))
		{
			$this->debug->unguard(false);
			return false;
		}
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	private function _validateUserSession()
	{
		
	}
	
	public function loginUser($name, $password)
	{
		$this->debug->guard();
		
		$userTablename = $this->configuration->getConfiguration('zeitgeist','userhandler','table_users');
		$sql = "SELECT * FROM " . $userTablename . " WHERE user_name = '" . $name . "' AND user_password = '". md5($password) . "'";
	
	    if ($res = $this->database->query($sql))
	    {
	        if ($this->database->numRows($res))
	        {
	            $row = $this->database->fetchArray($res);
	        	$this->session->setSessionVariable('user_userid', $row['user_id']);
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
	
	private function reloginFromSession()
	{
		
	}
	
	public function isLoggedIn()
	{
		
	}
	
	public function getUserdata()
	{
		
	}
	
	private function _loadUserdata()
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
