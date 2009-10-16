<?php

class testLrallachievements extends UnitTestCase
{
	public $database;
	public $miscfunctions;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
		$this->miscfunctions  = new miscFunctions();

		$achievements = new lrAchievements();
		$this->assertNotNull($achievements);
		unset($achievements);
    }

	function test_fastrounds()
	{
		$achievements = new lrAchievements();

		$this->database->query('TRUNCATE TABLE achievements');
		$this->database->query('TRUNCATE TABLE achievements_to_users');
		$this->database->query('TRUNCATE TABLE races');
		$this->database->query('TRUNCATE TABLE race_to_users');
		$this->database->query('TRUNCATE TABLE race_actions');

		$playerid = rand(501,1000);
		$achievementid = rand(501,1000);
		$session = zgSession::init();
		$session->setSessionVariable('user_id', $playerid);

		$sql = "INSERT INTO `race_actions` (`raceaction_id`, `raceaction_race`, `raceaction_player`, `raceaction_action`, `raceaction_parameter`, `raceaction_timestamp`) VALUES";
		$sql .= "(1, 1, 1, 1, '150,370', '2009-03-24 08:06:31');";
		$this->database->query($sql);
		
		$sql = "INSERT INTO `race_to_users` (`raceuser_race`, `raceuser_user`, `raceuser_order`, `raceuser_assessed`) VALUES ('1', '" . $playerid . "', '1', '0');";
		$this->database->query($sql);

		$sql = "INSERT INTO achievements (achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward, achievement_function) VALUES" ;
		$sql .= "('" . $achievementid . "', 'test1', 'test one', '', '1', '2', 'fastround1')";
		$this->database->query($sql);

		$sql = "INSERT INTO achievements (achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward, achievement_function) VALUES" ;
		$sql .= "('" . ($achievementid+1) . "', 'test1', 'test one', '', '1', '2', 'fastround2')";
		$this->database->query($sql);

		$sql = "INSERT INTO achievements (achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward, achievement_function) VALUES" ;
		$sql .= "('" . ($achievementid+2) . "', 'test1', 'test one', '', '1', '2', 'fastround3')";
		$this->database->query($sql);

		$ret = $achievements->assessAchievements();
		$this->assertTrue($ret);

		$sql = "SELECT * FROM `race_to_users` WHERE raceuser_user='" . $playerid . "' AND raceuser_assessed='0'";
		$res = $this->database->query($sql);
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$achievementfunctions = new lrAchievementfunctions();
		$ret = $achievementfunctions->getPlayerAchievements();
		$this->assertEqual($ret[$achievementid], true);
		$this->assertEqual($ret[($achievementid+1)], true);
		$this->assertEqual($ret[($achievementid+2)], true);

		$this->database->query('TRUNCATE TABLE race_actions');
		$this->database->query('TRUNCATE TABLE achievements_to_users');
		$this->database->query('TRUNCATE TABLE race_to_users');
		
		$sql = "INSERT INTO `race_actions` (`raceaction_race`, `raceaction_player`, `raceaction_action`, `raceaction_parameter`, `raceaction_timestamp`) VALUES";
		$sql .= "(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),";
		$sql .= "(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),";
		$sql .= "(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),";
		$sql .= "(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31'),(1, 1, 1, '150,370', '2009-03-24 08:06:31');";
		$this->database->query($sql);

		$sql = "INSERT INTO `race_to_users` (`raceuser_race`, `raceuser_user`, `raceuser_order`, `raceuser_assessed`) VALUES ('1', '" . $playerid . "', '1', '0');";
		$this->database->query($sql);

		$ret = $achievements->assessAchievements();
		$this->assertTrue($ret);

		$ret = $achievementfunctions->getPlayerAchievements();
		$this->assertEqual(empty($ret[$achievementid]), true);		
	}

}

?>
