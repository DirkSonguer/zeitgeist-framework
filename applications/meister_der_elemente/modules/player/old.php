<?php

defined('LINERACER_ACTIVE') or die();

class player
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


	public function editplayerdata($parameters=array())
	{
		$this->debug->guard();

		$currentId = $this->user->getUserId();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('player', 'templates', 'player_editplayerdata'));

		$editplayerdataForm = new zgStaticform();
		$editplayerdataForm->load('forms/editplayerdata.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $editplayerdataForm->process($parameters);

			if ($formvalid)
			{
				$updateProblems = false;
				$newUserdata = $parameters['editplayerdata'];

				// check and update username
				if ($newUserdata['user_username'] != $this->user->getUsername())
				{
					if (!$this->user->changeUsername($newUserdata['user_username']))
					{
						$editplayerdataForm->validateElement('user_username', false);
						$this->debug->write('Der Benutzername existiert bereits. Bitte wählen Sie einen anderen Benutzernamen aus.', 'warning');
						$this->messages->setMessage('Der Benutzername existiert bereits. Bitte wählen Sie einen anderen Benutzernamen aus.', 'userwarning');
						$updateProblems= true;
					}
				}

				// userdata
				$userfunctions = new lrUserfunctions();
				$userfunctions->changeUserdata($newUserdata);

				// check and update password
				if ( (!empty($newUserdata['user_password'])) || (!empty($newUserdata['user_password2'])) )
				{
					if ($newUserdata['user_password'] == $newUserdata['user_password2'])
					{
						$this->user->changePassword($newUserdata['user_password']);
					}
					else
					{
						$editplayerdataForm->validateElement('user_password', false);
						$editplayerdataForm->validateElement('user_password2', false);
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
			$userdata = $this->user->getUserdata();
			$userdata['user_password'] = '';
			$userdata['user_username'] = $this->user->getUsername();

			$processData = array();
			$processData['editplayerdata'] = $userdata;
			$formvalid = $editplayerdataForm->process($processData);
		}

		$formcreated = $editplayerdataForm->create($tpl);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
