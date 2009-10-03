<?php

class testLrstatisticfunctions extends UnitTestCase
{
	public $database;
	public $miscfunctions;
	public $configuration;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
		$this->miscfunctions  = new miscFunctions();
		$this->configuration = zgConfiguration::init();

		$statistics = new lrStatisticfunctions();
		$this->assertNotNull($statistics);
		unset($statistics);
    }

	function test_getUserStatistics()
	{
		$achievements = new lrAchievements();
		
		$this->database->query('TRUNCATE TABLE userdata');

		$playerid = rand(501,1000);
		$session = zgSession::init();
		$session->setSessionVariable('user_id', $playerid);

		$sql = "INSERT INTO `userdata` (`userdata_id`, `userdata_user`, `userdata_firstname`, `userdata_racepoints`, `userdata_raceswon`, `userdata_raceslost`, `userdata_currentcar`) ";
		$sql .= "VALUES(1, '" . $playerid . "', 'test', 100, 2, 3, 0);";
		$this->database->query($sql);

		$statistics = new lrStatisticfunctions();
		$ret = $statistics->getUserStatistics();
		$this->assertEqual($ret['userdata_racepoints'], 100);
	}

	function test_addRacepoints()
	{
		$achievements = new lrAchievements();
		
		$this->database->query('TRUNCATE TABLE userdata');

		$playerid = rand(501,1000);
		$session = zgSession::init();
		$session->setSessionVariable('user_id', $playerid);

		$sql = "INSERT INTO `userdata` (`userdata_id`, `userdata_user`, `userdata_firstname`, `userdata_racepoints`, `userdata_raceswon`, `userdata_raceslost`, `userdata_currentcar`) ";
		$sql .= "VALUES(1, '" . $playerid . "', 'test', 100, 2, 3, 0);";
		$this->database->query($sql);

		$statistics = new lrStatisticfunctions();
		$ret = $statistics->addRacepoints(100);
		$this->assertTrue($ret);

		$statistics = new lrStatisticfunctions();
		$ret = $statistics->getUserStatistics();
		$this->assertEqual($ret['userdata_racepoints'], 200);
	}
	
	function test_substractRacepoints()
	{
		$achievements = new lrAchievements();
		
		$this->database->query('TRUNCATE TABLE userdata');

		$playerid = rand(501,1000);
		$session = zgSession::init();
		$session->setSessionVariable('user_id', $playerid);

		$sql = "INSERT INTO `userdata` (`userdata_id`, `userdata_user`, `userdata_firstname`, `userdata_racepoints`, `userdata_raceswon`, `userdata_raceslost`, `userdata_currentcar`) ";
		$sql .= "VALUES(1, '" . $playerid . "', 'test', 100, 2, 3, 0);";
		$this->database->query($sql);

		$statistics = new lrStatisticfunctions();
		$ret = $statistics->substractRacepoints(50);
		$this->assertTrue($ret);

		$statistics = new lrStatisticfunctions();
		$ret = $statistics->getUserStatistics();
		$this->assertEqual($ret['userdata_racepoints'], 50);
	}


	function test_addWin()
	{
		$achievements = new lrAchievements();
		
		$this->database->query('TRUNCATE TABLE userdata');

		$playerid = rand(501,1000);
		$session = zgSession::init();
		$session->setSessionVariable('user_id', $playerid);

		$sql = "INSERT INTO `userdata` (`userdata_id`, `userdata_user`, `userdata_firstname`, `userdata_racepoints`, `userdata_raceswon`, `userdata_raceslost`, `userdata_currentcar`) ";
		$sql .= "VALUES(1, '" . $playerid . "', 'test', 100, 2, 3, 0);";
		$this->database->query($sql);

		$statistics = new lrStatisticfunctions();
		$ret = $statistics->addWin();
		$this->assertTrue($ret);
		
		$points = $this->configuration->getConfiguration('gamedefinitions', 'points', 'won') + 100;

		$statistics = new lrStatisticfunctions();
		$ret = $statistics->getUserStatistics();
		$this->assertEqual($ret['userdata_racepoints'], $points);
		$this->assertEqual($ret['userdata_raceswon'], 3);
	}
	
	function test_addLoss()
	{
		$achievements = new lrAchievements();
		
		$this->database->query('TRUNCATE TABLE userdata');

		$playerid = rand(501,1000);
		$session = zgSession::init();
		$session->setSessionVariable('user_id', $playerid);

		$sql = "INSERT INTO `userdata` (`userdata_id`, `userdata_user`, `userdata_firstname`, `userdata_racepoints`, `userdata_raceswon`, `userdata_raceslost`, `userdata_currentcar`) ";
		$sql .= "VALUES(1, '" . $playerid . "', 'test', 100, 2, 3, 0);";
		$this->database->query($sql);

		$statistics = new lrStatisticfunctions();
		$ret = $statistics->addLoss();
		$this->assertTrue($ret);
		
		$points = $this->configuration->getConfiguration('gamedefinitions', 'points', 'lost') + 100;

		$statistics = new lrStatisticfunctions();
		$ret = $statistics->getUserStatistics();
		$this->assertEqual($ret['userdata_racepoints'], $points);
		$this->assertEqual($ret['userdata_raceslost'], 4);
	}	
}

?>
