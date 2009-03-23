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
		$sql .= "(1, 'test1', 'test one', '', 1, 2, 'fastround1'),";
		$sql .= "(2, 'test2', 'test two', '', 2, 4, 'fastround2'),";
		$sql .= "(3, 'test3', 'test three', '', 3, 8, 'fastround3')";
		$this->database->query($sql);

		$ret = $achievements->assessAchievements();
		$this->assertTrue($ret);
	}

}

?>
