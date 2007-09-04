<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Userrights class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * @version 1.0.1 - 28.08.2007
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERRIGHTS
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgObjectcache::init();
 */
class zgUserrights
{
	private static $instance = false;
	
	private $debug;
	private $messages;

	/**
	 * Class constructor
	 * 
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messags = zgMessages::init();
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
			self::$instance = new zgUserrights();
		}

		return self::$instance;
	}
	

	public function hasUserright()
	{
		$this->debug->guard();
		
		echo "hasUserright<br />";
		$this->debug->unguard("yo");
		return "yo";
		
	}
	
	public function setUserright()
	{
		
	}
	
	public function saveUserrights()
	{
		
	}
	
	private function _loadAllRights()
	{
		
	}
	
	public function getUserrole()
	{
		
	}
	
	public function setUserrole()
	{
		
	}
	
	public function saveUserrole()
	{
		
	}
}
?>
