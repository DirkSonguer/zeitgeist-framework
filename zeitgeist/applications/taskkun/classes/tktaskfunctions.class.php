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
		$sql = "SELECT * FROM taskworkflow WHERE taskworkflow_tasktype='" . $taskdata['task_type'] . "' ORDER BY taskworkflow_order LIMIT 1";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting tasktypes from database', 'warning');
			$this->messages->setMessage('Problem getting tasktypes from database' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);
		$initialstatus = $row['taskworkflow_id'];

		$sql = 'INSERT INTO tasks(task_creator, task_name, task_description, task_hoursplanned, task_type, task_workflow, task_priority, task_begin, task_end, task_notes) ';
		$sql .= "VALUES('" . $this->user->getUserID() . "', ";
		$sql .= "'" . $taskdata['task_name'] . "', '" . $taskdata['task_description'] . "', '" . $taskdata['task_hoursplanned'] . "', '" . $taskdata['task_type'] . "', ";
		$sql .= "'" . $initialstatus . "', '" . $taskdata['task_priority'] . "', '" . $taskdata['task_begin'] . "', '" . $taskdata['task_end'] . "', '" . $taskdata['task_notes'] . "')";

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


	public function storeTags($tagstring, $taskid)
	{
		$this->debug->guard();

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


	public function addTasklog($taskid, $tasklog_description, $tasklog_hoursworked)
	{
		$this->debug->guard();

		if (strpos($tasklog_hoursworked, ',') !== false) $tasklog_hoursworked = str_replace(',','.', $tasklog_hoursworked);

		$sql = 'INSERT INTO tasklogs(tasklog_creator, tasklog_task, tasklog_description, tasklog_hoursworked) ';
		$sql .= "VALUES('" . $this->user->getUserID() . "', '" . $taskid . "', '" . $tasklog_description . "', '" . $tasklog_hoursworked . "')";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting task from tasks_to_users: ' . $taskid, 'warning');
			$this->messages->setMessage('Problem deleting task from tasks_to_users: ' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function deleteTask($taskid)
	{
		$this->debug->guard();

		$sql = "DELETE FROM tasks_to_users WHERE taskusers_task='" . $taskid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting task from tasks_to_users: ' . $taskid, 'warning');
			$this->messages->setMessage('Problem deleting task from tasks_to_users: ' . $taskid, 'warning');
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


	public function getTaskInformation($taskid)
	{
		$this->debug->guard();

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

		$this->debug->unguard($taskinformation);
		return $taskinformation;
	}


	public function getTaskTypes()
	{
		$this->debug->guard();

		$sql = "SELECT * FROM tasktypes";
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


	public function getTaskPriorities()
	{
		$this->debug->guard();

		$sql = "SELECT * FROM priorities ORDER BY priority_order";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting priorities from database', 'warning');
			$this->messages->setMessage('Problem getting priorities from database' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$priorities = array();
		while($row = $this->database->fetchArray($res))
		{
			$priorities[] = $row;
		}

		$this->debug->unguard($priorities);
		return $priorities;
	}


	public function getNumberofUsertasks()
	{
		$this->debug->guard();

		$sql = "SELECT COUNT(t.task_id) as open_usertasks FROM tasks_to_users tu LEFT JOIN tasks t ON tu.taskusers_task = t.task_id WHERE taskusers_user='" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$ret = $row['open_usertasks'];

		$this->debug->unguard($ret);
		return $ret;
	}


	public function getNumberofGrouptasks()
	{
		$this->debug->guard();

		$sql = "SELECT COUNT(t.task_id) as open_grouptasks FROM tasks t LEFT JOIN tasks_to_users tu ON t.task_id = tu.taskusers_task LEFT JOIN users u ON tu.taskusers_user = u.user_id WHERE taskusers_user is null";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$ret = $row['open_grouptasks'];

		$this->debug->unguard($ret);
		return $ret;
	}


	public function workflowUp($taskid)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM taskworkflow twf LEFT JOIN tasks t ON twf.taskworkflow_tasktype = t.task_type WHERE t.task_id='" . $taskid . "'";
		$res = $this->database->query($sql);

		$numWorkflowItems = $this->database->numRows($res);
		$row = $this->database->fetchArray($res);

		if ($row['task_workflow'] < $numWorkflowItems)
		{
			$row['task_workflow'] += 1;
		}

		$sql = "UPDATE tasks SET task_workflow='" . $row['task_workflow'] . "' WHERE task_id='" . $taskid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem writing the workflow update to the database', 'warning');
			$this->messages->setMessage('Problem writing the workflow update to the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM tasks_to_users WHERE taskusers_task='" . $taskid . "'";
		$res = $this->database->query($sql);

		$this->debug->unguard(true);
		return true;
	}


	public function workflowDown($taskid)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM taskworkflow twf LEFT JOIN tasks t ON twf.taskworkflow_tasktype = t.task_type WHERE t.task_id='" . $taskid . "'";
		$res = $this->database->query($sql);

		$numWorkflowItems = $this->database->numRows($res);
		$row = $this->database->fetchArray($res);

		if ($row['task_workflow'] > 1)
		{
			$row['task_workflow'] -= 1;
		}

		$sql = "UPDATE tasks SET task_workflow='" . $row['task_workflow'] . "' WHERE task_id='" . $taskid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem writing the workflow update to the database', 'warning');
			$this->messages->setMessage('Problem writing the workflow update to the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM tasks_to_users WHERE taskusers_task='" . $taskid . "'";
		$res = $this->database->query($sql);

		$this->debug->unguard(true);
		return true;
	}


}
?>
