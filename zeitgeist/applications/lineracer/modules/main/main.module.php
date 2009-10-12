<?php

defined('LINERACER_ACTIVE') or die();

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


	// ok
	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		
		if ($this->user->isLoggedIn())
		{
			$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_boxengasse'));
		}
		else
		{
			$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	// ok
	public function login($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();

		if ($this->user->isLoggedIn())
		{
			$this->debug->unguard(false);
			$tpl->redirect($tpl->createLink('main', 'index'));
		}

		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));
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


	// ok
	public function logout($parameters=array())
	{
		$this->debug->guard();

		$this->user->logout();

		$tpl = new lrTemplate();
		$tpl->redirect($tpl->createLink('main', 'index'));

		$this->debug->unguard(true);
		return true;
	}
	
	
	public function register($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_register'));

		$registrationform = new zgForm();
		$registrationform->load('forms/register.form.ini');

		if (!empty($parameters['submit']))
		{
			$valid = $registrationform->validate($parameters);
			
			if ($valid)
			{
				$registrationvalid = true;
				if ($parameters['register']['password'] != $parameters['register']['confirmpassword'])
				{
					$registrationform->validateElement('confirmpassword', false);
					$registrationvalid = false;
				}

				if ($registrationvalid)
				{
					$lruser = new lrUserfunctions();
					$birthday = $parameters['register']['birthday'].'.'.$parameters['register']['birthmonth'].'.'.$parameters['register']['birthyear'];
					$userCreated = $lruser->createUser($parameters['register']['username'], $parameters['register']['password'], $parameters['register']['email'], $birthday);
				}
				
				if ($userCreated)
				{
					$this->messages->setMessage('Dein Benutzer wurde erfolgreich angelegt.', 'usermessage');
					$tpl = new lrTemplate();
					$tpl->redirect($tpl->createLink('main', 'index'));
				}
				
			}
		}

		$registrationform->insert($tpl);
		
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}

}
?>
