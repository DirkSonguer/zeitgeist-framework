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

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgObjects::init();
 */
class zgaUserfunctions
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

}
?>
