<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');

class testGamedata extends UnitTestCase
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


	// Try to add a new component to an existing entity
	function test_addComponentToEntity()
	{
		$gamedata = new zgGamedata();
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');
		$testfunctions->createZeitgeistTable('game_entities');
		$testfunctions->createZeitgeistTable('game_entity_components');

		$entityname = uniqid();
		$entityid = $gamedata->createEntity($entityname);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $gamedata->createComponent($componentname, $componentdescription);
		
		$ret = $gamedata->addComponentToEntity($componentid, $entityid);
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
		unset($gamedata);
    }


	// Try to remove a component from an existing entity
	function test_removeComponentFromEntity()
	{
		$gamedata = new zgGamedata();
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');
		$testfunctions->createZeitgeistTable('game_entities');
		$testfunctions->createZeitgeistTable('game_entity_components');

		$entityname = uniqid();
		$entityid = $gamedata->createEntity($entityname);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $gamedata->createComponent($componentname, $componentdescription);
		
		$ret = $gamedata->addComponentToEntity($componentid, $entityid);

		$ret = $gamedata->removeComponentFromEntity($componentid, $entityid);
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
		unset($gamedata);
    }


	// Try adding data to a component
	function test_setComponentData()
	{
		$gamedata = new zgGamedata();
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');
		$testfunctions->createZeitgeistTable('game_entities');
		$testfunctions->createZeitgeistTable('game_entity_components');

		$entityname = uniqid();
		$entityid = $gamedata->createEntity($entityname);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $gamedata->createComponent($componentname, $componentdescription);
		
		$componentdataid = $gamedata->addComponentToEntity($componentid, $entityid);
		$res = $this->database->query("ALTER TABLE `game_component_".$componentid."` ADD `testdata1` VARCHAR( 16 ) NOT NULL ");
		$res = $this->database->query("ALTER TABLE `game_component_".$componentid."` ADD `testdata2` VARCHAR( 16 ) NOT NULL ");
		$this->assertTrue($res);

		$testdata = array();
		$testdata1 = uniqid();
		$testdata2 = uniqid();
		$testdata['testdata1'] = $testdata1;
		$testdata['testdata2'] = $testdata2;

		$ret = $gamedata->setComponentData($componentid, $entityid, $testdata);
		$this->assertTrue($res);

		$res = $this->database->query("SELECT * FROM game_component_".$componentid);
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['id'], $componentdataid);
		$this->assertEqual($ret['testdata1'], $testdata1);
		$this->assertEqual($ret['testdata2'], $testdata2);

		$testfunctions->dropZeitgeistTable('game_component_'.$componentid);
		$testfunctions->dropZeitgeistTable('game_components');
		$testfunctions->dropZeitgeistTable('game_entities');
		$testfunctions->dropZeitgeistTable('game_entity_components');

		unset($ret);
		unset($gamedata);
    }


	// Try getting data from a component
	function test_getComponentData()
	{
		$gamedata = new zgGamedata();
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');
		$testfunctions->createZeitgeistTable('game_entities');
		$testfunctions->createZeitgeistTable('game_entity_components');

		$entityname = uniqid();
		$entityid = $gamedata->createEntity($entityname);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $gamedata->createComponent($componentname, $componentdescription);
		
		$componentdataid = $gamedata->addComponentToEntity($componentid, $entityid);
		$res = $this->database->query("ALTER TABLE `game_component_".$componentid."` ADD `testdata1` VARCHAR( 16 ) NOT NULL ");
		$res = $this->database->query("ALTER TABLE `game_component_".$componentid."` ADD `testdata2` VARCHAR( 16 ) NOT NULL ");
		$this->assertTrue($res);

		$testdata = array();
		$testdata1 = uniqid();
		$testdata2 = uniqid();
		$testdata['testdata1'] = $testdata1;
		$testdata['testdata2'] = $testdata2;

		$ret = $gamedata->setComponentData($componentid, $entityid, $testdata);

		$ret = $gamedata->getComponentData($componentid, $entityid);
		$this->assertEqual($ret['id'], $componentdataid);
		$this->assertEqual($ret['testdata1'], $testdata1);
		$this->assertEqual($ret['testdata2'], $testdata2);

		$testfunctions->dropZeitgeistTable('game_component_'.$componentid);
		$testfunctions->dropZeitgeistTable('game_components');
		$testfunctions->dropZeitgeistTable('game_entities');
		$testfunctions->dropZeitgeistTable('game_entity_components');

		unset($ret);
		unset($gamedata);
    }


	// Try getting data from a component
	function test_getComponentListForEntity()
	{
		$gamedata = new zgGamedata();
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');
		$testfunctions->createZeitgeistTable('game_entities');
		$testfunctions->createZeitgeistTable('game_entity_components');

		$entityname = uniqid();
		$entityid = $gamedata->createEntity($entityname);

		$componentname1 = uniqid();
		$componentdescription1 = uniqid();
		$componentid1 = $gamedata->createComponent($componentname1, $componentdescription1);
		$componentdataid1 = $gamedata->addComponentToEntity($componentid1, $entityid);

		$componentname2 = uniqid();
		$componentdescription2 = uniqid();
		$componentid2 = $gamedata->createComponent($componentname2, $componentdescription2);
		$componentdataid2 = $gamedata->addComponentToEntity($componentid2, $entityid);

		$componentlist = $gamedata->getComponentListForEntity($entityid);
		$this->assertTrue($componentlist);
		$this->assertTrue($componentlist[$componentid1]);
		$this->assertTrue($componentlist[$componentid2]);

		$testfunctions->dropZeitgeistTable('game_component_'.$componentid1);
		$testfunctions->dropZeitgeistTable('game_component_'.$componentid2);
		$testfunctions->dropZeitgeistTable('game_components');
		$testfunctions->dropZeitgeistTable('game_entities');
		$testfunctions->dropZeitgeistTable('game_entity_components');

		unset($ret);
		unset($gamedata);
    }

}

?>
