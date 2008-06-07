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
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));

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
			$tpl->redirect($tpl->createLink('main', 'index'));
		}

		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_login'));
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


// TODO: alt
	public function registeraccount($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_register'));

		$registerForm = new zgForm();
		$registerForm->load('forms/register.form.ini');
		$formprocess = $registerForm->process($parameters);

		$thankyou = false;
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
					$thankyou = true;
				}
			}
		}

		$formstring = $registerForm->create();
		$tpl->assign('registerform', $formstring);

		if (!$thankyou)
		{
			$tpl->insertBlock('register');
		}
		else
		{
			$tpl->insertBlock('thankyou');
		}

		$tpl->assignDataset($parameters);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


// TODO: alt
	public function editaccount($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_editaccount'));
/*
		$editaccountForm = new zgForm();
		$editaccountForm->load('forms/editaccount.form.ini');
		$formprocess = $editaccountForm->process($parameters);

		$thankyou = false;
		if ($formprocess)
		{
			$formValid = true;

			if (!empty($parameters[$editaccountForm->name]['user_password1']) && (!empty($parameters[$editaccountForm->name]['user_password2'])) )
			{
				if ($editaccountForm->formelements['user_password1']->value != $editaccountForm->formelements['user_password2']->value)
				{
					$editaccountForm->validateElement('user_password1', false);
					$editaccountForm->validateElement('user_password2', false);
					$formValid = false;
				}
				else
				{
					$this->user->changePassword($editaccountForm->formelements['user_password1']->value);
				}
			}

			if($formValid)
			{
				$this->user->loadUserdata();
				foreach($parameters[$editaccountForm->name] as $parametername => $parametervalue)
				{
					if (strpos($parametername, 'userdata_') !== false)
					{
						if (!empty($this->user->userdata[$parametername]))
						{
							$this->user->userdata[$parametername] = $parametervalue;
						}
					}
				}

				$this->user->saveUserdata();
				$thankyou = true;
			}

			$userdata = array();
			$userdata['user_username'] = $this->user->getUsername();
			$editaccountForm->assignDataset($userdata);
			$editaccountForm->assignDataset($parameters);
		}
		else
		{
			$userdata = array();
			$userdata = $this->user->getUserdata();
			$userdata['user_username'] = $this->user->getUsername();
			$editaccountForm->assignDataset($userdata);
		}

		$formstring = $editaccountForm->create();
		$tpl->assign('userdataform', $formstring);

		if (!$thankyou)
		{
			$tpl->insertBlock('userdata');
		}
		else
		{
			$tpl->insertBlock('thankyou');
		}
																								   */
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
