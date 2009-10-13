<?php

defined('LINERACER_ACTIVE') or die();

class profile
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
		$tpl->load($this->configuration->getConfiguration('profile', 'templates', 'profile_index'));

		$profileform = new zgForm();
		$profileform->load('forms/profile.form.ini');

		if (!empty($parameters['submit']))
		{
			$valid = $profileform->validate($parameters);
			
			if ($valid)
			{
/*
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
*/
			}
		}
		else
		{
			$userdata['profiledata'] = $this->user->getUserdata();
			$userdata['profiledata']['user_username'] = $this->user->getUsername();
			$birthstring = $userdata['profiledata']['userdata_birthday'];
			$birthstring = explode('.', $birthstring);
			$userdata['profiledata']['userdata_birthday'] = $birthstring[0];
			$userdata['profiledata']['userdata_birthmonth'] = $birthstring[1];
			$userdata['profiledata']['userdata_birthyear'] = $birthstring[2];
			$valid = $profileform->validate($userdata);
		}

		$profileform->insert($tpl);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
