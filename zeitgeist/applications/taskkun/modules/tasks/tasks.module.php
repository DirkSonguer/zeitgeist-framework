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


	public function addadhoc($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasks', 'templates', 'tasks_addadhoc'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
