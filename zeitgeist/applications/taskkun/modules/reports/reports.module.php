<?php

defined('TASKKUN_ACTIVE') or die();

class reports
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

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('reports', 'templates', 'reports_index'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function showactivities($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('reports', 'templates', 'reports_showactivities'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
