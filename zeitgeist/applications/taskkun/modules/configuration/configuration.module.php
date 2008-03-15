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

		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['user_id'])) $currentId = $parameters['user_id'];

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('configuration', 'templates', 'configuration_index'));

		$updatesuccessful = true;
		if (!empty($parameters['submit']))
		{
			// check and update username
			if ( (!empty($parameters['user_username'])) )
			{
				// check and update username
				if ($parameters['user_username'] != $this->user->getUsername())
				{
					if ($this->user->changeUsername($parameters['user_username']))
					{
						$this->debug->write('Der Benutzername existiert bereits. Bitte wählen Sie einen anderen Benutzernamen aus.', 'warning');
						$this->messages->setMessage('Der Benutzername existiert bereits. Bitte wählen Sie einen anderen Benutzernamen aus.', 'userwarning');
						$updatesuccessful = false;
					}
				}
			}
			else
			{
				$this->messages->setMessage('Bitte füllen Sie alle Pflichtfelder aus', 'userwarning');
			}

			// update userdata
			if (!empty($parameters['userdata_lastname'])) $this->user->setUserdata('userdata_lastname', $parameters['userdata_lastname']);
			if (!empty($parameters['userdata_firstname'])) $this->user->setUserdata('userdata_firstname', $parameters['userdata_firstname']);
			if (!empty($parameters['userdata_address1'])) $this->user->setUserdata('userdata_address1', $parameters['userdata_address1']);
			if (!empty($parameters['userdata_address2'])) $this->user->setUserdata('userdata_address2', $parameters['userdata_address2']);
			if (!empty($parameters['userdata_zip'])) $this->user->setUserdata('userdata_zip', $parameters['userdata_zip']);
			if (!empty($parameters['userdata_city'])) $this->user->setUserdata('userdata_city', $parameters['userdata_city']);
			if (!empty($parameters['userdata_url'])) $this->user->setUserdata('userdata_url', $parameters['userdata_url']);

			// check and update password
			if ( (!empty($parameters['user_password'])) || (!empty($parameters['user_password2'])) )
			{
				if ($parameters['user_password'] == $parameters['user_password2'])
				{
					$this->user->changePassword($parameters['user_password']);
				}
				else
				{
					$this->messages->setMessage('Die Bestätigung stimmt nicht mit dem gewählten Passwort überein. Das Passwort wurde nicht geändert.', 'userwarning');
					$updatesuccessful = false;
				}
			}

			//done
			if ($updatesuccessful)
			{
				$this->messages->setMessage('Die Benutzerdaten wurden erfolgreich geändert', 'usermessage');
			}
		}

		// show userroles
		$sqlUser = "SELECT u.user_username, ud.* FROM users AS u LEFT JOIN userdata ud ON u.user_id = ud.userdata_user WHERE u.user_id = '" . $currentId . "'";
		$resUser = $this->database->query($sqlUser);
		$rowUser = $this->database->fetchArray($resUser);

		$rowUser['user_password'] = '';
		$tpl->assignDataset($rowUser);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
