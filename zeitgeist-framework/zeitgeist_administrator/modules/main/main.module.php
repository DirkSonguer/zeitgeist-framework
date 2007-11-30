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
		echo "index<br />";
		
		echo "in module main and action index: ";
//		var_dump($parameters);
		
		return true;
	}
	
	
	public function login($parameters=array())
	{
		echo "index<br />";
		
		echo "in module main and action login: ";
//		var_dump($parameters);
		
		return true;
	}



}
?>
