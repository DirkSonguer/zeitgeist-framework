<?php

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
	
	function test_createUser()
	{
		$trafficlogger = new zgTrafficlogger();
		$this->database->query('TRUNCATE TABLE trafficlog');
		$this->database->query('TRUNCATE TABLE trafficlog_parameters');
		
		$parameters = array();
		$parameters['test1'] = 'test1';
		$parameters['test2'] = 'test2';
		$ret = $trafficlogger->logPageview('1', '1', '1', $parameters);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM trafficlog");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['trafficlog_module'], '1');
		$this->assertEqual($ret['trafficlog_action'], '1');

		$res = $this->database->query("SELECT * FROM trafficlog_parameters");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['trafficparameters_key'], 'test1');
		$this->assertEqual($ret['trafficparameters_value'], 'test1');
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['trafficparameters_key'], 'test2');
		$this->assertEqual($ret['trafficparameters_value'], 'test2');

		unset($trafficlogger);
	}
	
}

?>
