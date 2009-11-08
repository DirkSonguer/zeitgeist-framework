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


	public function loadUserdata($userid)
	{
		$this->debug->guard();

		$userdataTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userdata');
		$sql = "SELECT * FROM " . $userdataTablename . " WHERE userdata_user = '" . $userid . "'";

		if ($res = $this->projectDatabase->query($sql))
		{
			$ret = array();
			if ($this->projectDatabase->numRows($res) > 0)
			{
				$ret = $this->projectDatabase->fetchArray($res);
			}
			else
			{
				$sql = "EXPLAIN " . $userdataTablename;
				$res = $this->projectDatabase->query($sql);
				
				while($row = $this->projectDatabase->fetchArray($res))
				{
					$ret[$row['Field']] = '';
				}
				
				$this->debug->write('The user seems to habe no assigned data. Userdata returned empty.', 'message');
				$this->messages->setMessage('The user seems to habe no assigned data. Userdata returned empty.', 'message');
			}

			$this->debug->unguard($ret);
			return $ret;
		}
		else
		{
			$this->debug->write('Error getting userdata for a user: could not find the userdata', 'error');
			$this->messages->setMessage('Error getting userdata for a user: could not find the userdata', 'error');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(false);
		return false;
	}


	public function saveUserdata($userid, $userdata)
	{
		$this->debug->guard();

		if ((!is_array($userdata)) || (count($userdata) < 1))
		{
			$this->debug->write('Problem setting the user data: array not valid', 'warning');
			$this->messages->setMessage('Problem setting the user data: array not valid', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$userdataTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userdata');
		$sql = 'DELETE FROM ' . $userdataTablename . " WHERE userdata_user='" . $userid . "'";
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem setting the user data: could not clean up the data table', 'warning');
			$this->messages->setMessage('Problem setting the user data: could not clean up the data table', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sqlkeys = '';
		$sqlvalues = '';
		foreach ($userdata as $key => $value)
		{
			if (($key != 'userdata_timestamp') && ($key != 'userdata_user'))
			{
				if ($sqlkeys != '') $sqlkeys .= ', ';
				$sqlkeys .= $key;
				if ($sqlvalues != '') $sqlvalues .= ', ';
				$sqlvalues .= "'" . $value . "'";
			}
		}

		$sql = "INSERT INTO " . $userdataTablename . "(userdata_user, " . $sqlkeys . ") VALUES('" . $userid . "'," . $sqlvalues . ")";
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem setting the user data: could not write the data', 'warning');
			$this->messages->setMessage('Problem setting the user data: could not write the data', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}

}
?>
