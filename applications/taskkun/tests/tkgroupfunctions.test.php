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

		unset($groupfunctions);
    }

	function test_getGroupsForUser()
	{
		$groupfunctions = new tkGroupfunctions();
		$user = zgUserhandler::init();

		$this->database->query('TRUNCATE TABLE groups');
		$this->database->query('TRUNCATE TABLE users_to_groups');

		$groupdata = array();
		$groupdata['group_description'] = 'this is a testgroup';
		$groupdata['group_name'] = 'test';
		$groupfunctions->addGroup($groupdata);
		$groupid = $this->database->insertId();
		$ret = $this->database->query("INSERT INTO users_to_groups(usergroup_user, usergroup_group) VALUES('1', '" . $groupid . "')");

		$groupdata = array();
		$groupdata['group_description'] = 'this is another testgroup';
		$groupdata['group_name'] = 'test2';
		$groupfunctions->addGroup($groupdata);
		$groupid = $this->database->insertId();
		$ret = $this->database->query("INSERT INTO users_to_groups(usergroup_user, usergroup_group) VALUES('1', '" . $groupid . "')");

		$_SESSION['user_userid'] = '1';
		$_SESSION['user_key'] = '1';
		$_SESSION['user_username'] = 'testuser';
		$_SESSION['user_instance'] = '1';
		$user->setLoginStatus(true);

		$ret = $groupfunctions->getGroupsForUser();

		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret[0]['group_name'], 'test');
		$this->assertEqual($ret[1]['group_name'], 'test2');

		unset($groupfunctions);
    }

	function test_getNumberofGroupsForUser()
	{
		$groupfunctions = new tkGroupfunctions();
		$user = zgUserhandler::init();

		$this->database->query('TRUNCATE TABLE groups');
		$this->database->query('TRUNCATE TABLE users_to_groups');

		$groupdata = array();
		$groupdata['group_description'] = 'this is a testgroup';
		$groupdata['group_name'] = 'test';
		$groupfunctions->addGroup($groupdata);
		$groupid = $this->database->insertId();
		$ret = $this->database->query("INSERT INTO users_to_groups(usergroup_user, usergroup_group) VALUES('1', '" . $groupid . "')");

		$groupdata = array();
		$groupdata['group_description'] = 'this is another testgroup';
		$groupdata['group_name'] = 'test2';
		$groupfunctions->addGroup($groupdata);
		$groupid = $this->database->insertId();
		$ret = $this->database->query("INSERT INTO users_to_groups(usergroup_user, usergroup_group) VALUES('1', '" . $groupid . "')");

		$_SESSION['user_userid'] = '1';
		$_SESSION['user_key'] = '1';
		$_SESSION['user_username'] = 'testuser';
		$_SESSION['user_instance'] = '1';
		$user->setLoginStatus(true);

		$ret = $groupfunctions->getNumberofGroupsForUser();

		$this->assertEqual($ret, 2);

		unset($groupfunctions);
    }

}

?>
