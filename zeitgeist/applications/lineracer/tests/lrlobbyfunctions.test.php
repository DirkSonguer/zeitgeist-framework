<?php

class testLrlobbyfunctions extends UnitTestCase
{
	public $database;
	public $miscfunctions;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
		$this->miscfunctions  = new miscFunctions();

		$lobbyfunctions = new lrLobbyfunctions();
		$this->assertNotNull($lobbyfunctions);
		unset($lobbyfunctions);
    }


	function test_joinGameroom()
	{
		$lobbyfunctions = new lrLobbyfunctions();
		
		$res = $this->database->query("TRUNCATE TABLE lobby");
		$res = $this->database->query("TRUNCATE TABLE lobby_to_users");

		$ret = $lobbyfunctions->joinGameroom(1);
		$this->assertEqual($ret, false);

		$res = $this->database->query("INSERT INTO lobby(lobby_circuit, lobby_maxplayers, lobby_gamecardsallowed) VALUES('1', '2', '1')");
		$ret = $lobbyfunctions->joinGameroom(1);
		$this->assertEqual($ret, true);
		
		$userfunctions = new lrUserfunctions();
		$ret = $userfunctions->waitingForGame();
		$this->assertEqual($ret, true);
		
		unset($lobbyfunctions);
	}


	function test_leaveGameroom()
	{
		$lobbyfunctions = new lrLobbyfunctions();
		
		$res = $this->database->query("TRUNCATE TABLE lobby");
		$res = $this->database->query("TRUNCATE TABLE lobby_to_users");

		$res = $this->database->query("INSERT INTO lobby(lobby_circuit, lobby_maxplayers, lobby_gamecardsallowed) VALUES('1', '2', '1')");
		$ret = $lobbyfunctions->joinGameroom(1);
		$this->assertEqual($ret, true);

		$userfunctions = new lrUserfunctions();
		$ret = $userfunctions->waitingForGame();
		$this->assertEqual($ret, true);

		$ret = $lobbyfunctions->leaveGameroom(1);
		$this->assertEqual($ret, true);
		
		$userfunctions = new lrUserfunctions();
		$ret = $userfunctions->waitingForGame();
		$this->assertEqual($ret, false);

		$res = $this->database->query("SELECT * FROM lobby");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		unset($lobbyfunctions);
	}


	function test_createGameroom()
	{
		$lobbyfunctions = new lrLobbyfunctions();

		$res = $this->database->query("TRUNCATE TABLE lobby");
		$res = $this->database->query("TRUNCATE TABLE lobby_to_users");

		$ret = $lobbyfunctions->createGameroom(1, 1, 1);
		$this->assertEqual($ret, true);

		$res = $this->database->query("SELECT * FROM lobby");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$res = $this->database->query("SELECT * FROM lobby_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$userfunctions = new lrUserfunctions();
		$ret = $userfunctions->waitingForGame();
		$this->assertEqual($ret, true);

		$ret = $lobbyfunctions->createGameroom(1, 1, 1);
		$this->assertEqual($ret, false);

		unset($lobbyfunctions);
	}
}

?>
