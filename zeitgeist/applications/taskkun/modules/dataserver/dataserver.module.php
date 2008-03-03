<?php


defined('TASKKUN_ACTIVE') or die();

class dataserver
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $dataserver;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		$this->dataserver = new zgDataserver();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function getusertasks($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT SUM(tl.tasklog_hoursworked) as task_hoursworked, t.*, tu.*, g.group_name, ";
		$sql .= "DATE_FORMAT(t.task_end, '%d.%m.%Y') as task_end, DATE_FORMAT(t.task_begin, '%d.%m.%Y') as task_begin ";
		$sql .= "FROM tasks_to_users tu ";
		$sql .= "LEFT JOIN tasks t ON tu.taskusers_task = t.task_id LEFT JOIN tasklogs tl ON t.task_id = tl.tasklog_task ";
		$sql .= "LEFT JOIN taskworkflow twf ON t.task_workflow = twf.taskworkflow_id ";
		$sql .= "LEFT JOIN users_to_groups u2g ON twf.taskworkflow_group = u2g.usergroup_group ";
		$sql .= "LEFT JOIN groups g ON u2g.usergroup_group = g.group_id ";
		$sql .= "WHERE taskusers_user='" . $this->user->getUserID() . "' ";
		$sql .= "AND u2g.usergroup_user = '" . $this->user->getUserID() . "' ";
		$sql .= "GROUP BY t.task_id";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getgrouptasks($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT SUM(tl.tasklog_hoursworked) as task_hoursworked, t.*, u.user_username, u.user_id, g.group_name, ";
		$sql .= "DATE_FORMAT(t.task_end, '%d.%m.%Y') as task_end, DATE_FORMAT(t.task_begin, '%d.%m.%Y') as task_begin ";
		$sql .= "FROM tasks t ";
		$sql .= "LEFT JOIN tasks_to_users tu ON t.task_id = tu.taskusers_task ";
		$sql .= "LEFT JOIN users u ON tu.taskusers_user = u.user_id ";
		$sql .= "LEFT JOIN tasklogs tl ON t.task_id = tl.tasklog_task ";
		$sql .= "LEFT JOIN taskworkflow twf ON t.task_workflow = twf.taskworkflow_id ";
		$sql .= "LEFT JOIN users_to_groups u2g ON twf.taskworkflow_group = u2g.usergroup_group ";
		$sql .= "LEFT JOIN groups g ON u2g.usergroup_group = g.group_id ";
		$sql .= "WHERE tu.taskusers_user is null ";
		$sql .= "AND u2g.usergroup_user = '" . $this->user->getUserID() . "' ";
		$sql .= "GROUP BY t.task_id";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getalltasks($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT SUM(tl.tasklog_hoursworked) as task_hoursworked, t.*, u.user_username, tt.tasktype_name, g.group_name, ";
		$sql .= "DATE_FORMAT(t.task_end, '%d.%m.%Y') as task_end, DATE_FORMAT(t.task_begin, '%d.%m.%Y') as task_begin, ";
		$sql .= "IF( ((t.task_end < NOW()) && (t.task_end != '00.00.0000')), 1, 0) as task_late, ";
		$sql .= "IF (SUM(tl.tasklog_hoursworked) > task_hoursplanned, 1, 0) as task_overdrawn ";
		$sql .= "FROM tasks t ";
		$sql .= "LEFT JOIN tasks_to_users tu ON t.task_id = tu.taskusers_task ";
		$sql .= "LEFT JOIN users u ON tu.taskusers_user = u.user_id ";
		$sql .= "LEFT JOIN tasklogs tl ON t.task_id = tl.tasklog_task ";
		$sql .= "LEFT JOIN tasktypes tt ON t.task_type = tt.tasktype_id ";
		$sql .= "LEFT JOIN taskworkflow twf ON t.task_workflow = twf.taskworkflow_id ";
		$sql .= "LEFT JOIN groups g ON twf.taskworkflow_group = g.group_id ";
		$sql .= "GROUP BY t.task_id";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function gettasklogs($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT t.*, u.user_username as tasklog_username, DATE_FORMAT(t.tasklog_timestamp, '%H:%i:%s, %d.%m.%Y') as tasklog_timestamp FROM tasklogs t ";
		$sql .= "LEFT JOIN users u ON t.tasklog_creator = u.user_id ";
		$sql .= "WHERE t.tasklog_task='" . $parameters['id'] . "' ORDER BY t.tasklog_timestamp";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function gettaskinformation($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT * FROM tasks t WHERE t.task_id='" . $parameters['id'] . "'";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getuserinformation($parameters=array())
	{
		$this->debug->guard();

		$sql = 'SELECT u.user_id, u.user_username, ud.*, ur.userrole_id, ur.userrole_name, g.group_name FROM users u ';
		$sql .= 'LEFT JOIN userdata ud ON u.user_id = ud.userdata_user ';
		$sql .= 'LEFT JOIN userroles_to_users u2u ON u2u.userroleuser_user = u.user_id ';
		$sql .= 'LEFT JOIN userroles ur ON u2u.userroleuser_userrole = ur.userrole_id ';
		$sql .= 'LEFT JOIN users_to_groups u2g ON u.user_id = u2g.usergroup_user ';
		$sql .= 'LEFT JOIN groups g ON u2g.usergroup_group = g.group_id';

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

		$xmlData = $this->dataserver->createXMLDatasetFromArray($userinformation);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getuserroles($parameters=array())
	{
		$this->debug->guard();

		$sql = 'SELECT u.*, COUNT(ur.userroleuser_id) as userrole_usercount FROM userroles u ';
		$sql .= 'LEFT JOIN userroles_to_users ur ON u.userrole_id = ur.userroleuser_userrole GROUP BY u.userrole_id';

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql, $this->database);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getusergroups($parameters=array())
	{
		$this->debug->guard();

		$sql = 'SELECT g.*, COUNT(g.group_id) as group_groupcount FROM groups g ';
		$sql .= 'LEFT JOIN users_to_groups u2g ON g.group_id = u2g.usergroup_group ';
		$sql .= 'GROUP BY g.group_id';

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql, $this->database);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}

}
?>
