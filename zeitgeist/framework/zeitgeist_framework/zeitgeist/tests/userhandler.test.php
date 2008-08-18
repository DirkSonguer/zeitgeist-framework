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

	function test_establishUserSession()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->establishUserSession();
		$this->assertFalse($testid);

		$_SESSION['user_userid'] = 1;
		$ret = $userhandler->establishUserSession();
		$this->assertFalse($testid);

		unset($_SESSION['user_userid']);
		unset($userhandler);
	}
	
	function test_createUser()
	{
		$userhandler = zgUserhandler::init();
		$this->database->query('TRUNCATE TABLE users');

		$testid = $userhandler->createUser('test', 'test');
		$this->assertTrue($testid);
		$res = $this->database->query("SELECT * FROM users WHERE user_id='" . $testid . "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		
		// TODO: Test userconfirmation entry

		$ret = $userhandler->createUser('test', 'test');
		$this->assertFalse($ret);

		unset($userhandler);
	}

	function test_deleteUser()
	{
		$userhandler = zgUserhandler::init();
		$this->database->query('TRUNCATE TABLE users');

		$testid = $userhandler->createUser('test', 'test');
		$ret = $userhandler->deleteUser($testid);

		$res = $this->database->query("SELECT * FROM users WHERE user_username='test'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		unset($userhandler);
	}

	function test_changePassword()
	{
		$userhandler = zgUserhandler::init();
		$this->database->query('TRUNCATE TABLE users');

		$testid = $userhandler->createUser('test', 'test');
		$ret = $userhandler->changePassword($testid, 'true');
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM users WHERE user_password='" . md5('true') . "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		unset($userhandler);
	}

	function test_changeUsername()
	{
		$userhandler = zgUserhandler::init();
		$this->database->query('TRUNCATE TABLE users');

		$testid = $userhandler->createUser('test', 'test');
		$ret = $userhandler->changeUsername($testid, 'test2');
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM users WHERE user_username='test2'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$testid = $userhandler->createUser('test', 'test');
		$ret = $userhandler->changeUsername($testid, 'test2');
		$this->assertFalse($ret);

		$res = $this->database->query("SELECT * FROM users WHERE user_username LIKE 'test%'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		unset($userhandler);
	}

	function test_activateUser()
	{
		$userhandler = zgUserhandler::init();
		$this->database->query('TRUNCATE TABLE users');

		$testid = $userhandler->createUser('test', 'test');
		$res = $this->database->query("SELECT * FROM users WHERE user_username='test' and user_active='0'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $userhandler->activateUser($testid);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM users WHERE user_username='test' and user_active='1'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $userhandler->activateUser($testid);
		$this->assertFalse($ret);

		unset($userhandler);
	}

	function test_deactivateUser()
	{
		$userhandler = zgUserhandler::init();
		$this->database->query('TRUNCATE TABLE users');

		$testid = $userhandler->createUser('test', 'test');
		$ret = $userhandler->activateUser($testid);
		$res = $this->database->query("SELECT * FROM users WHERE user_username='test' and user_active='1'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $userhandler->deactivateUser($testid);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM users WHERE user_username='test' and user_active='0'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		unset($userhandler);
	}

	function test_checkConfirmation()
	{
		$userhandler = zgUserhandler::init();
		$this->database->query('TRUNCATE TABLE users');
		$this->database->query('TRUNCATE TABLE userconfirmation');

		$testid = $userhandler->createUser('test', 'test');
		$res = $this->database->query("SELECT * FROM userconfirmation WHERE userconfirmation_user='" . $testid . "'");
		$ret = $this->database->fetchArray($res);
		$confirm = $ret['userconfirmation_key'];

		$ret = $userhandler->checkConfirmation('false');
		$this->assertFalse($ret);

		$ret = $userhandler->checkConfirmation($confirm);
		$this->assertEqual($ret, $testid);

		unset($userhandler);
	}

	function test_isLoggedIn()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->isLoggedIn();
		$this->assertFalse($ret);
/*
		$ret = $userhandler->login('test', 'test');
		$ret = $userhandler->isLoggedIn();
		$this->assertTrue($ret);
*/
		unset($userhandler);
	}	

	function test_login()
	{
		$userhandler = zgUserhandler::init();
		$this->database->query('TRUNCATE TABLE users');

		$testid = $userhandler->createUser('test', 'test');
		$ret = $userhandler->activateUser($testid);

		$ret = $userhandler->login('test', 'false');
		$this->assertFalse($ret);
		$ret = $userhandler->login('test', 'test');
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

}

?>
