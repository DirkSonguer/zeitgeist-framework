<?php

class testLrmovementfunctions extends UnitTestCase
{
	public $database;
	public $miscfunctions;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
		$this->miscfunctions  = new miscFunctions();

		$movementfunctions = new lrMovementfunctions();
		$this->assertNotNull($movementfunctions);
		unset($movementfunctions);
    }

	
	function test_validateMove()
	{
		$gamestates = new lrGamestates();
		$movementfunctions = new lrMovementfunctions();
		
		$this->miscfunctions->setupGame();
		
		$gamestates->loadGamestates(1);
		
		$ret = $movementfunctions->validateMove(1,1);
		$this->assertFalse($ret);

		$ret = $movementfunctions->validateMove(160,370);
		$this->assertTrue($ret);
	}
	
	
	function test_getMovement()
	{
		$gamestates = new lrGamestates();
		$movementfunctions = new lrMovementfunctions();
		$gameeventhandler = new lrGameeventhandler();
		
		$this->miscfunctions->setupGame();

		$gamestates->loadGamestates(1);
		$gameeventhandler->saveRaceaction('1', '150,200');
		$gameeventhandler->saveRaceaction('1', '170,200');
		$gameeventhandler->saveRaceaction('1', '190,200');
		$gameeventhandler->saveRaceaction('1', '210,200');
		$gamestates->loadGamestates(1);

		$ret = $movementfunctions->getMovement(1, -1);
		$this->assertEqual($ret[0], '210');
		$this->assertEqual($ret[1], '200');

		$ret = $movementfunctions->getMovement(1);
		$this->assertEqual($ret[1][0], '155');
		$this->assertEqual($ret[1][1], '380');
	}	


	function test_validateTerrain()
	{
		$gamestates = new lrGamestates();
		$movementfunctions = new lrMovementfunctions();
		
		$this->miscfunctions->setupGame();

		$gamestates->loadGamestates(1);

		$ret = $movementfunctions->validateTerrain(150,10);
		$this->assertEqual($ret[0], '150');
		$this->assertEqual($ret[1], '49');

		$ret = $movementfunctions->validateTerrain(150,610);
		$this->assertEqual($ret[0], '150');
		$this->assertEqual($ret[1], '596');

		$ret = $movementfunctions->validateTerrain(50,370);
		$this->assertEqual($ret[0], '124');
		$this->assertEqual($ret[1], '377');

		$ret = $movementfunctions->validateTerrain(250,370);
		$this->assertEqual($ret[0], '250');
		$this->assertEqual($ret[1], '370');
	}	
}

?>
