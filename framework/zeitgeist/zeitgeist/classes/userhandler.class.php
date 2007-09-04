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
	
	public $rights;

	/**
	 * Class constructor
	 * 
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messags = zgMessages::init();
		
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
		
	}
	
	private function _validateUserSession()
	{
		
	}
	
	public function loginUser()
	{
		$this->debug->guard();
		
		echo "loginUser<br />";
		$this->debug->unguard("yo");
		return "yo";
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
