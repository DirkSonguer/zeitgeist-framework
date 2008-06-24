<?php

defined('TASKKUN_ACTIVE') or die();

class tkTasktypefunctions
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
	 * gets all tasktype for the current instance
	 *
	 * instance-safe!
	 *
	 * @return array
	 */
	public function getTasktypes()
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


	/**
	 * gets all tasktypes for the current user
	 *
	 * instance-safe!
	 *
	 * @return array
	 */
	public function getTasktypesForUser()
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT * FROM tasktypes tt ";
		$sql .= "LEFT JOIN taskworkflows twf ON tt.tasktype_id = twf.taskworkflow_tasktype ";
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


	/**
	 * gets all information for a given tasktype
	 *
	 * instance-safe!
	 *
	 * @param integer $tasktypeid id of the tasktype
	 *
	 * @return array
	 */
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

		$tasktypeinformation = $this->database->fetchArray($res);

		$this->debug->unguard($tasktypeinformation);
		return $tasktypeinformation;
	}


	/**
	 * creates a new tasktype
	 * the new tasktype is created with default values
	 *
	 * instance-safe!
	 *
	 * @return array
	 */
	public function createTasktype()
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		$currentInstance = $userfunctions->getUserInstance($this->user->getUserId());

		$sql = "INSERT INTO tasktypes(tasktype_name, tasktype_description, tasktype_instance) VALUES ";
		$sql .= "('Neuer Aufgabentyp', 'Dies ist ein neuer Aufgabentyp. Bitte ändern Sie den Titel und diese Beschreibung gemäß Ihren Angaben', '" . $userfunctions->getUserInstance($this->user->getUserID()) . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem inserting tasktype information into database', 'warning');
			$this->messages->setMessage('Problem inserting tasktype information into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$tasktypeid = $this->database->insertId();

		$sql = "SELECT group_id FROM groups WHERE group_instance='" . $currentInstance . "' LIMIT 1";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$sql = "INSERT INTO taskworkflows(taskworkflow_title, taskworkflow_tasktype, taskworkflow_group, taskworkflow_order) ";
		$sql .= "VALUES ('Neuer Aufgabenablauf', '" . $tasktypeid . "', '" . $row['group_id'] . "', '1')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem inserting workflow for tasktype into database', 'warning');
			$this->messages->setMessage('Problem inserting workflow for tasktype into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($tasktypeid);
		return $tasktypeid;
	}


	/**
	 * updates a tasktype with the given data
	 *
	 * instance-safe!
	 *
	 * @param array $tasktypedata array with the tasktype data
	 *
	 * @return boolean
	 */
	public function updateTasktype($tasktypedata = array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "UPDATE tasktypes SET tasktype_name='" . $tasktypedata['tasktype_name'] . "', tasktype_description='" . $tasktypedata['tasktype_description'] . "' ";
		$sql .= "WHERE tasktype_id='" . $tasktypedata['tasktype_id'] . "' ";
		$sql .= "AND tasktype_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem writing tasktypedata to the database', 'warning');
			$this->messages->setMessage('Problem writing tasktypedata to the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * deletes a given tasktype
	 *
	 * instance-safe!
	 *
	 * @param integer $tasktypeid id of the tasktype to delete
	 *
	 * @return array
	 */
	public function deleteTasktype($tasktypeid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT tt.*, COUNT(t.task_id) as task_count FROM tasktypes tt ";
		$sql .= "LEFT JOIN tasks t ON tt.tasktype_id = t.task_type ";
		$sql .= "WHERE tt.tasktype_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= "AND tt.tasktype_id='" . $tasktypeid . "' ";
		$sql .= "GROUP BY tt.tasktype_id";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting tasktype information from database: '. $tasktypeid, 'warning');
			$this->messages->setMessage('Problem getting tasktype information from database: ' . $tasktypeid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);

		if ( (!isset($row['task_count'])) || ($row['task_count'] > 0) )
		{
			$this->debug->write('Tasktype does not exist, is out of bounds or has children: '. $tasktypeid, 'warning');
			$this->messages->setMessage('Tasktype does not exist, is out of bounds or has children: ' . $tasktypeid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM tasktypes WHERE tasktype_id='" . $tasktypeid . "' AND tasktype_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting tasktype information from database', 'warning');
			$this->messages->setMessage('Problem deleting tasktype information from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM taskworkflows WHERE taskworkflow_tasktype='" . $tasktypeid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting taskworkflows information from database', 'warning');
			$this->messages->setMessage('Problem deleting taskworkflows information from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($tasktypeid);
		return $tasktypeid;
	}


	/**
	 * gets all information for a workflow
	 *
	 * instance-safe!
	 *
	 * @param integer $tasktypeid id of the tasktype that is contained by the workflow
	 *
	 * @return array
	 */
	public function getWorkflowInformation($tasktypeid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT twf.* FROM taskworkflows twf ";
		$sql .= "LEFT JOIN tasktypes tt ON twf.taskworkflow_tasktype = tt.tasktype_id ";
		$sql .= "WHERE twf.taskworkflow_tasktype='" . $tasktypeid . "' AND tt.tasktype_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
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


	/**
	 * shifts a task up in the workflow
	 * happens if a task is finished
	 *
	 * instance-safe!
	 *
	 * @param integer $taskid id of the tasklog
	 *
	 * @return array
	 */
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

		$sql = "SELECT twf.*, t.task_workflow FROM taskworkflows twf LEFT JOIN tasks t ON twf.taskworkflow_tasktype = t.task_type ";
		$sql .= "WHERE t.task_id='" . $taskid . "' ORDER BY twf.taskworkflow_order";
		$res = $this->database->query($sql);

		$workflowOrder = array();
		while ($row = $this->database->fetchArray($res))
		{
			$workflowOrder[$row['taskworkflow_order']] = $row['taskworkflow_id'];
			$lastRow = $row;
		}

		$currentWorkflowId = array_search($lastRow['task_workflow'], $workflowOrder);
		if ($currentWorkflowId != count($workflowOrder))
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


	/**
	 * shifts a task down in the workflow
	 * happens if a task is send back
	 *
	 * instance-safe!
	 *
	 * @param integer $taskid id of the tasklog
	 *
	 * @return array
	 */
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

		$sql = "SELECT twf.*, t.task_workflow FROM taskworkflows twf LEFT JOIN tasks t ON twf.taskworkflow_tasktype = t.task_type ";
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
			// task cannot go further down
		}

		$sql = "DELETE FROM tasks_to_users WHERE taskusers_task='" . $taskid . "'";
		$res = $this->database->query($sql);

		$this->debug->unguard(true);
		return true;
	}

}
?>
