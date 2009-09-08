<?php

defined('ZGADMIN_ACTIVE') or die();

class setup
{
	protected $debug;
	protected $messages;
	protected $messagecache;
	protected $database;
	protected $configuration;
	protected $user;	
	protected $setupfunctions;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->messagecache = zgMessagecache::init();
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
			$tpl->insertBlock('applicationmodule');
		}

		$tpl->show();

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
