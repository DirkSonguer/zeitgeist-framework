<?php

class testLrgamestates extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();

		$gamestates = new lrGamestates();
		$this->assertNotNull($gamestates);
		unset($gamestates);
    }
	
	function createNewGame()
	{
		$this->database->query('TRUNCATE TABLE races');
		$this->database->query('TRUNCATE TABLE race_actions');
		$this->database->query('TRUNCATE TABLE race_events');
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
		$sql .= "VALUES(1, 2, NULL, NULL, 1, 1, 1, 0)";
		$res = $this->database->query($sql);
	}

	function test_loadGamestates()
	{
		$gamestates = new lrGamestates();
		$objects = zgObjectcache::init();
		
		$this->createNewGame();
		
		// this should not contain any data as race 0 does not exist
		$ret = $gamestates->loadGamestates(0);
		$this->assertFalse($ret);

		// load gamestates for the newly created match
		$ret = $gamestates->loadGamestates(1);
		$this->assertTrue($ret);

		// check if data is ok
		$ret = $objects->getObject('currentGamestates');
		$this->assertTrue(is_array($ret));		
		$this->assertEqual($ret['currentPlayer'], '1');		
		$this->assertEqual($ret['numPlayers'], '2');		
		$this->assertEqual($ret['playerdata'][1]['moves'][0][1], '150,370');		
		$this->assertEqual($ret['playerdata'][1]['vector'][0], '0');
	}

	function test_endTurn()
	{
		$gamestates = new lrGamestates();
		$objects = zgObjectcache::init();
		
		$this->createNewGame();
		
		// this should not contain any data as race 0 does not exist
		$gamestates->loadGamestates(1);
		$currentGamestates = $objects->getObject('currentGamestates');
		$this->assertEqual($currentGamestates['currentPlayer'], '1');

		$ret = $gamestates->endTurn();
		$this->assertTrue($ret);

		$objects->deleteObject('currentGamestates');

		$gamestates->loadGamestates(1);
		$currentGamestates = $objects->getObject('currentGamestates');
		$this->assertEqual($currentGamestates['currentPlayer'], '2');
	}

	function test_playerFinished()
	{
		$gamestates = new lrGamestates();
		$objects = zgObjectcache::init();
		$gameeventhandler = new lrGameeventhandler();
		
		// reset game and load gamestates
		$this->createNewGame();
		$gamestates->loadGamestates(1);
		
		// race should not be finished
		$ret = $gamestates->raceFinished();
		$this->assertFalse($ret);

		// create finished action for player and reload gamestates
		$configuration = zgConfiguration::init();
		$gameeventhandler->saveRaceaction($configuration->getConfiguration('gamedefinitions', 'actions', 'finish'), '1');
		$objects->deleteObject('currentGamestates');
		$gamestates->loadGamestates(1);

		// player should now be finished
		$ret = $gamestates->playerFinished();
		$this->assertTrue($ret);
		
		$gamestates->loadGamestates(1);
	}

	function test_raceFinished()
	{
		$gamestates = new lrGamestates();
		$objects = zgObjectcache::init();
		$gameeventhandler = new lrGameeventhandler();
		$configuration = zgConfiguration::init();

		// reset game and load gamestates
		$this->createNewGame();
		$gamestates->loadGamestates(1);

		// race should not be finished
		$ret = $gamestates->raceFinished();
		$this->assertFalse($ret);

		// create finished action for first player
		$gameeventhandler->saveRaceaction($configuration->getConfiguration('gamedefinitions', 'actions', 'finish'), '1');

		// end turn of player and load data for next one
		$gamestates->endTurn();
		$objects->deleteObject('currentGamestates');
		$gamestates->loadGamestates(1);

		// create finished action for second player and reload gamestates
		$gameeventhandler->saveRaceaction($configuration->getConfiguration('gamedefinitions', 'actions', 'finish'), '1');
		$objects->deleteObject('currentGamestates');
		$gamestates->loadGamestates(1);

		// race should now be finished
		$ret = $gamestates->raceFinished();
		$this->assertTrue($ret);
	}

}

?>
