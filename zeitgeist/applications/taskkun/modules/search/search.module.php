<?php

defined('TASKKUN_ACTIVE') or die();

class search
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
		$tpl->load($this->configuration->getConfiguration('search', 'templates', 'search_index'));
		$tpl->assign('documenttitle', 'Suche in Aufgaben');
		$tpl->assign('helptopic', '&topic=search');

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
