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


	function test_waitingForGame()
	{
		$userfunctions = new lrUserfunctions();
		
		$this->miscfunctions->setupGame();
		$res = $this->database->query("TRUNCATE TABLE lobby_to_users");

		$ret = $userfunctions->waitingForGame();
		$this->assertEqual($ret, false);

		$user = zgUserhandler::init();

		$res = $this->database->query("INSERT INTO lobby_to_users(lobbyuser_lobby, lobbyuser_user) VALUES('1', '" . $user->getUserId() . "')");
		$ret = $userfunctions->waitingForGame();
		$this->assertEqual($ret, true);
		
		unset($userfunctions);
	}


	function test_currentlyPlayingGame()
	{
		$userfunctions = new lrUserfunctions();
		
		$this->miscfunctions->setupGame();
		$res = $this->database->query("TRUNCATE TABLE race_to_users");

		$ret = $userfunctions->currentlyPlayingGame();
		$this->assertEqual($ret, false);

		$user = zgUserhandler::init();

		$res = $this->database->query("INSERT INTO race_to_users(raceuser_race, raceuser_user, raceuser_order) VALUES('1', '" . $user->getUserId() . "', '1')");
		$ret = $userfunctions->currentlyPlayingGame();
		$this->assertEqual($ret, true);
		
		unset($userfunctions);
	}
	
	                                                                                                                                        
	function test_getAvailableCircuits()
	{
		$userfunctions = new lrUserfunctions();
		
		$this->miscfunctions->setupGame();
		$res = $this->database->query("TRUNCATE TABLE circuits");
		$res = $this->database->query("TRUNCATE TABLE circuits_to_users");

		$ret = $userfunctions->getAvailableCircuits();
		$this->assertEqual(count($ret), 0);

		$res = $this->database->query("INSERT INTO circuits(circuit_name, circuit_description, circuit_startposition, circuit_startvector, circuit_public, circuit_active) VALUES('test1', 'test1', '1', '1', '1', '1')");
		$ret = $userfunctions->getAvailableCircuits();
		$this->assertEqual(count($ret), 1);

		$res = $this->database->query("INSERT INTO circuits(circuit_name, circuit_description, circuit_startposition, circuit_startvector, circuit_public, circuit_active) VALUES('test2', 'test2', '1', '1', '1', '0')");
		$ret = $userfunctions->getAvailableCircuits();
		$this->assertEqual(count($ret), 1);

		$res = $this->database->query("INSERT INTO circuits(circuit_name, circuit_description, circuit_startposition, circuit_startvector, circuit_public, circuit_active) VALUES('test3', 'test3', '1', '1', '0', '1')");
		$ret = $userfunctions->getAvailableCircuits();
		$this->assertEqual(count($ret), 1);

		$res = $this->database->query("INSERT INTO circuits(circuit_name, circuit_description, circuit_startposition, circuit_startvector, circuit_public, circuit_active) VALUES('test4', 'test4', '1', '1', '0', '0')");
		$ret = $userfunctions->getAvailableCircuits();
		$this->assertEqual(count($ret), 1);

		$user = zgUserhandler::init();

		$res = $this->database->query("INSERT INTO circuits_to_users(usercircuit_user, usercircuit_circuit) VALUES('" . $user->getUserId() . "', '3')");
		$ret = $userfunctions->getAvailableCircuits();
		$this->assertEqual(count($ret), 2);

		unset($userfunctions);
	}
	

}

?>
