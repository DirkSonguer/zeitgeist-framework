<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');

class testGamehandler extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$gamehandler = new zgGamehandler();
		$this->assertNotNull($gamehandler);
		unset($gamehandler);
    }


	// Try to insert an event into the event log
	function test_saveGameevent()
	{
		$gamehandler = new zgGamehandler();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_events');

		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$ret = $gamehandler->saveGameevent($action, $parameter, $player, $time);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_events");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['event_action'], $action);
		$this->assertEqual($ret['event_parameter'], $parameter);
		$this->assertEqual($ret['event_player'], $player);
		$this->assertEqual($ret['event_time'], $time);

		$testfunctions->dropZeitgeistTable('game_events');

		unset($ret);
		unset($gamehandler);
    }


	// Try to insert an event into the event log
	function test_removeGameevent()
	{
		$gamehandler = new zgGamehandler();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_events');

		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$ret = $gamehandler->saveGameevent($action, $parameter, $player, $time);

		$res = $this->database->query("SELECT * FROM game_events");
		$ret = $this->database->fetchArray($res);
		
		$ret = $gamehandler->removeGameevent($ret['event_id']);
		$this->assertTrue($ret);

		$testfunctions->dropZeitgeistTable('game_events');

		unset($ret);
		unset($gamehandler);
    }


	// Try to insert an action into the action log
	function test_logGameaction()
	{
		$gamehandler = new zgGamehandler();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_events');
		$testfunctions->createZeitgeistTable('game_eventlog');

		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$ret = $gamehandler->saveGameevent($action, $parameter, $player, $time);

		$res = $this->database->query("SELECT * FROM game_events");
		$ret = $this->database->fetchArray($res);

		$ret = $gamehandler->logGameevent($ret['event_id']);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_events");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM game_eventlog");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['eventlog_action'], $action);
		$this->assertEqual($ret['eventlog_parameter'], $parameter);
		$this->assertEqual($ret['eventlog_player'], $player);

		$testfunctions->dropZeitgeistTable('game_eventlog');
		$testfunctions->dropZeitgeistTable('game_events');

		unset($ret);
		unset($gamehandler);
    }


	// Try to handle game events 
	function test_handleGameevents()
	{
		$gamehandler = new zgGamehandler();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_actions');
		$testfunctions->createZeitgeistTable('game_eventlog');
		$testfunctions->createZeitgeistTable('game_events');

		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$res = $this->database->query("INSERT INTO game_actions(action_id, action_name, action_class) VALUES('".$action."', 'test', 'testaction')");

		$ret = $gamehandler->saveGameevent($action, $parameter, $player, $time);
		
		$ret = $gamehandler->handleGameevents(($time+1));
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_events");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM game_eventlog");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['eventlog_action'], $action);
		$this->assertEqual($ret['eventlog_parameter'], $parameter);
		$this->assertEqual($ret['eventlog_player'], $player);

		$testfunctions->dropZeitgeistTable('game_events');
		$testfunctions->dropZeitgeistTable('game_actions');
		$testfunctions->dropZeitgeistTable('game_eventlog');

		unset($ret);
		unset($gamehandler);
	}

}

?>
