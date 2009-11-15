<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');

class testUserdata extends UnitTestCase
{
	public $database;

	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$userdata = new zgUserdata();
		$this->assertNotNull($userdata);
		unset($userdata);
    }


	// Try to save userdata without any data
	function test_saveUserdata_nodata()
	{
		$userdata = new zgUserdata();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userdata');
		
		$ret = $userdata->saveUserdata('', '');
		$this->assertFalse($ret);

		$testfunctions->dropZeitgeistTable('userdata');
		unset($ret);
		unset($userdata);
    }


	// Try to save userdata without valid data
	function test_saveUserdata_withoutdata()
	{
		$userdata = new zgUserdata();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userdata');
		
		$testuser = rand(100,200);
		$testdata = array();
		
		$ret = $userdata->saveUserdata($testuser, $testdata);
		$this->assertFalse($ret);

		$testfunctions->dropZeitgeistTable('userdata');
		unset($ret);
		unset($userdata);
    }


	// Try to save userdata without a database
	function test_saveUserdata_withoutdatabase()
	{
		$userdata = new zgUserdata();
		
		$testuser = rand(100,200);
		$testdata = array();
		
		$ret = $userdata->saveUserdata($testuser, $testdata);
		$this->assertFalse($ret);

		unset($ret);
		unset($userdata);
    }


	// Save userdata
	function test_saveUserdata_success()
	{
		$userdata = new zgUserdata();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userdata');
		
		$testuser = rand(100,200);
		$testdata = array();
		$testdata['userdata_username'] = uniqid();
		
		$ret = $userdata->saveUserdata($testuser, $testdata);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM userdata");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['userdata_username'], $testdata['userdata_username']);

		$testfunctions->dropZeitgeistTable('userdata');
		unset($ret);
		unset($userdata);
    }	


	// Try to load userdata from empty user
	function test_loadUserdata_emptyuser()
	{
		$userdata = new zgUserdata();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userdata');

		$ret = $userdata->loadUserdata('');
		$this->assertTrue($ret);
		$this->assertEqual(count($ret), 5);

		$testfunctions->dropZeitgeistTable('userdata');
		unset($ret);
		unset($userdata);
    }	


	// Try to load userdata from non existant user
	function test_loadUserdata_nonexistantuser()
	{
		$userdata = new zgUserdata();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userdata');

		$ret = $userdata->loadUserdata(1);
		$this->assertTrue($ret);
		$this->assertEqual(count($ret), 5);

		$testfunctions->dropZeitgeistTable('userdata');
		unset($ret);
		unset($userdata);
    }	


	// Try to load userdata without a database
	function test_loadUserdata_without_database()
	{
		$userdata = new zgUserdata();

		$ret = $userdata->loadUserdata(1);
		$this->assertFalse($ret);

		unset($ret);
		unset($userdata);
    }	


	// Load userdata
	function test_loadUserdata()
	{
		$userdata = new zgUserdata();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userdata');

		$testuser = rand(100,200);
		$testdata = uniqid();

		$this->database->query("INSERT INTO userdata(userdata_user, userdata_username) VALUES('" . $testuser . "', '" . $testdata . "')");

		$ret = $userdata->loadUserdata($testuser);
		$this->assertEqual(count($ret), 5);
		$this->assertEqual($ret['userdata_username'], $testdata);

		$testfunctions->dropZeitgeistTable('userdata');
		unset($ret);
		unset($userdata);
    }

}

?>
