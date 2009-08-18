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
		// notice that "my_type" as type
		$this->messages->setMessage('Hello Messages', 'my_type');

		// get it back again
		// we only ask for messages with type "my_type"
		$messagearray = $this->messages->getMessagesByType('my_type');

		// show raw object we got back
		var_dump($messagearray); echo "<br /><br />";

		// alternatively you could ask for either all messages
		$messagearray = $this->messages->getAllMessages();

		// show raw object we got back
		var_dump($messagearray); echo "<br /><br />";

		// or just the messages sent by this module
		$messagearray = $this->messages->getAllMessages('messages.module.php');

		// show raw object we got back
		var_dump($messagearray); echo "<br /><br />";

		// as you can see the return value is an array of objects 
		// with type zgMessage
		
		// let's look at a message in detail
		$ourmessage = $messagearray[0];
		
		// this is our message we sent
		echo "Message: ".$ourmessage->message."<br />";

		// the type we chose
		echo "Type: ".$ourmessage->type."<br />";

		// this is the name of the file that sent the message
		echo "From: ".$ourmessage->from."<br />";

		// clear all messgages from cache
		$this->messages->clearAllMessages();
		
		// remember that as the message class is a singleton,
		// the application and framework share the same pool
		// of messages, so they send them to each other
		
		$this->debug->unguard(true);		
		return true;
	}

}
?>
