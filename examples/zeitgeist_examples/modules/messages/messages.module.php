<?php

defined('ZGEXAMPLES_ACTIVE') or die();

class messages
{
	public $debug;
	public $messages;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		
		// The message system is a singleton as we only need one global 
		// message system to hold all messages
		
		// You can bind it by calling the init() method
		$this->messages = zgMessages::init();
	}
	

	// This is the main action for this module
	public function index($parameters=array())
	{
		$this->debug->guard();

		// First clear all messages that might already be in the system		
		$this->messages->clearAllMessages();

		// Enter something into the message system		
		// notice that "my_type" as type
		$this->messages->setMessage('Hello Messages', 'my_type');
		$this->messages->setMessage('How are you today?', 'other_type');

		// Get it back again
		// We only ask for messages with type "my_type"
		// That should be only one of the messages
		$messagearray = $this->messages->getMessagesByType('my_type');

		// Show raw object we got back
		var_dump($messagearray); echo "<br /><br />";

		// Alternatively you could ask for either all messages
		// This should contain all two messages
		$messagearray = $this->messages->getAllMessages();

		// Show raw object we got back
		var_dump($messagearray); echo "<br /><br />";

/*
		// Or just the messages sent by this module
		// This however will check for the absolute name of the file
		// As there is no way to know what this will be for you, just check the
		// "from" field in your message object
		$messagearray = $this->messages->getAllMessages('messages.module.php');

		// Show raw object we got back
		var_dump($messagearray); echo "<br /><br />";
*/

		// As you can see the return value is an array of objects 
		// with type zgMessage
		
		// Let's look at a message in detail
		$ourmessage = $messagearray[0];
		
		// This is our message we sent
		echo "Message: ".$ourmessage->message."<br />";

		// The type we chose
		echo "Type: ".$ourmessage->type."<br />";

		// This is the name of the file that sent the message
		echo "From: ".$ourmessage->from."<br />";

		// Clear all messgages from cache
		$this->messages->clearAllMessages();
		
		// Remember that as the message class is a singleton,
		// the application and framework share the same pool
		// of messages, so they send them to each other
		
		// The message system should only be used to send strings.
		// If you need to share objects between classes, use the
		// object system
		
		$this->debug->unguard(true);		
		return true;
	}

}
?>
