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
	protected $setupfunctions;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		
		$this->userfunctions = new zgaUserfunctions();
		$this->setupfunctions = new zgaSetupfunctions();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function index($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_index'));
		
		$userdata = $this->userfunctions->getAllUsers();
		
		foreach ($userdata as $user)
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
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_edituser'));
/*
		$userdata = $this->userfunctions->getAllUsers();
		
		foreach ($userdata as $user)
		{
			$tpl->assignDataset($user);
			$tpl->insertBlock('applicationuser');
		}
*/

		// list userroles for user
		$userroles = $this->setupfunctions->getAllUserroles();
//		$userroleactions = $parameters['userroleactions'];

		foreach ($userroles as $userrole)
		{
			$tpl->assign('userrole_id', $userrole['userrole_id']);
			$tpl->assign('userrole_name', $userrole['userrole_name']);
			$tpl->assign('userrole_description', $userrole['userrole_description']);
			$tpl->assign('userrole_active', '');

/*			
			if (array_key_exists($action['action_id'], $userroleactions))
			{
				$tpl->assign('action_active', 'checked="checked"');
			}
			else
			{
				$tpl->assign('action_active', '');
			}
*/

			$tpl->insertBlock('userroles');
		}

		// list userrights for user
		$actions = $this->setupfunctions->getAllActions();
//		$userroleactions = $parameters['userroleactions'];

		foreach ($actions as $action)
		{
			$tpl->assign('action_id', $action['action_id']);
			$tpl->assign('action_name', $action['action_name']);
			$tpl->assign('module_name', $action['module_name']);
			$tpl->assign('action_active', '');

/*			
			if (array_key_exists($action['action_id'], $userroleactions))
			{
				$tpl->assign('action_active', 'checked="checked"');
			}
			else
			{
				$tpl->assign('action_active', '');
			}
*/

			$tpl->insertBlock('userrights');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
