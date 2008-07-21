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


	/**
	 * adds a task with given taskdata
	 *
	 * instance-safe!
	 *
	 * @param array $taskdata array with taskdata
	 *
	 * @return boolean
	 */
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
		$sql = "SELECT workflowaction_id FROM workflowactions WHERE workflowaction_workflow='" . $taskdata['task_type'] . "' ORDER BY workflowaction_order LIMIT 1";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting workflows from database', 'warning');
			$this->messages->setMessage('Problem getting workflows from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);
		$initialstatus = $row['workflowaction_id'];

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


	/**
	 * updates a task with given data
	 *
	 * instance-safe!
	 *
	 * @param array $taskdata array with taskdata
	 *
	 * @return boolean
	 */
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


	/**
	 * deletes a task with the given id and all its tasklogs
	 *
	 * instance-safe!
	 *
	 * @param integer $taskid id of the task to be deleted
	 *
	 * @return boolean
	 */
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


	/**
	 * gets all information about a task
	 * returns an array with all relevant information
	 * if the user has no rights for the task, it will return false
	 *
	 * instance-safe!
	 *
	 * @param integer $taskid
	 *
	 * @return boolean|array
	 */
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
		$sql = "SELECT t.*, twf.workflowaction_title, tt.workflow_name, ";
		$sql .= "DATE_FORMAT(t.task_end, '%d.%m.%Y') as task_end, DATE_FORMAT(t.task_begin, '%d.%m.%Y') as task_begin ";
		$sql .= "FROM tasks t ";
		$sql .= "LEFT JOIN workflowactions twf ON t.task_workflow = twf.workflowaction_id ";
		$sql .= "LEFT JOIN workflows tt ON t.task_type = tt.workflow_id ";
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


	/**
	 * gets the number of tasks for the current user
	 *
	 * instance-safe!
	 *
	 * @return integer
	 */
	public function getNumberofUsertasks()
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT COUNT(t.task_id) as open_usertasks FROM tasks_to_users tu LEFT JOIN tasks t ON tu.taskusers_task = t.task_id ";
		$sql .= "WHERE tu.taskusers_user='" . $this->user->getUserID() . "' AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$usertasks = $row['open_usertasks'];

		$this->debug->unguard($usertasks);
		return $usertasks;
	}


	/**
	 * gets the number of tasks for the current instance
	 *
	 * instance-safe!
	 *
	 * @return integer
	 */
	public function getNumberofGrouptasks()
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT COUNT(t.task_id) as open_grouptasks FROM tasks t ";
		$sql .= "LEFT JOIN tasks_to_users tu ON t.task_id = tu.taskusers_task LEFT JOIN users u ON tu.taskusers_user = u.user_id ";
		$sql .= "WHERE taskusers_user is null AND t.task_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$grouptasks = $row['open_grouptasks'];

		$this->debug->unguard($grouptasks);
		return $grouptasks;
	}


	/**
	 * stores a given string of tags into the database
	 * tags will be separated and stored individually
	 *
	 * instance-safe!
	 *
	 * @param string $tagstring string containing all tags to store
	 * @param integer $taskid id of the task to associate the tags with
	 *
	 * @return boolean
	 */
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


	/**
	 * accepts a task and binds it to the current user
	 *
	 * instance-safe!
	 *
	 * @param integer $taskid id of the task
	 *
	 * @return boolean
	 */
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


	/**
	 * declines an active task and send it back to the group
	 *
	 * instance-safe!
	 *
	 * @param integer $taskid id of the task
	 *
	 * @return boolean
	 */
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


	/**
	 * adds an ad-hoc task and tasklog for the current user
	 *
	 * instance-safe!
	 *
	 * @param array $adhocdata array containing all the tskdata
	 *
	 * @return boolean
	 */
	public function addAdhoc($adhocdata=array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$dateArray = explode('.', $adhocdata['task_date']);
		$adhocdata['task_date'] = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];

		if (strpos($adhocdata['task_hoursworked'], ',') !== false) $adhocdata['task_hoursworked'] = str_replace(',','.', $adhocdata['task_hoursworked']);

		$sql = 'INSERT INTO tasks(task_creator, task_name, task_description, task_hoursplanned, task_type, task_workflow, task_priority, task_begin, task_end, task_notes, task_instance) ';
		$sql .= "VALUES('" . $this->user->getUserID() . "', ";
		$sql .= "'" . $adhocdata['task_name'] . "', '" . $adhocdata['task_description'] . "', '" . $adhocdata['task_hoursworked'] . "', '0', ";
		$sql .= "'0', '2', '" . $adhocdata['task_date'] . "', '" . $adhocdata['task_date'] . "', '', '" . $userfunctions->getUserInstance($this->user->getUserID()) . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem adding task to database', 'warning');
			$this->messages->setMessage('Problem adding task to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$insertid = $this->database->insertId();

		$sql = 'INSERT INTO tasklogs(tasklog_creator, tasklog_task, tasklog_description, tasklog_hoursworked, tasklog_date) ';
		$sql .= "VALUES('" . $this->user->getUserID() . "', '" . $insertid . "', '" . $adhocdata['task_description'] . "', '" . $adhocdata['task_hoursworked'] . "', '" . $adhocdata['task_date'] . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem adding tasklog to database', 'warning');
			$this->messages->setMessage('Problem adding tasklog to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets the current workflow id of the task
	 *
	 * instance-safe!
	 *
	 * @param integer $taskid id of the task
	 *
	 * @return boolean
	 */
	public function getTaskWorkflow($taskid)
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

		$sql = "SELECT task_workflow FROM tasks WHERE task_id = '" . $taskid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting the workflow for the task: task not found: ' . $taskid, 'warning');
			$this->messages->setMessage('Problem getting the workflow for the task: task not found: ' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);
		$workflow = $row['task_workflow'];

		$this->debug->unguard($workflow);
		return $workflow;
	}
	
}
?>
