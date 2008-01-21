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


	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function register($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_register'));

		$registerForm = new zgForm();
		$registerForm->load('forms/register.form.ini');
		$formprocess = $registerForm->process($parameters);


		if ($formprocess)
		{
			if ($registerForm->formelements['user_password1']->value != $registerForm->formelements['user_password2']->value)
			{
				$registerForm->validateElement('user_password1', false);
				$registerForm->validateElement('user_password2', false);
			}
			else
			{
				echo "success!";
				die();
			}
		}

		$formstring = $registerForm->create();
		$tpl->assign('registerform', $formstring);

/*
		if (!empty($parameters['submit']))
		{
			if ( (!empty($parameters['user_username'])) && (!empty($parameters['user_password1'])) && (!empty($parameters['user_password2'])) && (!empty($parameters['userdata_email'])) )
			{
				if ($parameters['user_password1'] == $parameters['user_password2'])
				{
					if ($this->user->createUser($parameters['user_username'], $parameters['user_password1']))
					{
						$this->messages->setMessage('Vielen Dank, Sie sind nun registriert. Sie werden in Kürze eine Mail erhalten blablabla.', 'usermessage');
					}
				}
				else
				{
					$this->messages->setMessage('Bitte achten Sie darauf, dass die Passwörter identisch sind..', 'userwarning');
				}
			}
			else
			{
				$this->messages->setMessage('Bitte füllen Sie alle Pflichtfelder aus (Name, Passwort und E-Mail).', 'userwarning');
			}
		}
*/
		$tpl->assignDataset($parameters);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function login($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_login'));

		if ($this->user->isLoggedIn())
		{
			$tpl->redirect($tpl->createLink('main', 'index'));
		}

		if (!empty($parameters['login']))
		{
			if ( (!empty($parameters['username'])) && (!empty($parameters['password'])) )
			{
				if ($this->user->loginUser($parameters['username'], $parameters['password']))
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

		$this->user->logoutUser();

		$tpl = new lrTemplate();
		$tpl->redirect($tpl->createLink('main', 'index'));

		$this->debug->unguard(true);
		return true;
	}

}
?>
