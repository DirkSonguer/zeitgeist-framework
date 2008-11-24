<?php

defined('WELLNESSWELT_ACTIVE') or die();

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
		
//	$wwuser = wwWellnessweltUser::init();
//	$testid = $wwuser->createUser('test', 'test');
		
	}


	public function login($parameters=array())
	{
		$this->debug->guard();

		$tpl = new wwTemplate();
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
					$this->messages->setMessage('Der Benutzername und/oder das Passwort wurde nicht korrekt angegeben. Bitte geben Sie Ihren Benutzernamen und Ihr Passwort sorgfältig ein.', 'userwarning');
				}
			}
			else
			{
				$this->messages->setMessage('Bitte geben Sie einen gültigen Benutzernamen und das dazugehörige Passwort ein.', 'userwarning');
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
		$tpl = new wwTemplate();
		$this->messages->setMessage('Sie wurden ausgeloggt.', 'userwarning');
		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('main', 'index'));
		return(true);
	}	


	public function index($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new wwTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));
		$tpl->assign('documenttitle', 'Startseite');


		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
