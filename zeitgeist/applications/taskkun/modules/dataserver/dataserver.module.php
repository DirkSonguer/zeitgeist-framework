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


	/**
	 * gets all tasks for the current user
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. none are used
	 *
	 * @return boolean
	 */
	public function getusertasks($parameters=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT SUM(tl.tasklog_hoursworked) as task_hoursworked, t.*, tu.*, g.group_name, ";
		$sql .= "DATE_FORMAT(t.task_end, '%d.%m.%Y') as task_end, DATE_FORMAT(t.task_begin, '%d.%m.%Y') as task_begin, ";
		$sql .= "CASE WHEN ((t.task_end < CURDATE()) && (t.task_end != '00.00.0000')) THEN '2' ";
		$sql .= "WHEN ((t.task_end = CURDATE()) && (t.task_end != '00.00.0000'))  THEN '1' ";
		$sql .= "ELSE '0' END as task_late, ";
		$sql .= "CASE WHEN (SUM(tl.tasklog_hoursworked) > t.task_hoursplanned) THEN '2' ";
		$sql .= "WHEN ((SUM(tl.tasklog_hoursworked) <= task_hoursplanned) AND (SUM(tl.tasklog_hoursworked) >= t.task_hoursplanned) )  THEN '1' ";
		$sql .= "ELSE '0' END as task_overdrawn ";
		$sql .= "FROM tasks_to_users tu ";
		$sql .= "LEFT JOIN tasks t ON tu.taskusers_task = t.task_id LEFT JOIN tasklogs tl ON t.task_id = tl.tasklog_task ";
		$sql .= "LEFT JOIN taskworkflows twf ON t.task_workflow = twf.taskworkflow_id ";
		$sql .= "LEFT JOIN users_to_groups u2g ON twf.taskworkflow_group = u2g.usergroup_group ";
		$sql .= "LEFT JOIN groups g ON u2g.usergroup_group = g.group_id ";
		$sql .= "WHERE taskusers_user='" . $this->user->getUserID() . "' ";
		$sql .= "AND u2g.usergroup_user = '" . $this->user->getUserID() . "' ";
		$sql .= "AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= "GROUP BY t.task_id ";
		$sql .= "ORDER BY DATEDIFF(NOW(), t.task_end) DESC";

		$taskinformation = array();

		$res = $this->database->query($sql);
		while($row = $this->database->fetchArray($res))
		{
			$taskinformation[$row['task_id']] = $row;
		}

		$sql = "SELECT t.task_id, u.user_username as tasklog_username, tl.*, DATE_FORMAT(tl.tasklog_date, '%d.%m.%Y') as tasklog_date, ";
		$sql .= "IF (tl.tasklog_creator='" . $this->user->getUserID() . "', '1', '0') as tasklog_editdelete ";
		$sql .= "FROM tasks_to_users tu ";
		$sql .= "LEFT JOIN tasklogs tl ON tu.taskusers_task = tl.tasklog_task ";
		$sql .= "LEFT JOIN users u ON tu.taskusers_user = user_id ";
		$sql .= "LEFT JOIN tasks t ON tl.tasklog_task = t.task_id ";
		$sql .= "WHERE t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";

		$res = $this->database->query($sql);
		while($row = $this->database->fetchArray($res))
		{
			if (!empty($taskinformation[$row['task_id']]))
			{
				$taskinformation[$row['task_id']]['task_tasklogs'][] = $row;
			}
		}

		$xmlData = $this->dataserver->createXMLDatasetFromArray($taskinformation);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets all tasks for all groups of the the current user
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. none are used
	 *
	 * @return boolean
	 */
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
		$sql .= "LEFT JOIN taskworkflows twf ON t.task_workflow = twf.taskworkflow_id ";
		$sql .= "LEFT JOIN users_to_groups u2g ON twf.taskworkflow_group = u2g.usergroup_group ";
		$sql .= "LEFT JOIN groups g ON u2g.usergroup_group = g.group_id ";
		$sql .= "WHERE tu.taskusers_user is null ";
		$sql .= "AND u2g.usergroup_user = '" . $this->user->getUserID() . "' ";
		$sql .= "AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= "GROUP BY t.task_id ";
		$sql .= "ORDER BY DATEDIFF(NOW(), t.task_end) DESC";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets all active tasks for the current instance
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. none are used
	 *
	 * @return boolean
	 */
	public function getactivetasks($parameters=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT SUM(tl.tasklog_hoursworked) as task_hoursworked, t.*, u.user_username, tt.tasktype_name, g.group_name, g.group_id, ";
		$sql .= "DATE_FORMAT(t.task_end, '%d.%m.%Y') as task_end, DATE_FORMAT(t.task_begin, '%d.%m.%Y') as task_begin, ";
		$sql .= "CASE WHEN ((t.task_end < CURDATE()) && (t.task_end != '00.00.0000')) THEN '2' ";
		$sql .= "WHEN ((t.task_end = CURDATE()) && (t.task_end != '00.00.0000'))  THEN '1' ";
		$sql .= "ELSE '0' END as task_late, ";
		$sql .= "CASE WHEN (SUM(tl.tasklog_hoursworked) > task_hoursplanned) THEN '2' ";
		$sql .= "WHEN ((SUM(tl.tasklog_hoursworked) <= task_hoursplanned) AND (SUM(tl.tasklog_hoursworked) >= task_hoursplanned) )  THEN '1' ";
		$sql .= "ELSE '0' END as task_overdrawn ";
		$sql .= "FROM tasks t ";
		$sql .= "LEFT JOIN tasks_to_users tu ON t.task_id = tu.taskusers_task ";
		$sql .= "LEFT JOIN users u ON tu.taskusers_user = u.user_id ";
		$sql .= "LEFT JOIN tasklogs tl ON t.task_id = tl.tasklog_task ";
		$sql .= "LEFT JOIN tasktypes tt ON t.task_type = tt.tasktype_id ";
		$sql .= "LEFT JOIN taskworkflows twf ON t.task_workflow = twf.taskworkflow_id ";
		$sql .= "LEFT JOIN groups g ON twf.taskworkflow_group = g.group_id ";
		$sql .= "WHERE t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= "AND t.task_workflow > '0' ";
		$sql .= "GROUP BY t.task_id ";
		$sql .= "ORDER BY DATEDIFF(NOW(), t.task_end) DESC";

		$taskinformation = array();

		$res = $this->database->query($sql);
		while($row = $this->database->fetchArray($res))
		{
			$taskinformation[$row['task_id']] = $row;
		}

		$sql = "SELECT t.task_id, ta.tag_text FROM tasks t ";
		$sql .= "LEFT JOIN tags_to_tasks t2t ON t.task_id = tagtasks_task ";
		$sql .= "LEFT JOIN tags ta ON t2t.tagtasks_tag = ta.tag_id ";
		$sql .= "WHERE t.task_workflow > '0' AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";

		$res = $this->database->query($sql);
		while($row = $this->database->fetchArray($res))
		{
			if (!empty($taskinformation[$row['task_id']]))
			{
				if (!empty($taskinformation[$row['task_id']]['task_tags']))
				{
					$taskinformation[$row['task_id']]['task_tags'] .= ', ' . $row['tag_text'];
				}
				else
				{
					$taskinformation[$row['task_id']]['task_tags'] = $row['tag_text'];
				}
			}
		}

		$sql = "SELECT t.task_id, u.user_username as tasklog_username, tl.tasklog_description, tl.tasklog_hoursworked, ";
		$sql .= "DATE_FORMAT(tl.tasklog_date, '%d.%m.%Y') as tasklog_date ";
		$sql .= "FROM tasks t ";
		$sql .= "LEFT JOIN tasklogs tl ON t.task_id = tl.tasklog_task ";
		$sql .= "LEFT JOIN users u ON tl.tasklog_creator = u.user_id ";
		$sql .= "WHERE tl.tasklog_id is not null AND t.task_workflow > '0' AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";

		$res = $this->database->query($sql);
		while($row = $this->database->fetchArray($res))
		{
			if (!empty($taskinformation[$row['task_id']]))
			{
				$taskinformation[$row['task_id']]['task_tasklogs'][] = $row;
			}
		}

		$xmlData = $this->dataserver->createXMLDatasetFromArray($taskinformation);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets all archived tasks for the current instance
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. none are used
	 *
	 * @return boolean
	 */
	public function getarchivedtasks($parameters=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT SUM(tl.tasklog_hoursworked) as task_hoursworked, t.*, u.user_username, tt.tasktype_name, g.group_name, ";
		$sql .= "DATE_FORMAT(t.task_end, '%d.%m.%Y') as task_end, DATE_FORMAT(t.task_begin, '%d.%m.%Y') as task_begin, ";
		$sql .= "CASE WHEN ((t.task_end < CURDATE()) && (t.task_end != '00.00.0000')) THEN '2' ";
		$sql .= "WHEN ((t.task_end = CURDATE()) && (t.task_end != '00.00.0000'))  THEN '1' ";
		$sql .= "ELSE '0' END as task_late, ";
		$sql .= "CASE WHEN (SUM(tl.tasklog_hoursworked) > task_hoursplanned) THEN '2' ";
		$sql .= "WHEN ((SUM(tl.tasklog_hoursworked) <= task_hoursplanned) AND (SUM(tl.tasklog_hoursworked) >= task_hoursplanned) )  THEN '1' ";
		$sql .= "ELSE '0' END as task_overdrawn ";
		$sql .= "FROM tasks t ";
		$sql .= "LEFT JOIN tasks_to_users tu ON t.task_id = tu.taskusers_task ";
		$sql .= "LEFT JOIN users u ON tu.taskusers_user = u.user_id ";
		$sql .= "LEFT JOIN tasklogs tl ON t.task_id = tl.tasklog_task ";
		$sql .= "LEFT JOIN tasktypes tt ON t.task_type = tt.tasktype_id ";
		$sql .= "LEFT JOIN taskworkflows twf ON t.task_workflow = twf.taskworkflow_id ";
		$sql .= "LEFT JOIN groups g ON twf.taskworkflow_group = g.group_id ";
		$sql .= "WHERE t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= "AND t.task_workflow='0' ";
		$sql .= "GROUP BY t.task_id";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets all tasklogs for a given task
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. the id of the task is given
	 *
	 * @return boolean
	 */
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

		$sql = "SELECT t.*, u.user_username as tasklog_username, ";
		$sql .= "DATE_FORMAT(t.tasklog_timestamp, '%H:%i:%s, %d.%m.%Y') as tasklog_timestamp, ";
		$sql .= "DATE_FORMAT(t.tasklog_date, '%d.%m.%Y') as tasklog_date, ";
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


	// instance-safe
	public function gettaskinformation($parameters=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT * FROM tasks t WHERE t.task_id='" . $parameters['id'] . "' ";
		$sql .= "AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets all information about the current user
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. none are used
	 *
	 * @return boolean
	 */
	public function getuserinformation($parameters=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = 'SELECT COUNT(ttu.taskusers_task) as user_taskcount, u.user_id, u.user_active, u.user_username, ud.*, ur.userrole_id, ur.userrole_name, g.group_name ';
		$sql .= 'FROM users u ';
		$sql .= 'LEFT JOIN userdata ud ON u.user_id = ud.userdata_user ';
		$sql .= 'LEFT JOIN userroles_to_users u2u ON u2u.userroleuser_user = u.user_id ';
		$sql .= 'LEFT JOIN userroles ur ON u2u.userroleuser_userrole = ur.userrole_id ';
		$sql .= 'LEFT JOIN users_to_groups u2g ON u.user_id = u2g.usergroup_user ';
		$sql .= 'LEFT JOIN groups g ON u2g.usergroup_group = g.group_id ';
		$sql .= 'LEFT JOIN tasks_to_users ttu ON u.user_id = ttu.taskusers_user ';
		$sql .= "WHERE u.user_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
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

		$xmlData = $this->dataserver->createXMLDatasetFromArray($userinformation);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets all userroles for the current instance
	 *
	 * the userroles are global for the application, so we don't need to check the instance
	 *
	 * @param array $parameters contains the parameters of the call. none are used
	 *
	 * @return boolean
	 */
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


	/**
	 * gets all the tasktypes for the current instance
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. none are used
	 *
	 * @return boolean
	 */
	public function gettasktypes($parameters=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT tt.*, COUNT(t.task_id) as task_count FROM tasktypes tt ";
		$sql .= "LEFT JOIN tasks t ON tt.tasktype_id = t.task_type ";
		$sql .= "WHERE tt.tasktype_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= "GROUP BY tt.tasktype_id";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql, $this->database);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets all the groups for the current instance
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. none are used
	 *
	 * @return boolean
	 */
	public function getgroups($parameters=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = 'SELECT g.group_id, g.group_name, g.group_description, u2g.usergroup_user, twf.taskworkflow_tasktype, ';
		$sql .= 'COUNT(g.group_id) as group_tasktypecount ';
		$sql .= 'FROM groups g ';
		$sql .= 'LEFT JOIN taskworkflows twf ON g.group_id = twf.taskworkflow_group ';
		$sql .= 'LEFT JOIN users_to_groups u2g ON g.group_id = u2g.usergroup_group ';
		$sql .= "WHERE g.group_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= 'GROUP BY u2g.usergroup_user, group_id ORDER BY g.group_name';

		$res = $this->database->query($sql);
		$groupinformation = array();
		while($row = $this->database->fetchArray($res))
		{
			if (empty($groupinformation[$row['group_id']]))
			{
				$groupinformation[$row['group_id']] = $row;
				if (!empty($row['usergroup_user']))
				{
					$groupinformation[$row['group_id']]['group_usercount'] = 1;
				}
				else
				{
					$groupinformation[$row['group_id']]['group_usercount'] = 0;
				}

				if (empty($row['taskworkflow_tasktype']))
				{
					$groupinformation[$row['group_id']]['group_tasktypecount'] = 0;
				}
			}
			else
			{
				$groupinformation[$row['group_id']]['group_usercount'] += 1;
			}
		}

		$xmlData = $this->dataserver->createXMLDatasetFromArray($groupinformation);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets all tags for the current instance
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. none are used
	 *
	 * @return boolean
	 */
	public function getalltags($parameters=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = 'SELECT ta.*, COUNT(ta.tag_id) as tag_count FROM taskkun.tasks t ';
		$sql .= 'LEFT JOIN tags_to_tasks t2t ON t.task_id = t2t.tagtasks_task ';
		$sql .= 'LEFT JOIN tags ta ON t2t.tagtasks_tag = ta.tag_id ';
		$sql .= "AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= 'GROUP BY ta.tag_id ';
		$sql .= 'ORDER BY tag_text';

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting search results from database', 'warning');
			$this->messages->setMessage('Problem getting search results from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// store result and add score
		$tags = array();
		$tagvalues = array();
		while($row = $this->database->fetchArray($res))
		{
			$tags[] = $row;
			$tagvalues[$row['tag_id']] = $row['tag_count'];
		}

		$maxvalue = max($tagvalues);
		if ($maxvalue == 0) $maxvalue = 1;

		foreach($tags as $tagkey => $tagvalue)
		{
			$tags[$tagkey]['tag_score'] = floor(($tagvalue['tag_count']/$maxvalue)*5);
		}

		$xmlData = $this->dataserver->createXMLDatasetFromArray($tags);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * searches all data in the current instance
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. $searchterms contains a string with the user search
	 *
	 * @return boolean
	 */
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
				$sql .= "LEFT JOIN taskworkflows twf ON t.task_workflow = twf.taskworkflow_id ";
				$sql .= "LEFT JOIN groups g ON twf.taskworkflow_group = g.group_id ";
				$sql .= "WHERE (t.task_name LIKE '%" . $term . "%' OR t.task_description LIKE '%" . $term . "%' OR ta.tag_text LIKE '%" . $term . "%') ";
				$sql .= "AND t.task_workflow > 0 AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";

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


	/**
	 * gets data for worked hours for report
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. none are used
	 *
	 * @return boolean
	 */
	public function workedhourschartdata($parameters=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		if (empty($parameters['databegin']))
		{
			$parameters['databegin'] = date("Y-m-d", time()-(86400 * 14));
		}
		else
		{
			$dateArray = explode('.', $parameters['databegin']);
			$parameters['databegin'] = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];
		}

		if (empty($parameters['dataend']))
		{
			$parameters['dataend'] = date("Y-m-d", time()+(86400));
		}
		else
		{
			$dateArray = explode('.', $parameters['dataend']);
			$parameters['dataend'] = $dateArray[2] . '-' . $dateArray[1] . '-' . ($dateArray[0]+1);
		}

		if ( (empty($parameters['group'])) || ($parameters['group'] == -1) )
		{
			$groupstring = '';
		}
		else
		{
			$groupstring = "AND twf.taskworkflow_group = '" . $parameters['group'] . "' ";
		}

		if ( (empty($parameters['user'])) || ($parameters['user'] == -1) )
		{
			$userstring = '';
		}
		else
		{
			$userstring = "AND tl.tasklog_creator = '" . $parameters['user'] . "' ";
		}

		$sql = "SELECT SUM(tl.tasklog_hoursworked) as hoursworked, DATE_FORMAT(DATE(tl.tasklog_timestamp), '%d.%m.%Y') as dateworked FROM tasklogs tl ";
		$sql .= "LEFT JOIN taskworkflows twf ON tl.tasklog_taskworkflow = twf.taskworkflow_id ";

		$sql .= "WHERE tl.tasklog_timestamp > '" . $parameters['databegin'] . "' AND tl.tasklog_timestamp < '" . $parameters['dataend'] . "' ";
		$sql .= "AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= $groupstring;
		$sql .= $userstring;
		$sql .= "GROUP BY DATE(tl.tasklog_timestamp) ORDER BY tl.tasklog_timestamp";
		$res = $this->database->query($sql);

		$dataArray = array();
		$descArray = array();
		$maxValue = 0;
		$bar = new bar_outline( 50, '#dddddd', '#000000' );

		$tpl = new tkTemplate();
		$params = array();
		$linedata = array();
		while ($row = $this->database->fetchArray($res))
		{
			if ($maxValue < $row['hoursworked']) $maxValue = $row['hoursworked'];
			$linedata[] = $row['hoursworked'];
			$bar->add($row['hoursworked'], false);
			$descArray[] = $row['dateworked'];
		}

		$g = new graph();
		$g->title( 'Stundenübersicht der eingetragenen Aufgabenbeschreibungen', '{font-size: 12px;}' );
		$g->bg_colour = '#fafafa';

		if ( (empty($parameters['mode'])) || ($parameters['mode'] == 1) )
		{
			$g->data_sets[] = $bar;
			$g->set_x_labels($descArray);
			$g->set_x_label_style( 9, '#000000', 0, 1 );
			$g->set_x_axis_steps( 1 );
			$g->set_y_max( $maxValue+1 );
			$g->y_label_steps( 4 );
		}
		else
		{
			$g->set_data( $linedata );
			$g->line_dot( 2, 3, '#555555', '', 10 );
			$g->set_x_labels($descArray);
			$g->set_x_label_style( 9, '#000000', 0, 1 );
			$g->set_y_max( $maxValue+1 );
			$g->y_label_steps( 4 );
		}

		echo $g->render();
		die();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets data for finished tasks for report
	 *
	 * instance-safe!
	 *
	 * @param array $parameters contains the parameters of the call. none are used
	 *
	 * @return boolean
	 */
	public function finishedtaskschartdata($parameters=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		if (empty($parameters['databegin']))
		{
			$parameters['databegin'] = date("Y-m-d", time()-(86400 * 14));
		}
		else
		{
			$dateArray = explode('.', $parameters['databegin']);
			$parameters['databegin'] = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];
		}

		if (empty($parameters['dataend']))
		{
			$parameters['dataend'] = date("Y-m-d", time()+(86400));
		}
		else
		{
			$dateArray = explode('.', $parameters['dataend']);
			$parameters['dataend'] = $dateArray[2] . '-' . $dateArray[1] . '-' . ($dateArray[0]+1);
		}

		if ( (empty($parameters['tasktype'])) || ($parameters['tasktype'] == -1) )
		{
			$tasktypestring = '';
		}
		else
		{
			$tasktypestring = "AND t.task_type = '" . $parameters['tasktype'] . "' ";
		}

		if ( (empty($parameters['user'])) || ($parameters['user'] == -1) )
		{
			$userstring = '';
		}
		else
		{
			$userstring = "AND t.task_creator = '" . $parameters['user'] . "' ";
		}

		$sql = "SELECT COUNT(t.task_id) as finishedtasks, DATE_FORMAT(DATE(t.task_timestamp), '%d.%m.%Y') as datefinished FROM tasks t ";
		$sql .= "LEFT JOIN users u ON t.task_creator = u.user_id ";
		$sql .= "LEFT JOIN users_to_groups u2g ON u.user_id = u2g.usergroup_user ";
		$sql .= "WHERE t.task_timestamp > '" . $parameters['databegin'] . "' AND t.task_timestamp < '" . $parameters['dataend'] . "' ";
		$sql .= "AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= "AND t.task_workflow = '0' ";
		$sql .= $tasktypestring;
		$sql .= $userstring;
		$sql .= "GROUP BY DATE(t.task_timestamp) ORDER BY t.task_timestamp";
		$res = $this->database->query($sql);

//		die($sql);

		$dataArray = array();
		$descArray = array();
		$maxValue = 0;
		$bar = new bar_outline( 50, '#dddddd', '#000000' );

		$tpl = new tkTemplate();
		$params = array();
		$linedata = array();
		while ($row = $this->database->fetchArray($res))
		{
			if ($maxValue < $row['finishedtasks']) $maxValue = $row['finishedtasks'];
			$linedata[] = $row['finishedtasks'];
			$bar->add($row['finishedtasks'], false);
			$descArray[] = $row['datefinished'];
		}

		$g = new graph();
		$g->title( 'Übersicht über abgeschlossene Aufgaben', '{font-size: 12px;}' );
		$g->bg_colour = '#fafafa';

		if ( (empty($parameters['mode'])) || ($parameters['mode'] == 1) )
		{
			$g->data_sets[] = $bar;
			$g->set_x_labels($descArray);
			$g->set_x_label_style( 9, '#000000', 0, 1 );
			$g->set_x_axis_steps( 1 );
			$g->set_y_max( $maxValue+1 );
			$g->y_label_steps( 4 );
		}
		else
		{
			$g->set_data( $linedata );
			$g->line_dot( 2, 3, '#555555', '', 10 );
			$g->set_x_labels($descArray);
			$g->set_x_label_style( 9, '#000000', 0, 1 );
			$g->set_y_max( $maxValue+1 );
			$g->y_label_steps( 4 );
		}

		echo $g->render();
		die();

		$this->debug->unguard(true);
		return true;
	}

}
?>
