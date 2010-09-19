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

		$projectfunctions = new zgaProjectfunctions();
		$activeproject = $projectfunctions->getActiveProject();

		$this->projectDatabase = new zgDatabase();
		$this->projectDatabase->connect($activeproject['project_dbserver'], $activeproject['project_dbuser'], $activeproject['project_dbpassword'], $activeproject['project_dbdatabase'], true, true);
	}


	public function getAllUsers()
	{
		$this->debug->guard();

		$sql = "SELECT * FROM users u ";
		$sql .= "LEFT JOIN userroles_to_users r2u ON u.user_id = r2u.userroleuser_user ";
		$sql .= "GROUP BY u.user_id";

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
			$this->debug->write('Could not get userdata definition from project database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not get userdata definition from project database: could not connect to database', 'warning');
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


	public function getInformation($userid)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM users WHERE user_id = '" . $userid . "'";
		if ($res = $this->projectDatabase->query($sql))
		{
			if ($this->projectDatabase->numRows($res))
			{
				$ret = array();
				$row = $this->projectDatabase->fetchArray($res);
				$ret = $row;

				$this->debug->unguard($ret);
				return $ret;
			}
			else
			{
				$this->debug->write('Problem getting user information: id not found for given user', 'warning');
				$this->messages->setMessage('Problem getting user information: id not found for given user', 'warning');
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


	public function changeUserinformation($userid, $userinformation)
	{
		$this->debug->guard();

		if (count($userinformation) < 1)
		{
			$this->debug->write('Problem changing the information of a user: no new userdata was given for the user', 'warning');
			$this->messages->setMessage('Problem changing the information of a user: no new userdata was given for the user', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = 'UPDATE users SET ';
		foreach ($userinformation as $fieldkey => $fieldvalue)
		{
			$sql .= $fieldkey."='".$fieldvalue."',";
		}
		$sql = substr($sql, 0, -1);
		$sql .= " WHERE user_id='" . $userid . "'";

		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not update user information: could not connect to database', 'warning');
			$this->messages->setMessage('Could not update user information: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function deleteUser($userid)
	{
		$this->debug->guard();

		// user
		$sql = "DELETE FROM users WHERE user_id='" . $userid . "'";
		$res = $this->projectDatabase->query($sql);

		// userdata
		$sql = "DELETE FROM userdata WHERE userdata_user='" . $userid . "'";
		$res = $this->projectDatabase->query($sql);

		// userrights
		$sql = "DELETE FROM userrights WHERE userright_user='" . $userid . "'";
		$res = $this->projectDatabase->query($sql);

		// userroles
		$sql = "DELETE FROM userroles_to_users WHERE userroleuser_user='" . $userid . "'";
		$res = $this->projectDatabase->query($sql);

		// userconfirmation
		$sql = "DELETE FROM userconfirmation WHERE userconfirmation_user='" . $userid . "'";
		$res = $this->projectDatabase->query($sql);

		$this->debug->unguard(true);
		return true;
	}

}
?>
