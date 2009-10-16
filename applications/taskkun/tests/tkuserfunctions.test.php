<?php

class testTkuserfunctions extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();

		$userfunctions = new tkUserfunctions();
		$this->assertNotNull($userfunctions);
		unset($userfunctions);
    }

	function test_getUserInstance()
	{
		$userfunctions = new tkUserfunctions();
		$user = zgUserhandler::init();

		$this->database->query('TRUNCATE TABLE users');
		$ret = $this->database->query("INSERT INTO users(user_username, user_password, user_active, user_instance) VALUES('test', '" . md5('test') . "', '2', '5')");
		$userid = $this->database->insertId();

		$ret = $userfunctions->getUserInstance(-1);
		$this->assertFalse($ret);

		$ret = $userfunctions->getUserInstance($userid);
		$this->assertEqual($ret, 5);

		$_SESSION['user_userid'] = '9';
		$_SESSION['user_key'] = '1';
		$_SESSION['user_username'] = 'testuser';
		$_SESSION['user_instance'] = '2';
		$user->setLoginStatus(true);
		$ret = $userfunctions->getUserInstance(9);
		$this->assertEqual($ret, 2);

		unset($userfunctions);
    }

}

?>
