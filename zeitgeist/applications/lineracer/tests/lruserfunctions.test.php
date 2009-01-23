<?php

class testLruserfunctions extends UnitTestCase
{
	public $database;
	public $miscfunctions;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
		$this->miscfunctions  = new miscFunctions();

		$userfunctions = new lrUserfunctions();
		$this->assertNotNull($userfunctions);
		unset($userfunctions);
    }

	function test_getAvailableCircuits()
	{
		$userfunctions = new lrUserfunctions();
		
		$this->miscfunctions->setupGame();

		$res = $this->database->query("TRUNCATE TABLE circuits");
		$res = $this->database->query("TRUNCATE TABLE circuits_to_users");

		$res = $this->database->query("INSERT INTO circuits(circuit_name, circuit_description, circuit_startposition, circuit_startvector, circuit_public, circuit_active) VALUES('test1', 'test1', '1', '1', '1', '1')");
		$ret = $userfunctions->getAvailableCircuits();
		$this->assertEqual(count($ret), '1');

		$res = $this->database->query("INSERT INTO circuits(circuit_name, circuit_description, circuit_startposition, circuit_startvector, circuit_public, circuit_active) VALUES('test2', 'test2', '1', '1', '1', '0')");
		$ret = $userfunctions->getAvailableCircuits();
		$this->assertEqual(count($ret), '1');

		$res = $this->database->query("INSERT INTO circuits(circuit_name, circuit_description, circuit_startposition, circuit_startvector, circuit_public, circuit_active) VALUES('test3', 'test3', '1', '1', '0', '1')");
		$ret = $userfunctions->getAvailableCircuits();
		$this->assertEqual(count($ret), '2');

		$res = $this->database->query("INSERT INTO circuits(circuit_name, circuit_description, circuit_startposition, circuit_startvector, circuit_public, circuit_active) VALUES('test4', 'test4', '1', '1', '0', '0')");
		$ret = $userfunctions->getAvailableCircuits();
		$this->assertEqual(count($ret), '2');
	}
	

}

?>
