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

		if (!empty($parameters['createmodule']['module_active'])) $parameters['createmodule']['module_active'] = 1;

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_createmodule'));

		$createmoduleForm = new zgForm();
		$createmoduleForm->load('forms/createmodule.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $createmoduleForm->validate($parameters);

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveModule($parameters['createmodule']);
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

			$active = $createmoduleForm->getValue('module_active');
			if ($active) $createmoduleForm->setValue('module_active', 'checked="checked"');

			$formcreated = $createmoduleForm->insert($tpl);
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
		if (!empty($parameters['editmodule']['module_id'])) $currentId = $parameters['editmodule']['module_id'];
		if (!empty($parameters['editmodule']['module_active'])) $parameters['editmodule']['module_active'] = 1;

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_editmodule'));

		$editmoduleForm = new zgForm();
		$editmoduleForm->load('forms/editmodule.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $editmoduleForm->validate($parameters);

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveModule($parameters['editmodule']);
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
			$processData['editmodule'] = $moduledata;
			$formvalid = $editmoduleForm->validate($processData);
		}

		$active = $editmoduleForm->getValue('module_active');
		if ($active) $editmoduleForm->setValue('module_active', 'checked="checked"');

		$formcreated = $editmoduleForm->insert($tpl);

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

		if (!empty($parameters['createaction']['action_requiresuserright'])) $parameters['createaction']['action_requiresuserright'] = 1;
		if (!empty($parameters['createaction']['action_module']))
		{
			$currentModuleId = $parameters['createaction']['action_module'];
		}
		else
		{
			$currentModuleId = 0;
		}

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_createaction'));

		$createactionForm = new zgStaticform();
		$createactionForm->load('forms/createaction.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $createactionForm->process($parameters);

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveAction($parameters['createaction']);
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

			$formcreated = $createactionForm->create($tpl);
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
		if (!empty($parameters['editaction']['action_id'])) $currentId = $parameters['editaction']['action_id'];
		if (!empty($parameters['editaction']['action_requiresuserright'])) $parameters['editaction']['action_requiresuserright'] = 1;

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_editaction'));

		$editactionForm = new zgForm();
		$editactionForm->load('forms/editaction.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $editactionForm->validate($parameters);
			$currentModuleId = $parameters['editaction']['action_module'];

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveAction($parameters['editaction']);
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
			$processData['editaction'] = $actiondata;
			$formvalid = $editactionForm->insert($processData);
		}

		$formcreated = $editactionForm->create($tpl);

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

		$edituserroleForm = new zgStaticform();
		$edituserroleForm->load('forms/createuserrole.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $edituserroleForm->process($parameters);

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveuserrole($parameters['createuserrole'], $parameters['userroleactions']);
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

			$formcreated = $edituserroleForm->create($tpl);
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
		if (!empty($parameters['edituserrole']['userrole_id'])) $currentId = $parameters['edituserrole']['userrole_id'];

		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_edituserrole'));

		$edituserroleForm = new zgStaticform();
		$edituserroleForm->load('forms/edituserrole.form.ini');

		if (!empty($parameters['submit']))
		{

			$formvalid = $edituserroleForm->process($parameters);

			if ($formvalid)
			{
				$ret = $this->setupfunctions->saveuserrole($parameters['edituserrole'], $parameters['userroleactions']);
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
			$processData['edituserrole'] = $userroledata;
			$formvalid = $edituserroleForm->process($processData);

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

		$formcreated = $edituserroleForm->create($tpl);
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
	
	
}
?>
