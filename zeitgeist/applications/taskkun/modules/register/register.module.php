<?php

defined('TASKKUN_ACTIVE') or die();

class register
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
		$tpl->load($this->configuration->getConfiguration('register', 'templates', 'register_index'));
		$tpl->assign('documenttitle', 'Willkommen bei Taskkun');

		$tpl->show(false);

		$this->debug->unguard(true);
		return true;
	}


	public function registerinstance($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('register', 'templates', 'register_register'));
		$tpl->assign('documenttitle', 'Neue Instanz erstellen');

		$registerinstanceForm = new zgStaticform();
		$registerinstanceForm->load('forms/registerinstance.form.ini');

		$userfunctions = new tkUserfunctions();

		$formvalid = $registerinstanceForm->process($parameters);

		if (!empty($parameters['submit']))
		{
			if ($formvalid)
			{
				$insertProblems = false;
				$newUserdata = $parameters['registerinstance'];

				$instancefunctions = new tkInstancefunctions();
				$newinstance = $instancefunctions->createInstance($newUserdata['beta_key'], 0);

				if ($newUserdata['user_password'] != $newUserdata['user_password2'])
				{
					$edituserForm->validateElement('user_password', false);
					$edituserForm->validateElement('user_password2', false);
					$this->messages->setMessage('Die Bestätigung stimmt nicht mit dem gewählten Passwort überein. Das Passwort wurde nicht geändert.', 'userwarning');
					$insertProblems = true;
				}

				$userdata = array();
				if (!empty($newUserdata['userdata_lastname'])) $userdata['userdata_lastname'] = $newUserdata['userdata_lastname'];
				if (!empty($newUserdata['userdata_firstname'])) $userdata['userdata_firstname'] = $newUserdata['userdata_firstname'];
				if (!empty($newUserdata['userdata_address1'])) $userdata['userdata_address1'] = $newUserdata['userdata_address1'];
				if (!empty($newUserdata['userdata_address2'])) $userdata['userdata_address2'] = $newUserdata['userdata_address2'];
				if (!empty($newUserdata['userdata_zip'])) $userdata['userdata_zip'] = $newUserdata['userdata_zip'];
				if (!empty($newUserdata['userdata_city'])) $userdata['userdata_city'] = $newUserdata['userdata_city'];
				if (!empty($newUserdata['userdata_url'])) $userdata['userdata_url'] = $newUserdata['userdata_url'];

				if (!$insertProblems)
				{
					if (!$userfunctions->createUser($newUserdata['user_username'], $newUserdata['user_password'], '1', array(), $userdata, $newinstance))
					{
						$this->messages->setMessage('Die Benutzerdaten konnte nicht gespeichert werden.', 'userwarning');
					}
					else
					{
						$lastinsert = $this->messages->getMessagesByType('createduserid');
						if (count($lastinsert) == 1)
						{
							if ($this->configuration->getConfiguration('register', 'betakeys', $newUserdata['beta_key']) == 'true')
							{
								$this->messages->setMessage('Vielen Dank für deine Teilname! Deine Instanz wurde erfolgreich angelegt.', 'usermessage');
								$this->messages->setMessage('Mit dem eingegebenen Beta-Schlüssel bist du bereits freigeschaltet, du kannst Taskkun also sofort benutzen.', 'usermessage');
								$this->messages->setMessage('Bitte beachte in der Hilfe die Hinweise zur Closed Beta.', 'usermessage');
							}
							else
							{
								$this->user->deactivateUser($lastinsert[0]->message);
								$this->messages->setMessage('Ihre Instanz wurde erfolgreich angelegt. Sie können die Beta von Taskkun nutzen, sobald wir sie dafür freigeschaltet haben.', 'usermessage');
								$this->messages->setMessage('Die Freischaltung erfolgt manuell, bitte haben Sie also etwas Geduld. Sie werden per E-Mail kontaktiert, wenn es so weit ist.', 'usermessage');
								$this->messages->setMessage('Unrechtmäßige Anmeldungen zur Beta werden kommentarlos entfernt.', 'usermessage');
							}

							$this->debug->unguard(true);
							$tpl->redirect($tpl->createLink('register', 'index'));
							return true;
						}
						else
						{
							$this->messages->setMessage('Die Benutzerdaten konnte nicht gespeichert werden.', 'userwarning');
						}
					}
				}
			}
			else
			{
				$this->messages->setMessage('Fehler bei der Eingabe. Bitte überprüfen Sie Ihre Angaben sorgfältig.', 'userwarning');
			}
		}

		$formcreated = $registerinstanceForm->create($tpl);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
