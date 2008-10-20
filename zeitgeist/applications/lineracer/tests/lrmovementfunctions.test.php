<?php

class testLrmovementfunctions extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();

		$movementfunctions = new lrMovementfunctions();
		$this->assertNotNull($movementfunctions);
		unset($movementfunctions);
    }
	
}

?>
