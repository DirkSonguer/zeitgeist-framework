<?php

defined('TASKKUN_ACTIVE') or die();

class workflows
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


	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('workflows', 'templates', 'workflows_index'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function editworkflow($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('workflows', 'templates', 'workflows_editworkflow'));
		$tpl->assign('documenttitle', 'Aufgabenablauf bearbeiten');
		$tpl->assign('helptopic', '&topic=editworkflow');

		$editworkflowForm = new zgStaticform();
		$editworkflowForm->load('forms/editworkflow.form.ini');

		$workflowfunctions = new tkWorkflowfunctions();

		if (!empty($parameters['id']))
		{
			$workflowid = $parameters['id'];
		}
		elseif (!empty($parameters['editworkflow']['workflow_id']))
		{
			$workflowid = $parameters['editworkflow']['workflow_id'];
		}

		$workflowInformation = $workflowfunctions->getWorkflowInformation($workflowid);

		if (!empty($parameters['submit']))
		{
			$formvalid = $editworkflowForm->process($parameters);

			if ($formvalid)
			{
				$workflowParameters = $parameters['editworkflow'];
				if ($workflowfunctions->updateWorkflow($workflowParameters))
			{
				$this->messages->setMessage('Der Aufgabenablauf wurde geändert.', 'usermessage');
			}
			else
			{
				$this->messages->setMessage('Der Aufgabenablauf konnte nicht gespeichert werden. Bitte verständigen Sie einen Administrator.', 'usererror');
			}

				$tpl->redirect($tpl->createLink('workflows', 'index'));
				$this->debug->unguard(true);
				return true;
			}
		}
		else
		{
			$processData = array();
			$processData['editworkflow'] = $workflowInformation;
			$formvalid = $editworkflowForm->process($processData);
		}

		$formcreated = $editworkflowForm->create($tpl);

		$tpl->assignDataset($workflowInformation);
		if ($workflowInformation['workflow_count'] > 0)
		{
			$tpl->insertBlock('warningactivetasks');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function addworkflow($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('workflows', 'templates', 'workflows_editworkflow'));
		$tpl->assign('documenttitle', 'Aufgabenablauf bearbeiten');
		$tpl->assign('helptopic', '&topic=editworkflow');

		$editworkflowForm = new zgStaticform();
		$editworkflowForm->load('forms/editworkflow.form.ini');

		$workflowfunctions = new tkWorkflowfunctions();

		$workflowid = false;
		if (!empty($parameters['id']))
		{
			$workflowid = $parameters['id'];
		}
		elseif (!empty($parameters['workflowid']))
		{
			$workflowid = $parameters['workflowid'];
		}
		else
		{
			$workflowid = $workflowfunctions->createWorkflow();
		}

		$workflowInformation = $workflowfunctions->getWorkflowInformation($workflowid);

		if (!empty($parameters['submit']))
		{
			$formvalid = $editworkflowForm->process($parameters);

			if ($formvalid)
			{
				$workflowParameters = $parameters['editworkflow'];
				if ($workflowfunctions->updateWorkflow($workflowParameters))
			{
				$this->messages->setMessage('Der Aufgabenablauf wurde geändert.', 'usermessage');
			}
			else
			{
				$this->messages->setMessage('Der Aufgabenablauf konnte nicht gespeichert werden. Bitte verständigen Sie einen Administrator.', 'usererror');
			}

				$tpl->redirect($tpl->createLink('workflows', 'index'));
				$this->debug->unguard(true);
				return true;
			}
		}
		else
		{
			$processData = array();
			$processData['editworkflow'] = $workflowInformation;
			$formvalid = $editworkflowForm->process($processData);
		}

		$formcreated = $editworkflowForm->create($tpl);

		$tpl->assignDataset($workflowInformation);
		if ($workflowInformation['workflow_count'] > 0)
		{
			$tpl->insertBlock('warningactivetasks');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function deleteworkflow($parameters=array())
	{
		$this->debug->guard();

		$workflowfunctions = new tkWorkflowfunctions();

		if (!empty($parameters['id']))
		{
			if ($workflowfunctions->deleteWorkflow($parameters['id']))
			{
				$this->messages->setMessage('Der Aufgabenablauf wurde gelöscht', 'usermessage');
			}
			else
			{
				$this->messages->setMessage('Sie können diesen Aufgabenablauf nicht löschen. Bitte achten Sie darauf, dass der Ablauf keine aktiven Aufgaben mehr hat.', 'usererror');
			}
		}

		$this->debug->unguard(true);
		$tpl = new tkTemplate();
		$tpl->redirect($tpl->createLink('workflows', 'index'));
		return true;

		$this->debug->unguard(true);
		return true;
	}


	public function editworkflowinformation($parameters=array())
	{
		$this->debug->guard();

		$taskfunctions = new tkTaskfunctions();
		$workflowfunctions = new tkWorkflowfunctions();
		$userfunctions = new tkUserfunctions();

		$sql = "SELECT * FROM workflows WHERE workflow_id='" . $parameters['id'] . "' AND workflow_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		if (!$this->database->numRows($res) > 0)
		{
			$this->debug->write('The workflow is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The workflow is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$workflowinformation = $workflowfunctions->getWorkflowInformation($parameters['id']);

		$userfunctions = new tkUserfunctions();
		$currentInstance = $userfunctions->getUserInstance($this->user->getUserId());

		if ( (is_array($workflowinformation)) && (count($workflowinformation) > 0) )
		{
			if (!empty($parameters['formaction']))
			{
				$workflowinformation = $workflowfunctions->getWorkflowgroupInformation($parameters['id']);

				if (($parameters['formaction'] == 'stepdown') && ($parameters['formvalue'] < (count($workflowinformation))) )
				{
					$sql = "UPDATE workflows_to_groups SET workflowgroup_order='999' ";
					$sql .= "WHERE workflowgroup_workflow='" . $parameters['id'] . "' AND workflowgroup_order='" . ($parameters['formvalue']) . "'";
					$res = $this->database->query($sql);

					$sql = "UPDATE workflows_to_groups SET workflowgroup_order='" . $parameters['formvalue'] . "' ";
					$sql .= "WHERE workflowgroup_workflow='" . $parameters['id'] . "' AND workflowgroup_order='" . (($parameters['formvalue'])+1) . "'";
					$res = $this->database->query($sql);

					$sql = "UPDATE workflows_to_groups SET workflowgroup_order='" . (($parameters['formvalue'])+1) . "' ";
					$sql .= "WHERE workflowgroup_workflow='" . $parameters['id'] . "' AND workflowgroup_order='999'";
					$res = $this->database->query($sql);
				}

				if (($parameters['formaction'] == 'stepup') && ($parameters['formvalue'] > 1) )
				{
					$sql = "UPDATE workflows_to_groups SET workflowgroup_order='999' ";
					$sql .= "WHERE workflowgroup_workflow='" . $parameters['id'] . "' AND workflowgroup_order='" . $parameters['formvalue'] . "'";
					$res = $this->database->query($sql);

					$sql = "UPDATE workflows_to_groups SET workflowgroup_order='" . $parameters['formvalue'] . "' ";
					$sql .= "WHERE workflowgroup_workflow='" . $parameters['id'] . "' AND workflowgroup_order='" . (($parameters['formvalue'])-1) . "'";
					$res = $this->database->query($sql);

					$sql = "UPDATE workflows_to_groups SET workflowgroup_order='" . (($parameters['formvalue'])-1) . "' ";
					$sql .= "WHERE workflowgroup_workflow='" . $parameters['id'] . "' AND workflowgroup_order='999'";
					$res = $this->database->query($sql);
				}

				if ($parameters['formaction'] == 'deletestep')
				{
					$sql = "DELETE FROM workflows_to_groups ";
					$sql .= "WHERE workflowgroup_workflow='" . $parameters['id'] . "' AND workflowgroup_order='" . $parameters['formvalue'] . "'";
					$res = $this->database->query($sql);

					for ($i=$parameters['formvalue']; $i<count($workflowinformation); $i++)
					{
						$sql = "UPDATE workflows_to_groups SET workflowgroup_order='" . $i . "' ";
						$sql .= "WHERE workflowgroup_workflow='" . $parameters['id'] . "' AND workflowgroup_order='" . ($i+1) . "'";
						$res = $this->database->query($sql);
					}
				}

				if ($parameters['formaction'] == 'newstep')
				{
					for ($i=($parameters['formvalue']+1); $i<=count($workflowinformation); $i++)
					{
						$sql = "UPDATE workflows_to_groups SET workflowgroup_order='" . ($i+1) . "' WHERE ";
						$sql .= "workflowgroup_workflow='" . $parameters['id'] . "' AND workflowgroup_order='" . $i . "'";
						$res = $this->database->query($sql);
					}

					$sql = "SELECT group_id FROM groups WHERE group_instance='" . $currentInstance . "' LIMIT 1";
					$res = $this->database->query($sql);
					$row = $this->database->fetchArray($res);

					$sql = "INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) ";
					$sql .= "VALUES ('Neuer Aufgabenablauf', '" . $parameters['id'] . "', '" . $row['group_id'] . "', '" . ($parameters['formvalue']+1) . "')";
					$res = $this->database->query($sql);
				}

				if ($parameters['formaction'] == 'storedata')
				{
					$sql = "UPDATE workflows_to_groups SET workflowgroup_title='" . $parameters['inputdata'] . "', workflowgroup_group='" . $parameters['dropdowndata'] . "' ";
					$sql .= "WHERE workflowgroup_workflow='" . $parameters['id'] . "' AND workflowgroup_order='" . $parameters['formvalue'] . "'";
					$res = $this->database->query($sql);
				}
			}

			$sql = "SELECT w2g.*, g.group_name FROM workflows_to_groups w2g ";
			$sql .= "LEFT JOIN groups g ON w2g.workflowgroup_group = g.group_id ";
			$sql .= "WHERE w2g.workflowgroup_workflow='" . $parameters['id'] . "' AND g.group_instance='" . $currentInstance . "' ORDER BY w2g.workflowgroup_order";
			$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
			$this->dataserver->streamXMLDataset($xmlData);
		}
		die();

		$this->debug->unguard(true);
		return true;
	}


}
?>
