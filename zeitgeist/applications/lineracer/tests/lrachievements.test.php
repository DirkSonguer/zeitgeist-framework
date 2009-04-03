<?php

class testLrachievements extends UnitTestCase
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

	function test_assessAchievements()
	{
		$achievements = new lrAchievements();
		
		$this->database->query('TRUNCATE TABLE achievements');
		$this->database->query('TRUNCATE TABLE achievements_to_users');
		$this->database->query('TRUNCATE TABLE races');
		$this->database->query('TRUNCATE TABLE race_to_users');
		$this->database->query('TRUNCATE TABLE race_actions');

		$playerid = rand(501,1000);
		$session = zgSession::init();
		$session->setSessionVariable('user_userid', $playerid);

		$sql = "INSERT INTO `race_actions` (`raceaction_id`, `raceaction_race`, `raceaction_player`, `raceaction_action`, `raceaction_parameter`, `raceaction_timestamp`) VALUES";
		$sql .= "(1, 1, 1, 1, '150,370', '2009-03-24 08:06:31'), (2, 1, 1, 1, '156,386', '2009-03-24 08:06:49'), (3, 1, 1, 1, '178,417', '2009-03-24 08:07:10'), (4, 1, 1, 1, '217,450', '2009-03-24 08:07:26'),";
		$sql .= "(5, 1, 1, 1, '272,469', '2009-03-24 08:07:46'), (6, 1, 1, 1, '343,472', '2009-03-24 08:08:02'), (7, 1, 1, 4, '1', '2009-03-24 08:08:15'), (8, 1, 1, 1, '429,460', '2009-03-24 08:08:15'),";
		$sql .= "(9, 1, 1, 1, '497,444', '2009-03-24 08:08:29'), (10, 1, 1, 1, '555,414', '2009-03-24 08:08:47'), (11, 1, 1, 1, '600,370', '2009-03-24 08:08:57'), (12, 1, 1, 5, '2', '2009-03-24 08:09:07'),";
		$sql .= "(13, 1, 1, 1, '629,310', '2009-03-24 08:09:07'), (14, 1, 1, 3, '2', '2009-03-24 08:09:18'), (15, 1, 1, 1, '583,279', '2009-03-24 08:09:31'), (16, 1, 1, 1, '523,258', '2009-03-24 08:09:41'),";
		$sql .= "(17, 1, 1, 3, '1', '2009-03-24 08:09:50'), (18, 1, 1, 5, '3', '2009-03-24 08:09:58'), (19, 1, 1, 1, '388,232', '2009-03-24 08:09:58'), (20, 1, 1, 1, '236,214', '2009-03-24 08:10:08'),";
		$sql .= "(21, 1, 1, 3, '3', '2009-03-24 08:10:13'), (22, 1, 1, 1, '227,227', '2009-03-24 08:10:22'), (23, 1, 1, 1, '202,253', '2009-03-24 08:10:29'), (24, 1, 1, 1, '162,293', '2009-03-24 08:10:36'),";
		$sql .= "(25, 1, 1, 1, '135,344', '2009-03-24 08:10:45'), (26, 1, 1, 7, '1', '2009-03-24 08:10:52'), (27, 1, 1, 1, '124,414', '2009-03-24 08:10:52');";
		$this->database->query($sql);
		
		$sql = "INSERT INTO `race_to_users` (`raceuser_race`, `raceuser_user`, `raceuser_order`, `raceuser_assessed`) VALUES ('1', '" . $playerid . "', '1', '0');";
		$this->database->query($sql);

		$sql = "INSERT INTO achievements (achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward, achievement_function) VALUES" ;
		$sql .= "('test1', 'test one', '', '1', '2', 'fastround1')";
		$this->database->query($sql);

		$sql = "INSERT INTO achievements (achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward, achievement_function) VALUES" ;
		$sql .= "('test2', 'test two', '', '1', '4', 'fastround2')";
		$this->database->query($sql);

		$sql = "INSERT INTO achievements (achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward, achievement_function) VALUES" ;
		$sql .= "('test3', 'test three', '', '1', '8', 'fastround3')";
		$this->database->query($sql);

		$ret = $achievements->assessAchievements();
		$this->assertTrue($ret);

		$sql = "SELECT * FROM `race_to_users`WHERE raceuser_user='" . $playerid . "' AND raceuser_assessed='0'";
		$res = $this->database->query($sql);
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);
		
		$achievementfunctions = new lrAchievementfunctions();
		$ret = $achievementfunctions->getPlayerAchievements();
		$this->assertEqual(count($ret), 2);
	}

}

?>
