<?php

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

	function test_setUserroles()
	{
		$userroles = new zgUserroles();
		$res = $this->database->query("TRUNCATE TABLE userroles_to_users");
		
		$testroles = array();
		$testroles['1'] = true;
		$testroles['5'] = true;
		
		$ret = $userroles->setUserroles('1', $testroles);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM userroles_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		unset($userroles);
    }

	function test_getUserroles()
	{
		$userroles = new zgUserroles();
		$res = $this->database->query("TRUNCATE TABLE userroles_to_users");

		$this->database->query("INSERT INTO userroles_to_users(userroleuser_user, userroleuser_userrole) VALUES('1', '1')");
		$this->database->query("INSERT INTO userroles_to_users(userroleuser_user, userroleuser_userrole) VALUES('1', '5')");
		$this->database->query("INSERT INTO userroles_to_users(userroleuser_user, userroleuser_userrole) VALUES('2', '2')");
		$this->database->query("INSERT INTO userroles_to_users(userroleuser_user, userroleuser_userrole) VALUES('2', '6')");
		
		$ret = $userroles->getUserroles('1');
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret['1'], true);
		$this->assertEqual($ret['5'], true);
		unset($ret);

		$ret = $userroles->getUserroles('2');
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret['2'], true);
		$this->assertEqual($ret['6'], true);
		unset($ret);

		unset($userroles);
    }
}

?>
