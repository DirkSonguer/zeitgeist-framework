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
/*
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
*/
}

?>
