<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../test_configuration.php');

class testUserfunctions extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$user = new zgUserfunctions();
		$this->assertNotNull($user);
		unset($user);
    }


	// Try to create a user without data
	function test_createUser_without_data()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$newuserid = $user->createUser('', '');
		$this->assertFalse($newuserid);
		
		// check database		
		$res = $this->database->query("SELECT * FROM users WHERE user_id='" . $newuserid . "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);
		
		unset($ret);
		unset($user);
	}


	// Create a valid user
	function test_createUser_success()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$this->assertTrue($newuserid);
		
		// check database for valid user entry
		$res = $this->database->query("SELECT * FROM users WHERE user_id='" . $newuserid . "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		unset($ret);
		unset($user);
	}
	
	
	// Create a user twice
	function test_createUser_twice()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$this->assertTrue($newuserid);

		$ret = $user->createUser($username, $password);
		$this->assertFalse($ret);
		
		// check database for valid user entry
		$res = $this->database->query("SELECT * FROM users WHERE user_id='" . $newuserid . "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		unset($ret);
		unset($user);
	}


	// Try to log in with empty data
	function test_login_nodata()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');
		
		$ret = $user->login('', '');
		$this->assertFalse($ret);

		unset($ret);
		unset($user);
	}
	
	
	// Try login with existing user and wrong password
	function test_login_wrongpassword()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$user->activateUser($newuserid);

		$ret = $user->login($username, $password.'1');
		$this->assertFalse($ret);

		unset($ret);
		unset($user);
	}

	
	// Login with existing user and matching password
	function test_login_success()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$user->activateUser($newuserid);

		$ret = $user->login($username, $password);
		$this->assertTrue($ret);
		$this->assertEqual($ret['user_id'], $newuserid);

		unset($ret);
		unset($user);
	}


	// Deletes a user that does not exist
	function test_deleteUser_nodata()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);

		$ret = $user->deleteUser(($newuserid+1));
		$this->assertTrue($ret);

		// The delete should leave the existing user alone
		$res = $this->database->query("SELECT * FROM users WHERE user_id='" . $newuserid . "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		unset($ret);
		unset($user);
	}


	// Deletes an existing user
	function test_deleteUser_success()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$ret = $user->deleteUser($newuserid);

		$res = $this->database->query("SELECT * FROM users WHERE user_id='" . $newuserid . "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		unset($ret);
		unset($user);
	}


	// Try to change the password of a user withou any data
	function test_changePassword_nodata()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$ret = $user->changePassword($newuserid, '');
		$this->assertFalse($ret);

		$res = $this->database->query("SELECT * FROM users WHERE user_password='" . md5($password) . "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		unset($ret);
		unset($user);
	}


	// Change the password of a user
	function test_changePassword_success()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$ret = $user->changePassword($newuserid, $password.'1');
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM users WHERE user_password='" . md5($password.'1') . "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		unset($ret);
		unset($user);
	}


	// Try to change username without data
	function test_changeUsername_nodata()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$ret = $user->changeUsername($newuserid, '');
		$this->assertFalse($ret);

		$res = $this->database->query("SELECT * FROM users WHERE user_username='" . $username . "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		unset($ret);
		unset($user);
	}
	
	
	// Try changing username to already existing one
	function test_changeUsername_existingusername()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$this->assertTrue($newuserid);

		$newuserid = $user->createUser($username.'1', $password.'1');
		$this->assertTrue($newuserid);

		$ret = $user->changeUsername($newuserid, $username);
		$this->assertFalse($ret);

		$res = $this->database->query("SELECT * FROM users WHERE user_username='" . $username . "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		unset($ret);
		unset($user);
	}


	// Change username	
	function test_changeUsername_success()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$ret = $user->changeUsername($newuserid, $username.'1');
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM users WHERE user_username='" . ($username.'1') . "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		unset($ret);
		unset($user);
	}


	// Get confirmation key for nonexistant user
	function test_getConfirmationKey_invaliduser()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');
		$this->database->query('TRUNCATE TABLE userconfirmation');

		$ret = $user->getConfirmationKey(1);
		$this->assertFalse($ret);

		unset($ret);
		unset($user);
	}
	

	// Get confirmation key
	function test_getConfirmationKey_success()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');
		$this->database->query('TRUNCATE TABLE userconfirmation');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$confirmationkey = $user->getConfirmationKey($newuserid);
		$this->assertTrue($confirmationkey);

		$res = $this->database->query("SELECT * FROM userconfirmation WHERE userconfirmation_user='" . $newuserid . "'");
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['userconfirmation_key'], $confirmationkey);

		unset($ret);
		unset($user);
	}


	// Try confirmation for nonexistant key
	function test_checkConfirmation_invalidkey()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');
		$this->database->query('TRUNCATE TABLE userconfirmation');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$confirmationkey = $user->getConfirmationKey($newuserid);
		
		$ret = $user->checkConfirmation($confirmationkey.'1');
		$this->assertFalse($ret);

		unset($ret);
		unset($user);
	}


	// Check confirmation key
	function test_checkConfirmation_success()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');
		$this->database->query('TRUNCATE TABLE userconfirmation');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$confirmationkey = $user->getConfirmationKey($newuserid);
		
		$ret = $user->checkConfirmation($confirmationkey);
		$this->assertEqual($ret, $newuserid);

		unset($ret);
		unset($user);
	}


	// try activating nonexistant user
	function test_activateUser_invaliduser()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$ret = $user->activateUser(1);
		$this->assertTrue($ret);

		unset($ret);
		unset($userhandler);
	}
	
	
	// Activate user
	function test_activateUser_success()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');
		$this->database->query('TRUNCATE TABLE userconfirmation');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);

		$ret = $user->activateUser($newuserid);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM users WHERE user_username='" . $username . "' and user_active='1'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$res = $this->database->query("SELECT * FROM userconfirmation");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		unset($ret);
		unset($userhandler);
	}
	

	// try deactivating nonexistant user
	function test_deactivateUser_invaliduser()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');

		$ret = $user->deactivateUser(1);
		$this->assertTrue($ret);

		unset($ret);
		unset($userhandler);
	}
	
	
	// Deactivate user
	function test_deactivateUser_success()
	{
		$user = new zgUserfunctions();
		$this->database->query('TRUNCATE TABLE users');
		$this->database->query('TRUNCATE TABLE userconfirmation');

		$username = uniqid();
		$password = uniqid();
		$newuserid = $user->createUser($username, $password);
		$user->activateUser($newuserid);

		$ret = $user->deactivateUser($newuserid);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM users WHERE user_username='" . $username . "' and user_active='0'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$res = $this->database->query("SELECT * FROM userconfirmation");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		unset($ret);
		unset($userhandler);
	}

}

?>
