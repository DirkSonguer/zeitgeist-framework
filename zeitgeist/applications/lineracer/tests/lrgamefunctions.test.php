<?php

class testLrgamefunctions extends UnitTestCase
{
	public $database;
	public $miscfunctions;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
		$this->miscfunctions  = new miscFunctions();

		$gamefunctions = new lrGamefunctions();
		$this->assertNotNull($gamefunctions);
		unset($gamefunctions);
    }
	
	function test_startGame()
	{
		$gamefunctions = new lrGamefunctions();
		
		$this->miscfunctions->setupGame();

		$res = $this->database->query("TRUNCATE TABLE lobby");
		$res = $this->database->query("TRUNCATE TABLE lobby_to_users");
		$res = $this->database->query("TRUNCATE TABLE race_to_users");

		$res = $this->database->query("SELECT * FROM races");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$res = $this->database->query("INSERT INTO lobby(lobby_circuit, lobby_maxplayers, lobby_gamecardsallowed) VALUES('1', '1', '1')");
		$res = $this->database->query("SELECT * FROM lobby");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$lobby = $this->database->insertId();
		$res = $this->database->query("INSERT INTO lobby_to_users(lobbyuser_lobby, lobbyuser_user) VALUES('" . $lobby . "', '1')");
		$res = $this->database->query("INSERT INTO lobby_to_users(lobbyuser_lobby, lobbyuser_user) VALUES('" . $lobby . "', '2')");
		$res = $this->database->query("SELECT * FROM lobby_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$ret = $gamefunctions->startGame($lobby);
		$this->assertEqual($ret, '2');

		$res = $this->database->query("SELECT * FROM lobby");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM lobby_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM races");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$res = $this->database->query("SELECT * FROM race_to_users WHERE raceuser_race='2'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);
	}

	function test_endGame()
	{
		$gamefunctions = new lrGamefunctions();
		
		$this->miscfunctions->setupGame();

		$res = $this->database->query("TRUNCATE TABLE races_archive");
		$res = $this->database->query("TRUNCATE TABLE race_actions_archive");

		$res = $this->database->query("SELECT * FROM races");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $gamefunctions->endGame(1);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM races");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM races_archive");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$res = $this->database->query("SELECT * FROM race_actions");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM race_actions_archive");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 8);
	}

	function test_createLobby()
	{
		$gamefunctions = new lrGamefunctions();
		
		$this->miscfunctions->setupGame();

		$res = $this->database->query("TRUNCATE TABLE lobby");
		$res = $this->database->query("TRUNCATE TABLE lobby_to_users");

		$ret = $gamefunctions->createLobby(1, 1, 1);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM lobby");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$res = $this->database->query("SELECT * FROM lobby_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
	}

	function test_joinLobby()
	{
		$gamefunctions = new lrGamefunctions();
		
		$this->miscfunctions->setupGame();

		$res = $this->database->query("TRUNCATE TABLE lobby");
		$res = $this->database->query("TRUNCATE TABLE lobby_to_users");

		$res = $this->database->query("INSERT INTO lobby(lobby_circuit, lobby_maxplayers, lobby_gamecardsallowed) VALUES('1', '2', '1')");
		$lobby = $this->database->insertId();
		$res = $this->database->query("INSERT INTO lobby_to_users(lobbyuser_lobby, lobbyuser_user) VALUES('" . $lobby . "', '2')");

		$ret = $gamefunctions->joinLobby(1);
		$this->assertTrue($ret);

		$ret = $gamefunctions->joinLobby(1);
		$this->assertFalse($ret);

		$res = $this->database->query("SELECT * FROM lobby_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);
	}

}

?>
