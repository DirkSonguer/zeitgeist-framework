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

		$sql = "INSERT INTO `achievements` (`achievement_id`, `achievement_name`, `achievement_description`, `achievement_image`, `achievement_level`, `achievement_reward`, `achievement_function`) VALUES";
		$sql .= "(1, 'test1', 'test one', '', 1, 2, 'fastround1')";
		$this->database->query($sql);

		$ret = $achievements->assessAchievements();
		$this->assertTrue($ret);
	}

}

?>
