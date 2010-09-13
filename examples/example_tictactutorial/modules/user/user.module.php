<?php

defined('TICTACTUTORIAL_ACTIVE') or die();

class user
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function index($parameters=array())
	{
		$this->debug->guard();

		$this->debug->unguard(true);
		return true;
	}


	public function login($parameters=array())
	{
		$this->debug->guard();

		$tpl = new zgTemplate();
		$tpl->load($this->configuration->getConfiguration('application', 'application', 'templatepath') . '/user_login.tpl.html');

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
