<?php

defined('ZGEXAMPLES_ACTIVE') or die();

class templates
{
	public $debug;

	public function __construct()
	{
		$this->debug = zgDebug::init();
	}
	

	// this is the main action for this module
	public function index($parameters=array())
	{
		$this->debug->guard();
		
		// create a new object of type template
		$tpl = new zgTemplate();
		
		// load the example template
		// open it and take a look at how it works
		$tpl->load('_additional_files/example_template.tpl.html');

		$tpl->assign('examplecontent', 'Hello, Template!');
		
		$tpl->show();

		$this->debug->unguard(true);		
		return true;
	}

}
?>
