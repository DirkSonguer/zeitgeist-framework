<?php

class testLrgamestates extends UnitTestCase
{
	public $database;
	public $miscfunctions;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
		$this->miscfunctions  = new miscFunctions();

		$gamestates = new lrGamestates();
		$this->assertNotNull($gamestates);
		unset($gamestates);
    }
	
	function test_loadGamestates()
	{
		$gamestates = new lrGamestates();
		$objects = zgObjects::init();
		
		$this->miscfunctions->setupGame();
		
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
		$this->assertEqual($ret['playerdata'][1]['vector'][0], '5');
	}

	function test_endTurn()
	{
		$gamestates = new lrGamestates();
		$objects = zgObjects::init();
		
		$this->miscfunctions->setupGame();
		
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
		$objects = zgObjects::init();
		$gameeventhandler = new lrGameeventhandler();
		
		// reset game and load gamestates
		$this->miscfunctions->setupGame();
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
		$objects = zgObjects::init();
		$gameeventhandler = new lrGameeventhandler();
		$configuration = zgConfiguration::init();

		// reset game and load gamestates
		$this->miscfunctions->setupGame();
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
