<?php

class testTkinstancefunctions extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();

		$instancefunctions = new tkInstancefunctions();
		$this->assertNotNull($instancefunctions);
		unset($instancefunctions);
    }
	
	function test_createInstance()
	{
		$instancefunctions = new tkInstancefunctions();

		$this->database->query('TRUNCATE TABLE instances');

		$instancefunctions->createInstance('test', 1);

		$res = $this->database->query("SELECT * FROM instances");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['instance_name'], 'test');

		unset($instancefunctions);
    }

}

?>
