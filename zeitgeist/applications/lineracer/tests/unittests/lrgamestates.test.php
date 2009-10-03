<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../test_configuration.php');

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


	// negative test for loading gamestates	
	function test_loadGamestates_nodata()
	{
		$gamestates = new lrGamestates();
		$objects = zgObjects::init();

		// this should not contain any data as race 0 does not exist
		$ret = $gamestates->loadGamestates();
		$this->assertFalse($ret);
	}


	// load gamestates from testdata
	function test_loadGamestates_testdata()
	{
		$gamestates = new lrGamestates();
		$objects = zgObjects::init();
		
		$raceid = $this->miscfunctions->setupGame(2);

		// load gamestates for the newly created match
		$ret = $gamestates->loadGamestates();
		$this->assertTrue($ret);

		// check if data is ok
		$ret = $objects->getObject('currentGamestates');
		
		$this->assertTrue(is_array($ret));		
		$this->assertEqual($ret['round']['currentPlayer'], '1');
		$this->assertEqual($ret['race']['numPlayers'], '2');
		$this->assertEqual($ret['playerdata'][1]['actions'][0]['parameter'], '150,370');
		$this->assertEqual($ret['playerdata'][1]['vector'][0], '5');

		$raceid = $this->miscfunctions->setupGame(3);

		// load gamestates for the newly created match
		$ret = $gamestates->loadGamestates();
		$this->assertTrue($ret);

		// check if data is ok
		$ret = $objects->getObject('currentGamestates');
		
		$this->assertTrue(is_array($ret));		
		$this->assertEqual($ret['round']['currentPlayer'], '1');
		$this->assertEqual($ret['race']['numPlayers'], '3');
		$this->assertEqual($ret['playerdata'][2]['actions'][0]['parameter'], '175,380');
		$this->assertEqual($ret['playerdata'][2]['vector'][0], '-5');
	}


	function test_playerFinished()
	{
		$gamestates = new lrGamestates();
		$objects = zgObjects::init();
		$gameeventhandler = new lrGameeventhandler();
		
		// reset game and load gamestates
		$raceid = $this->miscfunctions->setupGame(1);
		$gamestates->loadGamestates();
		
		// race should not be finished
		$ret = $gamestates->playerFinished();
		$this->assertFalse($ret);

		// create finished action for player and reload gamestates
		$configuration = zgConfiguration::init();
		$gameeventhandler->saveRaceaction($configuration->getConfiguration('gamedefinitions', 'actions', 'finish'), '1');
		$objects->deleteObject('currentGamestates');
		$gamestates->loadGamestates();

		// player should now be finished
		$ret = $gamestates->playerFinished();
		$this->assertTrue($ret);
	}

	function test_raceFinished()
	{
		$gamestates = new lrGamestates();
		$objects = zgObjects::init();
		$gameeventhandler = new lrGameeventhandler();
		$configuration = zgConfiguration::init();
		$gamefunctions = new lrGamefunctions();

		// reset game and load gamestates
		$raceid = $this->miscfunctions->setupGame(2);
		$gamestates->loadGamestates();

		// race should not be finished
		$ret = $gamestates->raceFinished();
		$this->assertFalse($ret);

		// create finished action for first player
		$gameeventhandler->saveRaceaction($configuration->getConfiguration('gamedefinitions', 'actions', 'finish'), '1');

		// end turn of player and load data for next one
		$gamefunctions->endTurn();
		$objects->deleteObject('currentGamestates');
		$gamestates->loadGamestates();

		// race should not be finished
		$ret = $gamestates->raceFinished();
		$this->assertFalse($ret);

		// create finished action for second player and reload gamestates
		$gameeventhandler->saveRaceaction($configuration->getConfiguration('gamedefinitions', 'actions', 'finish'), '1');
		$objects->deleteObject('currentGamestates');
		$gamestates->loadGamestates();

		// race should now be finished
		$ret = $gamestates->raceFinished();
		$this->assertTrue($ret);
	}

}

?>
