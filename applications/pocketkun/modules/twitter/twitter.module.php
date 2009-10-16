<?php

defined('POCKETKUN_ACTIVE') or die();

class twitter
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

		$tpl = new pkTemplate();
		$tpl->load($this->configuration->getConfiguration('twitter', 'templates', 'twitter_index'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	// ok
	public function post($parameters=array())
	{
		$this->debug->guard();

		$this->debug->unguard(true);
		return true;
	}


	// ok
	public function show($parameters=array())
	{
		$this->debug->guard();

		$this->debug->unguard(true);
		return true;
	}


}
?>
