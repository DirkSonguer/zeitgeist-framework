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

		$achievement1 = rand(100,300);
		$achievement2 = rand(300,500);
		$playerid = rand(501,1000);

		$session = zgSession::init();
		$session->setSessionVariable('user_userid', $playerid);

		$this->database->query('TRUNCATE TABLE achievements');
		$this->database->query('TRUNCATE TABLE achievements_to_users');

		$ret = $achievements->getPlayerAchievements();
		$this->assertEqual(count($ret), 0);

		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement1 . "', 'test1', 'achievement1', '', '1', '2')");
		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement2 . "', 'test2', 'achievement2', '', '1', '2')");
		$this->database->query("INSERT INTO achievements_to_users(userachievement_user, userachievement_achievement, userachievement_race) VALUES('" . $playerid . "', '" . $achievement1 . "', '5')");
		$ret = $achievements->getPlayerAchievements();
		$this->assertEqual(count($ret), 1);

		$this->database->query("INSERT INTO achievements_to_users(userachievement_user, userachievement_achievement, userachievement_race) VALUES('" . $playerid . "', '" . $achievement2 . "', '5')");
		$ret = $achievements->getPlayerAchievements();
		$this->assertEqual(count($ret), 2);
	}
	
	function test_hasAchievement()
	{
		$achievements = new lrAchievements();

		$achievement1 = rand(100,300);
		$achievement2 = rand(300,500);
		$playerid = rand(501,1000);

		$session = zgSession::init();
		$session->setSessionVariable('user_userid', $playerid);

		$this->database->query('TRUNCATE TABLE achievements');
		$this->database->query('TRUNCATE TABLE achievements_to_users');

		$ret = $achievements->getPlayerAchievements();
		$this->assertEqual(count($ret), 0);

		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement1 . "', 'test1', 'achievement1', '', '1', '2')");
		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement2 . "', 'test2', 'achievement2', '', '1', '2')");
		$this->database->query("INSERT INTO achievements_to_users(userachievement_user, userachievement_achievement, userachievement_race) VALUES('" . $playerid . "', '" . $achievement1 . "', '5')");
		$ret = $achievements->hasAchievement($achievement1);
		$this->assertTrue($ret);

		$ret = $achievements->hasAchievement($achievement2);
		$this->assertFalse($ret);
	}
	
	function test_addAchievement()
	{
		$achievements = new lrAchievements();

		$achievement1 = rand(100,300);
		$achievement2 = rand(300,500);
		$playerid = rand(501,1000);

		$session = zgSession::init();
		$session->setSessionVariable('user_userid', $playerid);

		$this->database->query('TRUNCATE TABLE achievements');
		$this->database->query('TRUNCATE TABLE achievements_to_users');

		$ret = $achievements->getPlayerAchievements();
		$this->assertEqual(count($ret), 0);

		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement1 . "', 'test1', 'achievement1', '', '1', '2')");
		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement2 . "', 'test2', 'achievement2', '', '1', '2')");

		$achievements->addAchievement($achievement1);
		$ret = $achievements->hasAchievement($achievement1);
		$this->assertTrue($ret);

		$achievements->addAchievement($achievement2, 5);
		$ret = $achievements->hasAchievement($achievement2);
		$this->assertTrue($ret);
	}	
}

?>
