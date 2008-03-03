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

		if (!empty($parameters['submit']))
		{
			if ( (!empty($parameters['user_username'])) || (!empty($parameters['user_password'])) && (!empty($parameters['user_password2'])))
			{
				$sql = "SELECT * FROM users WHERE user_username = '" . $parameters['user_username'] . "'";
				$res = $this->database->query($sql);
				if ($this->database->numRows($res) > 0)
				{
					$this->messages->setMessage('A user with this name already exists in the database. Please choose another username.', 'userwarning');
				}
				else
				{
					if ($parameters['user_password'] == $parameters['user_password2'])
					{
						// insert user
						srand(microtime()*1000000);
						$key = rand(10000,1000000000);
						$key = md5($key);
						$sqlUser = "INSERT INTO users(user_username, user_key, user_password) VALUES('" . $parameters['user_username'] . "', '" . $key . "', '" . $parameters['user_password'] . "')";
						$resUser = $this->database->query($sqlUser);

						$currentId = $this->database->insertId();

						//userrole
						$sqlUserrole = "INSERT INTO userroles_to_users(userroleuser_userrole, userroleuser_user) VALUES('" . $parameters['userroleuser_id'] . "', '" . $currentId . "')";
						$resUserrole = $this->database->query($sqlUserrole);

						//userdata
						$userdataKeys = array();
						$userdataValues = array();
						foreach ($parameters['userdata'] as $key => $value)
						{
							$userdataKeys[] = $key;
							$userdataValues[] = $value;
						}

						$sqlUserdata = "INSERT INTO userdata(userdata_user, " . implode(', ', $userdataKeys) . ") VALUES('" . $currentId . "', '" . implode("', '", $userdataValues) . "')";
						$resPassword = $this->database->query($sqlUserdata);

						$this->debug->unguard(true);
						$tpl->redirect($tpl->createLink('users', 'index'));
					}
					else
					{
						$this->messages->setMessage('The given password does not match the confirmation.', 'userwarning');
					}
				}
			}
			else
			{
				$this->messages->setMessage('Please fill out all required fields (name, password and confirmation).', 'userwarning');
			}
		}

		// show userroles

		$sqlUserroles = "SELECT * FROM userroles";
		$resUserroles = $this->database->query($sqlUserroles);
		$i=0;
		while ($rowUserroles = $this->database->fetchArray($resUserroles))
		{
			$tpl->assign('userrolevalue', $rowUserroles['userrole_id']);
			$tpl->assign('userroletext', $rowUserroles['userrole_name']);
			if ($i==0)
			{
				$tpl->assign('userrolecheck', 'checked="checked"');
			}
			else
			{
				$tpl->assign('userrolecheck', '');
			}

			$tpl->insertBlock('userroleloop');
			$i++;
		}

		// show userdata
		$sqlUserdataTable = "EXPLAIN userdata";
		$resUserdataTable = $this->database->query($sqlUserdataTable);
		while ($rowUserdataTable = $this->database->fetchArray($resUserdataTable))
		{
			$userdataTable[$rowUserdataTable['Field']] = $rowUserdataTable['Type'];
		}

		foreach ($userdataTable as $dataKey => $dataValue)
		{
			if ( ($dataKey == 'userdata_user') || ($dataKey == 'userdata_id') ) continue;

			if ($dataValue == 'text')
			{
				$tpl->assign('userdatakey', $dataKey);
				$formData = '<textarea name="userdata[' . $dataKey . ']" class="formtext"></textarea>';
				$tpl->assign('userdatavalue', $formData);
			}
			elseif ($dataValue == 'tinyint(1)')
			{
				$tpl->assign('userdatakey', $dataKey);
				$formData = '<input type="checkbox" name="userdata[' . $dataKey . ']" value="">';
				$tpl->assign('userdatavalue', $formData);
			}
			elseif ($dataValue == 'date')
			{
				$tpl->assign('userdatakey', $dataKey);
				$formData = '<input type="text" maxlength="10" name="userdata[' . $dataKey . ']" value="" class="formtext" />';
				$tpl->assign('userdatavalue', $formData);
			}
			else
			{
				if (strpos($dataKey, '(') !== false)
				{
					$typeLength = substr($dataKey, strpos($dataKey, '(')+1, -1);
				}
				else
				{
					$typeLength = '30';
				}

				$tpl->assign('userdatakey', $dataKey);
				$formData = '<input type="text" maxlength="' . $typeLength . '" name="userdata[' . $dataKey . ']" value="" class="formtext" />';
				$tpl->assign('userdatavalue', $formData);
			}

			$tpl->insertBlock('userdataloop');
		}

		$tpl->assignDataset($parameters);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function edituser($parameters=array())
	{
		$this->debug->guard();

		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['user_id'])) $currentId = $parameters['user_id'];

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_edituser'));

		$updatesuccessful = true;
		if (!empty($parameters['submit']))
		{
			// check and update username
			if ( (!empty($parameters['user_username'])) )
			{
				// check and update username
				if ($parameters['user_username'] != $this->user->getUsername())
				{
					$sql = "SELECT * FROM users WHERE user_username='" . $parameters['user_username'] . "'";
					$res = $this->database->query($sql);
					if ($this->database->numRows($res) > 0)
					{
						$this->debug->write('Der Benutzername existiert bereits. Bitte wählen Sie einen anderen Benutzernamen aus.', 'userwarning');
						$this->messages->setMessage('Der Benutzername existiert bereits. Bitte wählen Sie einen anderen Benutzernamen aus.', 'userwarning');
						$updatesuccessful = false;
					}

					$sql = "UPDATE users SET user_username = '" . $parameters['user_username'] . "' WHERE user_id='" . $currentId . "'";
					$res = $this->database->query($sql);
					if (!$res)
					{
						$this->debug->write('Problem changing the username of a user', 'usererror');
						$this->messages->setMessage('Problem changing the username of a user', 'usererror');
						$updatesuccessful = false;
					}
				}
			}
			else
			{
				$this->messages->setMessage('Bitte füllen Sie die Pflichtfelder aus', 'userwarning');
			}

			// check and update password
			if ( (!empty($parameters['user_password'])) || (!empty($parameters['user_password2'])) )
			{
				if ($parameters['user_password'] == $parameters['user_password2'])
				{
					$sql = "UPDATE users SET user_password='" . md5($parameters['user_password']) . "' WHERE user_id='" . $currentId . "'";
					$res = $this->database->query($sql);
				}
				else
				{
					$this->messages->setMessage('Die Bestätigung stimmt nicht mit dem gewählten Passwort überein. Das Passwort wurde nicht geändert.', 'userwarning');
					$updatesuccessful = false;
				}
			}

			// do usergroups
			if ( (!empty($parameters['usergroups'])) && (is_array($parameters['usergroups'])) )
			{
				$sql = "DELETE FROM users_to_groups WHERE usergroup_user='" . $currentId . "'";
				$res = $this->database->query($sql);

				foreach ($parameters['usergroups'] as $usergroup)
				{
					$sql = "INSERT INTO users_to_groups(usergroup_user, usergroup_group) VALUES('" . $currentId . "', '" . $usergroup . "')";
					$res = $this->database->query($sql);
				}
			}

			//done
			if ($updatesuccessful)
			{
				$this->messages->setMessage('Die Benutzerdaten wurden erfolgreich geändert', 'usermessage');
//				$this->debug->unguard(true);
//				$ret = $this->index(array());
//				return $ret;
			}

		}

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

		$rowUser['user_password'] = '';
		$tpl->assignDataset($rowUser);
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


	public function edituserdata($parameters=array())
	{
		$this->debug->guard();

		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['user_id'])) $currentId = $parameters['user_id'];

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_edituserdata'));

		if (!empty($parameters['submit']))
		{
			// check userdata and update if necessary
			$sqlUserdata = "SELECT * FROM userdata WHERE userdata_user='" . $currentId . "'";
			$resUserdata = $this->database->query($sqlUserdata);
			$rowUserdata = $this->database->fetchArray($resUserdata);
			if ($rowUserdata != $parameters['userdata'])
			{
				$sqlUserdata = "DELETE FROM userdata WHERE userdata_user='" . $currentId . "'";
				$resPassword = $this->database->query($sqlUserdata);

				$userdataKeys = array();
				$userdataValues = array();
				foreach ($parameters['userdata'] as $key => $value)
				{
					$userdataKeys[] = $key;
					$userdataValues[] = $value;
				}

				$sqlUserdata = "INSERT INTO userdata(userdata_user, " . implode(', ', $userdataKeys) . ") VALUES('" . $currentId . "', '" . implode("', '", $userdataValues) . "')";
				$resUserdata = $this->database->query($sqlUserdata);

				if (!$resUserdata)
				{
					$this->messages->setMessage('Userdata could not be saved. Please make sure that all the entered values are correct.', 'userwarning');
				}
				else
				{
					$this->messages->setMessage('Userdata has been changed.', 'usermessage');
				}
			}
		}

		// show userdata
		$sqlUserdataTable = "EXPLAIN userdata";
		$resUserdataTable = $this->database->query($sqlUserdataTable);
		while ($rowUserdataTable = $this->database->fetchArray($resUserdataTable))
		{
			$userdataTable[$rowUserdataTable['Field']] = $rowUserdataTable['Type'];
		}

		$sqlUserdata = "SELECT * FROM userdata WHERE userdata_user='" . $currentId . "'";
		$resUserdata = $this->database->query($sqlUserdata);
		$rowUserdata = $this->database->fetchArray($resUserdata);

		foreach ($rowUserdata as $dataKey => $dataValue)
		{
			if ( ($dataKey == 'userdata_user') || ($dataKey == 'userdata_id') ) continue;

			if ($userdataTable[$dataKey] == 'text')
			{
				$tpl->assign('userdatakey', $dataKey);
				$formData = '<textarea name="userdata[' . $dataKey . ']" class="formtext">' . $dataValue . '</textarea>';
				$tpl->assign('userdatavalue', $formData);
			}
			elseif ($userdataTable[$dataKey] == 'tinyint(1)')
			{
				$checked = '';
				if ($dataValue == '1') $checked = 'checked="checked" ';
				$tpl->assign('userdatakey', $dataKey);
				$formData = '<input type="checkbox" name="userdata[' . $dataKey . ']" ' . $checked . 'value="' . $dataValue . '">';
				$tpl->assign('userdatavalue', $formData);
			}
			elseif ($userdataTable[$dataKey] == 'date')
			{
				$tpl->assign('userdatakey', $dataKey);
				$formData = '<input type="text" maxlength="10" name="userdata[' . $dataKey . ']" value="' . $dataValue . '" class="formtext" />';
				$tpl->assign('userdatavalue', $formData);
			}
			else
			{
				if (strpos($userdataTable[$dataKey], '(') !== false)
				{
					$typeLength = substr($userdataTable[$dataKey], strpos($userdataTable[$dataKey], '(')+1, -1);
				}
				else
				{
					$typeLength = '30';
				}

				$tpl->assign('userdatakey', $dataKey);
				$formData = '<input type="text" maxlength="' . $typeLength . '" name="userdata[' . $dataKey . ']" value="' . $dataValue . '" class="formtext" />';
				$tpl->assign('userdatavalue', $formData);
			}

			$tpl->insertBlock('userdataloop');
		}

		$sqlUser = "SELECT * FROM users WHERE user_id = '" . $currentId . "'";
		$resUser = $this->database->query($sqlUser);
		$rowUser = $this->database->fetchArray($resUser);

		$tpl->assignDataset($rowUser);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>