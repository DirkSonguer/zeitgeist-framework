<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');

class testGamesystem extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$gamesystem = new zgGamesystem();
		$this->assertNotNull($gamesystem);
		unset($gamesystem);
    }


	// Try to create a new entity
	function test_createEntity()
	{
		$gamesystem = new zgGamesystem();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_entities');

		$entityId = $gamesystem->createEntity();
		$this->assertTrue($entityId);

		// check database
		$res = $this->database->query("SELECT * FROM game_entities");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['entity_id'], $entityId);

		$testfunctions->dropZeitgeistTable('game_entities');

		unset($ret);
		unset($gamesystem);
    }

}

?>
