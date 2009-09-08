<?php

defined('ZGADMIN_ACTIVE') or die();

class users
{
	protected $debug;
	protected $messages;
	protected $messagecache;
	protected $database;
	protected $configuration;
	protected $user;
	protected $userfunctions;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->messagecache = zgMessagecache::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		
		$this->userfunctions = new zgaUserfunctions();

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
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
