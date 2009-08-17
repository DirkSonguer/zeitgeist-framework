<?php


defined('ZGEXAMPLES_ACTIVE') or die();

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
	

	// this is the main action for this module - the home page	
	public function index($parameters=array())
	{
		// simply create a new template, load and show it
		// more on using templates later
		$tpl = new zgTemplate();
		$tpl->load('templates/zgexamples/main_index.tpl.html');		
		$tpl->show();
		
		return true;
	}


	// this is the action for "basics".
	// as you can see the class name matches the module and the method matches the action
	// additionally the class and method has to be defined in the application database
	public function basics($parameters=array())
	{
		// simply create a new template, load and show it
		// more on using templates later
		$tpl = new zgTemplate();
		$tpl->load('templates/zgexamples/main_basics.tpl.html');	
		$tpl->show();
		
		return true;
	}



}
?>
