<?php

defined('ZGEXAMPLES_ACTIVE') or die();

class debug
{
	public $debug;

	public function __construct()
	{
		// the message system is a singleton as we only need one global 
		// message syste to hold all messages

		// you can bind it by calling the init() method
		$this->debug = zgDebug::init();
	}
	

	// this is the main action for this module
	public function index($parameters=array())
	{
		// the guarding method is part of the function guarding / tracing
		// mechanism. it automatically adds the function to the log with 
		// all parameters it was called with
		
		// you should use guarding with all methods, functions etc to get
		// a complete trace of your application
		$this->debug->guard();
		
		// send a simple debug message
		// this will be shown in the normal debug message log
		// notice "my_type" as type
		$this->debug->write('Hello Debug', 'my_type');

		// there are several kinds of types, which define the style of
		// a debug message in the log
		// special types defined per default are:
		$this->debug->write('Hello Message', 'message');
		$this->debug->write('Hello Warning', 'warning');
		$this->debug->write('Hello Error', 'error');
		
		// the style of each individual type is defined via css in
		// zeitgeist/configuration/debug.css

		// note that you have to define "DEBUGMODE" _before_ you load the
		// framework, otherwise debugging will be deactivated for performance
		// reasons
		
		// to show the debug messages, place the following code at the end of
		// your application:
		/*
			$debug->loadStylesheet('debug.css');
			$debug->showInnerLoops = true;
			$debug->showMiscInformation();
			$debug->showDebugMessages();
			$debug->showQueryMessages();
			$debug->showGuardMessages();
		*/

		// all database calls through the db abstraction class will be logged
		// automatically
		$database = new zgDatabase();
		$database->connect();
		
		// normal query
		$database->query("SELECT 'Hello Database'");

		// errors will be highlighted
		$database->query("'Hello Database Error'");

		$this->debug->unguard(true);		
		return true;
	}

}
?>
