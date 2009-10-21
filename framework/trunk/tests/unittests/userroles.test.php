<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');

class testUserroles extends UnitTestCase
{
	public $database;

	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$userroles = new zgUserroles();
		$this->assertNotNull($userroles);
		unset($userroles);
    }


	// Try saving userroles without data
	function test_saveUserroles_nodata()
	{
		$userroles = new zgUserroles();
		$res = $this->database->query("TRUNCATE TABLE userroles_to_users");
		
		$ret = $userroles->saveUserroles('', '');
		$this->assertFalse($ret);
		
		unset($ret);
		unset($userroles);
    }


	// Try saving userroles with empty data
	function test_saveUserroles_emptydata()
	{
		$userroles = new zgUserroles();
		$res = $this->database->query("TRUNCATE TABLE userroles_to_users");

		$testuser = rand(1,100);
		$testroles = array();

		$ret = $userroles->saveUserroles($testuser, $testroles);
		$this->assertFalse($ret);
		
		unset($ret);
		unset($userroles);
    }

	// Save userroles
	function test_saveUserroles_success()
	{
		$userroles = new zgUserroles();
		$res = $this->database->query("TRUNCATE TABLE userroles_to_users");

		$testuser = rand(1,100);
		
		$testrole1_id = rand(1, 50);
		$testrole1_name = uniqid();
		$testrole2_id = rand(50, 100);
		$testrole2_name = uniqid();

		$testroles = array();
		$testroles[$testrole1_id] = $testrole1_name;
		$testroles[$testrole2_id] = $testrole2_name;
		
		$ret = $userroles->saveUserroles($testuser, $testroles);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM userroles_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		unset($ret);
		unset($userroles);
    }
	

	// Try loading userroles for empty user
	function test_loadUserroles_nouser()
	{
		$userroles = new zgUserroles();
		$res = $this->database->query("TRUNCATE TABLE userroles_to_users");

		$ret = $userroles->loadUserroles('');
		$this->assertFalse($ret);
		
		unset($ret);
		unset($userroles);
    }


	// Try loading userroles for nonexistant user
	function test_loadUserroles_nonexistantuser()
	{
		$userroles = new zgUserroles();
		$res = $this->database->query("TRUNCATE TABLE userroles_to_users");

		$ret = $userroles->loadUserroles(1);
		$this->assertFalse($ret);
		
		unset($ret);
		unset($userroles);
    }


	// Load userroles
	function test_loadUserroles_success()
	{
		$userroles = new zgUserroles();
		$res = $this->database->query("TRUNCATE TABLE userroles_to_users");
		$res = $this->database->query("TRUNCATE TABLE userroles");

		$testuser = rand(1,100);
		$testrole1_id = rand(1, 50);
		$testrole1_name = uniqid();
		$testrole1_desc = uniqid();
		$testrole2_id = rand(50, 100);
		$testrole2_name = uniqid();
		$testrole2_desc = uniqid();

		$this->database->query("INSERT INTO userroles(userrole_id, userrole_name, userrole_description) VALUES('" . $testrole1_id . "', '" . $testrole1_name . "', '" . $testrole1_desc . "')");
		$this->database->query("INSERT INTO userroles(userrole_id, userrole_name, userrole_description) VALUES('" . $testrole2_id . "', '" . $testrole2_name . "', '" . $testrole2_desc . "')");

		$testroles = array();
		$testroles[$testrole1_id] = $testrole1_name;
		$testroles[$testrole2_id] = $testrole2_name;
		
		$userroles->saveUserroles($testuser, $testroles);
		$ret = $userroles->loadUserroles($testuser);
		
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret[$testrole1_id], $testrole1_name);
		$this->assertEqual($ret[$testrole2_id], $testrole2_name);

		unset($ret);
		unset($userroles);
    }


	// Try to identify nonexistant userrole
	function test_identifyRole_nodata()
	{
		$userroles = new zgUserroles();
		$res = $this->database->query("TRUNCATE TABLE userroles");

		$ret = $userroles->identifyRole('');
		$this->assertFalse($ret);

		unset($ret);
		unset($userroles);
    }


	// Try to identify invalid userrole
	function test_identifyRole_invaidrole()
	{
		$userroles = new zgUserroles();
		$res = $this->database->query("TRUNCATE TABLE userroles");

		$ret = $userroles->identifyRole('false');
		$this->assertFalse($ret);

		unset($ret);
		unset($userroles);
    }


	// Identify a userrole
	function test_identifyRole_success()
	{
		$userroles = new zgUserroles();
		$res = $this->database->query("TRUNCATE TABLE userroles");

		$testuser = rand(1,100);
		$testrole1_id = rand(1, 50);
		$testrole1_name = uniqid();
		$testrole1_desc = uniqid();

		$this->database->query("INSERT INTO userroles(userrole_id, userrole_name, userrole_description) VALUES('" . $testrole1_id . "', '" . $testrole1_name . "', '" . $testrole1_desc . "')");

		$ret = $userroles->identifyRole($testrole1_name);
		$this->assertTrue($ret);
		$this->assertEqual($ret, $testrole1_id);

		unset($ret);
		unset($userroles);
    }

}

?>
