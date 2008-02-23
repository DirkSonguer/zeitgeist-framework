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
		if (empty($taskdata['task_begin'])) $taskdata['task_begin'] = '';
		if (empty($taskdata['task_end'])) $taskdata['task_end'] = '';
		if (empty($taskdata['task_notes'])) $taskdata['task_notes'] = '';
		if (strpos($taskdata['task_hoursplanned'], ',') !== false) $taskdata['task_hoursplanned'] = str_replace(',','.', $taskdata['task_hoursplanned']);

		// get initial task status
		$sql = "SELECT * FROM taskstatus WHERE taskstatus_tasktype='" . $taskdata['task_type'] . "' ORDER BY taskstatus_order LIMIT 1";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting tasktypes from database', 'warning');
			$this->messages->setMessage('Problem getting tasktypes from database' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);
		$initialstatus = $row['taskstatus_id'];

		$sql = 'INSERT INTO tasks(task_creator, task_name, task_description, task_hoursplanned, task_type, task_status, task_priority, task_begin, task_end, task_notes) ';
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

}
?>
