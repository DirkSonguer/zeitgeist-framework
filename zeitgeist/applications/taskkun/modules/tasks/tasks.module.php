<?php

defined('TASKKUN_ACTIVE') or die();

class tasks
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


	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_index'));
		$tpl->assign('documenttitle', 'Aufgabenübersicht');
		$tpl->assign('tasklog_date:value', date('d.m.Y'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function addtask($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_addtask'));
		$tpl->assign('documenttitle', 'Aufgabe hinzufügen');

		$addtaskForm = new zgStaticform();
		$addtaskForm->load('forms/addtask.form.ini');
		$formvalid = $addtaskForm->process($parameters);

		$taskfunctions = new tkTaskfunctions();
		$tasktypefunctions = new tkTasktypefunctions();

		if (!empty($parameters['submit']))
		{
			if ($formvalid)
			{
				if (!$taskfunctions->addTask($parameters['addtask']))
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verständigen Sie einen Administrator', 'usererror');
				}
				else
				{
					$this->messages->setMessage('Die Informationen wurden gespeichert', 'usermessage');
					$this->debug->unguard(true);
					$tpl->redirect($tpl->createLink('tasks', 'showactivetasks'));
					return(true);
				}
			}
			else
			{
				$this->messages->setMessage('Bitte füllen Sie alle Formularfelder korrekt aus', 'userwarning');
			}
		}

		$formcreated = $addtaskForm->create($tpl);

		$tasktypes = $tasktypefunctions->getTaskTypesForUser();
		foreach ($tasktypes as $tasktype)
		{
			if (!empty($parameters['addtask']['task_type'])) $tpl->assign('tasktype_selected', 'selected="selected"');
			$tpl->assignDataset($tasktype);
			$tpl->insertBlock('tasktype_loop');
		}

		if (empty($parameters['addtask']['task_begin']))
		{
			$tpl->assign('task_begin:value', date('d.m.Y'));
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function edittask($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_edittask'));
		$tpl->assign('documenttitle', 'Aufgabe bearbeiten');

		$addtaskForm = new zgStaticform();
		$addtaskForm->load('forms/edittask.form.ini');

		$taskfunctions = new tkTaskfunctions();
		$tasktypefunctions = new tkTasktypefunctions();

		if (!empty($parameters['submit']))
		{
			$formvalid = $addtaskForm->process($parameters);

			if ($formvalid)
			{
				if (!$taskfunctions->updateTask($parameters['edittask']))
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verständigen Sie einen Administrator', 'usererror');
				}
				else
				{
					$this->messages->setMessage('Die Informationen wurden gespeichert', 'usermessage');
					$this->debug->unguard(true);
					$tpl->redirect($tpl->createLink('tasks', 'showactivetasks'));
					return true;
				}
			}
			else
			{
				$this->messages->setMessage('Bitte füllen Sie alle Formularfelder korrekt aus', 'userwarning');
			}

			$tpl->assign('priority_' . $parameters['edittask']['task_priority'], 'selected="selected"');
		}

		if(!empty($parameters['id']))
		{
			$taskinformation = $taskfunctions->getTaskInformation($parameters['id']);

			if ($taskinformation['task_begin'] == '00.00.0000') $taskinformation['task_begin'] = '';
			if ($taskinformation['task_end'] == '00.00.0000') $taskinformation['task_end'] = '';

			$tasktypes = $tasktypefunctions->getTaskTypes();
			foreach ($tasktypes as $tasktype)
			{
				$tpl->assignDataset($tasktype);
				if ($taskinformation['task_type'] == $tasktype['tasktype_id'])
				{
					$tpl->assign('tasktype_selected', 'selected="selected"');
				}
				else
				{
					$tpl->assign('tasktype_selected', '');
				}
				$tpl->insertBlock('tasktype_loop');
			}

			$tpl->assign('priority_' . $taskinformation['task_priority'], 'selected="selected"');

			$processData = array();
			$processData['edittask'] = $taskinformation;
			$formvalid = $addtaskForm->process($processData);
		}

		$formcreated = $addtaskForm->create($tpl);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function deletetask($parameters=array())
	{
		$this->debug->guard();

		$taskfunctions = new tkTaskfunctions();

		if (!empty($parameters['id']))
		{
			if ($taskfunctions->deleteTask($parameters['id']))
			{
				$this->messages->setMessage('Die Aufgabe wurde erfolgreich gelöscht', 'usermessage');
			}
			else
			{
				$this->messages->setMessage('Die Aufgabe konnte nicht gelöscht werden. Bitte verständigen Sie einen Administrator', 'usererror');
			}
		}

		$this->debug->unguard(true);
		$tpl = new tkTemplate();
		$tpl->redirect($tpl->createLink('tasks', 'showactivetasks'));
		return true;
	}


	public function accepttask($parameters=array())
	{
		$this->debug->guard();

		if (!empty($parameters['id']))
		{
			$taskfunctions = new tkTaskfunctions();
			$taskfunctions->acceptTask($parameters['id']);
		}

		$tpl = new tkTemplate();
		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('tasks', 'index'));

		$this->debug->unguard(true);
		return true;
	}


	public function declinetask($parameters=array())
	{
		$this->debug->guard();

		if (!empty($parameters['id']))
		{
			$taskfunctions = new tkTaskfunctions();
			$taskfunctions->declineTask($parameters['id']);
		}

		$tpl = new tkTemplate();
		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('tasks', 'index'));

		$this->debug->unguard(true);
		return true;
	}


	public function addtasklog($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_addtasklog'));
		$tpl->assign('documenttitle', 'Aufgabenbeschreibung hinzufügen');

		$addtasklogForm = new zgStaticform();
		$addtasklogForm->load('forms/addtasklog.form.ini');

		$taskfunctions = new tkTaskfunctions();
		$tasklogfunctions = new tkTasklogfunctions();
		$tasktypefunctions = new tkTasktypefunctions();

		$formvalid = $addtasklogForm->process($parameters);

		if (!empty($parameters['submitButton']))
		{
			$formcontent = $parameters['addtasklog'];

			if ($formvalid)
			{
				if (empty($formcontent['tasklog_hoursworked'])) $formcontent['tasklog_hoursworked'] = '0';

				if (!$tasklogfunctions->addTasklog($formcontent))
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verständigen Sie einen Administrator', 'usererror');
				}
				else
				{
					$this->messages->setMessage('Die Tätigkeitsbeschreibung wurde gespeichert', 'usermessage');
				}

				switch($parameters['submitButton'])
				{
					case 2:
						$tasktypefunctions->workflowDown($formcontent['tasklog_task']);
						$this->messages->setMessage('Die Aufgabe wurde zurückgegeben', 'usermessage');
						break;

					case 3:
						$tasktypefunctions->workflowUp($formcontent['tasklog_task']);
						$this->messages->setMessage('Die Aufgabe wurde abgeschlossen', 'usermessage');
						break;

					default:
						break;
				}

				$this->debug->unguard(true);
				$tpl->redirect($tpl->createLink('tasks', 'index'));
				return true;
			}

			switch($parameters['submitButton'])
			{
				case 2:
					$tasktypefunctions->workflowDown($formcontent['tasklog_task']);
					$this->messages->setMessage('Die Aufgabe wurde zurückgegeben', 'usermessage');
					$this->debug->unguard(true);
					$tpl->redirect($tpl->createLink('tasks', 'index'));
					return true;
					break;

				case 3:
					$tasktypefunctions->workflowUp($formcontent['tasklog_task']);
					$this->messages->setMessage('Die Aufgabe wurde abgeschlossen', 'usermessage');
					$this->debug->unguard(true);
					$tpl->redirect($tpl->createLink('tasks', 'index'));
					return true;
					break;

				default:
					break;
			}
		}

		$formcreated = $addtasklogForm->create($tpl);

		if (!empty($parameters['id']))
		{
			$taskid = $parameters['id'];
		}
		elseif (!empty($parameters['addtasklog']['tasklog_task']))
		{
			$taskid = $parameters['addtasklog']['tasklog_task'];
		}

		if (empty($parameters['addtasklog']['tasklog_date']))
		{
			$tpl->assign('tasklog_date:value', date('d.m.Y'));
		}

		$taskinformation = $taskfunctions->getTaskInformation($taskid);

		if (count($taskinformation['task_tasklogs']) > 0)
		{
			$tpl->insertBlock('tasklogs');
		}

		$tpl->assign('task_name', $taskinformation['task_name']);
		$tpl->assign('tasklog_task:value', $taskid);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function edittasklog($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_edittasklog'));
		$tpl->assign('documenttitle', 'Aufgabenbeschreibung bearbeiten');

		$edittasklogForm = new zgStaticform();
		$edittasklogForm->load('forms/edittasklog.form.ini');

		$taskfunctions = new tkTaskfunctions();
		$tasklogfunctions = new tkTasklogfunctions();

		if (!empty($parameters['submitButton']))
		{
			$formvalid = $edittasklogForm->process($parameters);

			if ($formvalid)
			{
				$formcontent = $parameters['edittasklog'];

				if (empty($formcontent['tasklog_hoursworked'])) $formcontent['tasklog_hoursworked'] = '0';

				if ($tasklogfunctions->updateTasklog($formcontent))
				{
					$this->messages->setMessage('Die Tätigkeitsbeschreibung wurde gespeichert', 'usermessage');
					$taskloginformation = $tasklogfunctions->getTasklog($parameters['id']);
					$parameters = array();
					$parameters['id'] = $taskloginformation['tasklog_task'];
					$tpl = new tkTemplate();
					$tpl->redirect($tpl->createLink('tasks', 'addtasklog', $parameters));
					return true;
				}
				else
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verständigen Sie einen Administrator', 'usererror');
				}
			}
			else
			{
				$this->messages->setMessage('Bitte füllen Sie alle Formularfelder korrekt aus', 'userwarning');
			}
		}

		if(!empty($parameters['id']))
		{
			$taskloginformation = $tasklogfunctions->getTasklog($parameters['id']);
			$processData = array();
			$processData['edittasklog'] = $taskloginformation;
			$formvalid = $edittasklogForm->process($processData);
		}

		if (!empty($parameters['id']))
		{
			$taskid = $parameters['id'];
		}
		elseif (!empty($parameters['edittasklog']['tasklog_task']))
		{
			$taskid = $parameters['edittasklog']['tasklog_task'];
		}

		$formcreated = $edittasklogForm->create($tpl);

		$tpl->assign('tasklog_task:value', $taskid);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function deletetasklog($parameters=array())
	{
		$this->debug->guard();

		if (!empty($parameters['id']))
		{
			$sql = "SELECT tasklog_task FROM tasklogs WHERE tasklog_id='" . $parameters['id'] . "'";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Problem getting tasklog information for tasklog: ' . $parameters['id'], 'warning');
				$this->messages->setMessage('Problem getting tasklog information for tasklog: ' . $parameters['id'], 'warning');
				$this->debug->unguard(false);
				return false;
			}
			$taskloginformation = $this->database->fetchArray($res);
			$taskid = $taskloginformation['tasklog_task'];

			$taskfunctions = new tkTaskfunctions();
			$tasklogfunctions = new tkTasklogfunctions();

			if ($tasklogfunctions->deleteTasklog($parameters['id'], $taskid))
			{
				$this->messages->setMessage('Die Aufgabenbeschreibung wurde erfolgreich gelöscht', 'usermessage');
			}
			else
			{
				$this->messages->setMessage('Die Aufgabenbeschreibung konnte nicht gelöscht werden. Bitte verständigen Sie einen Administrator', 'usererror');
			}
		}

		$this->debug->unguard(true);
		$parameters = array();
		$parameters['id'] = $taskid;
		$tpl = new tkTemplate();
		$tpl->redirect($tpl->createLink('tasks', 'addtasklog', $parameters));
		return true;
	}


	public function showactivetasks($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_showactivetasks'));
		$tpl->assign('documenttitle', 'Aufgabenübersicht');

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function taskdetails($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_taskdetails'));
		$tpl->assign('documenttitle', 'Aufgabendetails');

		$addtaskForm = new zgStaticform();
		$addtaskForm->load('forms/edittask.form.ini');

		$taskfunctions = new tkTaskfunctions();
		$tasktypefunctions = new tkTasktypefunctions();

		if (!empty($parameters['id']))
		{
			$taskid = $parameters['id'];

			$taskinformation = $taskfunctions->getTaskInformation($taskid);

			if ($taskinformation['task_begin'] == '00.00.0000') $taskinformation['task_begin'] = '';
			if ($taskinformation['task_end'] == '00.00.0000') $taskinformation['task_end'] = '';

			$tpl->assign('priority_' . $taskinformation['task_priority'], 'selected="selected"');
			$tpl->assign('tasktype_name', $taskinformation['tasktype_name']);

			$processData = array();
			$processData['edittask'] = $taskinformation;
			$formvalid = $addtaskForm->process($processData);
		}

		$formcreated = $addtaskForm->create($tpl);
		$tpl->assign('taskid', $taskid);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
