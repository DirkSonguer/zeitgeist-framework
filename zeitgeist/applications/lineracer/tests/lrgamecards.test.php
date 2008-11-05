<?php

class testLrgamecards extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();

		$gameeventhandler = new lrGameeventhandler();
		$this->assertNotNull($gameeventhandler);
		unset($gameeventhandler);
    }
	
	function setupGame()
	{
		$this->database->query('TRUNCATE TABLE races');
		$this->database->query('TRUNCATE TABLE race_actions');
		$this->database->query('TRUNCATE TABLE race_events');
		$this->database->query('TRUNCATE TABLE race_actions');
		$this->database->query('TRUNCATE TABLE users_to_gamecards');

		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('1', '1', '1', '150,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('1', '2', '1', '170,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('1', '3', '1', '190,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('1', '4', '1', '210,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('1', '1', '1', '155,380')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('1', '2', '1', '175,380')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('1', '3', '1', '195,380')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('1', '4', '1', '215,380')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO races(race_player1, race_player2, race_player3, race_player4, race_circuit, race_activeplayer, race_currentround, race_gamecardsallowed)";
		$sql .= "VALUES(1, 2, 3, 4, 1, 1, 1, 0)";
		$res = $this->database->query($sql);
	}

	function test_dash()
	{
		$gamestates = new lrGamestates();
		$gameeventhandler = new lrGameeventhandler();
		$objects = zgObjectcache::init();
		$configuration = zgConfiguration::init();

		$this->setupGame();

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

		$this->setupGame();

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
