<?php

defined('LINERACER_ACTIVE') or die();

class shop
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


	// ok
	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		
		$tpl->load($this->configuration->getConfiguration('shop', 'templates', 'shop_index'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
