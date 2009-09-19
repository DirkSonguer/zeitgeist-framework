<?php

defined('ZGEXAMPLES_ACTIVE') or die();

class userhandler
{
	public $debug;

	public function __construct()
	{
		$this->debug = zgDebug::init();
	}
	
	// This is the main action for this module
	public function index($parameters=array())
	{
		$this->debug->guard();

		// This is just an overview of what the userhandler class can do
		
		// There are several user classes: userfunctions, userrights, userroles
		// and userdata are worker classes. They contain the methods to handle
		// a generic user. The userhandler class however only handles one specific
		// user - the active user of a Zeitgeist application.
		
		// Almost all user functions should have test cases. So for more
		// detailed examples, see /zeitgeist/tests/user*.test.php
		
		// First, get an object for the current user
		// Notice that this class is a singleton as all classes and methods
		// should have access to the same user.
		$user = zgUserhandler::init();

		// establishUserSession checks if the current user has an active session
		if (!$user->establishUserSession())
		{
			// User not logged in
			echo "User not logged in<br />";

			// First we have to create a user to use one. To do this we'll use the
			// generic user methods.
			$userfunctions = new zgUserfunctions();
			
			// As there are no users in the database, let's create one with
			// name "test" and password "test".
			$userid = $userfunctions->createUser('test', 'test');
			echo "User created with id: ".$userid."<br />";
	
			// If double opt in is activated, the user has to be activated
			// before he can log in.
			if ($userfunctions->activateUser($userid))
			{
				echo "User with id ".$userid." has been activated<br />";
			}
	
			// As we have a user now, we can log in. We'll do this with the
			// object of the current user
			if ($user->login('test', 'test'))
			{
				echo "User with id ".$userid." is logged in<br />";
			}
	
			// Verify that the user is logged in
			if ($user->isLoggedIn())
			{
				echo "User with id ".$userid." is really logged in<br />";
			}
		}
		else
		{
			// User is in
			echo "User is logged in<br />";
			
			// Get the user id
			$userid = $user->getUserID();
			echo "User has the id ".$userid."<br />";

			// Log the user out again
			if ($user->logout())
			{
				echo "User with id ".$userid." is logged out<br />";
			}
			
			// clean up afterwards		
			$database = new zgDatabase();
			$database->connect();
			$database->query('TRUNCATE TABLE users');
		}

		echo '<a href="./index.php?module=userhandler">Reload page</a><br />';

		$this->debug->unguard(true);		
		return true;
	}

}
?>
