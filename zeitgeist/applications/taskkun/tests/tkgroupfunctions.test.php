<?php

class testTkgroupfunctions extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();

		$groupfunctions = new tkGroupfunctions();
		$this->assertNotNull($groupfunctions);
		unset($groupfunctions);
    }

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

		unset($instancefunctions);
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

		unset($instancefunctions);
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

		unset($instancefunctions);
    }	

	function test_getGroupdata()
	{
		$groupfunctions = new tkGroupfunctions();

		$this->database->query('TRUNCATE TABLE groups');

		$groupdata = array();
		$groupdata['group_description'] = 'this is a testgroup';
		$groupdata['group_name'] = 'test';
		$groupfunctions->addGroup($groupdata);
		$groupid = $this->database->insertId();

		$ret = $groupfunctions->getGroupdata(0);
		$this->assertFalse($ret);

		$ret = $groupfunctions->getGroupdata($groupid);
		$this->assertEqual($ret['group_description'], 'this is a testgroup');
		$this->assertEqual($ret['group_name'], 'test');

		unset($instancefunctions);
    }

}

?>
