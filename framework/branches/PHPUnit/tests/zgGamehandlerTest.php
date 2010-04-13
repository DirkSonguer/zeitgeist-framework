<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgGamehandler test case.
 */
class zgGamehandlerTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @var zgGamehandler
	 */
	private $zgGamehandler;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->zgGamehandler = new zgGamehandler(/* parameters */);
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->zgGamehandler = null;
		parent::tearDown();
	}


	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();
	}


	/**
	 * Tests zgGamehandler->__construct()
	 */
	public function test__construct()
	{
		// TODO Auto-generated zgGamehandlerTest->test__construct()
		$this->markTestIncomplete( "__construct test not implemented" );
		
		$this->zgGamehandler->__construct(/* parameters */);
	
	}


	/**
	 * Tests zgGamehandler->saveGameevent()
	 */
	public function testSaveGameevent()
	{
		$this->setUp();
		$testfunctions = new testFunctions();
		
		$testfunctions->createZeitgeistTable( 'game_events' );
		
		$game = rand( 0, 1000 );
		$action = rand( 0, 1000 );
		$parameter = uniqid();
		$player = rand( 1, 1000 );
		$time = rand( 1, 1000 );
		
		// insert event 
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, $player, $game );
		$this->assertTrue( $ret );
		
		// check database
		$res = $this->database->query( "SELECT * FROM game_events" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 1 );
		
		// check values in database
		$ret = $this->database->fetchArray( $res );
		$this->assertEquals( $ret ['event_action'], $action );
		$this->assertEquals( $ret ['event_parameter'], $parameter );
		$this->assertEquals( $ret ['event_player'], $player );
		$this->assertEquals( $ret ['event_time'], $time );
		
		$testfunctions->dropZeitgeistTable( 'game_events' );
		$this->tearDown();
	}


	/**
	 * Tests zgGamehandler->handleGameevents()
	 */
	public function testHandleGameevents()
	{
		$this->setUp();
		$testfunctions = new testFunctions();
		
		$testfunctions->createZeitgeistTable( 'game_actions' );
		$testfunctions->createZeitgeistTable( 'game_eventlog' );
		$testfunctions->createZeitgeistTable( 'game_events' );
		
		$game = rand( 0, 1000 );
		$action = rand( 0, 1000 );
		$parameter = uniqid();
		$player = rand( 1, 1000 );
		$time = rand( 1, 1000 );
		
		// insert an action and save an event for it
		$res = $this->database->query( "INSERT INTO game_actions(action_id, action_name, action_class) VALUES('" . $action . "', 'test', 'testaction')" );
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, $player, $game );
		
		// handle the event
		$ret = $this->zgGamehandler->handleGameevents( $game, ($time + 1) );
		$this->assertTrue( $ret );
		
		$testfunctions->dropZeitgeistTable( 'game_actions' );
		$testfunctions->dropZeitgeistTable( 'game_eventlog' );
		$testfunctions->dropZeitgeistTable( 'game_events' );
		$this->tearDown();
	}


	/**
	 * Tests zgGamehandler->getGameevents()
	 */
	public function testGetGameevents()
	{
		// TODO Auto-generated zgGamehandlerTest->testGetGameevents()
		$this->markTestIncomplete( "getGameevents test not implemented" );
		
		$this->zgGamehandler->getGameevents(/* parameters */);
	
	}


	/**
	 * Tests zgGamehandler->logGameevents()
	 */
	public function testLogGameevents_Player()
	{
		$this->setUp();
		$testfunctions = new testFunctions();
		
		$testfunctions->createZeitgeistTable( 'game_events' );
		$testfunctions->createZeitgeistTable( 'game_eventlog' );
		
		$game = rand( 0, 1000 );
		$action = rand( 0, 1000 );
		$parameter = uniqid();
		$player = rand( 1, 1000 );
		$time = rand( 1, 1000 );
		
		// save events
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, $player, $game );
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, ($player + 1), $game );
		
		// log events
		$ret = $this->zgGamehandler->logGameevents( $time, $player );
		$this->assertTrue( $ret );
		
		// check database
		$res = $this->database->query( "SELECT * FROM game_events" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 1 );
		
		$res = $this->database->query( "SELECT * FROM game_eventlog" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 1 );
		
		$testfunctions->dropZeitgeistTable( 'game_eventlog' );
		$testfunctions->dropZeitgeistTable( 'game_events' );
		$this->tearDown();
	}


	/**
	 * Tests zgGamehandler->logGameevents()
	 */
	public function testLogGameevents_PlayerGame()
	{
		$this->setUp();
		$testfunctions = new testFunctions();
		
		$testfunctions->createZeitgeistTable( 'game_events' );
		$testfunctions->createZeitgeistTable( 'game_eventlog' );
		
		$game = rand( 0, 1000 );
		$action = rand( 0, 1000 );
		$parameter = uniqid();
		$player = rand( 1, 1000 );
		$time = rand( 1, 1000 );
		
		// save events
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, $player, $game );
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, $player, ($game + 1) );
		
		// log events
		$ret = $this->zgGamehandler->logGameevents( $time, $player, $game );
		$this->assertTrue( $ret );
		
		// check database
		$res = $this->database->query( "SELECT * FROM game_events" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 1 );
		
		$res = $this->database->query( "SELECT * FROM game_eventlog" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 1 );
		
		$testfunctions->dropZeitgeistTable( 'game_eventlog' );
		$testfunctions->dropZeitgeistTable( 'game_events' );
		$this->tearDown();
	}


	/**
	 * Tests zgGamehandler->logGameevents()
	 */
	public function testLogGameevents_NoEventsToLog()
	{
		$this->setUp();
		$testfunctions = new testFunctions();
		
		$testfunctions->createZeitgeistTable( 'game_events' );
		$testfunctions->createZeitgeistTable( 'game_eventlog' );
		
		$game = rand( 0, 1000 );
		$action = rand( 0, 1000 );
		$parameter = uniqid();
		$player = rand( 1, 1000 );
		$time = rand( 1, 1000 );
		
		// save events
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, $player, $game );
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, ($player + 1), $game );
		
		// log events
		$ret = $this->zgGamehandler->logGameevents( ($time - 1) );
		$this->assertTrue( $ret );
		
		// check database
		$res = $this->database->query( "SELECT * FROM game_events" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 2 );
		
		$res = $this->database->query( "SELECT * FROM game_eventlog" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 0 );
		
		$testfunctions->dropZeitgeistTable( 'game_eventlog' );
		$testfunctions->dropZeitgeistTable( 'game_events' );
		$this->tearDown();
	}


	/**
	 * Tests zgGamehandler->removeGameevents()
	 */
	public function testRemoveGameevents_Player()
	{
		$this->setUp();
		$testfunctions = new testFunctions();
		
		$testfunctions->createZeitgeistTable( 'game_events' );
		
		$game = rand( 0, 1000 );
		$action = rand( 0, 1000 );
		$parameter = uniqid();
		$player = rand( 1, 1000 );
		$time = rand( 1, 1000 );
		
		// save events
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, $player, $game );
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, ($player + 1), $game );
		
		// try removing event
		$ret = $this->zgGamehandler->removeGameevents( $time, $player );
		$this->assertTrue( $ret );
		
		// check data in database
		$res = $this->database->query( "SELECT * FROM game_events" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 1 );
		
		$testfunctions->dropZeitgeistTable( 'game_events' );
		$this->tearDown();
	}


	/**
	 * Tests zgGamehandler->removeGameevents()
	 */
	public function testRemoveGameevents_PlayerGame()
	{
		$this->setUp();
		$testfunctions = new testFunctions();
		
		$testfunctions->createZeitgeistTable( 'game_events' );
		
		$game = rand( 0, 1000 );
		$action = rand( 0, 1000 );
		$parameter = uniqid();
		$player = rand( 1, 1000 );
		$time = rand( 1, 1000 );
		
		// save events
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, $player, $game );
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, $player, ($game + 1) );
		
		// try removing event
		$ret = $this->zgGamehandler->removeGameevents( $time, $player, $game );
		$this->assertTrue( $ret );
		
		// check data in database
		$res = $this->database->query( "SELECT * FROM game_events" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 1 );
		
		$testfunctions->dropZeitgeistTable( 'game_events' );
		$this->tearDown();
	}


	/**
	 * Tests zgGamehandler->removeGameevents()
	 */
	public function testRemoveGameevents_All()
	{
		$this->setUp();
		$testfunctions = new testFunctions();
		
		$testfunctions->createZeitgeistTable( 'game_events' );
		
		$game = rand( 0, 1000 );
		$action = rand( 0, 1000 );
		$parameter = uniqid();
		$player = rand( 1, 1000 );
		$time = rand( 1, 1000 );
		
		// save events
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, $player, $game );
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, ($player + 1), $game );
		
		// try removing event
		$ret = $this->zgGamehandler->removeGameevents( $time );
		$this->assertTrue( $ret );
		
		// check data in database
		$res = $this->database->query( "SELECT * FROM game_events" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 0 );
		
		$testfunctions->dropZeitgeistTable( 'game_events' );
		$this->tearDown();
	}


	/**
	 * Tests zgGamehandler->removeGameevents()
	 */
	public function testRemoveGameevents_NoEventsToRemove()
	{
		$this->setUp();
		$testfunctions = new testFunctions();
		
		$testfunctions->createZeitgeistTable( 'game_events' );
		
		$game = rand( 0, 1000 );
		$action = rand( 0, 1000 );
		$parameter = uniqid();
		$player = rand( 1, 1000 );
		$time = rand( 1, 1000 );
		
		// save events
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, $player, $game );
		$ret = $this->zgGamehandler->saveGameevent( $action, $parameter, $time, ($player + 1), $game );
		
		// try removing event
		$ret = $this->zgGamehandler->removeGameevents( ($time - 1) );
		$this->assertTrue( $ret );
		
		// check data in database
		$res = $this->database->query( "SELECT * FROM game_events" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 2 );
		
		$testfunctions->dropZeitgeistTable( 'game_events' );
		$this->tearDown();
	}

}

