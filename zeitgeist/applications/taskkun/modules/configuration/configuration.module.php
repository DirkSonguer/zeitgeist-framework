<?php

defined('TASKKUN_ACTIVE') or die();

class configuration
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

		$currentId = $this->user->getUserId();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('configuration', 'templates', 'configuration_index'));
		$tpl->assign('documenttitle', 'Benutzerdaten ändern');
		$tpl->assign('helptopic', '&topic=configuration');

		$userfunctions = new tkUserfunctions();

		$configurationForm = new zgStaticform();
		$configurationForm->load('forms/configuration.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $configurationForm->process($parameters);

			if ($formvalid)
			{
				$updateProblems = false;
				$newUserdata = $parameters['configuration'];

				// check and update username
				if ($newUserdata['user_username'] != $this->user->getUsername())
				{
					if (!$this->user->changeUsername($newUserdata['user_username']))
					{
						$configurationForm->validateElement('user_username', false);
						$this->debug->write('Der Benutzername existiert bereits. Bitte wählen Sie einen anderen Benutzernamen aus.', 'warning');
						$this->messages->setMessage('Der Benutzername existiert bereits. Bitte wählen Sie einen anderen Benutzernamen aus.', 'userwarning');
						$updateProblems= true;
					}
				}

				// userdata
				$userfunctions->changeUserdata($newUserdata, $currentId);

				// check and update password
				if ( (!empty($newUserdata['user_password'])) || (!empty($newUserdata['user_password2'])) )
				{
					if ($newUserdata['user_password'] == $newUserdata['user_password2'])
					{
						$this->user->changePassword($newUserdata['user_password']);
					}
					else
					{
						$configurationForm->validateElement('user_password', false);
						$configurationForm->validateElement('user_password2', false);
						$this->messages->setMessage('Die Bestätigung stimmt nicht mit dem gewählten Passwort überein. Das Passwort wurde nicht geändert.', 'userwarning');
						$updateProblems = true;
					}
				}

				if (!$updateProblems)
				{
					$this->messages->setMessage('Die Daten wurden erfolgreich gespeichert.', 'usermessage');
				}
			}
			else
			{
				$this->messages->setMessage('Fehler bei der Eingabe. Bitte überprüfen Sie Ihre Angaben sorgfältig.', 'userwarning');
			}
		}
		else
		{
			$userdata = $userfunctions->getUserdata($currentId);

			$userdata['user_password'] = '';

			$processData = array();
			$processData['configuration'] = $userdata;
			$formvalid = $configurationForm->process($processData);
		}

		$formcreated = $configurationForm->create($tpl);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
