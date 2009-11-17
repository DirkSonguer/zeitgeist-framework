<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');

class testUserrights extends UnitTestCase
{
	public $database;

	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$userrights = new zgUserrights();
		$this->assertNotNull($userrights);
		unset($userrights);
    }


	// Try saving userrights without data
	function test_saveUserrights_nodata()
	{
		$userrights = new zgUserrights();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userrights');

		$ret = $userrights->saveUserrights('', '');
		$this->assertFalse($ret);
		
		$testfunctions->dropZeitgeistTable('userrights');
		unset($ret);
		unset($userrights);
    }


	// Try saving userrights with empty data
	function test_saveUserrights_emptydata()
	{
		$userrights = new zgUserrights();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userrights');

		$testuser = rand(1,100);
		$testrights = array();

		$ret = $userrights->saveUserrights($testuser, $testrights);
		$this->assertFalse($ret);

		$testfunctions->dropZeitgeistTable('userrights');
		unset($ret);
		unset($userrights);
    }


	// Try saving userrights without userdata
	function test_saveUserrights_without_database()
	{
		$userrights = new zgUserrights();

		$testuser = rand(1,100);
		$testrights = array();

		$ret = $userrights->saveUserrights($testuser, $testrights);
		$this->assertFalse($ret);

		unset($ret);
		unset($userrights);
    }


	// Save userrights
	function test_saveUserrights_success()
	{
		$userrights = new zgUserrights();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userrights');
		
		$testuser = rand(1,100);
		$testright1 = rand(1, 50);
		$testright2 = rand(50, 100);

		$testrights = array();
		$testrights[$testright1] = true;
		$testrights[$testright2] = true;
		
		$ret = $userrights->saveUserrights($testuser, $testrights);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM userrights");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$testfunctions->dropZeitgeistTable('userrights');
		unset($ret);
		unset($userrights);
    }


	// Try loading userrights for empty user
	function test_loadUserrights_nouser()
	{
		$userrights = new zgUserrights();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userrights');

		$ret = $userrights->loadUserrights('');
		$this->assertFalse($ret);
		
		$testfunctions->dropZeitgeistTable('userrights');
		unset($ret);
		unset($userrights);
    }


	// Try loading userrights for nonexistant user
	function test_loadUserrights_nonexistantuser()
	{
		$userrights = new zgUserrights();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userrights');

		$ret = $userrights->loadUserrights(1);
		$this->assertFalse($ret);
		
		$testfunctions->dropZeitgeistTable('userrights');
		unset($ret);
		unset($userrights);
    }


	// Try loading userrights without database
	function test_loadUserrights_without_database()
	{
		$userrights = new zgUserrights();

		$ret = $userrights->loadUserrights(1);
		$this->assertFalse($ret);
		
		unset($ret);
		unset($userrights);
    }


	// Load userrights without adding rolebased rights
	function test_loadUserrights_success_noroles()
	{
		$userrights = new zgUserrights();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userrights');
		
		$testuser = rand(1,100);
		$testright1 = rand(1, 50);
		$testright2 = rand(50, 100);
		
		$this->database->query("INSERT INTO userrights(userright_user, userright_action) VALUES('" . $testuser . "', '" . $testright1 . "')");
		$this->database->query("INSERT INTO userrights(userright_user, userright_action) VALUES('" . $testuser . "', '" . $testright2 . "')");
		
		$ret = $userrights->loadUserrights($testuser);
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret[$testright1], true);
		$this->assertEqual($ret[$testright2], true);

		$testfunctions->dropZeitgeistTable('userrights');
		unset($ret);
		unset($userrights);
    }


	// Load userrights with adding rolebased rights
	function test_loadUserrights_success_withroles()
	{
		$userrights = new zgUserrights();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('userrights');
		$testfunctions->createZeitgeistTable('userroles_to_users');
		$testfunctions->createZeitgeistTable('userroles_to_actions');
		$testfunctions->createZeitgeistTable('userroles');
		
		$testuser = rand(1,100);
		$testrole = rand(1,100);
		$testright1 = rand(1, 50);
		$testright2 = rand(50, 100);
		$testright3 = rand(100, 150);
		$testright4 = rand(150, 200);
		
		$this->database->query("INSERT INTO userrights(userright_user, userright_action) VALUES('" . $testuser . "', '" . $testright1 . "')");
		$this->database->query("INSERT INTO userrights(userright_user, userright_action) VALUES('" . $testuser . "', '" . $testright2 . "')");

		$this->database->query("INSERT INTO userroles(userrole_id, userrole_name, userrole_description) VALUES('" . $testrole . "','test', 'test')");
		
		$this->database->query("INSERT INTO userroles_to_users(userroleuser_user, userroleuser_userrole) VALUES($testuser, $testrole)");
		$this->database->query("INSERT INTO userroles_to_actions(userroleaction_userrole, userroleaction_action) VALUES($testrole, $testright3)");
		$this->database->query("INSERT INTO userroles_to_actions(userroleaction_userrole, userroleaction_action) VALUES($testrole, $testright4)");

		$ret = $userrights->loadUserrights($testuser);
		$this->assertEqual(count($ret), 4);
		$this->assertEqual($ret[$testright1], true);
		$this->assertEqual($ret[$testright2], true);
		$this->assertEqual($ret[$testright3], true);
		$this->assertEqual($ret[$testright4], true);

		$testfunctions->dropZeitgeistTable('userrights');
		$testfunctions->dropZeitgeistTable('userroles_to_users');
		$testfunctions->dropZeitgeistTable('userroles_to_actions');
		$testfunctions->dropZeitgeistTable('userroles');
		unset($ret);
		unset($userrights);
    }

}

?>
