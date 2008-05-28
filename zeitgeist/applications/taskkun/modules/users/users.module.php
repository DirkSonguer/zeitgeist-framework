<?php


defined('TASKKUN_ACTIVE') or die();

class users
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $objects;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjectcache::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_index'));
		$tpl->assign('documenttitle', 'Benutzerübersicht');
		$tpl->assign('helptopic', '&topic=users');

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function adduser($parameters=array())
	{

		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_adduser'));
		$tpl->assign('documenttitle', 'Benutzer hinzufügen');
		$tpl->assign('helptopic', '&topic=adduser');

		$adduserForm = new zgStaticform();
		$adduserForm->load('forms/adduser.form.ini');

		$userfunctions = new tkUserfunctions();

		$formvalid = $adduserForm->process($parameters);

		if (!empty($parameters['submit']))
		{
			if ($formvalid)
			{
				$insertProblems = false;
				$newUserdata = $parameters['adduser'];

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
					if (!$userfunctions->createUser($newUserdata['user_username'], $newUserdata['user_password'], $newUserdata['userroleuser_userrole'], $newUserdata['userroleuser_usergroups'], $userdata))
					{
						$this->messages->setMessage('Die Benutzerdaten konnte nicht gespeichert werden.', 'userwarning');
					}
					else
					{
						$this->messages->setMessage('Die Daten wurden erfolgreich gespeichert.', 'usermessage');
						$this->debug->unguard(true);
						$tpl->redirect($tpl->createLink('users', 'index'));
						return true;
					}
				}
			}
			else
			{
				$this->messages->setMessage('Fehler bei der Eingabe. Bitte überprüfen Sie Ihre Angaben sorgfältig.', 'userwarning');
			}
		}

		$formcreated = $adduserForm->create($tpl);

		// show userroles
		$currentUserrole = 0;
		if (!empty($parameters['adduser']['userroleuser_userrole']))
		{
			$currentUserrole = $parameters['adduser']['userroleuser_userrole'];
		}

		$userroles = $userfunctions->getUserroles();
		foreach ($userroles as $rowUserrole)
		{
			$tpl->assign('userrole_value', $rowUserrole['userrole_id']);
			$tpl->assign('userrole_text', $rowUserrole['userrole_name']);
			if ($rowUserrole['userrole_id'] == $currentUserrole)
			{
				$tpl->assign('userrole_check', 'checked="checked"');
			}
			else
			{
				$tpl->assign('userrole_check', '');
			}

			$tpl->insertBlock('userrole_loop');
		}

		// show usergroups
		$currentUsergroups = array();
		if (!empty($parameters['adduser']['userroleuser_usergroups']))
		{
			foreach ($parameters['adduser']['userroleuser_usergroups'] as $tempUsergroup)
			{
				$currentUsergroups[$tempUsergroup[0]] = 1;
			}
		}

		$usergroups = $userfunctions->getUsergroups();
		foreach ($usergroups as $rowUsergroup)
		{
			$tpl->assign('usergroup_value', $rowUsergroup['group_id']);
			$tpl->assign('usergroup_text', $rowUsergroup['group_name']);
			if (!empty($currentUsergroups[$rowUsergroup['group_id']]))
			{
				$tpl->assign('usergroup_check', 'selected="selected"');
			}
			else
			{
				$tpl->assign('usergroup_check', '');
			}

			$tpl->insertBlock('usergroup_loop');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function edituser($parameters=array())
	{
		$this->debug->guard();

		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['edituser']['user_id'])) $currentId = $parameters['edituser']['user_id'];

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_edituser'));
		$tpl->assign('documenttitle', 'Benutzer bearbeiten');
		$tpl->assign('helptopic', '&topic=edituser');

		$edituserForm = new zgStaticform();
		$edituserForm->load('forms/edituser.form.ini');

		$userfunctions = new tkUserfunctions();

		if (!empty($parameters['submit']))
		{
			$formvalid = $edituserForm->process($parameters);

			if ($formvalid)
			{
				$updateProblems = false;
				$newUserdata = $parameters['edituser'];

				// check and update username
				if ($newUserdata['user_username'] != $userfunctions->getUsername($currentId))
				{
					if (!$userfunctions->changeUsername($newUserdata['user_username'], $currentId))
					{
						$edituserForm->validateElement('user_username', false);
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
						$userfunctions->changePassword($newUserdata['user_password'], $currentId);
					}
					else
					{
						$edituserForm->validateElement('user_password', false);
						$edituserForm->validateElement('user_password2', false);
						$this->messages->setMessage('Die Bestätigung stimmt nicht mit dem gewählten Passwort überein. Das Passwort wurde nicht geändert.', 'userwarning');
						$updateProblems = true;
					}
				}

				// userroles
				if ( (!empty($newUserdata['userroleuser_userrole'])) && ($newUserdata['userroleuser_userrole'] != $userfunctions->getUserroleForUser($currentId)) )
				{
					if (!$userfunctions->changeUserrole($newUserdata['userroleuser_userrole'], $currentId))
					{
						$this->messages->setMessage('Die Nutzerrolle konnte nicht gespeichert werden.', 'userwarning');
						$updateProblems = true;
					}
				}

				// usergroups
				if (!empty($newUserdata['userroleuser_usergroups']))
				{
					if (!$userfunctions->changeUsergroups($newUserdata['userroleuser_usergroups'], $currentId))
					{
						$this->messages->setMessage('Die Gruppen konnten nicht gespeichert werden.', 'userwarning');
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
			$userinformation = $userfunctions->getUserdata($currentId);
			$userinformation['user_password'] = '';

			$processData = array();
			$processData['edituser'] = $userinformation;
			$formvalid = $edituserForm->process($processData);
		}

		$formcreated = $edituserForm->create($tpl);

		// show userroles
		$currentUserrole = $userfunctions->getUserroleForUser($currentId);
		$userroles = $userfunctions->getUserroles();

		foreach ($userroles as $rowUserrole)
		{
			$tpl->assign('userrole_value', $rowUserrole['userrole_id']);
			$tpl->assign('userrole_text', $rowUserrole['userrole_name']);
			if ($rowUserrole['userrole_id'] == $currentUserrole)
			{
				$tpl->assign('userrole_check', 'checked="checked"');
			}
			else
			{
				$tpl->assign('userrole_check', '');
			}

			$tpl->insertBlock('userrole_loop');
		}

		// show usergroups
		$tempUsergroups = $userfunctions->getUsergroupsForUser($currentId);
		$currentUsergroups = array();
		foreach ($tempUsergroups as $tempUsergroup)
		{
			$currentUsergroups[$tempUsergroup['group_id']] = 1;
		}

		$usergroups = $userfunctions->getUsergroups();
		foreach ($usergroups as $rowUsergroup)
		{
			$tpl->assign('usergroup_value', $rowUsergroup['group_id']);
			$tpl->assign('usergroup_text', $rowUsergroup['group_name']);
			if (!empty($currentUsergroups[$rowUsergroup['group_id']]))
			{
				$tpl->assign('usergroup_check', 'selected="selected"');
			}
			else
			{
				$tpl->assign('usergroup_check', '');
			}

			$tpl->insertBlock('usergroup_loop');
		}

		$tpl->assign('user_id:value', $currentId);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function deleteuser($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		if (!empty($parameters['id']))
		{
			$userfunctions = new tkUserfunctions();
			if ($userfunctions->deleteuser($parameters['id']))
			{
				$this->messages->setMessage('Der Benutzer wurde gelöscht', 'usermessage');
			}
			else
			{
				$this->messages->setMessage('Der Benutzer konnte nicht gelöscht werden. Bitte verständigen Sie einen Administrator', 'usererror');
			}
		}

		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('users', 'index'));

		$this->debug->unguard(true);
		return true;
	}


	public function activateuser($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		if (!empty($parameters['id']))
		{
			$userfunctions = new tkUserfunctions();

			if ($parameters['id'] != $this->user->getUserId())
			{
				if ($userfunctions->activateuser($parameters['id']))
				{
					$this->messages->setMessage('Das Benutzerkonto wurde aktiviert', 'usermessage');
				}
				else
				{
					$this->messages->setMessage('Das Benutzerkonto konnte nicht aktiviert werden. Bitte verständigen Sie einen Administrator', 'usererror');
				}
			}
			else
			{
				$this->messages->setMessage('Es ist nicht möglich das eigene Benutzerkonto zu deaktivieren', 'userwarning');
			}
		}

		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('users', 'index'));

		$this->debug->unguard(true);
		return true;
	}


	public function deactivateuser($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		if (!empty($parameters['id']))
		{
			$userfunctions = new tkUserfunctions();

			if ($parameters['id'] != $this->user->getUserId())
			{
				if ($userfunctions->deactivateuser($parameters['id']))
				{
					$this->messages->setMessage('Das Benutzerkonto wurde deaktiviert', 'usermessage');
				}
				else
				{
					$this->messages->setMessage('Das Benutzerkonto konnte nicht deaktiviert werden. Bitte verständigen Sie einen Administrator', 'usererror');
				}
			}
			else
			{
				$this->messages->setMessage('Es ist nicht möglich das eigene Benutzerkonto zu deaktivieren', 'userwarning');
			}
		}

		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('users', 'index'));

		$this->debug->unguard(true);
		return true;
	}

}
?>