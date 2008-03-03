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

		if (!empty($parameters['submit']))
		{
			if ( (!empty($parameters['task_name'])) && (!empty($parameters['task_description'])) && (!empty($parameters['task_hoursplanned'])) && (!empty($parameters['task_tags'])) )
			{

				$taskfunctions = new tkTaskfunctions();
				if (!$taskfunctions->addTask($parameters))
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verständigen Sie einen Administrator', 'usererror');
					$tpl->assignDataset($parameters);
				}
				else
				{
					$this->messages->setMessage('Die Informtionen wurden gespeichert', 'usermessage');
					unset($tpl);
					$this->debug->unguard(true);
					$ret = $this->showalltasks(array());
					return($ret);
				}
			}
			else
			{
				$this->messages->setMessage('Bitte füllen Sie alle Pflichtfelder aus', 'userwarning');
				$tpl->assignDataset($parameters);
			}
		}

		$taskfunctions = new tkTaskfunctions();

		$tasktypes = $taskfunctions->getTaskTypes();
		foreach($tasktypes as $tasktype)
		{
			$tpl->assignDataset($tasktype);
			$tpl->insertBlock('tasktype_loop');
		}

		$priorities = $taskfunctions->getTaskPriorities();
		foreach($priorities as $priority)
		{
			$tpl->assignDataset($priority);
			if ($priority['priority_default'] == '1')
			{
				$tpl->assign('priority_selected', 'selected="selected"');
			}
			else
			{
				$tpl->assign('priority_selected', '');
			}
			$tpl->insertBlock('priority_loop');
		}

		if (empty($parameters['task_begin']))
		{
			$tpl->assign('task_begin', date('d.m.Y'));
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

		if (!empty($parameters['submit']))
		{
			if ( (!empty($parameters['task_name'])) && (!empty($parameters['task_description'])) && (!empty($parameters['task_hoursplanned'])) && (!empty($parameters['task_tags'])) )
			{

				$taskfunctions = new tkTaskfunctions();
				if (!$taskfunctions->updateTask($parameters))
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verständigen Sie einen Administrator', 'usererror');

					$tasktypes = $taskfunctions->getTaskTypes();
					foreach($tasktypes as $tasktype)
					{
						$tpl->assignDataset($tasktype);
						if ($parameters['task_type'] == $tasktype['tasktype_id'])
						{
							$tpl->assign('tasktype_selected', 'selected="selected"');
						}
						else
						{
							$tpl->assign('tasktype_selected', '');
						}
						$tpl->insertBlock('tasktype_loop');
					}

					$priorities = $taskfunctions->getTaskPriorities();
					foreach($priorities as $priority)
					{
						$tpl->assignDataset($priority);
						if ($parameters['task_priority'] == $priority['priority_id'])
						{
							$tpl->assign('priority_selected', 'selected="selected"');
						}
						else
						{
							$tpl->assign('priority_selected', '');
						}
						$tpl->insertBlock('priority_loop');
					}

					$tpl->assignDataset($parameters);
				}
				else
				{
					$this->messages->setMessage('Die Informtionen wurden gespeichert', 'usermessage');
					unset($tpl);
					$this->debug->unguard(true);
					$ret = $this->showalltasks(array());
					return($ret);
				}
			}
			else
			{
				$this->messages->setMessage('Bitte füllen Sie alle Pflichtfelder aus', 'userwarning');

				$taskfunctions = new tkTaskfunctions();
				$tasktypes = $taskfunctions->getTaskTypes();
				foreach($tasktypes as $tasktype)
				{
					$tpl->assignDataset($tasktype);
					if ($parameters['task_type'] == $tasktype['tasktype_id'])
					{
						$tpl->assign('tasktype_selected', 'selected="selected"');
					}
					else
					{
						$tpl->assign('tasktype_selected', '');
					}
					$tpl->insertBlock('tasktype_loop');
				}

				$priorities = $taskfunctions->getTaskPriorities();
				foreach($priorities as $priority)
				{
					$tpl->assignDataset($priority);
					if ($parameters['task_priority'] == $priority['priority_id'])
					{
						$tpl->assign('priority_selected', 'selected="selected"');
					}
					else
					{
						$tpl->assign('priority_selected', '');
					}
					$tpl->insertBlock('priority_loop');
				}

				$tpl->assignDataset($parameters);
			}
		}

		if(!empty($parameters['id']))
		{
			$taskfunctions = new tkTaskfunctions();
			$taskinformation = $taskfunctions->getTaskInformation($parameters['id']);

			if ($taskinformation['task_begin'] == '00.00.0000') $taskinformation['task_begin'] = '';
			if ($taskinformation['task_end'] == '00.00.0000') $taskinformation['task_end'] = '';

			$tasktypes = $taskfunctions->getTaskTypes();
			foreach($tasktypes as $tasktype)
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

			$priorities = $taskfunctions->getTaskPriorities();
			foreach($priorities as $priority)
			{
				$tpl->assignDataset($priority);
				if ($taskinformation['task_priority'] == $priority['priority_id'])
				{
					$tpl->assign('priority_selected', 'selected="selected"');
				}
				else
				{
					$tpl->assign('priority_selected', '');
				}
				$tpl->insertBlock('priority_loop');
			}

			$tpl->assignDataset($taskinformation);
		}

		$tpl->show();

		$this->debug->unguard(true);
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


	public function processtask($parameters=array())
	{
		$this->debug->guard();

		$taskfunctions = new tkTaskfunctions();
		$taskstored = false;

		if ( (!empty($parameters['submitButton'])) && ($parameters['submitButton'] > 0) )
		{
			if (!empty($parameters['tasklog_description']))
			{
				if (empty($parameters['tasklog_hoursworked'])) $parameters['tasklog_hoursworked'] = '0';

				if (!$taskfunctions->addTasklog($parameters['taskid'], $parameters['tasklog_description'], $parameters['tasklog_hoursworked']))
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
				$this->messages->setMessage('Bitte geben Sie eine Tätigkeitsbeschreibung an', 'userwarning');
			}
		}

		if (!empty($parameters['submitButton']))
		{
			switch($parameters['submitButton'])
			{
				case 1:
					$this->debug->unguard(true);
					$ret = $this->index($parameters);
					return $ret;
				break;

				case 2:
					$taskfunctions->workflowDown($parameters['taskid']);
					$this->debug->unguard(true);
					$ret = $this->index($parameters);
					return $ret;
				break;

				case 3:
					$taskfunctions->workflowUp($parameters['taskid']);
					$this->debug->unguard(true);
					$ret = $this->index($parameters);
					return $ret;
				break;

				default:
				    break;
			}
		}

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_processtask'));

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

		$tpl->assign('task_name', $taskinformation['task_name']);
		$tpl->assign('taskid', $taskid);

		$tpl->show();

		$this->debug->unguard(true);
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


	public function showalltasks($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_showalltasks'));

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
		$ret = $this->showalltasks($parameters);
		return $ret;
	}

}
?>
