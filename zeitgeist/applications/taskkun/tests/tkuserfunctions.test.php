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

		$this->database->query('TRUNCATE TABLE users');
		$ret = $this->database->query("INSERT INTO users(user_username, user_password, user_active, user_instance) VALUES('test', '" . md5('test') . "', '2', '5')");
		$userid = $this->database->insertId();

		$ret = $userfunctions->getUserInstance(-1);
		$this->assertFalse($ret);

		$ret = $userfunctions->getUserInstance(1);
		$this->assertEqual($ret, 2);

		$ret = $userfunctions->getUserInstance(2);
		$this->assertEqual($ret, 5);

		unset($userfunctions);
    }

}

?>
