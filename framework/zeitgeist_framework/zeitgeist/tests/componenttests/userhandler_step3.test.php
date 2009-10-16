<?php

class testUserhandler_s3 extends UnitTestCase
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

	function test_isLoggedIn_true()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->isLoggedIn();
		$this->assertTrue($ret);

		unset($userhandler);
	}	

	function test_logout()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->logout();
		$this->assertTrue($ret);

		$ret = $userhandler->logout();
		$this->assertFalse($ret);

		unset($userhandler);
	}	

	function test_isLoggedIn_false()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->isLoggedIn();
		$this->assertFalse($ret);

		unset($userhandler);
	}	

}

?>
