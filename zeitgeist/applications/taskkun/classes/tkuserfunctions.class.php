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


	public function getUserInstance($userid)
	{
		$this->debug->guard();

		if ($userid == $this->user->getUserID())
		{
			if (!$ret = $this->session->getSessionVariable('user_instance'))
			{
				$sql = "SELECT user_instance FROM users WHERE user_id='" . $userid . "'";
				$res = $this->database->query($sql);
				$row = $this->database->fetchArray($res);
				$this->session->setSessionVariable('user_instance', $row['user_instance']);
				$ret = $row['user_instance'];
			}
		}
		else
		{
			$sql = "SELECT user_instance FROM users WHERE user_id='" . $userid . "'";
			$res = $this->database->query($sql);
			$row = $this->database->fetchArray($res);
			$ret = $row['user_instance'];
		}

		$this->debug->unguard($ret);
		return $ret;
	}


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


	public function getUsername($userid)
	{
		$this->debug->guard();

		$sql = "SELECT user_username FROM users WHERE user_id='" . $userid . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$ret = $row['user_username'];

		$this->debug->unguard($ret);
		return $ret;
	}


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

		$sql = "UPDATE users SET user_username = '" . $username . "' WHERE user_id='" . $userid . "'";
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


	public function changePassword($password, $userid)
	{
		$this->debug->guard();

		$sql = "UPDATE users SET user_password = '" . md5($password) . "' WHERE user_id='" . $userid . "'";
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


	public function changeUserdata($userdata, $userid)
	{
		$this->debug->guard();

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
			if (!empty($userdata['userdata_lastname'])) $this->user->setUserdata('userdata_lastname', $userdata['userdata_lastname']);
			if (!empty($userdata['userdata_firstname'])) $this->user->setUserdata('userdata_firstname', $userdata['userdata_firstname']);
			if (!empty($userdata['userdata_address1'])) $this->user->setUserdata('userdata_address1', $userdata['userdata_address1']);
			if (!empty($userdata['userdata_address2'])) $this->user->setUserdata('userdata_address2', $userdata['userdata_address2']);
			if (!empty($userdata['userdata_zip'])) $this->user->setUserdata('userdata_zip', $userdata['userdata_zip']);
			if (!empty($userdata['userdata_city'])) $this->user->setUserdata('userdata_city', $userdata['userdata_city']);
			if (!empty($userdata['userdata_url'])) $this->user->setUserdata('userdata_url', $userdata['userdata_url']);
		}

		$this->debug->unguard(true);
		return true;
	}


	public function changeUserrole($userrole, $userid)
	{
		$this->debug->guard();

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


	public function getUserrole($userid)
	{
		$this->debug->guard();

		$sql = "SELECT userroleuser_userrole FROM userroles_to_users WHERE userroleuser_user='" . $userid . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$ret = $row['userroleuser_userrole'];

		$this->debug->unguard($ret);
		return $ret;
	}


	public function changeGroups($groups, $userid)
	{
		$this->debug->guard();

		$sql = "UPDATE users SET user_password = '" . md5($password) . "' WHERE user_id='" . $userid . "'";
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


	public function getUserdata($userid)
	{
		$this->debug->guard();

		$sql = "SELECT u.user_username, ud.* FROM users AS u LEFT JOIN userdata ud ON u.user_id = ud.userdata_user WHERE u.user_id = '" . $userid . "' AND u.user_instance='" . $this->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$this->debug->unguard($row );
		return $row ;
	}

}
?>
