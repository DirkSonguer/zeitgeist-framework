<?php

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

	function test_setUserdata()
	{
		$userdata = new zgUserdata();
		$res = $this->database->query("TRUNCATE TABLE userdata");
		
		$testdata = array();
		$testdata['userdata_firstname'] = 'Mr';
		$testdata['userdata_lastname'] = 'Test';
		
		$ret = $userdata->setUserdata('1', $testdata);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM userdata");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		unset($userdata);
    }

	function test_getUserdata()
	{
		$userdata = new zgUserdata();
		$res = $this->database->query("TRUNCATE TABLE userdata");
		$this->database->query("INSERT INTO userdata(userdata_user, userdata_firstname, userdata_lastname) VALUES('1', 'Mr', 'Test')");
		$this->database->query("INSERT INTO userdata(userdata_user, userdata_firstname, userdata_lastname) VALUES('2', 'Mrs', 'Test')");

		$ret = $userdata->getUserdata('1');
		$this->assertEqual(count($ret), 12);
		$this->assertEqual($ret['userdata_firstname'], 'Mr');
		$this->assertEqual($ret['userdata_lastname'], 'Test');
		unset($ret);

		$ret = $userdata->getUserdata('2');
		$this->assertEqual(count($ret), 12);
		$this->assertEqual($ret['userdata_firstname'], 'Mrs');
		$this->assertEqual($ret['userdata_lastname'], 'Test');
		unset($ret);

		unset($userdata);
    }

}

?>
