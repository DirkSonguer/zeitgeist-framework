<?php

class testLrgameeventhandler extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();

		$gameeventhandler = new lrGameeventhandler();
		$this->assertNotNull($gameeventhandler);
		unset($gameeventhandler);
    }
	
	function createNewGame()
	{
		$this->database->query('TRUNCATE TABLE races');
		$this->database->query('TRUNCATE TABLE race_actions');
		$this->database->query('TRUNCATE TABLE race_eventhandler');
		$this->database->query('TRUNCATE TABLE race_moves');
		$this->database->query('TRUNCATE TABLE users_to_gamecards');

		$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('1', '1', '1', '150,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('1', '2', '1', '170,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('1', '3', '1', '190,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('1', '4', '1', '210,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO races(race_player1, race_player2, race_player3, race_player4, race_circuit, race_activeplayer, race_currentround, race_gamecardsallowed)";
		$sql .= "VALUES(1, 2, 3, 4, 1, 1, 1, 0)";
		$res = $this->database->query($sql);
	}
	
	function test_saveRaceevent()
	{
		$gameeventhandler = new lrGameeventhandler();
		
		$this->createNewGame();
		
		$ret = $gameeventhandler->saveRaceevent('1', '2', '3', '1');
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM race_eventhandler");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['raceevent_player'], '1');		
		$this->assertEqual($ret['raceevent_action'], '2');		
		$this->assertEqual($ret['raceevent_parameter'], '3');		
	}	

	function test_handleRaceevents()
	{
		$gamestates = new lrGamestates();
		$gameeventhandler = new lrGameeventhandler();
		
		$this->createNewGame();
		
		$gamestates->loadGamestates(1);
		$gameeventhandler->saveRaceevent('1', '1', '2', '1');

		$ret = $gameeventhandler->handleRaceevents();
		$this->assertTrue($ret);
	}

}

?>
