<?php

defined('TASKKUN_ACTIVE') or die();

class tkTaskfunctions
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function addTask($taskdata=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		if (empty($taskdata['task_hoursplanned'])) $taskdata['task_hoursplanned'] = '0';
		if (empty($taskdata['task_begin']))
		{
			$taskdata['task_begin'] = '';
		}
		else
		{
			$dateArray = explode('.', $taskdata['task_begin']);
			$taskdata['task_begin'] = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];
		}

		if (empty($taskdata['task_end']))
		{
			$taskdata['task_end'] = '';
		}
		else
		{
			$dateArray = explode('.', $taskdata['task_end']);
			$taskdata['task_end'] = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];
		}

		if (empty($taskdata['task_notes'])) $taskdata['task_notes'] = '';
		if (strpos($taskdata['task_hoursplanned'], ',') !== false) $taskdata['task_hoursplanned'] = str_replace(',','.', $taskdata['task_hoursplanned']);

		// get initial task workflow status
		$sql = "SELECT taskworkflow_id FROM taskworkflow WHERE taskworkflow_tasktype='" . $taskdata['task_type'] . "' ORDER BY taskworkflow_order LIMIT 1";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting tasktypes from database', 'warning');
			$this->messages->setMessage('Problem getting tasktypes from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);
		$initialstatus = $row['taskworkflow_id'];

		$sql = 'INSERT INTO tasks(task_creator, task_name, task_description, task_hoursplanned, task_type, task_workflow, task_priority, task_begin, task_end, task_notes, task_instance) ';
		$sql .= "VALUES('" . $this->user->getUserID() . "', ";
		$sql .= "'" . $taskdata['task_name'] . "', '" . $taskdata['task_description'] . "', '" . $taskdata['task_hoursplanned'] . "', '" . $taskdata['task_type'] . "', ";
		$sql .= "'" . $initialstatus . "', '" . $taskdata['task_priority'] . "', '" . $taskdata['task_begin'] . "', '" . $taskdata['task_end'] . "', '" . $taskdata['task_notes'] . "', '" . $userfunctions->getUserInstance($this->user->getUserID()) . "')";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem writing taskdata to the database', 'warning');
			$this->messages->setMessage('Problem writing taskdata to the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		$insertid = $this->database->insertId();

		$this->storeTags($taskdata['task_tags'], $insertid);

		$this->debug->unguard(true);
		return true;
	}


	public function updateTask($taskdata=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($taskdata['task_id']))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (empty($taskdata['task_hoursplanned'])) $taskdata['task_hoursplanned'] = '0';
		if (empty($taskdata['task_begin']))
		{
			$taskdata['task_begin'] = '';
		}
		else
		{
			$dateArray = explode('.', $taskdata['task_begin']);
			$taskdata['task_begin'] = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];
		}

		if (empty($taskdata['task_end']))
		{
			$taskdata['task_end'] = '';
		}
		else
		{
			$dateArray = explode('.', $taskdata['task_end']);
			$taskdata['task_end'] = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];
		}

		if (empty($taskdata['task_notes'])) $taskdata['task_notes'] = '';
		if (strpos($taskdata['task_hoursplanned'], ',') !== false) $taskdata['task_hoursplanned'] = str_replace(',','.', $taskdata['task_hoursplanned']);

		$sql = "UPDATE tasks SET task_name='" . $taskdata['task_name'] . "', task_description='" . $taskdata['task_description'] . "', ";
		$sql .= "task_hoursplanned='" . $taskdata['task_hoursplanned'] . "', task_priority='" . $taskdata['task_priority'] . "', ";
		$sql .= "task_begin='" . $taskdata['task_begin'] . "', task_end='" . $taskdata['task_end'] . "', task_notes='" . $taskdata['task_notes'] . "' ";
		$sql .= "WHERE task_id='" . $taskdata['task_id'] . "'";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem writing taskdata to the database', 'warning');
			$this->messages->setMessage('Problem writing taskdata to the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->storeTags($taskdata['task_tags'], $taskdata['task_id']);

		$this->debug->unguard(true);
		return true;
	}


	public function deleteTask($taskid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($taskid))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM tasks_to_users WHERE taskusers_task='" . $taskid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting task from tasks_to_users: ' . $taskid, 'warning');
			$this->messages->setMessage('Problem deleting task from tasks_to_users: ' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM tasklogs WHERE tasklog_task='" . $taskid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting tasklogs for task: ' . $taskid, 'warning');
			$this->messages->setMessage('Problem deleting tasklogs for task: ' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM tasks WHERE task_id='" . $taskid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting task from tasks: ' . $taskid, 'warning');
			$this->messages->setMessage('Problem deleting task from tasks: ' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function addTasklog($tasklogdata=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($tasklogdata['tasklog_task']))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (empty($tasklogdata['tasklog_date']))
		{
			$tasklogdata['tasklog_date'] = '';
		}
		else
		{
			$dateArray = explode('.', $tasklogdata['tasklog_date']);
			$tasklogdata['tasklog_date'] = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];
		}

		if (strpos($tasklogdata['tasklog_hoursworked'], ',') !== false) $tasklogdata['tasklog_hoursworked'] = str_replace(',','.', $tasklogdata['tasklog_hoursworked']);

		$sql = 'INSERT INTO tasklogs(tasklog_creator, tasklog_task, tasklog_description, tasklog_hoursworked, tasklog_date) ';
		$sql .= "VALUES('" . $this->user->getUserID() . "', '" . $tasklogdata['tasklog_task'] . "', '" . $tasklogdata['tasklog_description'] . "', '" . $tasklogdata['tasklog_hoursworked'] . "', '" . $tasklogdata['tasklog_date'] . "')";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting task from tasks_to_users: ' . $tasklogdata['taskid'], 'warning');
			$this->messages->setMessage('Problem deleting task from tasks_to_users: ' . $tasklogdata['taskid'], 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function updateTasklog($tasklogdata=array())
	{
		$this->debug->guard();

		$sql = "SELECT * FROM tasklogs WHERE tasklog_id='" . $tasklogdata['tasklog_id'] . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting tasklog information for tasklog: ' . $tasklogdata['tasklog_id'], 'warning');
			$this->messages->setMessage('Problem getting tasklog information for tasklog: ' . $tasklogdata['tasklog_id'], 'warning');
			$this->debug->unguard(false);
			return false;
		}
		$taskloginformation = $this->database->fetchArray($res);

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($taskloginformation['tasklog_task']))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (empty($tasklogdata['tasklog_date']))
		{
			$tasklogdata['tasklog_date'] = '';
		}
		else
		{
			$dateArray = explode('.', $tasklogdata['tasklog_date']);
			$tasklogdata['tasklog_date'] = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];
		}

		if (strpos($tasklogdata['tasklog_hoursworked'], ',') !== false) $tasklogdata['tasklog_hoursworked'] = str_replace(',','.', $tasklogdata['tasklog_hoursworked']);

		$sql = "UPDATE tasklogs SET tasklog_description='" . $tasklogdata['tasklog_description'] . "', tasklog_hoursworked='" . $tasklogdata['tasklog_hoursworked'] . "', ";
		$sql .= "tasklog_date='" . $tasklogdata['tasklog_date'] . "' WHERE tasklog_id='" . $tasklogdata['tasklog_id'] . "'";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem updating tasklog: ' . $tasklogdata['tasklog_id'], 'warning');
			$this->messages->setMessage('Problem updating tasklog: ' . $tasklogdata['tasklog_id'], 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function deleteTasklog($tasklogid, $taskid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($taskid))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM tasklogs WHERE tasklog_id='" . $tasklogid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting tasklog: ' . $tasklogid, 'warning');
			$this->messages->setMessage('Problem deleting tasklog: ' . $tasklogid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function getTasklog($tasklogid)
	{
		$this->debug->guard();

		$sql = "SELECT tl.*, ";
		$sql .= "DATE_FORMAT(tl.tasklog_date, '%d.%m.%Y') as tasklog_date ";
		$sql .= " FROM tasklogs tl ";
		$sql .= "WHERE tasklog_id='" . $tasklogid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting tasklog information for tasklog: ' . $tasklogid, 'warning');
			$this->messages->setMessage('Problem getting tasklog information for tasklog: ' . $tasklogid, 'warning');
			$this->debug->unguard(false);
			return false;
		}
		$taskloginformation = $this->database->fetchArray($res);

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($taskloginformation['tasklog_task']))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($taskloginformation);
		return $taskloginformation;
	}


	public function getTaskInformation($taskid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($taskid))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// task information
		$sql = "SELECT t.*, ";
		$sql .= "DATE_FORMAT(t.task_end, '%d.%m.%Y') as task_end, DATE_FORMAT(t.task_begin, '%d.%m.%Y') as task_begin ";
		$sql .= "FROM tasks t ";
		$sql .= "WHERE task_id='" . $taskid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting task information for task: ' . $taskid, 'warning');
			$this->messages->setMessage('Problem getting task information for task: ' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}
		$taskinformation = $this->database->fetchArray($res);

		// tags
		$sql = "SELECT * FROM tags t ";
		$sql .= "LEFT JOIN tags_to_tasks t2t ON t.tag_id = t2t.tagtasks_tag ";
		$sql .= "WHERE t2t.tagtasks_task = '" . $taskid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting tag information for task: ' . $taskid, 'warning');
			$this->messages->setMessage('Problem getting tag information for task: ' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$Tasktags = array();
		while ($row = $this->database->fetchArray($res))
		{
			$Tasktags[] = $row['tag_text'];
		}
		$taskinformation['task_tags'] = implode(', ', $Tasktags);

		// tasklogs
		$sql = "SELECT tasklog_id, tasklog_description, tasklog_hoursworked, tasklog_timestamp FROM tasklogs tl WHERE tasklog_task = '" . $taskid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting tasklog information for task: ' . $taskid, 'warning');
			$this->messages->setMessage('Problem getting tasklog information for task: ' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$Tasklogs = array();
		while ($row = $this->database->fetchArray($res))
		{
			$Tasklogs[] = $row;
		}
		$taskinformation['task_tasklogs'] = $Tasklogs;

		$this->debug->unguard($taskinformation);
		return $taskinformation;
	}


	public function getTaskTypes()
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT * FROM tasktypes WHERE tasktype_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting tasktypes from database', 'warning');
			$this->messages->setMessage('Problem getting tasktypes from database' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$tasktypes = array();
		while($row = $this->database->fetchArray($res))
		{
			$tasktypes[] = $row;
		}

		$this->debug->unguard($tasktypes);
		return $tasktypes;
	}


	public function getTaskTypesForUser()
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT * FROM tasktypes tt ";
		$sql .= "LEFT JOIN taskworkflow twf ON tt.tasktype_id = twf.taskworkflow_tasktype ";
		$sql .= "LEFT JOIN users_to_groups u2g ON twf.Taskworkflow_group = u2g.usergroup_group ";
		$sql .= "WHERE tt.tasktype_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= "AND u2g.usergroup_user='" . $this->user->getUserID() . "' ";
		$sql .= "GROUP BY tt.tasktype_id";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting tasktypes from database', 'warning');
			$this->messages->setMessage('Problem getting tasktypes from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$tasktypes = array();
		while($row = $this->database->fetchArray($res))
		{
			$tasktypes[] = $row;
		}

		$this->debug->unguard($tasktypes);
		return $tasktypes;
	}


	public function getTasktypeInformation($tasktypeid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT tt.*, COUNT(t.task_id) as tasktype_count FROM tasktypes tt LEFT JOIN tasks t ON tt.tasktype_id = t.task_type ";
		$sql .= "WHERE tt.tasktype_id='" . $tasktypeid . "' AND tt.tasktype_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= "GROUP BY tt.tasktype_id;";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting tasktype information from database: '. $tasktypeid, 'warning');
			$this->messages->setMessage('Problem getting tasktype information from database: ' . $tasktypeid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);

		$this->debug->unguard($row);
		return $row;
	}


	public function getWorkflowInformation($tasktypeid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT * FROM taskworkflow t WHERE taskworkflow_tasktype='" . $tasktypeid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting workflow for tasktype from database: '. $tasktypeid, 'warning');
			$this->messages->setMessage('Problem getting workflow for tasktype from database: ' . $tasktypeid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$workflow = array();
		while ($row = $this->database->fetchArray($res))
		{
			$workflow[] = $row;
		}

		$this->debug->unguard($workflow);
		return $workflow;
	}


	public function getNumberofUsertasks()
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT COUNT(t.task_id) as open_usertasks FROM tasks_to_users tu LEFT JOIN tasks t ON tu.taskusers_task = t.task_id ";
		$sql .= "WHERE tu.taskusers_user='" . $this->user->getUserID() . "' AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$ret = $row['open_usertasks'];

		$this->debug->unguard($ret);
		return $ret;
	}


	public function getNumberofGrouptasks()
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT COUNT(t.task_id) as open_grouptasks FROM tasks t ";
		$sql .= "LEFT JOIN tasks_to_users tu ON t.task_id = tu.taskusers_task LEFT JOIN users u ON tu.taskusers_user = u.user_id ";
		$sql .= "WHERE taskusers_user is null AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$ret = $row['open_grouptasks'];

		$this->debug->unguard($ret);
		return $ret;
	}


	public function storeTags($tagstring, $taskid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($taskid))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if ($tagstring == '')
		{
			$this->debug->write('Tagstring is empty: no tags found', 'warning');
			$this->messages->setMessage('Tagstring is empty: no tags found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$tagarray = explode(',', $tagstring);
		$sql = "DELETE FROM tags_to_tasks WHERE tagtasks_task='" . $taskid . "'";
		$res = $this->database->query($sql);

		foreach ($tagarray as $tagkey => $tagvalue)
		{
			$tagarray[$tagkey] = ltrim($tagvalue);
			$tagarray[$tagkey] = rtrim($tagarray[$tagkey]);
			$sql = "INSERT INTO tags(tag_text) VALUES('" . $tagarray[$tagkey] . "') ON DUPLICATE KEY UPDATE tag_id=LAST_INSERT_ID(tag_id), tag_text='" . $tagarray[$tagkey] . "'";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Problem writing tags to the database', 'warning');
				$this->messages->setMessage('Problem writing tags to the database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
			$insertid = $this->database->insertId();

			$sql = "INSERT INTO tags_to_tasks(tagtasks_tag, tagtasks_task) VALUES('" . $insertid . "', '" . $taskid . "')";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Problem writing tags to the database', 'warning');
				$this->messages->setMessage('Problem writing tags to the database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}

		$this->debug->unguard(true);
		return true;
	}


	public function workflowUp($taskid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($taskid))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "SELECT twf.*, t.task_workflow FROM taskworkflow twf LEFT JOIN tasks t ON twf.taskworkflow_tasktype = t.task_type ";
		$sql .= "WHERE t.task_id='" . $taskid . "' ORDER BY twf.taskworkflow_order";
		$res = $this->database->query($sql);

		$workflowOrder = array();
		while ($row = $this->database->fetchArray($res))
		{
			$workflowOrder[$row['taskworkflow_order']] = $row['taskworkflow_id'];
			$lastRow = $row;
		}

		$currentWorkflowId = array_search($lastRow['task_workflow'], $workflowOrder);
		if ($currentWorkflowId != $workflowOrder[count($workflowOrder)])
		{
			if (!empty($workflowOrder[$currentWorkflowId+1]))
			{
				$nextWorkflowId = $workflowOrder[$currentWorkflowId+1];

				$sql = "UPDATE tasks SET task_workflow='" . $nextWorkflowId . "' WHERE task_id='" . $taskid . "'";
				$res = $this->database->query($sql);
				if (!$res)
				{
					$this->debug->write('Problem writing the workflow update to the database', 'warning');
					$this->messages->setMessage('Problem writing the workflow update to the database', 'warning');
					$this->debug->unguard(false);
					return false;
				}
			}
			else
			{
				$this->debug->write('Problem defining the workflow', 'warning');
				$this->messages->setMessage('Problem defining the workflow', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		else
		{
			// archive
			$sql = "UPDATE tasks SET task_workflow='0' WHERE task_id='" . $taskid . "'";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Problem writing the workflow update to the database', 'warning');
				$this->messages->setMessage('Problem writing the workflow update to the database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}

		$sql = "DELETE FROM tasks_to_users WHERE taskusers_task='" . $taskid . "'";
		$res = $this->database->query($sql);

		$this->debug->unguard(true);
		return true;
	}


	public function workflowDown($taskid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($taskid))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "SELECT twf.*, t.task_workflow FROM taskworkflow twf LEFT JOIN tasks t ON twf.taskworkflow_tasktype = t.task_type ";
		$sql .= "WHERE t.task_id='" . $taskid . "' ORDER BY twf.taskworkflow_order";
		$res = $this->database->query($sql);

		$workflowOrder = array();
		while ($row = $this->database->fetchArray($res))
		{
			$workflowOrder[$row['taskworkflow_order']] = $row['taskworkflow_id'];
			$lastRow = $row;
		}

		$currentWorkflowId = array_search($lastRow['task_workflow'], $workflowOrder);
		if ($currentWorkflowId > 1)
		{
			if (!empty($workflowOrder[$currentWorkflowId+1]))
			{
				$nextWorkflowId = $workflowOrder[$currentWorkflowId-1];

				$sql = "UPDATE tasks SET task_workflow='" . $nextWorkflowId . "' WHERE task_id='" . $taskid . "'";
				$res = $this->database->query($sql);
				if (!$res)
				{
					$this->debug->write('Problem writing the workflow update to the database', 'warning');
					$this->messages->setMessage('Problem writing the workflow update to the database', 'warning');
					$this->debug->unguard(false);
					return false;
				}
			}
			else
			{
				$this->debug->write('Problem defining the workflow', 'warning');
				$this->messages->setMessage('Problem defining the workflow', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		else
		{
			// TODO: ins archiv
		}

		$sql = "DELETE FROM tasks_to_users WHERE taskusers_task='" . $taskid . "'";
		$res = $this->database->query($sql);

		$this->debug->unguard(true);
		return true;
	}


	public function acceptTask($taskid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($taskid))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "INSERT INTO tasks_to_users(taskusers_task, taskusers_user) VALUES ('" . $taskid . "', '" . $this->user->getUserID() . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not accept the task woth taskid: ' . $taskid, 'warning');
			$this->messages->setMessage('Could not accept the task woth taskid: ' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function declineTask($taskid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($taskid))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM tasks_to_users WHERE taskusers_task='" . $taskid . "' AND taskusers_user='" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not accept the task woth taskid: ' . $taskid, 'warning');
			$this->messages->setMessage('Could not accept the task woth taskid: ' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function addAdhoc($adhocdata=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		if (!$userfunctions->checkRightsForTask($tasklogdata['tasklog_task']))
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (empty($tasklogdata['tasklog_date']))
		{
			$tasklogdata['tasklog_date'] = '';
		}
		else
		{
			$dateArray = explode('.', $tasklogdata['tasklog_date']);
			$tasklogdata['tasklog_date'] = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];
		}

		if (strpos($tasklogdata['tasklog_hoursworked'], ',') !== false) $tasklogdata['tasklog_hoursworked'] = str_replace(',','.', $tasklogdata['tasklog_hoursworked']);

		$sql = 'INSERT INTO tasklogs(tasklog_creator, tasklog_task, tasklog_description, tasklog_hoursworked, tasklog_date) ';
		$sql .= "VALUES('" . $this->user->getUserID() . "', '" . $tasklogdata['tasklog_task'] . "', '" . $tasklogdata['tasklog_description'] . "', '" . $tasklogdata['tasklog_hoursworked'] . "', '" . $tasklogdata['tasklog_date'] . "')";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting task from tasks_to_users: ' . $tasklogdata['taskid'], 'warning');
			$this->messages->setMessage('Problem deleting task from tasks_to_users: ' . $tasklogdata['taskid'], 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}

}
?>
