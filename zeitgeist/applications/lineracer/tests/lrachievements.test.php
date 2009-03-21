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

		$ret = $achievements->assessAchievements();
		$this->assertTrue($ret);
	}

}

?>
