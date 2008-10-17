<?php

class testLrgamecardfunctions extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();

		$gamecardfunctions = new lrGamecardfunctions();
		$this->assertNotNull($gamecardfunctions);
		unset($gamecardfunctions);
    }
	
	function test_addGamecard()
	{
		$gamecardfunctions = new lrGamecardfunctions();
		$this->database->query('TRUNCATE TABLE users_to_gamecards');

		$gamecardfunctions->addGamecard('1', '1');
		$gamecardfunctions->addGamecard('2', '2');

		$res = $this->database->query("SELECT * FROM users_to_gamecards");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$gamecardfunctions->addGamecard('2', '1');
		$gamecardfunctions->addGamecard('1', '1');
		$res = $this->database->query("SELECT * FROM users_to_gamecards");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 3);
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['usergamecard_gamecard'], '1');		
		$this->assertEqual($ret['usergamecard_user'], '1');		
		$this->assertEqual($ret['usergamecard_count'], '2');		
	}


	function test_removeGamecard()
	{
		$gamecardfunctions = new lrGamecardfunctions();
		$this->database->query('TRUNCATE TABLE users_to_gamecards');

		$gamecardfunctions->addGamecard('1', '1');
		$gamecardfunctions->addGamecard('2', '2');

		$res = $this->database->query("SELECT * FROM users_to_gamecards");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$gamecardfunctions->addGamecard('2', '1');
		$gamecardfunctions->addGamecard('1', '1');
		$res = $this->database->query("SELECT * FROM users_to_gamecards");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 3);
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['usergamecard_gamecard'], '1');		
		$this->assertEqual($ret['usergamecard_user'], '1');		
		$this->assertEqual($ret['usergamecard_count'], '2');		
	}












/*
	function test_addGroup()
	{
		$groupfunctions = new tkGroupfunctions();

		$this->database->query('TRUNCATE TABLE groups');

		$groupdata = array();
		$ret = $groupfunctions->addGroup($groupdata);
		$this->assertFalse($ret);

		$groupdata['group_description'] = 'this is a testgroup';
		$groupdata['group_name'] = 'test';
		$ret = $groupfunctions->addGroup($groupdata);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM groups");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['group_name'], 'test');

		unset($groupfunctions);
    }
	
	function test_updateGroup()
	{
		$groupfunctions = new tkGroupfunctions();

		$this->database->query('TRUNCATE TABLE groups');

		$groupdata = array();
		$groupdata['group_description'] = 'this is a testgroup';
		$groupdata['group_name'] = 'test';
		$groupfunctions->addGroup($groupdata);
		$groupid = $this->database->insertId();

		$groupdata = array();
		$ret = $groupfunctions->updateGroup($groupdata);
		$this->assertFalse($ret);

		$groupdata['group_id'] = $groupid;
		$groupdata['group_description'] = 'this is some other testgroup';
		$groupdata['group_name'] = 'updated';
		$ret = $groupfunctions->updateGroup($groupdata);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM groups");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['group_name'], 'updated');

		unset($groupfunctions);
    }
	
	function test_deleteGroup()
	{
		$groupfunctions = new tkGroupfunctions();

		$this->database->query('TRUNCATE TABLE groups');

		$groupdata = array();
		$groupdata['group_description'] = 'this is a testgroup';
		$groupdata['group_name'] = 'test';
		$groupfunctions->addGroup($groupdata);
		$groupid = $this->database->insertId();

		$ret = $groupfunctions->deleteGroup($groupid);

		$res = $this->database->query("SELECT * FROM groups");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		unset($groupfunctions);
    }	
 */
}

?>
