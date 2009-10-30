<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Handles user functions
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST ADMINISTRATOR
 * @subpackage ZGA USERFUNCTIONS
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgaUserfunctions
{
	protected $debug;
	protected $messages;
	protected $projectfunctions;
	protected $projectDatabase;
	protected $configuration;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->projectfunctions = new zgaProjectfunctions();
		$activeproject = $this->projectfunctions->getActiveProject();

		$this->projectDatabase = new zgDatabase();
		$this->projectDatabase->connect($activeproject['project_dbserver'], $activeproject['project_dbuser'], $activeproject['project_dbpassword'], $activeproject['project_dbdatabase'], false, true);
	}
	
	
	public function getAllUsers()
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM users u ";
		$sql .= "LEFT JOIN userroles_to_users r2u ON u.user_id = r2u.userroleuser_user ";
//		$sql .= "LEFT JOIN userroles r ON r2u.user_id = r2u.userroleuser_user ";
	
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get user data from project database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not get user data from project database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$userdata = array();
		while ($row = $this->projectDatabase->fetchArray($res))
		{
			$userdata[] = $row;
		}		

		$this->debug->unguard($userdata);
		return $userdata;
	}
	
	function getUserdataDefinition()
	{
		$this->debug->guard();
		
		$sql = "EXPLAIN userdata";
	
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get userdata definitio from project database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not get userdata definitio from project database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$userdata = array();
		while ($row = $this->projectDatabase->fetchArray($res))
		{
			$userdata[] = $row;
		}		

		$this->debug->unguard($userdata);
		return $userdata;
	}
		

}
?>
