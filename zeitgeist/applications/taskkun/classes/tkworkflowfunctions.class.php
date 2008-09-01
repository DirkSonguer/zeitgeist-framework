<?php

defined('TASKKUN_ACTIVE') or die();

class tkWorkflowfunctions
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
	 * gets all workflows for the current instance
	 *
	 * instance-safe!
	 *
	 * @return array
	 */
	public function getWorkflows()
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT * FROM workflows WHERE workflow_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting workflows from database', 'warning');
			$this->messages->setMessage('Problem getting workflows from database' . $taskid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$workflows = array();
		while($row = $this->database->fetchArray($res))
		{
			$workflows[] = $row;
		}

		$this->debug->unguard($workflows);
		return $workflows;
	}


	/**
	 * gets all workflows for the current user
	 *
	 * instance-safe!
	 *
	 * @return array
	 */
	public function getWorkflowsForUser()
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT * FROM workflows wf ";
		$sql .= "LEFT JOIN workflows_to_groups w2g ON wf.workflow_id = w2g.workflowgroup_workflow ";
		$sql .= "LEFT JOIN users_to_groups u2g ON w2g.workflowgroup_group = u2g.usergroup_group ";
		$sql .= "WHERE wf.workflow_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= "AND u2g.usergroup_user='" . $this->user->getUserID() . "' ";
		$sql .= "GROUP BY wf.workflow_id";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting workflows from database', 'warning');
			$this->messages->setMessage('Problem getting workflows from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$workflows = array();
		while($row = $this->database->fetchArray($res))
		{
			$workflows[] = $row;
		}

		$this->debug->unguard($workflows);
		return $workflows;
	}


	/**
	 * gets all information for a given workflow
	 *
	 * instance-safe!
	 *
	 * @param integer $workflowid id of the workflow
	 *
	 * @return array
	 */
	public function getWorkflowInformation($workflowid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT wf.*, COUNT(t.task_id) as workflow_count FROM workflows wf LEFT JOIN tasks t ON wf.workflow_id = t.task_workflow ";
		$sql .= "WHERE wf.workflow_id='" . $workflowid . "' AND wf.workflow_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= "GROUP BY wf.workflow_id;";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting workflow information from database: '. $workflowid, 'warning');
			$this->messages->setMessage('Problem getting workflow information from database: ' . $workflowid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$workflowinformation = $this->database->fetchArray($res);

		$this->debug->unguard($workflowinformation);
		return $workflowinformation;
	}


	/**
	 * creates a new workflow
	 * the new workflow is created with default values
	 *
	 * instance-safe!
	 *
	 * @return array
	 */
	public function createWorkflow()
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();
		$currentInstance = $userfunctions->getUserInstance($this->user->getUserId());

//TODO: Localisation!
		$sql = "INSERT INTO workflows(workflow_name, workflow_description, workflow_instance) VALUES ";
		$sql .= "('Neuer Aufgabentyp', 'Dies ist ein neuer Aufgabentyp. Bitte ändern Sie den Titel und diese Beschreibung gemäß Ihren Angaben', '" . $userfunctions->getUserInstance($this->user->getUserID()) . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem inserting workflow information into database', 'warning');
			$this->messages->setMessage('Problem inserting workflow information into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$workflowid = $this->database->insertId();

		$sql = "SELECT group_id FROM groups WHERE group_instance='" . $currentInstance . "' LIMIT 1";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$sql = "INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) ";
		$sql .= "VALUES ('Neuer Aufgabenablauf', '" . $workflowid . "', '" . $row['group_id'] . "', '1')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem inserting workflow for workflow into database', 'warning');
			$this->messages->setMessage('Problem inserting workflow for workflow into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($workflowid);
		return $workflowid;
	}


	/**
	 * updates a workflow with the given data
	 *
	 * instance-safe!
	 *
	 * @param array $workflowdata array with the workflow data
	 *
	 * @return boolean
	 */
	public function updateWorkflow($workflowdata = array())
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "UPDATE workflows SET workflow_name='" . $workflowdata['workflow_name'] . "', workflow_description='" . $workflowdata['workflow_description'] . "' ";
		$sql .= "WHERE workflow_id='" . $workflowdata['workflow_id'] . "' ";
		$sql .= "AND workflow_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem writing workflowdata to the database', 'warning');
			$this->messages->setMessage('Problem writing workflowdata to the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * deletes a given workflow
	 *
	 * instance-safe!
	 *
	 * @param integer $workflowid id of the workflow to delete
	 *
	 * @return array
	 */
	public function deleteWorkflow($workflowid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT wf.*, COUNT(t.task_id) as task_count FROM workflows wf ";
		$sql .= "LEFT JOIN tasks t ON wf.workflow_id = t.task_workflow ";
		$sql .= "WHERE wf.workflow_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$sql .= "AND wf.workflow_id='" . $workflowid . "' ";
		$sql .= "GROUP BY wf.workflow_id";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting workflow information from database: '. $workflowid, 'warning');
			$this->messages->setMessage('Problem getting workflow information from database: ' . $workflowid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);

		if ( (!isset($row['task_count'])) || ($row['task_count'] > 0) )
		{
			$this->debug->write('Workflow does not exist, is out of bounds or has children: '. $workflowid, 'warning');
			$this->messages->setMessage('Workflow does not exist, is out of bounds or has children: ' . $workflowid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM workflows WHERE workflow_id='" . $workflowid . "' AND workflow_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting workflow information from database', 'warning');
			$this->messages->setMessage('Problem deleting workflow information from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM workflows_to_groups WHERE workflowgroup_workflow='" . $workflowid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting workflowgroup information from database', 'warning');
			$this->messages->setMessage('Problem deleting workflowgroup information from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($workflowid);
		return $workflowid;
	}


	/**
	 * gets workflowgroup information for a given workflow
	 *
	 * instance-safe!
	 *
	 * @param integer $workflowid id of the workflow that is contained by the workflow
	 *
	 * @return array
	 */
	public function getWorkflowgroupInformation($workflowid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT w2g.* FROM workflows_to_groups w2g ";
		$sql .= "LEFT JOIN workflows wf ON w2g.workflowgroup_workflow = wf.workflow_id ";
		$sql .= "WHERE w2g.workflowgroup_workflow='" . $workflowid . "' AND wf.workflow_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "' ";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting workflow from database: '. $workflowid, 'warning');
			$this->messages->setMessage('Problem getting workflow from database: ' . $workflowid, 'warning');
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

		$sql = "SELECT w2g.*, t.task_workflowgroup FROM workflows_to_groups w2g LEFT JOIN tasks t ON w2g.workflowgroup_workflow = t.task_workflow ";
		$sql .= "WHERE t.task_id='" . $taskid . "' ORDER BY w2g.workflowgroup_order";
		$res = $this->database->query($sql);

		$workflowOrder = array();
		while ($row = $this->database->fetchArray($res))
		{
			$workflowOrder[$row['workflowgroup_order']] = $row['workflowgroup_id'];
			$lastRow = $row;
		}

		$currentWorkflowId = array_search($lastRow['task_workflowgroup'], $workflowOrder);
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

		$sql = "SELECT w2g.*, t.task_workflow FROM workflows_to_groups w2g LEFT JOIN tasks t ON w2g.workflowgroup_workflow = t.task_workflow ";
		$sql .= "WHERE t.task_id='" . $taskid . "' ORDER BY w2g.workflowgroup_order";
		$res = $this->database->query($sql);

		$workflowOrder = array();
		while ($row = $this->database->fetchArray($res))
		{
			$workflowOrder[$row['workflowgroup_order']] = $row['workflowgroup_id'];
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
