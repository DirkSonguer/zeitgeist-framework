<?php


defined('ZGADMIN_ACTIVE') or die();

class main
{
	public $debug;
	public $messages;
	public $database;
	public $configuration;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();
	}
	
	
	public function index($parameters=array())
	{
		$tpl = new adminTemplate();
		$tpl->load('templates/admin/login.tpl.html');
		
		$tpl->show();
		
		return true;
	}
	
	
	public function login($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new adminTemplate();
		$tpl->load('templates/admin/login.tpl.html');
		
		$tpl->show();
				
		$this->debug->unguard(true);
		return true;
	}



}
?>
