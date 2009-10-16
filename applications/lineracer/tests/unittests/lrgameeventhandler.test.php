<?php

class testLrgameeventhandler extends UnitTestCase
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

	function test_saveRaceaction()
	{
		$gamestates = new lrGamestates();
		$gameeventhandler = new lrGameeventhandler();
		$objects = zgObjects::init();
		
		$raceid = $this->miscfunctions->setupGame(1);
		$gamestates->loadGamestates();
		
		$ret = $gameeventhandler->saveRaceaction('1', '150,200');
		$this->assertTrue($ret);

		$ret = $gameeventhandler->saveRaceaction('1', '170,200');
		$this->assertTrue($ret);

		$ret = $gameeventhandler->saveRaceaction('1', '170,200');
		$this->assertTrue($ret);

		$objects->deleteObject('currentGamestates');
		$ret = $gamestates->loadGamestates();
		$this->assertTrue($ret);

		$ret = $objects->getObject('currentGamestates');
		$this->assertTrue(is_array($ret));

		$this->assertEqual($ret['playerdata'][1]['actions'][0]['type'], '1');
		$this->assertEqual($ret['playerdata'][1]['actions'][0]['parameter'], '150,370');
	}
	
	function test_saveRaceevent()
	{
		$gamestates = new lrGamestates();
		$gameeventhandler = new lrGameeventhandler();
		$objects = zgObjects::init();
		
		$raceid = $this->miscfunctions->setupGame(1);
		$gamestates->loadGamestates();
		
		$ret = $gameeventhandler->saveRaceevent('1', '2', '3', '1');
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM race_events");
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
		
		$raceid = $this->miscfunctions->setupGame();

		$gamestates->loadGamestates();
		$gameeventhandler->saveRaceevent('1', '1', '2');

		$ret = $gameeventhandler->handleRaceeevents();
		$this->assertTrue($ret);
	}

}

?>
