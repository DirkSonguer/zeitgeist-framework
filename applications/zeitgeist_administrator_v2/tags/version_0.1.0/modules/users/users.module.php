<?php

defined('ZGADMIN_ACTIVE') or die();

class users
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $userfunctions;
	protected $userdata;
	protected $userroles;
	protected $userrights;
	protected $setupfunctions;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		
		$this->userfunctions = new zgaUserfunctions();
		$this->userdata = new zgaUserdata();
		$this->userroles = new zgaUserroles();
		$this->userrights = new zgaUserrights();
		$this->setupfunctions = new zgaSetupfunctions();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function index($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_index'));
		
		$userlist = $this->userfunctions->getAllUsers();
		
		foreach ($userlist as $user)
		{
			$tpl->assignDataset($user);
			$tpl->insertBlock('applicationuser');
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

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_edituser'));

		$userForm = new zgForm();
		$userForm->load('forms/edituser.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $userForm->validate($parameters);

			if ($formvalid)
			{
				$userfunctions = new zgaUserfunctions();
				$userinformation = $userfunctions->getInformation($currentId);
				$username = $userinformation['user_username'];
				$userkey = $userinformation['user_key'];
				
				$update = true;

				// User name
				if ($username != $parameters['edituser']['user_username'])
				{
					$usernamearray = array('user_username' => $parameters['edituser']['user_username']);
					$ret = $userfunctions->changeUserinformation($currentId, $usernamearray);
					if (!$ret)
					{
						$this->messages->setMessage('Could not change username', 'userwarning');
						$update = false;
					}
					else
					{
						$this->messages->setMessage('Username data was changed', 'usermessage');
					}
				}


				// User key
				if ($userkey != $parameters['edituser']['user_key'])
				{
					$userkeyarray = array('user_key' => $parameters['edituser']['user_key']);
					$ret = $userfunctions->changeUserinformation($currentId, $userkeyarray);
					if (!$ret)
					{
						$this->messages->setMessage('Could not change user key', 'userwarning');
						$update = false;
					}
					else
					{
						$this->messages->setMessage('User key data was changed', 'usermessage');
					}
				}


				// Password check
				if ( (!empty($parameters['edituser']['user_password'])) && (!empty($parameters['edituser']['user_pwconfirmation'])) )
				{
					if ($parameters['edituser']['user_password'] == $parameters['edituser']['user_pwconfirmation'])
					{
						$userkeyarray = array('user_password' => $parameters['edituser']['user_passwordf']);
						$ret = $userfunctions->changeUserinformation($currentId, $userkeyarray);
						if (!$ret) 
						{
							$this->messages->setMessage('Could not change user key', 'userwarning');
							$update = false;
						}
						else
						{
							$this->messages->setMessage('User key data was changed', 'usermessage');
						}
					}
					else
					{
						$this->messages->setMessage('The passwords do not match up', 'userwarning');
						$update = false;
					}
				}


				// Userdata changes
				$userdata = $this->userdata->loadUserdata($currentId);
				$userdataChanges = false;
				
				foreach ($parameters['userdata'] as $key => $value)
				{
					if ( (!empty($userdata[$key])) && ($userdata[$key] != $value) )
					{
						$userdata[$key] = $value;
						$userdataChanges = true;
					}
				}

				$ret = $this->userdata->saveUserdata($currentId, $userdata);
				if (!$ret)
				{
					$this->messages->setMessage('The userdata could not be saved', 'userwarning');
					$update = false;
				}


				// Saving Userroles
				$ret = $this->userroles->saveUserroles($currentId, $parameters['userroles']);
				if (!$ret)
				{
					$this->messages->setMessage('The userroles could not be saved', 'userwarning');
					$update = false;
				}
				

				// Saving Userrights
				$ret = $this->userrights->saveUserrights($currentId, $parameters['userrights']);
				if (!$ret)
				{
					$this->messages->setMessage('The userroles could not be saved', 'userwarning');
					$update = false;
				}
				

				// Check if everything went fine
				if ($update)
				{
					$tpl->redirect($tpl->createLink('users', 'index'));
					$this->debug->unguard(true);
					return true;
				}
			}
		}
		else
		{
			$userprofile = array();
			
			$userinformation = $this->userfunctions->getInformation($currentId);
			$userprofile['user_username'] = $userinformation['user_username'];
			$userprofile['user_key'] = $userinformation['user_key'];

			$parameters['edituser'] = $userprofile;
			$userForm->validate($parameters);
		}


		// list userdata for user
		$userdata = $this->userdata->loadUserdata($currentId);
		foreach ($userdata as $key => $value)
		{
			if ( ($key != 'userdata_id') && ($key != 'userdata_user') )
			{
				$tpl->assign('userdatafield_key', $key);
				$tpl->assign('userdatafield_value', $value);
				$tpl->insertBlock('userdatafield');
			}
		}


		// list userroles for user
		$userroles = $this->setupfunctions->getAllUserroles();
		$userrolesForUser = $this->userroles->loadUserroles($currentId);
		foreach ($userroles as $userrole)
		{
			$tpl->assign('userrole_id', $userrole['userrole_id']);
			$tpl->assign('userrole_name', $userrole['userrole_name']);
			$tpl->assign('userrole_description', $userrole['userrole_description']);

			if (array_key_exists($userrole['userrole_id'], $userrolesForUser))
			{
				$tpl->assign('userrole_active', 'checked="checked"');
			}
			else
			{
				$tpl->assign('userrole_active', '');
			}

			$tpl->insertBlock('userroles');
		}


		// list userrights for user
		$actions = $this->setupfunctions->getAllActions();
		$userrightsForUser = $this->userrights->loadUserrights($currentId);
		foreach ($actions as $action)
		{
			$tpl->assign('action_id', $action['action_id']);
			$tpl->assign('action_name', $action['action_name']);
			$tpl->assign('module_name', $action['module_name']);
			$tpl->assign('action_active', '');

			if (array_key_exists($action['action_id'], $userrightsForUser))
			{
				$tpl->assign('action_active', 'checked="checked"');
			}
			else
			{
				$tpl->assign('action_active', '');
			}

			$tpl->insertBlock('userrights');
		}


		$userForm->insert($tpl);
		$tpl->assign('user_id', $currentId);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function deleteuser($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();

		if (empty($parameters['id']))
		{
			$tpl->redirect($tpl->createLink('users', 'index'));
			$this->debug->unguard(false);
			return false;
		}

		$ret = $this->userfunctions->deleteUser($parameters['id']);

		if (!$ret)
		{
			$this->messages->setMessage('Could not delete the user, it\'s is still available', 'userwarning');
		}
		else
		{
			$this->messages->setMessage('User deleted', 'usermessage');
		}
		
		$tpl->redirect($tpl->createLink('users', 'index'));
		
		$this->debug->unguard(true);
		return true;
	}


}
?>
