<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Handles application management
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST ADMINISTRATOR
 * @subpackage ZGA SETUPFUNCTIONS
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgaSetupfunctions
{
	protected $debug;
	protected $messages;
	protected $projDatabase;
	protected $configuration;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->projectDatabase = new zgDatabase();
		$this->projectDatabase->connect('localhost', 'root', '', 'pokendesign', true, true);
	}
	
	
	public function getAllModules()
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM modules m ";
	
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get module data from project database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not get module data from project database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$modules = array();
		while ($row = $this->projectDatabase->fetchArray($res))
		{
			$modules[] = $row;
		}		

		$this->debug->unguard($modules);
		return $modules;
	}

}
?>
