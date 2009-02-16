<?php

class testLrgamefunctions extends UnitTestCase
{
	public $database;
	public $miscfunctions;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
		$this->miscfunctions  = new miscFunctions();

		$gamefunctions = new lrGamefunctions();
		$this->assertNotNull($gamefunctions);
		unset($gamefunctions);
    }
	
	function test_startRace()
	{
		$gamefunctions = new lrGamefunctions();
		
		$res = $this->database->query("TRUNCATE TABLE lobby");
		$res = $this->database->query("TRUNCATE TABLE lobby_to_users");
		$res = $this->database->query("TRUNCATE TABLE race_to_users");

		$raceid = $this->miscfunctions->setupGame();
		$res = $this->database->query("SELECT * FROM races");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$lobbyid = rand(100,500);
		$player1 = rand(100,500);
		$player2 = rand(501,1000);

		$res = $this->database->query("INSERT INTO lobby(lobby_id, lobby_circuit, lobby_maxplayers, lobby_gamecardsallowed) VALUES('" . $lobbyid . "', '1', '1', '1')");
		$res = $this->database->query("SELECT * FROM lobby");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$res = $this->database->query("INSERT INTO lobby_to_users(lobbyuser_lobby, lobbyuser_user, lobbyuser_confirmation) VALUES('" . $lobbyid . "', '" . $player1 . "', '1')");
		$res = $this->database->query("INSERT INTO lobby_to_users(lobbyuser_lobby, lobbyuser_user, lobbyuser_confirmation) VALUES('" . $lobbyid . "', '" . $player2 . "', '1')");
		$res = $this->database->query("SELECT * FROM lobby_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$session = zgSession::init();
		$session->setSessionVariable('user_userid', $player1);

		$ret = $gamefunctions->startRace();
		$this->assertEqual($ret, ($raceid+1));

		$res = $this->database->query("SELECT * FROM lobby");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM lobby_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM races");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$res = $this->database->query("SELECT * FROM race_to_users WHERE raceuser_race='" . ($raceid+1). "'");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);
	}

	function test_endRace()
	{
		$gamefunctions = new lrGamefunctions();
		
		$raceid = $this->miscfunctions->setupGame(2);

		$res = $this->database->query("TRUNCATE TABLE races_archive");
		$res = $this->database->query("TRUNCATE TABLE race_actions_archive");
		$res = $this->database->query("TRUNCATE TABLE race_to_users_archive");

		$res = $this->database->query("SELECT * FROM races");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $gamefunctions->endRace();
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM races");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM races_archive");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$res = $this->database->query("SELECT * FROM race_actions");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM race_actions_archive");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 4);

		$res = $this->database->query("SELECT * FROM race_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM race_to_users_archive");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);
	}
	

	function test_forfeit()
	{
		$gamefunctions = new lrGamefunctions();
		$configuration = zgConfiguration::init();
		$objects = zgObjects::init();
		$gameeventhandler = new lrGameeventhandler();
		$gamestates = new lrGamestates();
		
		$raceid = $this->miscfunctions->setupGame(2);
		$gamestates->loadGamestates();
		$currentGamestates = $objects->getObject('currentGamestates');
		$this->assertEqual($currentGamestates['move']['currentPlayer'], 1);

		$ret = $gamefunctions->forfeit();
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM race_actions");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 5);

		$objects->deleteObject('currentGamestates');
		$gamestates->loadGamestates();
		$currentGamestates = $objects->getObject('currentGamestates');
		$this->assertEqual($currentGamestates['move']['currentPlayer'], 2);

		$gameeventhandler->saveRaceaction($configuration->getConfiguration('gamedefinitions', 'actions', 'finish'), '1');
		$objects->deleteObject('currentGamestates');
		$gamestates->loadGamestates();

		$res = $this->database->query("SELECT * FROM race_actions");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 6);
		
		$ret = $gamestates->raceFinished();
		$this->assertTrue($ret);
	}
	

	function test_endTurn()
	{
		$gamefunctions = new lrGamefunctions();
		$gamestates = new lrGamestates();
		$objects = zgObjects::init();
		
		$raceid = $this->miscfunctions->setupGame(2);
		
		// first turn, player 1, round 1
		$gamestates->loadGamestates();
		$currentGamestates = $objects->getObject('currentGamestates');
		$this->assertEqual($currentGamestates['move']['currentPlayer'], '1');
		$this->assertEqual($currentGamestates['move']['currentRound'], '1');

		// end turn, get next player
		$ret = $gamefunctions->endTurn();
		$this->assertTrue($ret);

		$objects->deleteObject('currentGamestates');

		// second turn, player 2, round 1
		$gamestates->loadGamestates();
		$currentGamestates = $objects->getObject('currentGamestates');
		$this->assertEqual($currentGamestates['move']['currentPlayer'], '2');
		$this->assertEqual($currentGamestates['move']['currentRound'], '1');

		// end turn, get next player
		$ret = $gamefunctions->endTurn();
		$this->assertTrue($ret);

		$objects->deleteObject('currentGamestates');

		// third turn, player 1, round 2
		$gamestates->loadGamestates();
		$currentGamestates = $objects->getObject('currentGamestates');
		$this->assertEqual($currentGamestates['move']['currentPlayer'], '1');
		$this->assertEqual($currentGamestates['move']['currentRound'], '2');
	}
	
}

?>
