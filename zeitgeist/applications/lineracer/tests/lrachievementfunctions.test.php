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

		$achievementfunctions = new lrAchievementfunctions();
		$this->assertNotNull($achievementfunctions);
		unset($achievementfunctions);
    }

	function test_getPlayerAchievements()
	{
		$achievementfunctions = new lrAchievementfunctions();

		$achievement1 = rand(100,300);
		$achievement2 = rand(300,500);
		$playerid = rand(501,1000);

		$session = zgSession::init();
		$session->setSessionVariable('user_userid', $playerid);

		$this->database->query('TRUNCATE TABLE achievements');
		$this->database->query('TRUNCATE TABLE achievements_to_users');

		$ret = $achievementfunctions->getPlayerAchievements();
		$this->assertEqual(count($ret), 0);

		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement1 . "', 'test1', 'achievement1', '', '1', '2')");
		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement2 . "', 'test2', 'achievement2', '', '1', '2')");
		$this->database->query("INSERT INTO achievements_to_users(userachievement_user, userachievement_achievement, userachievement_race) VALUES('" . $playerid . "', '" . $achievement1 . "', '5')");
		$ret = $achievementfunctions->getPlayerAchievements();
		$this->assertEqual(count($ret), 1);

		$this->database->query("INSERT INTO achievements_to_users(userachievement_user, userachievement_achievement, userachievement_race) VALUES('" . $playerid . "', '" . $achievement2 . "', '5')");
		$ret = $achievementfunctions->getPlayerAchievements();
		$this->assertEqual(count($ret), 2);
	}
	
	function test_hasAchievement()
	{
		$achievementfunctions = new lrAchievementfunctions();

		$achievement1 = rand(100,300);
		$achievement2 = rand(300,500);
		$playerid = rand(501,1000);

		$session = zgSession::init();
		$session->setSessionVariable('user_userid', $playerid);

		$this->database->query('TRUNCATE TABLE achievements');
		$this->database->query('TRUNCATE TABLE achievements_to_users');

		$ret = $achievementfunctions->getPlayerAchievements();
		$this->assertEqual(count($ret), 0);

		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement1 . "', 'test1', 'achievement1', '', '1', '2')");
		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement2 . "', 'test2', 'achievement2', '', '1', '2')");
		$this->database->query("INSERT INTO achievements_to_users(userachievement_user, userachievement_achievement, userachievement_race) VALUES('" . $playerid . "', '" . $achievement1 . "', '5')");
		$ret = $achievementfunctions->hasAchievement($achievement1);
		$this->assertTrue($ret);

		$ret = $achievementfunctions->hasAchievement($achievement2);
		$this->assertFalse($ret);
	}
	
	function test_addAchievement()
	{
		$achievementfunctions = new lrAchievementfunctions();

		$achievement1 = rand(100,300);
		$achievement2 = rand(300,500);
		$playerid = rand(501,1000);

		$session = zgSession::init();
		$session->setSessionVariable('user_userid', $playerid);

		$this->database->query('TRUNCATE TABLE achievements');
		$this->database->query('TRUNCATE TABLE achievements_to_users');

		$ret = $achievementfunctions->getPlayerAchievements();
		$this->assertEqual(count($ret), 0);

		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement1 . "', 'test1', 'achievement1', '', '1', '2')");
		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement2 . "', 'test2', 'achievement2', '', '1', '2')");

		$achievementfunctions->addAchievement($achievement1);
		$ret = $achievementfunctions->hasAchievement($achievement1);
		$this->assertTrue($ret);

		$ret = $achievementfunctions->getPlayerAchievements();
		$this->assertEqual(count($ret), 1);

		$achievementfunctions->addAchievement($achievement1);
		$ret = $achievementfunctions->getPlayerAchievements();
		$this->assertEqual(count($ret), 1);

		$achievementfunctions->addAchievement($achievement2, 5);
		$ret = $achievementfunctions->hasAchievement($achievement2);
		$this->assertTrue($ret);

		$ret = $achievementfunctions->getPlayerAchievements();
		$this->assertEqual(count($ret), 2);
	}	
}

?>
