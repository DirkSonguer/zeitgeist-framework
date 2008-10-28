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
		$sql .= "VALUES(1, 2, NULL, NULL, 1, 1, 1, 0)";
		$res = $this->database->query($sql);
	}


	function test_loadGamestates()
	{
		$gamestates = new lrGamestates();
		
		$this->createNewGame();
		
		$ret = $gamestates->loadGamestates(0);
		$this->assertFalse($ret);

		$ret = $gamestates->loadGamestates(1);
		$this->assertTrue($ret);

		$objects = zgObjectcache::init();
		$ret = $objects->getObject('currentGamestates');
		$this->assertTrue(is_array($ret));		
		$this->assertEqual($ret['currentPlayer'], '1');		
		$this->assertEqual($ret['numPlayers'], '2');		
		$this->assertEqual($ret['playerdata'][1]['moves'][0][1], '150,370');		
		$this->assertEqual($ret['playerdata'][1]['vector'][0], '0');
	}


	function test_saveGameaction()
	{
		$gamestates = new lrGamestates();
		
		$this->createNewGame();
		
		$objects = zgObjectcache::init();
		
		$ret = $gamestates->loadGamestates(1);
		$this->assertTrue($ret);

		$ret = $gamestates->saveGameaction('1', '150,200');
		$this->assertTrue($ret);

		$ret = $gamestates->saveGameaction('1', '170,200');
		$this->assertTrue($ret);

		$ret = $gamestates->saveGameaction('1', '170,200');
		$this->assertTrue($ret);

		$objects->deleteObject('currentGamestates');
		$ret = $gamestates->loadGamestates(1);
		$this->assertTrue($ret);

		$objects = zgObjectcache::init();
		$ret = $objects->getObject('currentGamestates');
		$this->assertTrue(is_array($ret));
		$this->assertEqual($ret['playerdata'][1]['moves'][1][1], '150,200');
		$this->assertEqual($ret['playerdata'][1]['vector'][1], '0');
		
		var_dump($ret);
	}

	function test_raceFinished()
	{
		$gamestates = new lrGamestates();
		$objects = zgObjectcache::init();
		
		$this->createNewGame();
		
		$ret = $gamestates->raceFinished();
		$this->assertFalse($ret);

		$configuration = zgConfiguration::init();
		$gamestates->saveGameaction($configuration->getConfiguration('gamedefinitions', 'actions', 'finished'), '1');
		$objects->deleteObject('currentGamestates');
		$gamestates->loadGamestates(1);

		$currentGamestates = $objects->getObject('currentGamestates');
		$currentGamestates['currentPlayer'] = 2;
		$currentGamestates = $objects->storeObject('currentGamestates', $currentGamestates);
		$gamestates->saveGameaction($configuration->getConfiguration('gamedefinitions', 'actions', 'finished'), '1');

		$ret = $gamestates->raceFinished();
		$this->assertTrue($ret);
		
		$gamestates->loadGamestates(1);
	}

	function test_playerFinished()
	{
		$gamestates = new lrGamestates();
		$objects = zgObjectcache::init();
		
		$this->createNewGame();
		
		$ret = $gamestates->playerFinished();
		$this->assertFalse($ret);

		$configuration = zgConfiguration::init();
		$gamestates->saveGameaction($configuration->getConfiguration('gamedefinitions', 'actions', 'finished'), '1');
		$objects->deleteObject('currentGamestates');
		$gamestates->loadGamestates(1);

		$ret = $gamestates->playerFinished();
		$this->assertTrue($ret);
		
		$gamestates->loadGamestates(1);
	}
}

?>
