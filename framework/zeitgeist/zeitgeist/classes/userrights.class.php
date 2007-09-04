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
 * NOTE: This class handles the userrights
 * Although it is a stand alone class, it is most likely called directly from the userhandler class
 */
class zgUserrights
{
	private $debug;
	private $messages;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
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
