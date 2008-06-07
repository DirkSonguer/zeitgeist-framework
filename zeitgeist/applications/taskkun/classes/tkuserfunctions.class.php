<?php

defined('TASKKUN_ACTIVE') or die();

class tkUserfunctions
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $session;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->session = zgSession::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * gets the instance of a given userid
	 * the function acts as check for the user instance
	 *
	 * instance-safe!
	 *
	 * @param integer $userid id of a user
	 *
	 * @return boolean
	 */
	public function getUserInstance($userid)
	{
		$this->debug->guard();

		$userinstance = false;
		if ($userid == $this->user->getUserID())
		{
			if (!$userinstance = $this->session->getSessionVariable('user_instance'))
			{
				$sql = "SELECT user_instance FROM users WHERE user_id='" . $userid . "'";
				$res = $this->database->query($sql);
				$row = $this->database->fetchArray($res);
				$this->session->setSessionVariable('user_instance', $row['user_instance']);
				$userinstance = $row['user_instance'];
			}
		}
		else
		{
			$sql = "SELECT user_instance FROM users WHERE user_id='" . $userid . "'";
			$res = $this->database->query($sql);
			$row = $this->database->fetchArray($res);
			$userinstance = $row['user_instance'];
		}

		$this->debug->unguard($userinstance);
		return $userinstance;
	}


	/**
	 * Creates a new user with a given name and password
	 * Optional a usergroup and userdata can be given
	 *
	 * instance-safe!
	 *
	 * @param string $name name of the user
	 * @param string $password password of the user
	 * @param integer $userrole id of the userrole
	 * @param array $usergroups array containing the groups of the user
	 * @param array $userdata array containing the userdata
	 *
	 * @return boolean
	 */
	public function createUser($name, $password, $userrole=1, $usergroups=array(), $userdata=array())
	{
		$this->debug->guard();

		if (!$this->user->createUser($name, $password, $userrole, $userdata))
		{
			$this->debug->unguard(false);
			return false;
		}

		$lastinsert = $this->database->insertId();
		$sql = "SELECT * FROM userdata WHERE userdata_id='" . $lastinsert . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$userid = $row['userdata_user'];
		$sql = "UPDATE users SET user_instance='" . $this->getUserInstance($this->user->getUserID()) . "' WHERE user_id='" . $userid . "'";
		$res = $this->database->query($sql);

		if (!$this->changeUsergroups($usergroups, $userid))
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * checks if the given task is in the current instance
	 *
	 * instance-safe!
	 *
	 * @param integer $taskid id of the task to check
	 *
	 * @return boolean
	 */
	public function checkRightsForTask($taskid)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM tasks WHERE task_id='" . $taskid . "' AND task_instance='" . $this->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		$numTasks = $this->database->numRows($res);

		if ($numTasks == 0)
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * checks if the given user is in the current instance
	 *
	 * instance-safe!
	 *
	 * @param integer $userid id of the user to check
	 *
	 * @return boolean
	 */
	public function checkRightsForUser($userid)
	{
		$this->debug->guard();

		$currentUser = $this->user->getUserID();
		if ($userid == $currentUser)
		{
			$this->debug->unguard(true);
			return true;
		}

		if ($this->getUserInstance($userid) != $this->getUserInstance($currentUser))
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets the username for a given userid
	 *
	 * instance-safe!
	 *
	 * @param integer $userid userid to get the name for
	 *
	 * @return string
	 */
	public function getUsername($userid)
	{
		$this->debug->guard();

		$sql = "SELECT user_username FROM users WHERE user_id='" . $userid . "' AND user_instance='" . $this->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		if ($this->database->numRows($res) > 0)
		{
			$this->debug->write('Problem reading out username: user not found', 'warning');
			$this->messages->setMessage('Problem reading out username: user not found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);
		$username = $row['user_username'];

		$this->debug->unguard($username);
		return $username;
	}


	/**
	 * changes the username for a given userid
	 *
	 * instance-safe!
	 *
	 * @param string $username new username for the user
	 * @param integer $userid id of the user to change the name for
	 *
	 * @return boolean
	 */
	public function changeUsername($username, $userid)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM users WHERE user_username='" . $username . "'";
		$res = $this->database->query($sql);
		if ($this->database->numRows($res) > 0)
		{
			$this->debug->write('Problem changing username: username already exists', 'warning');
			$this->messages->setMessage('Problem changing username: username already exists', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "UPDATE users SET user_username = '" . $username . "' WHERE user_id='" . $userid . "' AND user_instance='" . $this->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem changing the username of user ' . $userid, 'warning');
			$this->messages->setMessage('Problem changing the username of user ' . $userid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if ($userid == $this->user->getUserID())
		{
			$this->session->setSessionVariable('user_username', $username);
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * changes the password for a given userid
	 *
	 * instance-safe!
	 *
	 * @param string $password new password for the user
	 * @param integer $userid id of the user to change the password for
	 *
	 * @return boolean
	 */
	public function changePassword($password, $userid)
	{
		$this->debug->guard();

		$sql = "UPDATE users SET user_password = '" . md5($password) . "' ";
		$sql .= "WHERE user_id='" . $userid . "' AND user_instance='" . $this->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem changing the password of user ' . $userid, 'warning');
			$this->messages->setMessage('Problem changing the password of user ' . $userid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * changes the userdata for a given userid
	 *
	 * instance-safe!
	 *
	 * @param array $userdata array that contains all userdata fields as array fields
	 * @param integer $userid id of the user to change the password for
	 *
	 * @return boolean
	 */
	public function changeUserdata($userdata, $userid)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM users WHERE user_id='" . $userid . "' AND user_instance='" . $this->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		if (!$this->database->numRows($res) > 0)
		{
			$this->debug->write('The user is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The user is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if ($userid != $this->user->getUserID())
		{
			$sql = "UPDATE userdata SET ";
			$updatestring = '';
			if (!empty($userdata['userdata_lastname'])) $updatestring .= "userdata_lastname='" . $userdata['userdata_lastname'] . "', ";
			if (!empty($userdata['userdata_firstname'])) $updatestring .= "userdata_firstname='" . $userdata['userdata_firstname'] . "', ";
			if (!empty($userdata['userdata_address1'])) $updatestring .= "userdata_address1='" . $userdata['userdata_address1'] . "', ";
			if (!empty($userdata['userdata_address2'])) $updatestring .= "userdata_address2='" . $userdata['userdata_address2'] . "', ";
			if (!empty($userdata['userdata_zip'])) $updatestring .= "userdata_zip='" . $userdata['userdata_zip'] . "', ";
			if (!empty($userdata['userdata_city'])) $updatestring .= "userdata_city='" . $userdata['userdata_city'] . "', ";
			if (!empty($userdata['userdata_url'])) $updatestring .= "userdata_url='" . $userdata['userdata_url'] . "', ";
			if (strlen($updatestring) > 2)
			{
				$sql .= substr($updatestring, 0, -2);
			}
			else
			{
				$this->debug->write('Nothing to update', 'warning');
				$this->messages->setMessage('Nothing to update', 'warning');
				$this->debug->unguard(false);
				return false;
			}

			$sql .= " WHERE userdata_user='" . $userid . "'";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Problem storing the userdata of user ' . $userid, 'warning');
				$this->messages->setMessage('Problem storing the userdata of user ' . $userid, 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		else
		{
			if (!empty($userdata['userdata_lastname'])) $this->user->setUserdata('userdata_lastname', $userdata['userdata_lastname'], false);
			if (!empty($userdata['userdata_firstname'])) $this->user->setUserdata('userdata_firstname', $userdata['userdata_firstname'], false);
			if (!empty($userdata['userdata_address1'])) $this->user->setUserdata('userdata_address1', $userdata['userdata_address1'], false);
			if (!empty($userdata['userdata_address2'])) $this->user->setUserdata('userdata_address2', $userdata['userdata_address2'], false);
			if (!empty($userdata['userdata_zip'])) $this->user->setUserdata('userdata_zip', $userdata['userdata_zip'], false);
			if (!empty($userdata['userdata_city'])) $this->user->setUserdata('userdata_city', $userdata['userdata_city'], false);
			if (!empty($userdata['userdata_url'])) $this->user->setUserdata('userdata_url', $userdata['userdata_url'], false);

			$this->user->saveUserdata();
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * changes the userroles for a given userid
	 *
	 * instance-safe!
	 *
	 * @param integer $userrole id of the new userrole
	 * @param integer $userid id of the user to change the password for
	 *
	 * @return boolean
	 */
	public function changeUserrole($userrole, $userid)
	{
		$this->debug->guard();

		if (!$this->checkRightsForUser($userid))
		{
			$this->debug->write('The user is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The user is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "UPDATE userroles_to_users SET userroleuser_userrole = '" . $userrole . "' WHERE userroleuser_user='" . $userid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem changing the userrole of user ' . $userid, 'warning');
			$this->messages->setMessage('Problem changing the userrole of user ' . $userid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets all userroles
	 * userroles are global so we don't need any instance-checking
	 *
	 * instance-safe!
	 *
	 * @return array
	 */
	public function getUserroles()
	{
		$this->debug->guard();

		$sql = "SELECT * FROM userroles";
		$res = $this->database->query($sql);

		$userroles = array();
		while ($row = $this->database->fetchArray($res))
		{
			$userroles[] = $row;
		}

		$this->debug->unguard($userroles);
		return $userroles;
	}


	/**
	 * gets the userrole for a given user
	 *
	 * instance-safe!
	 *
	 * @param integer $userid id of the user
	 *
	 * @return integer
	 */
	public function getUserroleForUser($userid)
	{
		$this->debug->guard();

		if (!$this->checkRightsForUser($userid))
		{
			$this->debug->write('The user is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The user is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "SELECT userroleuser_userrole FROM userroles_to_users uru ";
		$sql .= "LEFT JOIN users u ON uru.userroleuser_user = u.user_id ";
		$sql .= "WHERE uru.userroleuser_user='" . $userid . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$userrole = $row['userroleuser_userrole'];

		$this->debug->unguard($userrole);
		return $userrole;
	}


	/**
	 * gets all usergroups for th current instance
	 *
	 * instance-safe!
	 *
	 * @return array
	 */
	public function getUsergroups()
	{
		$this->debug->guard();

		$sql = "SELECT * FROM groups g ";
		$sql .= "WHERE g.group_instance='" . $this->getUserInstance($this->user->getUserID()) . "' ";
		$res = $this->database->query($sql);

		$usergroups = array();
		while ($row = $this->database->fetchArray($res))
		{
			$usergroups[] = $row;
		}

		$this->debug->unguard($usergroups);
		return $usergroups;
	}


	/**
	 * gets the usergroup for a given user
	 *
	 * instance-safe!
	 *
	 * @param integer $userid id of the user
	 *
	 * @return array
	 */
	public function getUsergroupsForUser($userid)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM groups g LEFT JOIN users_to_groups u2g ON g.group_id = u2g.usergroup_group ";
		$sql .= "WHERE g.group_instance='" . $this->getUserInstance($this->user->getUserID()) . "' AND u2g.usergroup_user='" . $userid . "' ";
		$sql .= "GROUP BY g.group_name";
		$res = $this->database->query($sql);

		$usergroups = array();
		while ($row = $this->database->fetchArray($res))
		{
			$usergroups[] = $row;
		}

		$this->debug->unguard($usergroups);
		return $usergroups;
	}


	/**
	 * changes the usergroups for a given user
	 *
	 * instance-safe!
	 *
	 * @param array $groups array that contains the ids of the new groups
	 * @param integer $userid id of the user
	 *
	 * @return
	 */
	public function changeUsergroups($groups, $userid)
	{
		$this->debug->guard();

		if (!$this->checkRightsForUser($userid))
		{
			$this->debug->write('The user is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The user is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if ( (!is_array($groups)) || (count($groups) == 0) )
		{
			$this->debug->write('Error reading groups: group structure is not an array', 'warning');
			$this->messages->setMessage('Error reading groups: group structure is not an array', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "SELECT * FROM users_to_groups WHERE usergroup_user='" . $userid . "'";
		$res = $this->database->query($sql);
		$usergroups = array();
		while ($row = $this->database->fetchArray($res))
		{
			$usergroups[$row['usergroup_group']] = $row['usergroup_user'];
		}

		$usergroupsChanged = false;
		foreach ($groups as $group)
		{
			if (!empty($usergroups[$group]))
			{
				unset($usergroups[$group]);
			}
			else
			{
				$usergroupsChanged = true;
			}
		}

		if (count($usergroups) > 0)
		{
			$usergroupsChanged = true;
		}

		if ($usergroupsChanged)
		{
			$sql = "DELETE FROM users_to_groups WHERE usergroup_user='" . $userid . "'";
			$res = $this->database->query($sql);

			$sql = 'INSERT INTO users_to_groups(usergroup_group, usergroup_user) VALUES';
			foreach ($groups as $group)
			{
				$sql .= "('". $group ."','". $userid ."'),";
			}

			$sql = substr($sql, 0, -1);
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Problem changing the usergroups of a user: could not insert groups', 'warning');
				$this->messages->setMessage('Problem changing the usergroups of a user: could not insert groups', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets all userdata for a given user
	 *
	 * instance-safe!
	 *
	 * @param integer $userid id of the user
	 *
	 * @return array
	 */
	public function getUserdata($userid)
	{
		$this->debug->guard();

		$sql = "SELECT u.user_username, ud.* FROM users AS u LEFT JOIN userdata ud ON u.user_id = ud.userdata_user ";
		$sql .= "WHERE u.user_id = '" . $userid . "' AND u.user_instance='" . $this->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		$userdata= $this->database->fetchArray($res);

		$this->debug->unguard($userdata);
		return $userdata;
	}


	/**
	 * deletes a given user
	 *
	 * instance-safe!
	 *
	 * @param integer $userid id of the user
	 *
	 * @return boolean
	 */
	public function deleteuser($userid)
	{
		$this->debug->guard();

		if (!$this->checkRightsForUser($userid))
		{
			$this->debug->write('The user is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The user is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if ($userid == $this->user->getUserId())
		{
			$this->debug->write('Problem deleting the user: a user can not delete himself', 'warning');
			$this->messages->setMessage('Problem deleting the user: a user can not delete himself', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// user account
		$sql = "DELETE FROM users WHERE user_id='" . $userid . "'";
		$res = $this->database->query($sql);

		// userdata
		$sql = "DELETE FROM userdata WHERE userdata_user='" . $userid . "'";
		$res = $this->database->query($sql);

		// userrights
		$sql = "DELETE FROM userrights WHERE userright_user='" . $userid . "'";
		$res = $this->database->query($sql);

		// userrole
		$sql = "DELETE FROM userroles_to_users WHERE userroleuser_user='" . $userid . "'";
		$res = $this->database->query($sql);

		// user tasklogs
		$sql = "DELETE FROM tasklogs WHERE tasklog_creator='" . $userid . "'";
		$res = $this->database->query($sql);

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * activates the account of a given user
	 *
	 * instance-safe!
	 *
	 * @param integer $userid id of the user
	 *
	 * @return boolean
	 */
	public function activateuser($userid)
	{
		$this->debug->guard();

		if (!$this->checkRightsForUser($userid))
		{
			$this->debug->write('The user is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The user is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$this->user->activateUser($userid))
		{
			$this->debug->write('Problem activating a user: userhandler returned false', 'warning');
			$this->messages->setMessage('Problem activating a user: userhandler returned false', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * deactivates the account of a given user
	 *
	 * instance-safe!
	 *
	 * @param integer $userid id of the user
	 *
	 * @return boolean
	 */
	public function deactivateuser($userid)
	{
		$this->debug->guard();

		if (!$this->checkRightsForUser($userid))
		{
			$this->debug->write('The user is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The user is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$this->user->deactivateUser($userid))
		{
			$this->debug->write('Problem deactivating a user: userhandler returned false', 'warning');
			$this->messages->setMessage('Problem deactivating a user: userhandler returned false', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets all information about all users
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. none are used
	 *
	 * @return boolean
	 */
	public function getUserinformation()
	{
		$this->debug->guard();

		$sql = 'SELECT COUNT(ttu.taskusers_task) as user_taskcount, u.user_id, u.user_active, u.user_username, ud.*, ur.userrole_id, ur.userrole_name, g.group_name ';
		$sql .= 'FROM users u ';
		$sql .= 'LEFT JOIN userdata ud ON u.user_id = ud.userdata_user ';
		$sql .= 'LEFT JOIN userroles_to_users u2u ON u2u.userroleuser_user = u.user_id ';
		$sql .= 'LEFT JOIN userroles ur ON u2u.userroleuser_userrole = ur.userrole_id ';
		$sql .= 'LEFT JOIN users_to_groups u2g ON u.user_id = u2g.usergroup_user ';
		$sql .= 'LEFT JOIN groups g ON u2g.usergroup_group = g.group_id ';
		$sql .= 'LEFT JOIN tasks_to_users ttu ON u.user_id = ttu.taskusers_user ';
		$sql .= "WHERE u.user_instance='" . $this->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= 'GROUP BY g.group_name, u.user_id ';
		$sql .= 'ORDER BY u.user_id';

		$res = $this->database->query($sql);
		$userinformation = array();
		while($row = $this->database->fetchArray($res))
		{
			if (empty($userinformation[$row['user_id']]))
			{
				$userinformation[$row['user_id']] = $row;
			}
			else
			{
				$userinformation[$row['user_id']]['group_name'] .= ', ' . $row['group_name'];
			}
		}

		$this->debug->unguard($userinformation);
		return $userinformation;
	}

}
?>
