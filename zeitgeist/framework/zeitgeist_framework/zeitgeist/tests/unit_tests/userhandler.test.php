<?php

class testUserhandler extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$userhandler = zgUserhandler::init();
		$this->assertNotNull($userhandler);
		unset($userhandler);
    }
	
	
	// Try logging in without data
	function test_login_nodata()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->login('', '');
		$this->assertFalse($ret);

		unset($ret);
		unset($userhandler);
	}

	
	// Try logging in with wrong password
	function test_login_wrongpassword()
	{
		$userhandler = zgUserhandler::init();
		$userfunctions = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$userid = $userfunctions->createUser($username, $password);
		$userfunctions->activateUser($userid);

		$ret = $userhandler->login($username, 'false');
		$this->assertFalse($ret);

		unset($ret);
		unset($userhandler);
	}

	
	// Try logging in with wrong user
	function test_login_wronguser()
	{
		$userhandler = zgUserhandler::init();
		$userfunctions = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$userid = $userfunctions->createUser($username, $password);
		$userfunctions->activateUser($userid);

		$ret = $userhandler->login('false', $password);
		$this->assertFalse($ret);

		unset($ret);
		unset($userhandler);
	}

	
	// Log in
	function test_login_success()
	{
		$userhandler = zgUserhandler::init();
		$userfunctions = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$userid = $userfunctions->createUser($username, $password);
		$userfunctions->activateUser($userid);

		$ret = $userhandler->login($username, $password);
		$this->assertTrue($ret);

		unset($ret);
		unset($userhandler);
	}

	
	// Log out user that was logged in in previous test
	function test_logout_success()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->logout();
		$this->assertTrue($ret);

		unset($ret);
		unset($userhandler);
	}


	// Log out user that is not logged in
	function test_logout_notloggedin()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->logout();
		$this->assertFalse($ret);

		unset($ret);
		unset($userhandler);
	}


	// Try getting userid from empty userhandler object
	function test_getUserID_nouser()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->getUserID();
		$this->assertFalse($ret);

		unset($ret);
		unset($userhandler);
	}

	
	// Get user id
	function test_getUserID_success()
	{
		$userhandler = zgUserhandler::init();
		$userfunctions = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$userid = $userfunctions->createUser($username, $password);
		$userfunctions->activateUser($userid);

		$userhandler->login($username, $password);

		$ret = $userhandler->getUserID();
		$this->assertEqual($userid, $ret);

		unset($ret);
		unset($userhandler);
	}


	// Try go get login state from anonymous user
	function test_isLoggedIn_notloggedin()
	{
		$userhandler = zgUserhandler::init();
		$userhandler->logout();

		$ret = $userhandler->isLoggedIn();
		$this->assertFalse($ret);

		unset($ret);
		unset($userhandler);
	}


	// Get login state
	function test_isLoggedIn_loggedin()
	{
		$userhandler = zgUserhandler::init();
		$userfunctions = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$userid = $userfunctions->createUser($username, $password);
		$userfunctions->activateUser($userid);

		$userhandler->login($username, $password);

		$ret = $userhandler->isLoggedIn();
		$this->assertTrue($ret);

		unset($ret);
		unset($userhandler);
	}

}

?>
