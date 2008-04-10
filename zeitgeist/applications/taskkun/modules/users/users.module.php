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

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function adduser($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_adduser'));

		$adduserForm = new zgStaticform();
		$adduserForm->load('forms/adduser.form.ini');

		$userfunctions = new tkUserfunctions();

		if (!empty($parameters['submit']))
		{
			$formvalid = $adduserForm->process($parameters);

			if ($formvalid)
			{
				$updateProblems = false;
				$newUserdata = $parameters['adduser'];

				// check and update username
				if ($newUserdata['user_username'] != $userfunctions->getUsername($currentId))
				{
					if (!$userfunctions->changeUsername($newUserdata['user_username'], $currentId))
					{
						$adduserForm->validateElement('user_username', false);
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
						$adduserForm->validateElement('user_password', false);
						$adduserForm->validateElement('user_password2', false);
						$this->messages->setMessage('Die Bestätigung stimmt nicht mit dem gewählten Passwort überein. Das Passwort wurde nicht geändert.', 'userwarning');
						$updateProblems = true;
					}
				}

				// userroles
				if ( (!empty($newUserdata['userroleuser_userrole'])) && ($newUserdata['userroleuser_userrole'] != $userfunctions->getUserrole($currentId)) )
				{
					if (!$userfunctions->changeUserrole($newUserdata['userroleuser_userrole'], $currentId))
					{
						$this->messages->setMessage('Die Nutzerrolle konnte nicht gespeichert werden.', 'userwarning');
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
			$processData = array();
			$formvalid = $adduserForm->process($processData);
		}

		$formcreated = $adduserForm->create($tpl);

		// show userroles
		$sqlUser = "SELECT u.*, ur.* FROM users AS u, userroles_to_users AS uru LEFT JOIN userroles ur ON ur.userrole_id = uru.userroleuser_userrole WHERE u.user_id = uru.userroleuser_user AND u.user_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";
		$resUser = $this->database->query($sqlUser);
		$rowUser = $this->database->fetchArray($resUser);

		$sqlUserroles = "SELECT * FROM userroles";
		$resUserroles = $this->database->query($sqlUserroles);
		while ($rowUserroles = $this->database->fetchArray($resUserroles))
		{
			$tpl->assign('userrole_value', $rowUserroles['userrole_id']);
			$tpl->assign('userrole_text', $rowUserroles['userrole_name']);
			if ($rowUserroles['userrole_id'] == $rowUser['userrole_id'])
			{
				$tpl->assign('userrole_check', 'checked="checked"');
			}
			else
			{
				$tpl->assign('userrole_check', '');
			}

			$tpl->insertBlock('userrole_loop');
		}

		$sqlUsergroups = "SELECT group_id, group_name, IF(u2g.usergroup_user='" . $currentId . "', '1','0') as group_selected ";
		$sqlUsergroups .= "FROM groups g LEFT JOIN users_to_groups u2g ON g.group_id = u2g.usergroup_group GROUP BY g.group_id";
		$resUsergroups = $this->database->query($sqlUsergroups);
		while ($rowUsergroups = $this->database->fetchArray($resUsergroups))
		{
			$tpl->assign('usergroup_value', $rowUsergroups['group_id']);
			$tpl->assign('usergroup_text', $rowUsergroups['group_name']);
			if ($rowUsergroups['group_id'] == '1')
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


	public function edituser($parameters=array())
	{
		$this->debug->guard();

		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['edituser']['user_id'])) $currentId = $parameters['edituser']['user_id'];

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_edituser'));

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
				if ( (!empty($newUserdata['userroleuser_userrole'])) && ($newUserdata['userroleuser_userrole'] != $userfunctions->getUserrole($currentId)) )
				{
					if (!$userfunctions->changeUserrole($newUserdata['userroleuser_userrole'], $currentId))
					{
						$this->messages->setMessage('Die Nutzerrolle konnte nicht gespeichert werden.', 'userwarning');
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
			$sqlUser = "SELECT u.user_username, ud.* FROM users AS u LEFT JOIN userdata ud ON u.user_id = ud.userdata_user WHERE u.user_id = '" . $currentId . "'";
			$resUser = $this->database->query($sqlUser);
			$rowUser = $this->database->fetchArray($resUser);

			$rowUser['user_password'] = '';

			$processData = array();
			$processData['edituser'] = $rowUser;
			$formvalid = $edituserForm->process($processData);
		}

		$formcreated = $edituserForm->create($tpl);

		// show userroles
		$sqlUser = "SELECT u.*, ur.* FROM users AS u, userroles_to_users AS uru LEFT JOIN userroles ur ON ur.userrole_id = uru.userroleuser_userrole WHERE u.user_id = uru.userroleuser_user AND u.user_id = '" . $currentId . "'";
		$resUser = $this->database->query($sqlUser);
		$rowUser = $this->database->fetchArray($resUser);

		$sqlUserroles = "SELECT * FROM userroles";
		$resUserroles = $this->database->query($sqlUserroles);
		while ($rowUserroles = $this->database->fetchArray($resUserroles))
		{
			$tpl->assign('userrole_value', $rowUserroles['userrole_id']);
			$tpl->assign('userrole_text', $rowUserroles['userrole_name']);
			if ($rowUserroles['userrole_id'] == $rowUser['userrole_id'])
			{
				$tpl->assign('userrole_check', 'checked="checked"');
			}
			else
			{
				$tpl->assign('userrole_check', '');
			}

			$tpl->insertBlock('userrole_loop');
		}

		$sqlUsergroups = "SELECT group_id, group_name, IF(u2g.usergroup_user='" . $currentId . "', '1','0') as group_selected ";
		$sqlUsergroups .= "FROM groups g LEFT JOIN users_to_groups u2g ON g.group_id = u2g.usergroup_group GROUP BY g.group_id";
		$resUsergroups = $this->database->query($sqlUsergroups);
		while ($rowUsergroups = $this->database->fetchArray($resUsergroups))
		{
			$tpl->assign('usergroup_value', $rowUsergroups['group_id']);
			$tpl->assign('usergroup_text', $rowUsergroups['group_name']);
			if ($rowUsergroups['group_id'] == '1')
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
			// user account
			$sql = "DELETE FROM users WHERE user_id='" . $parameters['id'] . "'";
			$res = $this->database->query($sql);

			// userdata
			$sql = "DELETE FROM userdata WHERE userdata_user='" . $parameters['id'] . "'";
			$res = $this->database->query($sql);

			// userrights
			$sql = "DELETE FROM userrights WHERE userright_user='" . $parameters['id'] . "'";
			$res = $this->database->query($sql);

			// userrole
			$sql = "DELETE FROM userroles_to_users WHERE userroleuser_user='" . $parameters['id'] . "'";
			$res = $this->database->query($sql);
		}

		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('users', 'index'));

		$this->debug->unguard(true);
		return true;
	}

}
?>