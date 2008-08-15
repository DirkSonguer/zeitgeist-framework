<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Userroles class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgUserroles
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;

	/**
	 * Class constructor
	 */
	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * Returns roles for a given user
	 *
	 * @param integer $userid id of the user
	 *
	 * @return boolean
	 */
	public function getUserroles($userid)
	{
		$this->debug->guard();

		$userrolesTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userroles_to_users');
		$sql = "SELECT * FROM " . $userrolesTablename . " WHERE userroleuser_user = '" . $userid . "'";

		if ($res = $this->database->query($sql))
		{
			$ret = array();
			while ($row = $this->database->fetchArray($res))
			{
				$ret[$row['userroleuser_userrole']] = true;
			}

			if (count($ret) == 0)
			{
				$this->debug->write('Possible problem getting the roles of a user: there seems to be no roles assigned to the user', 'warning');
				$this->messages->setMessage('Possible problem getting the roles of a user: there seems to be no roles assigned to the user', 'warning');
				$this->debug->unguard(false);
				return false;
			}

			$this->debug->unguard($ret);
			return $ret;
		}
		else
		{
			$this->debug->write('Problem getting userrole for a user: could not find the userrole', 'warning');
			$this->messages->setMessage('Problem getting userrole for a user: could not find the userrole', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(false);
		return false;
	}


	public function setUserroles()
	{
		// TODO: FUNCTION!
	}
	
	
	public function addUserrole()
	{
		// TODO: FUNCTION!
	}


	public function deleteUserrole()
	{
		// TODO: FUNCTION!
	}



}
?>
