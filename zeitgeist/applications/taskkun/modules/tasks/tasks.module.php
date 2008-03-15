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

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function addtask($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_addtask'));

		$addtaskForm = new zgStaticform();
		$addtaskForm->load('forms/addtask.form.ini');
		$formvalid = $addtaskForm->process($parameters);

		$taskfunctions = new tkTaskfunctions();
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

		$tasktypes = $taskfunctions->getTaskTypesForUser();
		foreach ($tasktypes as $tasktype)
		{
			if (!empty($parameters['addtask']['task_type'])) $tpl->assign('tasktype_selected', 'selected="selected"');
			$tpl->assignDataset($tasktype);
			$tpl->insertBlock('tasktype_loop');
		}

		if (empty($parameters['task_begin']))
		{
			$tpl->assign('task_begin:value', date('d.m.Y'));
		}

		$tpl->insertBlock('addtask');
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function edittask($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_edittask'));

		$addtaskForm = new zgStaticform();
		$addtaskForm->load('forms/edittask.form.ini');

		$taskfunctions = new tkTaskfunctions();

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

			$tasktypes = $taskfunctions->getTaskTypes();
			foreach ($tasktypes as $tasktype)
			{
				$tpl->assignDataset($tasktype);
				if ($parameters['edittask']['task_type'] == $tasktype['tasktype_id'])
				{
					$tpl->assign('tasktype_selected', 'selected="selected"');
				}
				else
				{
					$tpl->assign('tasktype_selected', '');
				}
				$tpl->insertBlock('tasktype_loop');
			}

			$tpl->assign('priority_' . $parameters['edittask']['task_priority'], 'selected="selected"');
		}

		if(!empty($parameters['id']))
		{
			$taskinformation = $taskfunctions->getTaskInformation($parameters['id']);

			if ($taskinformation['task_begin'] == '00.00.0000') $taskinformation['task_begin'] = '';
			if ($taskinformation['task_end'] == '00.00.0000') $taskinformation['task_end'] = '';

			$tasktypes = $taskfunctions->getTaskTypes();
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

		$tpl->insertBlock('edittask');
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
			$sql = "INSERT INTO tasks_to_users(taskusers_task, taskusers_user) VALUES ('" . $parameters['id'] . "', '" . $this->user->getUserID() . "')";
			$res = $this->database->query($sql);
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
			$sql = "DELETE FROM tasks_to_users WHERE taskusers_task='" . $parameters['id'] . "' AND taskusers_user='" . $this->user->getUserID() . "'";
			$res = $this->database->query($sql);
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

		$taskfunctions = new tkTaskfunctions();
		$taskstored = false;

		if ( (!empty($parameters['submitButton'])) && ($parameters['submitButton'] > 0) )
		{
			if (!empty($parameters['tasklog_description']))
			{
				if (empty($parameters['tasklog_hoursworked'])) $parameters['tasklog_hoursworked'] = '0';

				if (!$taskfunctions->addTasklog($parameters))
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verständigen Sie einen Administrator', 'usererror');
				}
				else
				{
					$this->messages->setMessage('Die Tätigkeitsbeschreibung wurde gespeichert', 'usermessage');
					$taskstored = true;
				}
			}
			else
			{
				if ($parameters['submitButton'] == 1)
				{
					$this->messages->setMessage('Bitte geben Sie eine Tätigkeitsbeschreibung an', 'userwarning');
				}
			}
		}

		if (!empty($parameters['submitButton']))
		{
			switch($parameters['submitButton'])
			{
				case 1:
					$this->debug->unguard(true);
					$tpl->redirect($tpl->createLink('tasks', 'index'));
					return true;
					break;

				case 2:
					$taskfunctions->workflowDown($parameters['taskid']);
					$this->debug->unguard(true);
					$tpl->redirect($tpl->createLink('tasks', 'index'));
					return true;
					break;

				case 3:
					$taskfunctions->workflowUp($parameters['taskid']);
					$this->debug->unguard(true);
					$tpl->redirect($tpl->createLink('tasks', 'index'));
					return true;
					break;

				default:
					break;
			}
		}

		if (!empty($parameters['id']))
		{
			$taskid = $parameters['id'];
		}
		elseif (!empty($parameters['taskid']))
		{
			$taskid = $parameters['taskid'];
		}

		$taskfunctions = new tkTaskfunctions();
		$taskinformation = $taskfunctions->getTaskInformation($taskid);

		if (count($taskinformation['task_tasklogs']) > 0)
		{
			$tpl->insertBlock('tasklogs');
		}

		$tpl->assign('task_name', $taskinformation['task_name']);
		$tpl->assign('taskid', $taskid);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function edittasklog($parameters=array())
	{
		$this->debug->guard();

		$taskfunctions = new tkTaskfunctions();
		$taskstored = false;

		if ( (!empty($parameters['submitButton'])) && ($parameters['submitButton'] > 0) )
		{
			if (!empty($parameters['tasklog_description']))
			{
				if (empty($parameters['tasklog_hoursworked'])) $parameters['tasklog_hoursworked'] = '0';

				if (!$taskfunctions->updateTasklog($parameters))
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verständigen Sie einen Administrator', 'usererror');
				}
				else
				{
					$this->messages->setMessage('Die Tätigkeitsbeschreibung wurde gespeichert', 'usermessage');
					$sql = "SELECT tl.* FROM tasklogs tl ";
					$sql .= "WHERE tasklog_id='" . $parameters['tasklogid'] . "'";
					$res = $this->database->query($sql);
					if (!$res)
					{
						$this->debug->write('Problem getting tasklog information for tasklog: ' . $parameters['tasklogid'], 'warning');
						$this->messages->setMessage('Problem getting tasklog information for tasklog: ' . $parameters['tasklogid'], 'warning');
						$this->debug->unguard(false);
						return false;
					}
					$taskloginformation = $this->database->fetchArray($res);
					$parameters = array();
					$parameters['id'] = $taskloginformation['tasklog_task'];
					$tpl = new tkTemplate();
					$tpl->redirect($tpl->createLink('tasks', 'addtasklog', $parameters));
					return true;
				}
			}
			else
			{
				if ($parameters['submitButton'] == 1)
				{
					$this->messages->setMessage('Bitte geben Sie eine Tätigkeitsbeschreibung an', 'userwarning');
				}
			}
		}

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_edittasklog'));

		if (!empty($parameters['id']))
		{
			$tasklogid = $parameters['id'];
		}
		elseif (!empty($parameters['tasklogid']))
		{
			$tasklogid = $parameters['tasklogid'];
		}

		$taskfunctions = new tkTaskfunctions();
		$taskloginformation = $taskfunctions->getTasklog($tasklogid);

		$tpl->assign('tasklogid', $tasklogid);
		$tpl->assignDataset($taskloginformation);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function deletetasklog($parameters=array())
	{
		$this->debug->guard();

		if (!empty($parameters['id']))
		{
			$sql = "SELECT * FROM tasklogs WHERE tasklog_id='" . $parameters['id'] . "'";
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
			if ($taskfunctions->deleteTasklog($parameters['id'], $taskid))
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


	public function addadhoc($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_addadhoc'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function showactivetasks($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_showactivetasks'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function taskdetails($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_taskdetails'));


		if (!empty($parameters['id']))
		{
			$taskid = $parameters['id'];
		}

		$tpl->assign('taskid', $taskid);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
