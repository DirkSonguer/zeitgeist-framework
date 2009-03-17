<?php

class testLrachievementfunctions extends UnitTestCase
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

	function test_getPlayerAchievements()
	{
		$achievements = new lrAchievements();

		$achievementid = rand(100,500);
		$playerid = rand(501,1000);

		$this->database->query('TRUNCATE TABLE achievements');
		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward') VALUES('" . $achievementid . "', 'testachievement', 'achievement', '', '1', '2')");
		$this->database->query('TRUNCATE TABLE achievements_to_users');
		$this->database->query("INSERT INTO achievements_to_users(userachievement_user, userachievement_achievement, userachievement_race) VALUES('" . $achievementid . "', '" . $playerid . "', '5')");

		$ret = $achievements->getPlayerAchievements();
		$this->assertEqual(count($ret), 1);		
		
	}
}

?>
