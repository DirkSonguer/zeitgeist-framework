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

		$game = rand(0,1000);
		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, $player, $game);
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


	// Try to remove all game events for a player
	function test_removeGameevents_player()
	{
		$gamehandler = new zgGamehandler();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_events');

		$game = rand(0,1000);
		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, $player, $game);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, ($player+1), $game);

		$ret = $gamehandler->removeGameevents($time, $player);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM game_events");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$testfunctions->dropZeitgeistTable('game_events');

		unset($ret);
		unset($gamehandler);
    }


	// Try to remove all game events for a player and a game
	function test_removeGameevents_playergame()
	{
		$gamehandler = new zgGamehandler();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_events');

		$game = rand(0,1000);
		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, $player, ($game+1));
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, $player, $game);

		$ret = $gamehandler->removeGameevents($time, $player, $game);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM game_events");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$testfunctions->dropZeitgeistTable('game_events');

		unset($ret);
		unset($gamehandler);
    }


	// Try to remove all game events
	function test_removeGameevents_all()
	{
		$gamehandler = new zgGamehandler();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_events');

		$game = rand(0,1000);
		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, $player, $game);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, ($player+1), $game);

		$ret = $gamehandler->removeGameevents($time);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM game_events");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$testfunctions->dropZeitgeistTable('game_events');

		unset($ret);
		unset($gamehandler);
    }


	// Try to remove events but events are in the future
	function test_removeGameevents_none()
	{
		$gamehandler = new zgGamehandler();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_events');

		$game = rand(0,1000);
		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, $player, $game);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, ($player+1), $game);

		$ret = $gamehandler->removeGameevents(($time-1));
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM game_events");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$testfunctions->dropZeitgeistTable('game_events');

		unset($ret);
		unset($gamehandler);
    }


	// Try to insert an action into the action log but for a future date
	function test_logGameevents_none()
	{
		$gamehandler = new zgGamehandler();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_events');
		$testfunctions->createZeitgeistTable('game_eventlog');

		$game = rand(0,1000);
		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, $player, $game);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, ($player+1), $game);

		$ret = $gamehandler->logGameevents(($time-1));
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_events");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$res = $this->database->query("SELECT * FROM game_eventlog");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$testfunctions->dropZeitgeistTable('game_eventlog');
		$testfunctions->dropZeitgeistTable('game_events');

		unset($ret);
		unset($gamehandler);
    }


	// Try to insert an action into the action log
	function test_logGameevents_player()
	{
		$gamehandler = new zgGamehandler();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_events');
		$testfunctions->createZeitgeistTable('game_eventlog');

		$game = rand(0,1000);
		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, $player, $game);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, ($player+1), $game);

		$ret = $gamehandler->logGameevents($time, $player);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_events");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$res = $this->database->query("SELECT * FROM game_eventlog");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$testfunctions->dropZeitgeistTable('game_eventlog');
		$testfunctions->dropZeitgeistTable('game_events');

		unset($ret);
		unset($gamehandler);
    }


	// Try to insert an action into the action log
	function test_logGameevents_success()
	{
		$gamehandler = new zgGamehandler();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_events');
		$testfunctions->createZeitgeistTable('game_eventlog');

		$game = rand(0,1000);
		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$ret = $gamehandler->saveGameevent($action, $parameter, $time, $player, $game);

		$ret = $gamehandler->logGameevents($time, $player, $game);
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

		$game = rand(0,1000);
		$action = rand(0,1000);
		$parameter = uniqid();
		$player = rand(1,1000);
		$time = rand(1,1000);
		$res = $this->database->query("INSERT INTO game_actions(action_id, action_name, action_class) VALUES('".$action."', 'test', 'testaction')");

		$ret = $gamehandler->saveGameevent($action, $parameter, $time, $player, $game);
		
		$ret = $gamehandler->handleGameevents($game, ($time+1));
		$this->assertTrue($ret);

		$testfunctions->dropZeitgeistTable('game_actions');
		$testfunctions->dropZeitgeistTable('game_eventlog');
		$testfunctions->dropZeitgeistTable('game_events');

		unset($ret);
		unset($gamehandler);
	}

}

?>
