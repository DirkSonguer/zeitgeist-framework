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

	function test_createUser()
	{
		$userhandler = zgUserhandler::init();
		$this->database->query('TRUNCATE TABLE users');

		$ret = $userhandler->createUser('test', 'test');
		$this->assertTrue($ret);
		$res = $this->database->query("SELECT * FROM users WHERE user_username='test'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
/*
		$ret = $userhandler->createUser('test', 'test', '2');
		$this->assertTrue($ret);
		$res = $this->database->query("SELECT * FROM users WHERE user_username='test'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
 */
		$this->database->query('TRUNCATE TABLE users');
		unset($userhandler);
	}

	
}

?>
