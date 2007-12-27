<?php


defined('ZGADMIN_ACTIVE') or die();

class users
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $managedDatabase;
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

		$mdb_server = $this->configuration->getConfiguration('administrator', 'databases', 'manageddb_server');
		$mdb_username = $this->configuration->getConfiguration('administrator', 'databases', 'manageddb_username');
		$mdb_userpw = $this->configuration->getConfiguration('administrator', 'databases', 'manageddb_userpw');
		$mdb_database = $this->configuration->getConfiguration('administrator', 'databases', 'manageddb_database');
		$this->managedDatabase = new zgDatabase();
		$this->managedDatabase->connect($mdb_server, $mdb_username, $mdb_userpw, $mdb_database);
	}
	
	
	public function index($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_index'));		

		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}

	
	public function adduser($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_adduser'));
		
		if (!empty($parameters['submit']))
		{
			if ( (!empty($parameters['user_username'])) || (!empty($parameters['user_password'])) && (!empty($parameters['user_password2'])))
			{
				$sql = "SELECT * FROM users WHERE user_username = '" . $parameters['user_username'] . "'";
				$res = $this->managedDatabase->query($sql);
				if ($this->managedDatabase->numRows($res) > 0)
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
						$resUser = $this->managedDatabase->query($sqlUser);
						
						$currentId = $this->managedDatabase->insertId();
						
						//userrole
						$sqlUserrole = "INSERT INTO userroles_to_users(userroleuser_userrole, userroleuser_user) VALUES('" . $parameters['userroleuser_id'] . "', '" . $currentId . "')";
						$resUserrole = $this->managedDatabase->query($sqlUserrole);
	
						//userdata
						$userdataKeys = array();
						$userdataValues = array();
						foreach ($parameters['userdata'] as $key => $value)
						{
							$userdataKeys[] = $key;
							$userdataValues[] = $value;
						}
	
						$sqlUserdata = "INSERT INTO userdata(userdata_user, " . implode(', ', $userdataKeys) . ") VALUES('" . $currentId . "', '" . implode("', '", $userdataValues) . "')";
						$resPassword = $this->managedDatabase->query($sqlUserdata);
	
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
		$resUserroles = $this->managedDatabase->query($sqlUserroles);
		$i=0;
	    while ($rowUserroles = $this->managedDatabase->fetchArray($resUserroles))
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
		$resUserdataTable = $this->managedDatabase->query($sqlUserdataTable);
		while ($rowUserdataTable = $this->managedDatabase->fetchArray($resUserdataTable))
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
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_edituser'));
		
		if (!empty($parameters['submit']))
		{
			if ( (!empty($parameters['user_username'])) && (!empty($parameters['user_key'])) )
			{
				// check and update username/ key
				$sqlPassword = "UPDATE users SET user_username='" . $parameters['user_username'] . "', user_key='" . $parameters['user_key'] . "' WHERE user_id='" . $currentId . "'";
				$resPassword = $this->managedDatabase->query($sqlPassword);						
				
				// check and update password
				if ((!empty($parameters['user_password'])) || (!empty($parameters['user_password2'])))
				{
					if ($parameters['user_password'] == $parameters['user_password2'])
					{
						$sqlPassword = "UPDATE users SET user_password='" . md5($parameters['user_password']) . "' WHERE user_id='" . $currentId . "'";
						$resPassword = $this->managedDatabase->query($sqlPassword);						
					}
					else
					{
						$this->messages->setMessage('The given password does not match the confirmation. Password was not changed.', 'userwarning');
					}
				}
				
				// check userrole and update it if necessary
				$currentUserrole = $parameters['userroleuser_id'];
				$sqlUserrole = "SELECT * FROM userroles_to_users WHERE userroleuser_user = '" . $currentId . "'";
				$resUserrole = $this->managedDatabase->query($sqlUserrole);
			    $rowUserrole = $this->managedDatabase->fetchArray($resUserrole);
			    if ($rowUserrole['userroleuser_userrole'] != $parameters['userroleuser_id'])
			    {
			    	$currentUserrole = $rowUserrole['userroleuser_userrole'];
					$sqlUserrole = "UPDATE userroles_to_users SET userroleuser_userrole='" . $parameters['userroleuser_id'] . "' WHERE userroleuser_user='" . $currentId . "'";
					$resUserrole = $this->managedDatabase->query($sqlUserrole);							
			    }

			    $this->messages->setMessage('User and data has been changed', 'usermessage');			    
			}
			else
			{
				$this->messages->setMessage('Please fill out all required fields (name and key).', 'userwarning');
			}
		}

		// show userroles
		$sqlUser = "SELECT u.*, ur.* FROM users AS u, userroles_to_users AS uru LEFT JOIN userroles ur ON ur.userrole_id = uru.userroleuser_userrole WHERE u.user_id = uru.userroleuser_user AND u.user_id = '" . $currentId . "'";
		$resUser = $this->managedDatabase->query($sqlUser);
	    $rowUser = $this->managedDatabase->fetchArray($resUser);
	    
		$sqlUserroles = "SELECT * FROM userroles";
		$resUserroles = $this->managedDatabase->query($sqlUserroles);
	    while ($rowUserroles = $this->managedDatabase->fetchArray($resUserroles))
	    {
	    	$tpl->assign('userrolevalue', $rowUserroles['userrole_id']);
	    	$tpl->assign('userroletext', $rowUserroles['userrole_name']);
	    	if ($rowUserroles['userrole_id'] == $rowUser['userrole_id'])
	    	{
	    		$tpl->assign('userrolecheck', 'checked="checked"');
	    	}
	    	else
	    	{
	    		$tpl->assign('userrolecheck', '');
	    	}
	    	
	    	$tpl->insertBlock('userroleloop');
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
		
		$tpl = new zgaTemplate();
		if (!empty($parameters['id']))
		{
			// user account
			$sql = "DELETE FROM users WHERE user_id='" . $parameters['id'] . "'";
			$res = $this->managedDatabase->query($sql);

			// userdata
			$sql = "DELETE FROM userdata WHERE userdata_user='" . $parameters['id'] . "'";
			$res = $this->managedDatabase->query($sql);

			// userrights
			$sql = "DELETE FROM userrights WHERE userright_user='" . $parameters['id'] . "'";
			$res = $this->managedDatabase->query($sql);

			// userrole
			$sql = "DELETE FROM userroles_to_users WHERE userroleuser_user='" . $parameters['id'] . "'";
			$res = $this->managedDatabase->query($sql);
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
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_edituserdata'));
		
		if (!empty($parameters['submit']))
		{
		    // check userdata and update if necessary
			$sqlUserdata = "SELECT * FROM userdata WHERE userdata_user='" . $currentId . "'";
			$resUserdata = $this->managedDatabase->query($sqlUserdata);
		    $rowUserdata = $this->managedDatabase->fetchArray($resUserdata);
		    if ($rowUserdata != $parameters['userdata'])
		    {
				$sqlUserdata = "DELETE FROM userdata WHERE userdata_user='" . $currentId . "'";
				$resPassword = $this->managedDatabase->query($sqlUserdata);

				$userdataKeys = array();
				$userdataValues = array();
				foreach ($parameters['userdata'] as $key => $value)
				{
					$userdataKeys[] = $key;
					$userdataValues[] = $value;
				}

				$sqlUserdata = "INSERT INTO userdata(userdata_user, " . implode(', ', $userdataKeys) . ") VALUES('" . $currentId . "', '" . implode("', '", $userdataValues) . "')";
				$resUserdata = $this->managedDatabase->query($sqlUserdata);
				
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
		$resUserdataTable = $this->managedDatabase->query($sqlUserdataTable);
		while ($rowUserdataTable = $this->managedDatabase->fetchArray($resUserdataTable))
		{
			$userdataTable[$rowUserdataTable['Field']] = $rowUserdataTable['Type'];
		}
	    
		$sqlUserdata = "SELECT * FROM userdata WHERE userdata_user='" . $currentId . "'";
		$resUserdata = $this->managedDatabase->query($sqlUserdata);
	    $rowUserdata = $this->managedDatabase->fetchArray($resUserdata);

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
		$resUser = $this->managedDatabase->query($sqlUser);
	    $rowUser = $this->managedDatabase->fetchArray($resUser);

	    $tpl->assignDataset($rowUser);
	    $tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}

	
	public function edituserrights($parameters=array())
	{
		$this->debug->guard();
		
		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['user_id'])) $currentId = $parameters['user_id'];
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_edituserrights'));
		
		if (!empty($parameters['submit']))
		{
		    // check userrights and update if necessary
			$sqlUserrights = "SELECT * FROM userrights WHERE userright_user='" . $currentId . "'";
			$resUserrights = $this->managedDatabase->query($sqlUserrights);
		    $rowUserrights = $this->managedDatabase->fetchArray($resUserrights);

			$sqlUserrole = "SELECT * FROM userroles_to_users WHERE userroleuser_user = '" . $currentId . "'";
			$resUserrole = $this->managedDatabase->query($sqlUserrole);
		    $rowUserrole = $this->managedDatabase->fetchArray($resUserrole);
			    		    
			$sqlUserrolerights = "SELECT * FROM userroles_to_actions WHERE userroleaction_userrole = '" . $rowUserrole['userroleuser_userrole'] . "'";
			$resUserrolerights = $this->managedDatabase->query($sqlUserrolerights);
		    while ($rowUserrolerights = $this->managedDatabase->fetchArray($resUserrolerights))
		    {
		    	if (!empty($parameters['userrights'][$rowUserrolerights['userroleaction_action']]))
		    	{
		    		unset($parameters['userrights'][$rowUserrolerights['userroleaction_action']]);
		    	}
		    }

		    if ($rowUserrights != $parameters['userrights'])
		    {
				$sqlUserdata = "DELETE FROM userrights WHERE userright_user='" . $currentId . "'";
				$resUserdata = $this->managedDatabase->query($sqlUserdata);

				$success = true;
				foreach ($parameters['userrights'] as $key => $value)
				{
					$sqlUserdata = "INSERT INTO userrights(userright_user, userright_action) VALUES('" . $currentId . "', '" . $key . "')";
					$resUserdata = $this->managedDatabase->query($sqlUserdata);
					
					if (!$resUserdata)
					{
						$success = false;
						$this->messages->setMessage('Could not save userright. Please contact an administrator.', 'userwarning');
					}	
				}
				
				if ($success) $this->messages->setMessage('Userrights were updated.', 'usermessage');
		    }
		    else
		    {
			    $this->messages->setMessage('No update needed.', 'usermessage');
			}
		}

		// show userroles & userrights
		$sqlUser = "SELECT u.*, ur.* FROM users AS u, userroles_to_users AS uru LEFT JOIN userroles ur ON ur.userrole_id = uru.userroleuser_userrole WHERE u.user_id = uru.userroleuser_user AND u.user_id = '" . $currentId . "'";
		$resUser = $this->managedDatabase->query($sqlUser);
	    $rowUser = $this->managedDatabase->fetchArray($resUser);
		
		$userrole = array();
		$sqlUserrole = "SELECT uta.* FROM userroles_to_actions uta WHERE uta.userroleaction_userrole = '" . $rowUser['userrole_id'] . "'";
		$resUserrole = $this->managedDatabase->query($sqlUserrole);
	    while ($rowUserrole = $this->managedDatabase->fetchArray($resUserrole))
	    {
	    	$userrole[$rowUserrole['userroleaction_action']] = $rowUserrole['userroleaction_userrole'];
	    }
	    
		$userrights = array();
		$sqlUserrights = "SELECT * FROM userrights WHERE userright_user = '" . $currentId . "'";
		$resUserrights = $this->managedDatabase->query($sqlUserrights);
	    while ($rowUserrights = $this->managedDatabase->fetchArray($resUserrights))
	    {
	    	$userrights[$rowUserrights['userright_action']] = $rowUserrights['userright_user'];
	    }

		$sqlRights = "SELECT a.* FROM actions a WHERE a.action_requiresuserright = '1'";
		$resRights = $this->managedDatabase->query($sqlRights);
	    while ($rowRights = $this->managedDatabase->fetchArray($resRights))
	    {
	    	$tpl->assign('right_id', $rowRights['action_id']);
	    	$tpl->assign('right_name', $rowRights['action_name']);
	    	$tpl->assign('right_description', $rowRights['action_description']);
	    	
	    	$right_active = '';
	    	
	    	if (!empty($userrole[$rowRights['action_id']]))
	    	{
	    		$right_active .= 'checked="checked" onclick="javascript:showRightsMessage(' . $rowRights['action_id'] . ');"';
	    	}
	    	elseif (!empty($userrights[$rowRights['action_id']]))
	    	{
	    		$right_active .= 'checked="checked"';
	    	}
	    	
	    	$tpl->assign('right_active', $right_active);

	    	$tpl->insertBlock('rightsloop');
	    }

	    $tpl->assignDataset($rowUser);
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}

}
?>