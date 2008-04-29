<?php

defined('TASKKUN_ACTIVE') or die();

class tasktypes
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
		$tpl->load($this->configuration->getConfiguration('tasktypes', 'templates', 'tasktypes_index'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function edittasktype($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasktypes', 'templates', 'tasktypes_edittasktype'));
		$tpl->assign('documenttitle', 'Aufgabenablauf bearbeiten');
		$tpl->assign('helptopic', '&topic=edittasktype');

		if (!empty($parameters['id']))
		{
			$tasktypeid = $parameters['id'];
		}
		elseif (!empty($parameters['tasktypeid']))
		{
			$tasktypeid = $parameters['tasktypeid'];
		}

		$tasktypefunctions = new tkTasktypefunctions();
		$tasktypeInformation = $tasktypefunctions->getTasktypeInformation($tasktypeid);

		$tpl->assignDataset($tasktypeInformation);
		if ($tasktypeInformation['tasktype_count'] > 0)
		{
			$tpl->insertBlock('warningactivetasks');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function edittasktypeinformation($parameters=array())
	{
		$this->debug->guard();

		$taskfunctions = new tkTaskfunctions();
		$tasktypefunctions = new tkTasktypefunctions();

		$tasktypeinformation = $tasktypefunctions->getTasktypeInformation($parameters['id']);

		$userfunctions = new tkUserfunctions();

		if ( (is_array($tasktypeinformation)) && (count($tasktypeinformation) > 0) )
		{
			if (!empty($parameters['formaction']))
			{
				$workflowinformation = $tasktypefunctions->getWorkflowInformation($parameters['id']);

				if (($parameters['formaction'] == 'stepdown') && ($parameters['formvalue'] < (count($workflowinformation))) )
				{
					$sql = "UPDATE taskworkflow SET taskworkflow_order='999' ";
					$sql .= "WHERE taskworkflow_tasktype='" . $parameters['id'] . "' AND taskworkflow_order='" . ($parameters['formvalue']) . "'";
					$res = $this->database->query($sql);

					$sql = "UPDATE taskworkflow SET taskworkflow_order='" . $parameters['formvalue'] . "' ";
					$sql .= "WHERE taskworkflow_tasktype='" . $parameters['id'] . "' AND taskworkflow_order='" . (($parameters['formvalue'])+1) . "'";
					$res = $this->database->query($sql);

					$sql = "UPDATE taskworkflow SET taskworkflow_order='" . (($parameters['formvalue'])+1) . "' ";
					$sql .= "WHERE taskworkflow_tasktype='" . $parameters['id'] . "' AND taskworkflow_order='999'";
					$res = $this->database->query($sql);
				}

				if (($parameters['formaction'] == 'stepup') && ($parameters['formvalue'] > 1) )
				{
					$sql = "UPDATE taskworkflow SET taskworkflow_order='999' ";
					$sql .= "WHERE taskworkflow_tasktype='" . $parameters['id'] . "' AND taskworkflow_order='" . $parameters['formvalue'] . "'";
					$res = $this->database->query($sql);

					$sql = "UPDATE taskworkflow SET taskworkflow_order='" . $parameters['formvalue'] . "' ";
					$sql .= "WHERE taskworkflow_tasktype='" . $parameters['id'] . "' AND taskworkflow_order='" . (($parameters['formvalue'])-1) . "'";
					$res = $this->database->query($sql);

					$sql = "UPDATE taskworkflow SET taskworkflow_order='" . (($parameters['formvalue'])-1) . "' ";
					$sql .= "WHERE taskworkflow_tasktype='" . $parameters['id'] . "' AND taskworkflow_order='999'";
					$res = $this->database->query($sql);
				}

				if ($parameters['formaction'] == 'deletestep')
				{
					$sql = "DELETE FROM taskworkflow ";
					$sql .= "WHERE taskworkflow_tasktype='" . $parameters['id'] . "' AND taskworkflow_order='" . $parameters['formvalue'] . "'";
					$res = $this->database->query($sql);

					for ($i=$parameters['formvalue']; $i<count($workflowinformation); $i++)
					{
						$sql = "UPDATE taskworkflow SET taskworkflow_order='" . $i . "' ";
						$sql .= "WHERE taskworkflow_tasktype='" . $parameters['id'] . "' AND taskworkflow_order='" . ($i+1) . "'";
						$res = $this->database->query($sql);
					}
				}

				if ($parameters['formaction'] == 'newstep')
				{
					for ($i=($parameters['formvalue']+1); $i<=count($workflowinformation); $i++)
					{
						$sql = "UPDATE taskworkflow SET taskworkflow_order='" . ($i+1) . "' WHERE ";
						$sql .= "taskworkflow_tasktype='" . $parameters['id'] . "' AND taskworkflow_order='" . $i . "'";
						$res = $this->database->query($sql);
					}

					$sql = "INSERT INTO taskworkflow(taskworkflow_title, taskworkflow_tasktype, taskworkflow_group, taskworkflow_order) ";
					$sql .= "VALUES ('Neuer Aufgabenablauf', '" . $tasktypeinformation['tasktype_id'] . "', '1', '" . ($parameters['formvalue']+1) . "')";
					$res = $this->database->query($sql);
				}

				if ($parameters['formaction'] == 'storedata')
				{
					$sql = "UPDATE taskworkflow SET taskworkflow_title='" . $parameters['inputdata'] . "', taskworkflow_group='" . $parameters['dropdowndata'] . "' ";
					$sql .= "WHERE taskworkflow_tasktype='" . $parameters['id'] . "' AND taskworkflow_order='" . $parameters['formvalue'] . "'";
					$res = $this->database->query($sql);
				}

				// TODO: INSTANCING!!!!!

			}

			$userfunctions = new tkUserfunctions();
			$currentInstance = $userfunctions->getUserInstance($this->user->getUserId());

			$sql = "SELECT twf.*, g.group_name FROM taskworkflow twf ";
			$sql .= "LEFT JOIN groups g ON twf.taskworkflow_group = g.group_id ";
			$sql .= "WHERE taskworkflow_tasktype='" . $parameters['id'] . "' AND g.group_instance='" . $currentInstance . "' ORDER BY taskworkflow_order";
			$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
			$this->dataserver->streamXMLDataset($xmlData);
		}
		die();

		$this->debug->unguard(true);
		return true;
	}


}
?>
