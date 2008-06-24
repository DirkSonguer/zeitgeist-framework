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
		$tpl->assign('documenttitle', 'Willkommen bei Taskkun');

		$taskfunctions = new tkTaskfunctions();

		$usertasks = $taskfunctions->getNumberofUsertasks();
		if ($usertasks > 0)
		{
			$tpl->assign('open_usertasks', $usertasks);
		}
		else
		{
			$tpl->assign('open_usertasks', 'keine');
		}

		$grouptasks = $taskfunctions->getNumberofGrouptasks();
		if ($grouptasks > 0)
		{
			$tpl->assign('open_grouptasks', $grouptasks);
		}
		else
		{
			$tpl->assign('open_grouptasks', 'keine');
		}

		$tpl->assign('current_date', date('d.m.Y - H:i:s'));
		$tpl->assign('taskkun_username', $this->user->getUsername());

		if ( ($this->user->hasUserrole('Administrator')) || ($this->user->hasUserrole('Manager')) ) $tpl->insertBlock('managermenu');
		if ($this->user->hasUserrole('Administrator')) $tpl->insertBlock('adminmenu');

		$tpl->show(false);

		$this->debug->unguard(true);
		return true;
	}


	public function login($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_login'));
		$tpl->assign('documenttitle', 'Login');

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
					$this->messages->setMessage('Der Benutzername und/oder das Passwort wurde nicht korrekt angegeben. Bitte geben Sie Ihren Benutzernamen und Ihr Passwort sorgfältig ein.');
				}
			}
			else
			{
				$this->messages->setMessage('Bitte geben Sie einen gültigen Benutzernamen und das dazugehörige  Passwort ein.', 'userwarning');
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
