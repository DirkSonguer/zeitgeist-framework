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
			$formValid = true;
			if ($registerForm->formelements['user_password1']->value != $registerForm->formelements['user_password2']->value)
			{
				$registerForm->validateElement('user_password1', false);
				$registerForm->validateElement('user_password2', false);
				$formValid = false;
			}

			$sql = "SELECT * FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " WHERE user_username = '" . $registerForm->formelements['user_username']->value . "'";
			$res = $this->database->query($sql);
			if ($this->database->numRows($res) > 0)
			{
				$registerForm->validateElement('user_username', false);
				$registerForm->formelements['user_username']->currentErrormsg = 1;
				$formValid = false;
			}

			$sql = "SELECT * FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_userdata') . " WHERE userdata_email = '" . $registerForm->formelements['userdata_email']->value . "'";
			$res = $this->database->query($sql);
			if ($this->database->numRows($res) > 0)
			{
				$registerForm->validateElement('userdata_email', false);
				$registerForm->formelements['userdata_email']->currentErrormsg = 1;
				$formValid = false;
			}

			if($formValid)
			{
				// TODO: Set Userrole and Userdata
				$userdata = array();
				$userdata['userdata_email'] = $registerForm->formelements['userdata_email']->value;

				if ($this->user->createUser($registerForm->formelements['user_username']->value, $registerForm->formelements['user_password1']->value, 1, $userdata))
				{
					unset($tpl);
					$tpl = new lrTemplate();
					$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_register_thankyou'));
				}
			}
		}

		$formstring = $registerForm->create();
		$tpl->assign('registerform', $formstring);

		$tpl->assignDataset($parameters);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function login($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));

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

		$tpl = new lrTemplate();
		$tpl->redirect($tpl->createLink('main', 'index'));

		$this->debug->unguard(true);
		return true;
	}

}
?>
