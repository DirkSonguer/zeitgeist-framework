<?php

defined('ZGADMIN_ACTIVE') or die();

class setup
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;	
	protected $setupfunctions;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->setupfunctions = new zgaSetupfunctions();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function index($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_index'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function showmodules($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_showmodules'));

		$moduledata = $this->setupfunctions->getAllModules();
		
		foreach ($moduledata as $module)
		{			
			$tpl->assignDataset($module);
			
			if ($module['module_active'] == 1)
			{
				$tpl->insertBlock('moduleactive');
			}
			else
			{
				$tpl->insertBlock('moduleinactive');
			}
			
			$tpl->insertBlock('applicationmodule');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function deletemodule($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();

		if (empty($parameters['id']))
		{
			$tpl->redirect($tpl->createLink('setup', 'showmodules'));
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->setupfunctions->deleteModule($parameters['id']);

		if (!$ret)
		{
			$this->messages->setMessage('Could not delete the module, it\'s is still available', 'userwarning');
		}
		else
		{
			$this->messages->setMessage('Module deleted', 'usermessage');
		}
		
		$tpl->redirect($tpl->createLink('setup', 'showmodules'));
		
		$this->debug->unguard(true);
		return true;
	}


	public function createmodule($parameters=array())
	{
		$this->debug->guard();

		if (!empty($parameters['moduledata']['module_active'])) $parameters['moduledata']['module_active'] = 1;

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_createmodule'));

		$moduleForm = new zgForm();
		$moduleForm->load('forms/moduledata.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $moduleForm->validate($parameters);

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveModule($parameters['moduledata']);
				if (!$ret)
				{
					$this->messages->setMessage('Could not save module data to database', 'userwarning');
				}
				else
				{
					$this->messages->setMessage('Module was created', 'usermessage');
				}

				$tpl->redirect($tpl->createLink('setup', 'showmodules'));
				$this->debug->unguard(true);
				return true;
			}

			$active = $moduleForm->getElementValue('module_active');
			if ($active) $moduleForm->setElementValue('module_active', 'checked="checked"');

			$formcreated = $moduleForm->insert($tpl);
		}

		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}


	public function editmodule($parameters=array())
	{
		$this->debug->guard();

		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['moduledata']['module_id'])) $currentId = $parameters['moduledata']['module_id'];
		if (!empty($parameters['moduledata']['module_active'])) $parameters['moduledata']['module_active'] = 1;

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_editmodule'));

		$moduleForm = new zgForm();
		$moduleForm->load('forms/moduledata.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $moduleForm->validate($parameters);

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveModule($parameters['moduledata']);
				if (!$ret)
				{
					$this->messages->setMessage('Could not save module data to database', 'userwarning');
				}
				else
				{
					$this->messages->setMessage('Module data was changed', 'usermessage');
				}

				$tpl->redirect($tpl->createLink('setup', 'showmodules'));
				$this->debug->unguard(true);
				return true;
			}
		}
		else
		{
			$moduledata = $this->setupfunctions->getModule($currentId);

			$processData = array();
			$processData['moduledata'] = $moduledata;
			$formvalid = $moduleForm->validate($processData);
		}

		$active = $moduleForm->getElementValue('module_active');
		if ($active) $moduleForm->setElementValue('module_active', 'checked="checked"');

		$formcreated = $moduleForm->insert($tpl);

		$tpl->assign('module_id:value', $currentId);
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}


	public function activatemodule($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();

		if (empty($parameters['id']))
		{
			$tpl->redirect($tpl->createLink('setup', 'showmodules'));
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->setupfunctions->activateModule($parameters['id']);
		if (!$ret)
		{
			$this->messages->setMessage('Could not activate the module', 'userwarning');
		}
		else
		{
			$this->messages->setMessage('Module activated', 'usermessage');
		}
		
		$tpl->redirect($tpl->createLink('setup', 'showmodules'));
		
		$this->debug->unguard(true);
		return true;
	}


	public function deactivatemodule($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();

		if (empty($parameters['id']))
		{
			$tpl->redirect($tpl->createLink('setup', 'showmodules'));
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->setupfunctions->deactivateModule($parameters['id']);
		if (!$ret)
		{
			$this->messages->setMessage('Could not deactivate the module', 'userwarning');
		}
		else
		{
			$this->messages->setMessage('Module deactivated', 'usermessage');
		}
		
		$tpl->redirect($tpl->createLink('setup', 'showmodules'));
		
		$this->debug->unguard(true);
		return true;
	}


	public function showactions($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_showactions'));

		$actiondata = $this->setupfunctions->getAllActions();
		
		foreach ($actiondata as $action)
		{
			$tpl->assignDataset($action);
			$tpl->insertBlock('applicationaction');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function createaction($parameters=array())
	{
		$this->debug->guard();

		if (!empty($parameters['actiondata']['action_requiresuserright'])) $parameters['actiondata']['action_requiresuserright'] = 1;
		if (!empty($parameters['actiondata']['action_module']))
		{
			$currentModuleId = $parameters['actiondata']['action_module'];
		}
		else
		{
			$currentModuleId = 0;
		}

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_createaction'));

		$actionForm = new zgForm();
		$actionForm->load('forms/actiondata.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $actionForm->validate($parameters);

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveAction($parameters['actiondata']);
				if (!$ret)
				{
					$this->messages->setMessage('Could not save action data to database', 'userwarning');
				}
				else
				{
					$this->messages->setMessage('Action was created', 'usermessage');
				}

				$tpl->redirect($tpl->createLink('setup', 'showactions'));
				$this->debug->unguard(true);
				return true;
			}

			$active = $actionForm->getElementValue('action_requiresuserright');
			if ($active) $actionForm->setElementValue('action_requiresuserright', 'checked="checked"');

			$formcreated = $actionForm->insert($tpl);
		}

		$modules = $this->setupfunctions->getAllModules();
		foreach ($modules as $module)
		{
			$tpl->assign('module_id', $module['module_id']);
			$tpl->assign('module_name', $module['module_name']);

			if ($module['module_id'] != $currentModuleId)
			{
				$tpl->assign('module_isactive', '');
			}
			else
			{
				$tpl->assign('module_isactive', 'selected="selected"');
			}
			

			$tpl->insertBlock('module_loop');
		}

		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}


	public function editaction($parameters=array())
	{
		$this->debug->guard();

		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['actiondata']['action_id'])) $currentId = $parameters['actiondata']['action_id'];
		if (!empty($parameters['actiondata']['action_requiresuserright'])) $parameters['actiondata']['action_requiresuserright'] = 1;

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_editaction'));

		$actionForm = new zgForm();
		$actionForm->load('forms/actiondata.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $actionForm->validate($parameters);
			$currentModuleId = $parameters['actiondata']['action_module'];

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveAction($parameters['actiondata']);
				if (!$ret)
				{
					$this->messages->setMessage('Could not save action data to database', 'userwarning');
				}
				else
				{
					$this->messages->setMessage('Action data was changed', 'usermessage');
				}

				$tpl->redirect($tpl->createLink('setup', 'showactions'));
				$this->debug->unguard(true);
				return true;
			}
		}
		else
		{
			$actiondata = $this->setupfunctions->getAction($currentId);
			$currentModuleId = $actiondata['action_module'];

			$processData = array();
			$processData['actiondata'] = $actiondata;
			$formvalid = $actionForm->validate($processData);
		}

		$active = $actionForm->getElementValue('action_requiresuserright');
		if ($active) $actionForm->setElementValue('action_requiresuserright', 'checked="checked"');

		$formcreated = $actionForm->insert($tpl);

		$modules = $this->setupfunctions->getAllModules();
		foreach ($modules as $module)
		{
			$tpl->assign('module_id', $module['module_id']);
			$tpl->assign('module_name', $module['module_name']);

			if ($module['module_id'] != $currentModuleId)
			{
				$tpl->assign('module_isactive', '');
			}
			else
			{
				$tpl->assign('module_isactive', 'selected="selected"');
			}
			

			$tpl->insertBlock('module_loop');
		}

		$tpl->assign('action_id:value', $currentId);
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}


	public function deleteaction($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();

		if (empty($parameters['id']))
		{
			$tpl->redirect($tpl->createLink('setup', 'showactions'));
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->setupfunctions->deleteAction($parameters['id']);

		if (!$ret)
		{
			$this->messages->setMessage('Could not delete action, it\'s is still available', 'userwarning');
		}
		else
		{
			$this->messages->setMessage('Action deleted', 'usermessage');
		}
		
		$tpl->redirect($tpl->createLink('setup', 'showactions'));
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function showuserroles($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_showuserroles'));

		$roledata = $this->setupfunctions->getAllUserroles();
		
		foreach ($roledata as $userrole)
		{
			$tpl->assignDataset($userrole);
			$tpl->insertBlock('applicationuserrole');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}	


	public function createuserrole($parameters=array())
	{
		$this->debug->guard();

		$currentId = 1;

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_createuserrole'));

		$userroleForm = new zgForm();
		$userroleForm->load('forms/userroledata.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $userroleForm->validate($parameters);
			if (empty($parameters['userroleactions'])) $parameters['userroleactions'] = array();

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveuserrole($parameters['userroledata'], $parameters['userroleactions']);
				if (!$ret)
				{
					$this->messages->setMessage('Could not save userrole to database', 'userwarning');
				}
				else
				{
					$this->messages->setMessage('Userrole was created', 'usermessage');
				}

				$tpl->redirect($tpl->createLink('setup', 'showuserroles'));
				$this->debug->unguard(true);
				return true;
			}
			else
			{
				$actions = $this->setupfunctions->getAllActions();
				$userroleactions = $parameters['userroleactions'];

				foreach ($actions as $action)
				{
					$tpl->assign('action_id', $action['action_id']);
					$tpl->assign('action_name', $action['action_name']);
					$tpl->assign('module_name', $action['module_name']);
					
					if (array_key_exists($action['action_id'], $userroleactions))
					{
						$tpl->assign('action_active', 'checked="checked"');
					}
					else
					{
						$tpl->assign('action_active', '');
					}

					$tpl->insertBlock('userrole_action');
				}
			}

			$formcreated = $userroleForm->insert($tpl);
		}
		else
		{
			$actions = $this->setupfunctions->getAllActions();
	
			foreach ($actions as $action)
			{
				$tpl->assign('action_id', $action['action_id']);
				$tpl->assign('action_name', $action['action_name']);
				$tpl->assign('module_name', $action['module_name']);
				
				$tpl->insertBlock('userrole_action');
			}
		}

		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}


	public function edituserrole($parameters=array())
	{
		$this->debug->guard();

		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['userroledata']['userrole_id'])) $currentId = $parameters['userroledata']['userrole_id'];

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_edituserrole'));

		$userroleForm = new zgForm();
		$userroleForm->load('forms/userroledata.form.ini');

		if (!empty($parameters['submit']))
		{

			$formvalid = $userroleForm->validate($parameters);
			if (empty($parameters['userroleactions'])) $parameters['userroleactions'] = array();

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveuserrole($parameters['userroledata'], $parameters['userroleactions']);
				if (!$ret)
				{
					$this->messages->setMessage('Could not save userrole to database', 'userwarning');
				}
				else
				{
					$this->messages->setMessage('userrole was changed', 'usermessage');
				}

				$tpl->redirect($tpl->createLink('setup', 'showuserroles'));
				$this->debug->unguard(true);
				return true;
			}
			else
			{
				$actions = $this->setupfunctions->getAllActions();
				$userroleactions = $parameters['userroleactions'];

				foreach ($actions as $action)
				{
					$tpl->assign('action_id', $action['action_id']);
					$tpl->assign('action_name', $action['action_name']);
					$tpl->assign('module_name', $action['module_name']);
					
					if (array_key_exists($action['action_id'], $userroleactions))
					{
						$tpl->assign('action_active', 'checked="checked"');
					}
					else
					{
						$tpl->assign('action_active', '');
					}

					$tpl->insertBlock('userrole_action');
				}
			}
		}
		else
		{
			$userroledata = $this->setupfunctions->getuserrole($currentId);

			$processData = array();
			$processData['userroledata'] = $userroledata;
			$formvalid = $userroleForm->validate($processData);

			$actions = $this->setupfunctions->getAllActions();
			$userroleactions = $this->setupfunctions->getUserroleActions($currentId);

			foreach ($actions as $action)
			{
				$tpl->assign('action_id', $action['action_id']);
				$tpl->assign('action_name', $action['action_name']);
				$tpl->assign('module_name', $action['module_name']);
				
				if (in_array($action['action_id'], $userroleactions))
				{
					$tpl->assign('action_active', 'checked="checked"');
				}
				else
				{
					$tpl->assign('action_active', '');
				}

				$tpl->insertBlock('userrole_action');
			}
		}

		$formcreated = $userroleForm->insert($tpl);
		$tpl->assign('userrole_id:value', $currentId);
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}


	public function deleteuserrole($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();

		if (empty($parameters['id']))
		{
			$tpl->redirect($tpl->createLink('setup', 'showuserroles'));
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->setupfunctions->deleteUserrole($parameters['id']);

		if (!$ret)
		{
			$this->messages->setMessage('Could not delete userrole, it\'s is still available', 'userwarning');
		}
		else
		{
			$this->messages->setMessage('Userrole deleted', 'usermessage');
		}
		
		$tpl->redirect($tpl->createLink('setup', 'showuserroles'));
		
		$this->debug->unguard(true);
		return true;
	}


	public function showuserdata($parameters=array())
	{
		$this->debug->guard();

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_showuserdata'));

		$userfunctions = new zgaUserfunctions();
		$userdata = $userfunctions->getUserdataDefinition();
		foreach ($userdata as $field)
		{
			if ( ($field['Field'] == 'userdata_id') || ($field['Field'] == 'userdata_user') ) continue;
			$tpl->assign('userdata_field', $field['Field']);
			$type = split('\(',$field['Type']);
			$tpl->assign('userdata_type', $type[0]);
			if (!empty($type[1])) $tpl->assign('userdata_length', substr($type[1], 0, -1));
				else $tpl->assign('userdata_length', '');
			$tpl->insertBlock('userdata');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}	


	public function createuserdata($parameters=array())
	{
		$this->debug->guard();

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_createuserdata'));

		$userdataForm = new zgForm();
		$userdataForm->load('forms/userdata.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $userdataForm->validate($parameters);

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveUserdata($parameters['userdata']);
				if (!$ret)
				{
					$this->messages->setMessage('Could not save userddata field to database', 'userwarning');
				}
				else
				{
					$this->messages->setMessage('Userdata field was created', 'usermessage');
				}

				$tpl->redirect($tpl->createLink('setup', 'showuserdata'));
				$this->debug->unguard(true);
				return true;
			}

			$formcreated = $userdataForm->insert($tpl);
		}

		$types = array('text', 'varchar', 'int', 'tinyint', 'timestamp');
		foreach ($types as $type)
		{
			$tpl->assign('type_name', $type);
			$tpl->insertBlock('types_loop');
		}

		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}


	public function edituserdata($parameters=array())
	{
		$this->debug->guard();

		$currentField = '';
		if (!empty($parameters['id'])) $currentField = $parameters['id'];
		if (!empty($parameters['userdata']['field_oldname'])) $currentField = $parameters['userdata']['userfield_oldnamerole_id'];

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_edituserdata'));

		$userdataForm = new zgForm();
		$userdataForm->load('forms/userdata.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $userdataForm->validate($parameters);

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveUserdata($parameters['userdata']);
				if (!$ret)
				{
					$this->messages->setMessage('Could not save userddata field to database', 'userwarning');
				}
				else
				{
					$this->messages->setMessage('Userdata field was created', 'usermessage');
				}

				$tpl->redirect($tpl->createLink('setup', 'showuserdata'));
				$this->debug->unguard(true);
				return true;
			}
		}
		else
		{
			$userfunctions = new zgaUserfunctions();
			$userdata = $userfunctions->getUserdataDefinition();
			$fieldinfo = array();
			foreach ($userdata as $field)
			{
				if ($field['Field'] != $currentField) continue;
				$fieldinfo['field_name'] = substr($field['Field'], 9);
				$type = split('\(',$field['Type']);
				$fieldinfo['field_type'] = $type[0];
				if (!empty($type[1])) $fieldinfo['field_length'] = substr($type[1], 0, -1);
					else $fieldinfo['field_length'] = '';
			}

			$parameters['userdata'] = $fieldinfo;
			$formvalid = $userdataForm->validate($parameters);
		}

		$formcreated = $userdataForm->insert($tpl);

		$tpl->assign('field_oldname', $currentField);

		$types = array('text', 'varchar', 'int', 'tinyint', 'timestamp');
		foreach ($types as $type)
		{
			$tpl->assign('type_name', $type);
			if ($parameters['userdata']['field_type'] == $type)
			{
				$tpl->assign('type_isactive', 'selected="selected"');
			}
			else
			{
				$tpl->assign('type_isactive', '');
			}
			$tpl->insertBlock('types_loop');
		}

		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}


	public function deleteuserdata($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();

		if (empty($parameters['id']))
		{
			$tpl->redirect($tpl->createLink('setup', 'showuserdata'));
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->setupfunctions->deleteUserdata($parameters['id']);

		if (!$ret)
		{
			$this->messages->setMessage('Could not delete userdata field, it\'s is still available', 'userwarning');
		}
		else
		{
			$this->messages->setMessage('Userdata field deleted', 'usermessage');
		}
		
		$tpl->redirect($tpl->createLink('setup', 'showuserdata'));
		
		$this->debug->unguard(true);
		return true;
	}

}
?>
