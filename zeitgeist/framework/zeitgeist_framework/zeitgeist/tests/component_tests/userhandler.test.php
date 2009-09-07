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
		
		$userid = $userhandler->getUserId();
		$this->assertEqual($userid, $testid);

		unset($userhandler);
	}

	function test_isLoggedIn_true()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->isLoggedIn();
		$this->assertTrue($ret);

		unset($userhandler);
	}
	
	function test_getUserdata()
	{
		$userhandler = zgUserhandler::init();
		$res = $this->database->query("TRUNCATE TABLE userdata");

		$ret = $userhandler->getUserdata();
		$this->assertEqual(count($ret), 12);

		unset($userhandler);		
	}

	function test_setUserdata()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->setUserdata('userdata_firstname', 'Mr', false);
		$this->assertTrue($ret);

		$ret = $userhandler->setUserdata('userdata_lastname', 'Test');
		$this->assertTrue($ret);

		$ret = $userhandler->getUserdata();
		$this->assertEqual(count($ret), 12);
		$this->assertEqual($ret['userdata_firstname'], 'Mr');
		$this->assertEqual($ret['userdata_lastname'], 'Test');

		unset($userhandler);		
	}
	
	function test_hasUserright()
	{
		$userhandler = zgUserhandler::init();
		
		$userid = $userhandler->getUserId();
		$res = $this->database->query("TRUNCATE TABLE userrights");
		$res = $this->database->query("TRUNCATE TABLE userroles_to_users");
		$res = $this->database->query("TRUNCATE TABLE userroles_to_actions");
		$this->database->query("INSERT INTO userrights(userright_user, userright_action) VALUES('" . $userid . "', '2')");
		$this->database->query("INSERT INTO userrights(userright_user, userright_action) VALUES('" . $userid . "', '6')");
		$this->database->query("INSERT INTO userroles_to_users(userroleuser_user, userroleuser_userrole) VALUES('" . $userid . "', '1')");
		$this->database->query("INSERT INTO userroles_to_actions(userroleaction_userrole, userroleaction_action) VALUES('1', '3')");
		$this->database->query("INSERT INTO userroles_to_actions(userroleaction_userrole, userroleaction_action) VALUES('1', '7')");

		$ret = $userhandler->hasUserright('0');
		$this->assertFalse($ret);
		$ret = $userhandler->hasUserright('2');
		$this->assertTrue($ret);
		$ret = $userhandler->hasUserright('3');
		$this->assertTrue($ret);

		unset($userhandler);
	}

	function test_addUserright()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->addUserright('1');
		$this->assertTrue($ret);

		$ret = $userhandler->hasUserright('1');
		$this->assertTrue($ret);

		unset($userhandler);		
	}

	function test_deleteUserright()
	{
		$userhandler = zgUserhandler::init();

		$ret = $userhandler->hasUserright('2');
		$this->assertTrue($ret);

		$ret = $userhandler->deleteUserright('2');
		$this->assertTrue($ret);

		$ret = $userhandler->hasUserright('2');
		$this->assertFalse($ret);

		unset($userhandler);		
	}


	function test_hasUserrole()
	{
		$userhandler = zgUserhandler::init();
		
		$userid = $userhandler->getUserId();
		$res = $this->database->query("TRUNCATE TABLE userroles");
		$res = $this->database->query("TRUNCATE TABLE userroles_to_users");
		$this->database->query("INSERT INTO userroles(userrole_name, userrole_description) VALUES('testrole', 'Test')");
		$testrole = $this->database->insertId();
		$this->database->query("INSERT INTO userroles(userrole_name, userrole_description) VALUES('anothertest', 'Test')");
		$anothertest = $this->database->insertId();
		
		$this->database->query("INSERT INTO userroles_to_users(userroleuser_user, userroleuser_userrole) VALUES('" . $userid . "', '" . $testrole . "')");

		$ret = $userhandler->hasUserrole('false');
		$this->assertFalse($ret);
		$ret = $userhandler->hasUserrole('testrole');
		$this->assertTrue($ret);
		$ret = $userhandler->hasUserrole('anothertest');
		$this->assertFalse($ret);
		$ret = $userhandler->hasUserrole(true);
		$this->assertFalse($ret);

		unset($userhandler);		
	}
}

?>
