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

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT SUM(tl.tasklog_hoursworked) as task_hoursworked, t.*, tu.*, g.group_name, ";
		$sql .= "DATE_FORMAT(t.task_end, '%d.%m.%Y') as task_end, DATE_FORMAT(t.task_begin, '%d.%m.%Y') as task_begin ";
		$sql .= "FROM tasks_to_users tu ";
		$sql .= "LEFT JOIN tasks t ON tu.taskusers_task = t.task_id LEFT JOIN tasklogs tl ON t.task_id = tl.tasklog_task ";
		$sql .= "LEFT JOIN taskworkflow twf ON t.task_workflow = twf.taskworkflow_id ";
		$sql .= "LEFT JOIN users_to_groups u2g ON twf.taskworkflow_group = u2g.usergroup_group ";
		$sql .= "LEFT JOIN groups g ON u2g.usergroup_group = g.group_id ";
		$sql .= "WHERE taskusers_user='" . $this->user->getUserID() . "' ";
		$sql .= "AND u2g.usergroup_user = '" . $this->user->getUserID() . "' ";
		$sql .= "AND t.task_instance='" . $userfunctions->getUserInstance() . "' ";
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

		$userfunctions = new tkUserfunctions();

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
		$sql .= "AND t.task_instance='" . $userfunctions->getUserInstance() . "' ";
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

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT SUM(tl.tasklog_hoursworked) as task_hoursworked, t.*, u.user_username, tt.tasktype_name, g.group_name, ";
		$sql .= "DATE_FORMAT(t.task_end, '%d.%m.%Y') as task_end, DATE_FORMAT(t.task_begin, '%d.%m.%Y') as task_begin, ";
		$sql .= "CASE WHEN ((t.task_end < CURDATE()) && (t.task_end != '00.00.0000')) THEN '2' ";
		$sql .= "WHEN ((t.task_end = CURDATE()) && (t.task_end != '00.00.0000'))  THEN '1' ";
		$sql .= "ELSE '0' END as task_late, ";
		$sql .= "CASE WHEN (SUM(tl.tasklog_hoursworked) > task_hoursplanned) THEN '2' ";
		$sql .= "WHEN ((SUM(tl.tasklog_hoursworked) <= task_hoursplanned) AND (SUM(tl.tasklog_hoursworked) >= task_hoursplanned-1) )  THEN '1' ";
		$sql .= "ELSE '0' END as task_overdrawn ";
		$sql .= "FROM tasks t ";
		$sql .= "LEFT JOIN tasks_to_users tu ON t.task_id = tu.taskusers_task ";
		$sql .= "LEFT JOIN users u ON tu.taskusers_user = u.user_id ";
		$sql .= "LEFT JOIN tasklogs tl ON t.task_id = tl.tasklog_task ";
		$sql .= "LEFT JOIN tasktypes tt ON t.task_type = tt.tasktype_id ";
		$sql .= "LEFT JOIN taskworkflow twf ON t.task_workflow = twf.taskworkflow_id ";
		$sql .= "LEFT JOIN groups g ON twf.taskworkflow_group = g.group_id ";
		$sql .= "WHERE t.task_instance='" . $userfunctions->getUserInstance() . "' ";
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

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($parameters['id']))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "SELECT t.*, u.user_username as tasklog_username, DATE_FORMAT(t.tasklog_timestamp, '%H:%i:%s, %d.%m.%Y') as tasklog_timestamp, ";
		$sql .= "IF (t.tasklog_creator='" . $this->user->getUserID() . "', '1', '0') as tasklog_editdelete ";
		$sql .= "FROM tasklogs t ";
		$sql .= "LEFT JOIN users u ON t.tasklog_creator = u.user_id ";
		$sql .= "WHERE t.tasklog_task='" . $parameters['id'] . "' ";
		$sql .= "ORDER BY t.tasklog_timestamp";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function gettaskinformation($parameters=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT * FROM tasks t WHERE t.task_id='" . $parameters['id'] . "' ";
		$sql .= "AND t.task_instance='" . $userfunctions->getUserInstance() . "' ";

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


	public function searchtasks($parameters=array())
	{
		$this->debug->guard();

		$items = array();
		$itemscore = array();

		$userfunctions = new tkUserfunctions();

		if (!empty($parameters['searchterms']))
		{
			$terms = explode(' ', $parameters['searchterms']);

			foreach ($terms as $term)
			{
				// search in database
				$sql = "SELECT ta.*, t.*, u.user_username, tt.tasktype_name, g.group_name, ";
				$sql .= "DATE_FORMAT(t.task_end, '%d.%m.%Y') as task_end, DATE_FORMAT(t.task_begin, '%d.%m.%Y') as task_begin ";
				$sql .= "FROM tasks t ";
				$sql .= "LEFT JOIN tags_to_tasks t2t ON t.task_id = t2t.tagtasks_task ";
				$sql .= "LEFT JOIN tags ta ON t2t.tagtasks_tag = ta.tag_id ";
				$sql .= "LEFT JOIN tasks_to_users tu ON t.task_id = tu.taskusers_task ";
				$sql .= "LEFT JOIN users u ON tu.taskusers_user = u.user_id ";
				$sql .= "LEFT JOIN tasktypes tt ON t.task_type = tt.tasktype_id ";
				$sql .= "LEFT JOIN taskworkflow twf ON t.task_workflow = twf.taskworkflow_id ";
				$sql .= "LEFT JOIN groups g ON twf.taskworkflow_group = g.group_id ";
				$sql .= "WHERE t.task_name LIKE '%" . $term . "%' OR t.task_description LIKE '%" . $term . "%' OR ta.tag_text LIKE '%" . $term . "%'";
				$sql .= "AND t.task_instance='" . $userfunctions->getUserInstance() . "' ";
				$res = $this->database->query($sql);
				if (!$res)
				{
					$this->debug->write('Problem getting search results from database', 'warning');
					$this->messages->setMessage('Problem getting search results from database', 'warning');
					$this->debug->unguard(false);
					return false;
				}

				// store result and add score
				$tempitems = array();
				$tempscore = array();
				while($row = $this->database->fetchArray($res))
				{
					$score = 0;

					if (strpos(strtoupper($row['task_name']), strtoupper($term)) !== false)
					{
						if (empty($tempitems[$row['task_id']])) $score += 3;
					}

					if (strpos(strtoupper($row['task_description']), strtoupper($term)) !== false)
					{
						if (empty($tempitems[$row['task_id']])) $score += 1;
					}

					if (strpos(strtoupper($row['tag_text']), strtoupper($term)) !== false)
					{
						$score += 6;
					}

					if ($score > 0)
					{
						if (empty($tempitems[$row['task_id']]))
						{
							$tempitems[$row['task_id']] = $row;
							$tempscore[$row['task_id']] = $score;
						}
						else
						{
							$tempscore[$row['task_id']] += $score;
						}
					}
				}

				// fill the real item arrays
				foreach ($tempitems as $itemkey => $itemvalue)
				{
					if (empty($items[$itemkey]))
					{
						$items[$itemkey] = $itemvalue;
						$itemscore[$itemkey] = $tempscore[$itemkey];
					}
					else
					{
						$itemscore[$itemkey] += $tempscore[$itemkey];
					}
				}
			}

			arsort($itemscore);
			$tempitems = array();
			foreach ($itemscore as $itemid => $score)
			{
				$items[$itemid]['score'] = $score;
				$tempitems[] = $items[$itemid];
			}

			$items = $tempitems;
		}

		$xmlData = $this->dataserver->createXMLDatasetFromArray($items);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}

}
?>
