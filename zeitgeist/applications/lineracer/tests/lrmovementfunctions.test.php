<?php

class testLrmovementfunctions extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();

		$movementfunctions = new lrMovementfunctions();
		$this->assertNotNull($movementfunctions);
		unset($movementfunctions);
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
	
	
	function test_validateMove()
	{
		$gamestates = new lrGamestates();
		$movementfunctions = new lrMovementfunctions();
		
		$this->createNewGame();
		
		$gamestates->loadGamestates(1);
		
		$ret = $movementfunctions->validateMove(1,1);
		$this->assertFalse($ret);

		$ret = $movementfunctions->validateMove(160,370);
		$this->assertTrue($ret);
	}
	
	
	function test_getMovement()
	{
		$gamestates = new lrGamestates();
		$movementfunctions = new lrMovementfunctions();
		
		$this->createNewGame();

		$gamestates->loadGamestates(1);
		$gamestates->saveGameaction('1', '150,200');
		$gamestates->saveGameaction('1', '170,200');
		$gamestates->saveGameaction('1', '190,200');
		$gamestates->saveGameaction('1', '210,200');
		$gamestates->loadGamestates(1);

		$ret = $movementfunctions->getMovement(-1);
		$this->assertEqual($ret[0], '150');
		$this->assertEqual($ret[1], '200');

		$ret = $movementfunctions->getMovement();
		$this->assertEqual($ret[1][0], '150');
		$this->assertEqual($ret[1][1], '200');
	}	


	function test_validateTerrain()
	{
		$gamestates = new lrGamestates();
		$movementfunctions = new lrMovementfunctions();
		
		$this->createNewGame();

		$gamestates->loadGamestates(1);

		$ret = $movementfunctions->validateTerrain(150,10);
		$this->assertEqual($ret[0], '150');
		$this->assertEqual($ret[1], '49');

		$ret = $movementfunctions->validateTerrain(150,610);
		$this->assertEqual($ret[0], '150');
		$this->assertEqual($ret[1], '596');

		$ret = $movementfunctions->validateTerrain(50,370);
		$this->assertEqual($ret[0], '124');
		$this->assertEqual($ret[1], '370');

		$ret = $movementfunctions->validateTerrain(250,370);
		$this->assertEqual($ret[0], '243');
		$this->assertEqual($ret[1], '370');
	}	
}

?>
