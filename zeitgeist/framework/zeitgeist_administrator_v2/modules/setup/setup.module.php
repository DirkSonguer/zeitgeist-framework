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

		$createmoduleForm = new zgStaticform();
		$createmoduleForm->load('forms/createmodule.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $createmoduleForm->process($parameters);

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

			$formcreated = $createmoduleForm->create($tpl);
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

		$editmoduleForm = new zgStaticform();
		$editmoduleForm->load('forms/editmodule.form.ini');

		if (!empty($parameters['submit']))
		{
			$formvalid = $editmoduleForm->process($parameters);

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
			
			$this->debug->write($moduledata,"error");

			$processData = array();
			$processData['editmodule'] = $moduledata;
			$formvalid = $editmoduleForm->process($processData);
		}

		$formcreated = $editmoduleForm->create($tpl);

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
	
}
?>
