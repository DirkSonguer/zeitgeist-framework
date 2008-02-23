<?php

defined('TASKKUN_ACTIVE') or die();

class main
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
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));

		$sql = "SELECT COUNT(t.task_id) as open_usertasks FROM tasks_to_users tu LEFT JOIN tasks t ON tu.taskusers_task = t.task_id WHERE taskusers_user='" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		if ($row['open_usertasks'] > 0)
		{
			$tpl->assignDataset($row);
		}
		else
		{
			$tpl->assign('open_usertasks', 'keine');
		}

		$sql = "SELECT COUNT(t.task_id) as open_grouptasks FROM tasks t LEFT JOIN tasks_to_users tu ON t.task_id = tu.taskusers_task LEFT JOIN users u ON tu.taskusers_user = u.user_id WHERE taskusers_id is null";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);
		$tpl->assignDataset($row);

		$tpl->assign('current_date', date('d.m.Y - H:i:s'));
		$tpl->assign('taskkun_username', $this->user->getUsername());

		if ($this->user->hasUserrole('Administrator')) $tpl->insertBlock('managermenu');

		$tpl->show(false);

		$this->debug->unguard(true);
		return true;
	}


	public function login($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_login'));

		if ($this->user->isLoggedIn())
		{
			$tpl->redirect($tpl->createLink('main', 'index'));
		}

		if (!empty($parameters['login']))
		{
			if ( (!empty($parameters['username'])) && (!empty($parameters['password'])) )
			{
				if ($this->user->login($parameters['username'], $parameters['password']))
				{
					$tpl->redirect($tpl->createLink('main', 'index'));
				}
				else
				{
					$this->messages->setMessage('Username and/or password was not correct. Please enter your username and password carefully.', 'userwarning');
				}
			}
			else
			{
				$this->messages->setMessage('Please enter a valid username and password.', 'userwarning');
			}
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function logout($parameters=array())
	{
		$this->debug->guard();

		$this->user->logout();

		$tpl = new tkTemplate();
		$tpl->redirect($tpl->createLink('main', 'index'));

		$this->debug->unguard(true);
		return true;
	}

}
?>
