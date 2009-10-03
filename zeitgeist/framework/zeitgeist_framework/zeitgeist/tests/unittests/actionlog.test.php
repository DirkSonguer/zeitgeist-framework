<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../test_configuration.php');

class testActionlog extends UnitTestCase
{
	public $database;

	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$actionlog = new zgActionlog();
		$this->assertNotNull($actionlog);
		unset($actionlog);		
    }
	
	
	// Test logging a pageview
	function test_logAction_success()
	{
		$actionlog = new zgActionlog();
		$this->database->query('TRUNCATE TABLE actionlog');
		$this->database->query('TRUNCATE TABLE actionlog_parameters');
		
		$param1 = rand(100, 5000);
		$param2 = rand(100, 5000);
		$param3 = rand(100, 5000);
		
		$parameters = array();
		$parameters['test1'] = 'test'.$param1;
		$parameters['test2'] = 'test'.$param2;
		$ret = $actionlog->logAction($param1, $param2, $parameters);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM actionlog");

		// should be only one entry we just entered
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['actionlog_module'], $param1);
		$this->assertEqual($ret['actionlog_action'], $param2);

		$res = $this->database->query("SELECT * FROM actionlog_parameters");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['actionparameters_key'], 'test1');
		$this->assertEqual($ret['actionparameters_value'], 'test'.$param1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['actionparameters_key'], 'test2');
		$this->assertEqual($ret['actionparameters_value'], 'test'.$param2);

		unset($actionlog);
	}
	
}

?>
