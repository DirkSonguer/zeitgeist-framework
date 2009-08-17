<?php

defined('ZGEXAMPLES_ACTIVE') or die();

class messages
{
	public $debug;
	public $messages;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		
		// the message system is a singleton as we only need one global 
		// message syste to hold all messages
		
		// you can bind it by calling the init() method
		$this->messages = zgMessages::init();
	}
	

	// this is the main action for this module
	public function index($parameters=array())
	{
		$this->debug->guard();

		// enter something into the message system		
		// notice that we use "my_type" as type
		$this->messages->setMessage('Hello Messages', 'my_type');

		// get it back again
		// we only ask for messages with type "my_type"
		$messagearray = $this->messages->getMessagesByType('my_type');

		// show raw object we get back
		var_dump($messagearray); echo "<br /><br />";

		// as you can see the return value is an array of objects 
		// with type zgMessage
		// here we have the first (and only) message
		$ourmessage = $messagearray[0];
		
		// this is our message we sent
		echo "Message: ".$ourmessage->message."<br />";

		// the type we chose
		echo "Type: ".$ourmessage->type."<br />";
		echo "Message: ".$ourmessage->message."<br />";



		
		return true;
		$this->debug->unguard(true);		
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
