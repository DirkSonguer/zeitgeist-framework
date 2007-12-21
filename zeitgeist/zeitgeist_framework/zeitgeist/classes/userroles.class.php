<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Userroles class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERROLES
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class handles the userroles
 * Although it is a stand alone class, it is most likely called directly from the userhandler class
 */
class zgUserroles
{
	protected $debug;
	protected $messages;
	protected $session;
	protected $configuration;
	protected $database;
	
	public $userrole;
	
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->session = zgSession::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();
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
