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


	// Try to create a new entity without a name
	function test_createEntity_without_entityname()
	{
		$gamesystem = new zgGamesystem();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_entities');

		$entityid = $gamesystem->createEntity();
		$this->assertTrue($entityid);

		// check database
		$res = $this->database->query("SELECT * FROM game_entities");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['entity_id'], $entityid);

		$testfunctions->dropZeitgeistTable('game_entities');

		unset($ret);
		unset($gamesystem);
    }


	// Try to create a new entity
	function test_createEntity_with_entityname()
	{
		$gamesystem = new zgGamesystem();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_entities');

		$entityname = uniqid();

		$entityid = $gamesystem->createEntity($entityname);
		$this->assertTrue($entityid);

		// check database
		$res = $this->database->query("SELECT * FROM game_entities");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['entity_id'], $entityid);
		$this->assertEqual($ret['entity_name'], $entityname);

		$testfunctions->dropZeitgeistTable('game_entities');

		unset($ret);
		unset($gamesystem);
    }


	// Try to delete an existing entity
	function test_deleteEntity()
	{
		$gamesystem = new zgGamesystem();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_entities');

		$entityid = $gamesystem->createEntity();
		$this->assertTrue($entityid);

		$ret = $gamesystem->deleteEntity($entityid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_entities");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$testfunctions->dropZeitgeistTable('game_entities');

		unset($ret);
		unset($gamesystem);
    }


	// Try to create a new component
	function test_createComponent()
	{
		$gamesystem = new zgGamesystem();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');

		$componentname = uniqid();
		$componentdescription = uniqid();

		$componentid = $gamesystem->createComponent($componentname, $componentdescription);
		$this->assertTrue($componentid);

		// check database
		$res = $this->database->query("SELECT * FROM game_components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['component_id'], $componentid);
		$this->assertEqual($ret['component_name'], $componentname);
		$this->assertEqual($ret['component_description'], $componentdescription);

		// check database
		$res = $this->database->query("SELECT * FROM game_component_".$componentid);
		$this->assertTrue($res);

		$testfunctions->dropZeitgeistTable('game_component_'.$componentid);
		$testfunctions->dropZeitgeistTable('game_components');

		unset($ret);
		unset($gamesystem);
    }


	// Try to delete component
	function test_deleteComponent()
	{
		$gamesystem = new zgGamesystem();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $gamesystem->createComponent($componentname, $componentdescription);

		$ret = $gamesystem->deleteComponent($componentid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM game_component_".$componentid);
		$this->assertFalse($res);

		$testfunctions->dropZeitgeistTable('game_components');

		unset($ret);
		unset($gamesystem);
    }


	// Try to create a new component to an existing entity
	function test_addComponentToEntity()
	{
		$gamesystem = new zgGamesystem();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');
		$testfunctions->createZeitgeistTable('game_entities');
		$testfunctions->createZeitgeistTable('game_entity_components');

		$entityname = uniqid();
		$entityid = $gamesystem->createEntity($entityname);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $gamesystem->createComponent($componentname, $componentdescription);
		
		$ret = $gamesystem->addComponentToEntity($componentid, $entityid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_entity_components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$res = $this->database->query("SELECT * FROM game_component_".$componentid);
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$testfunctions->dropZeitgeistTable('game_component_'.$componentid);
		$testfunctions->dropZeitgeistTable('game_components');
		$testfunctions->dropZeitgeistTable('game_entities');
		$testfunctions->dropZeitgeistTable('game_entity_components');

		unset($ret);
		unset($gamesystem);
    }


	// Try to remove a component from an existing entity
	function test_removeComponentFromEntity()
	{
		$gamesystem = new zgGamesystem();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');
		$testfunctions->createZeitgeistTable('game_entities');
		$testfunctions->createZeitgeistTable('game_entity_components');

		$entityname = uniqid();
		$entityid = $gamesystem->createEntity($entityname);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $gamesystem->createComponent($componentname, $componentdescription);
		
		$ret = $gamesystem->addComponentToEntity($componentid, $entityid);

		$ret = $gamesystem->removeComponentFromEntity($componentid, $entityid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_entity_components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM game_component_".$componentid);
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$testfunctions->dropZeitgeistTable('game_component_'.$componentid);
		$testfunctions->dropZeitgeistTable('game_components');
		$testfunctions->dropZeitgeistTable('game_entities');
		$testfunctions->dropZeitgeistTable('game_entity_components');

		unset($ret);
		unset($gamesystem);
    }

}

?>
