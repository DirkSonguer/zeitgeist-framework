<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../test_configuration.php');

class testTrafficlogger extends UnitTestCase
{

	public $database;

	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$trafficlogger = new zgTrafficlogger();
		$this->assertNotNull($trafficlogger);
		unset($trafficlogger);		
    }
	
	
	// Test logging a pageview
	function test_logPageview()
	{
		$trafficlogger = new zgTrafficlogger();
		$this->database->query('TRUNCATE TABLE trafficlog');
		$this->database->query('TRUNCATE TABLE trafficlog_parameters');
		
		$param1 = rand(100, 5000);
		$param2 = rand(100, 5000);
		$param3 = rand(100, 5000);
		
		$parameters = array();
		$parameters['test1'] = 'test'.$param1;
		$parameters['test2'] = 'test'.$param2;
		$ret = $trafficlogger->logPageview($param1, $param2, $param3, $parameters);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM trafficlog");

		// should be only one entry we just entered
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['trafficlog_module'], $param1);
		$this->assertEqual($ret['trafficlog_action'], $param2);
		$this->assertEqual($ret['trafficlog_user'], $param3);

		$res = $this->database->query("SELECT * FROM trafficlog_parameters");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['trafficparameters_key'], 'test1');
		$this->assertEqual($ret['trafficparameters_value'], 'test'.$param1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['trafficparameters_key'], 'test2');
		$this->assertEqual($ret['trafficparameters_value'], 'test'.$param2);

		unset($trafficlogger);
	}
	
}

?>
