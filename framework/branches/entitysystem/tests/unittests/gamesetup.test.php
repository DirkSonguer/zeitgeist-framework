<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');

class testGamesetup extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$gamedata = new zgGamedata();
		$this->assertNotNull($gamedata);
		unset($gamedata);
    }


	// Try to create a new entity without a name
	function test_createEntity_without_entityname()
	{
		$gamedata = new zgGamedata();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_entities');

		$entityid = $gamedata->createEntity();
		$this->assertTrue($entityid);

		// check database
		$res = $this->database->query("SELECT * FROM game_entities");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['entity_id'], $entityid);

		$testfunctions->dropZeitgeistTable('game_entities');

		unset($ret);
		unset($gamedata);
    }


	// Try to create a new entity
	function test_createEntity_with_entityname()
	{
		$gamedata = new zgGamedata();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_entities');

		$entityname = uniqid();

		$entityid = $gamedata->createEntity($entityname);
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
		unset($gamedata);
    }


	// Try to delete an existing entity
	function test_deleteEntity()
	{
		$gamedata = new zgGamedata();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_entities');

		$entityid = $gamedata->createEntity();
		$this->assertTrue($entityid);

		$ret = $gamedata->deleteEntity($entityid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_entities");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$testfunctions->dropZeitgeistTable('game_entities');

		unset($ret);
		unset($gamedata);
    }


	// Try to create a new component
	function test_createComponent()
	{
		$gamedata = new zgGamedata();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');

		$componentname = uniqid();
		$componentdescription = uniqid();

		$componentid = $gamedata->createComponent($componentname, $componentdescription);
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
		unset($gamedata);
    }


	// Try to delete component
	function test_deleteComponent()
	{
		$gamedata = new zgGamedata();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $gamedata->createComponent($componentname, $componentdescription);

		$ret = $gamedata->deleteComponent($componentid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM game_component_".$componentid);
		$this->assertFalse($res);

		$testfunctions->dropZeitgeistTable('game_components');

		unset($ret);
		unset($gamedata);
    }

}

?>
