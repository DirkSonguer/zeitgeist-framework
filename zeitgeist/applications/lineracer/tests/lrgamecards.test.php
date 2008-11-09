<?php

class testLrgamecards extends UnitTestCase
{
	public $database;
	public $miscfunctions;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
		$this->miscfunctions  = new miscFunctions();

		$gameeventhandler = new lrGameeventhandler();
		$this->assertNotNull($gameeventhandler);
		unset($gameeventhandler);
    }

	function test_dash()
	{
		$gamestates = new lrGamestates();
		$gameeventhandler = new lrGameeventhandler();
		$objects = zgObjectcache::init();
		$configuration = zgConfiguration::init();

		$this->miscfunctions->setupGame();

		$gamestates->loadGamestates(1);
		$currentGamestates = $objects->getObject('currentGamestates');
		$this->assertEqual($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'][0], 5);
		$this->assertEqual($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'][1], 10);

		$gameeventhandler->saveRaceevent('1', $configuration->getConfiguration('gamedefinitions', 'events', 'playgamecard'), '2');
		$objects->deleteObject('currentGamestates');

		$gamestates->loadGamestates(1);
		$currentGamestates = $objects->getObject('currentGamestates');
		$this->assertEqual($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'][0], 10);
		$this->assertEqual($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'][1], 20);
	}

	function test_fullbreak()
	{
		$gamestates = new lrGamestates();
		$gameeventhandler = new lrGameeventhandler();
		$objects = zgObjectcache::init();
		$configuration = zgConfiguration::init();

		$this->miscfunctions->setupGame();

		$gamestates->loadGamestates(1);
		$currentGamestates = $objects->getObject('currentGamestates');
		$this->assertEqual($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'][0], 5);
		$this->assertEqual($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'][1], 10);

		$gameeventhandler->saveRaceevent('1', $configuration->getConfiguration('gamedefinitions', 'events', 'playgamecard'), '3');
		$objects->deleteObject('currentGamestates');

		$gamestates->loadGamestates(1);
		$currentGamestates = $objects->getObject('currentGamestates');
		$this->assertEqual($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'][0], 0);
		$this->assertEqual($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'][1], 0);
	}
}

?>
