<?php

defined('TASKKUN_ACTIVE') or die();

class tkTasklogfunctions
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


	// instance-safe
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


	// instance-safe
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


	// instance-safe
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


	// instance-safe
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

}
?>
