<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');

class testEntitysystem extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$entitysystem = new zgEntitysystem();
		$this->assertNotNull($entitysystem);
		unset($entitysystem);
    }


	// Try to create a new entity without a name
	function test_createEntity_without_entityname()
	{
		$entitysystem = new zgEntitysystem();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('entities');

		$entityid = $entitysystem->createEntity();
		$this->assertTrue($entityid);

		// check database
		$res = $this->database->query("SELECT * FROM entities");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['entity_id'], $entityid);

		$testfunctions->dropZeitgeistTable('entities');

		unset($ret);
		unset($entitysystem);
    }


	// Try to create a new entity
	function test_createEntity_with_entityname()
	{
		$entitysystem = new zgEntitysystem();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('entities');

		$entityname = uniqid();

		$entityid = $entitysystem->createEntity($entityname);
		$this->assertTrue($entityid);

		// check database
		$res = $this->database->query("SELECT * FROM entities");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['entity_id'], $entityid);
		$this->assertEqual($ret['entity_name'], $entityname);

		$testfunctions->dropZeitgeistTable('entities');

		unset($ret);
		unset($entitysystem);
    }


	// Try to delete an existing entity
	function test_deleteEntity()
	{
		$entitysystem = new zgEntitysystem();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('entities');

		$entityid = $entitysystem->createEntity();
		$this->assertTrue($entityid);

		$ret = $entitysystem->deleteEntity($entityid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM entities");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$testfunctions->dropZeitgeistTable('entities');

		unset($ret);
		unset($entitysystem);
    }


	// Try to add a new component to an existing entity
	function test_addComponentToEntity()
	{
		$entitysystem = new zgEntitysystem();
		$entitysetup = new zgEntitysetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('components');
		$testfunctions->createZeitgeistTable('entities');
		$testfunctions->createZeitgeistTable('entity_components');

		$entityname = uniqid();
		$entityid = $entitysystem->createEntity($entityname);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $entitysetup->createComponent($componentname, $componentdescription);
		
		$ret = $entitysystem->addComponentToEntity($componentid, $entityid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM entity_components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$res = $this->database->query("SELECT * FROM component_".$componentid);
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$testfunctions->dropZeitgeistTable('component_'.$componentid);
		$testfunctions->dropZeitgeistTable('components');
		$testfunctions->dropZeitgeistTable('entities');
		$testfunctions->dropZeitgeistTable('entity_components');

		unset($ret);
		unset($entitysystem);
    }


	// Try to remove a component from an existing entity
	function test_removeComponentFromEntity()
	{
		$entitysystem = new zgEntitysystem();
		$entitysetup = new zgEntitysetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('components');
		$testfunctions->createZeitgeistTable('entities');
		$testfunctions->createZeitgeistTable('entity_components');

		$entityname = uniqid();
		$entityid = $entitysystem->createEntity($entityname);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $entitysetup->createComponent($componentname, $componentdescription);
		
		$ret = $entitysystem->addComponentToEntity($componentid, $entityid);

		$ret = $entitysystem->removeComponentFromEntity($componentid, $entityid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM entity_components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM component_".$componentid);
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$testfunctions->dropZeitgeistTable('component_'.$componentid);
		$testfunctions->dropZeitgeistTable('components');
		$testfunctions->dropZeitgeistTable('entities');
		$testfunctions->dropZeitgeistTable('entity_components');

		unset($ret);
		unset($entitysystem);
    }


	// Try adding data to a component
	function test_setComponentData()
	{
		$entitysystem = new zgEntitysystem();
		$entitysetup = new zgEntitysetup();
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('components');
		$testfunctions->createZeitgeistTable('entities');
		$testfunctions->createZeitgeistTable('entity_components');

		$entityname = uniqid();
		$entityid = $entitysystem->createEntity($entityname);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $entitysetup->createComponent($componentname, $componentdescription);
		
		$componentdataid = $entitysystem->addComponentToEntity($componentid, $entityid);
		$res = $this->database->query("ALTER TABLE `component_".$componentid."` ADD `testdata1` VARCHAR( 16 ) NOT NULL ");
		$res = $this->database->query("ALTER TABLE `component_".$componentid."` ADD `testdata2` VARCHAR( 16 ) NOT NULL ");
		$this->assertTrue($res);

		$testdata = array();
		$testdata1 = uniqid();
		$testdata2 = uniqid();
		$testdata['testdata1'] = $testdata1;
		$testdata['testdata2'] = $testdata2;

		$ret = $entitysystem->setComponentData($componentid, $entityid, $testdata);
		$this->assertTrue($res);

		$res = $this->database->query("SELECT * FROM component_".$componentid);
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['id'], $componentdataid);
		$this->assertEqual($ret['testdata1'], $testdata1);
		$this->assertEqual($ret['testdata2'], $testdata2);

		$testfunctions->dropZeitgeistTable('component_'.$componentid);
		$testfunctions->dropZeitgeistTable('components');
		$testfunctions->dropZeitgeistTable('entities');
		$testfunctions->dropZeitgeistTable('entity_components');

		unset($ret);
		unset($entitysystem);
    }


	// Try getting data from a component
	function test_getComponentData()
	{
		$entitysystem = new zgEntitysystem();
		$entitysetup = new zgEntitysetup();
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('components');
		$testfunctions->createZeitgeistTable('entities');
		$testfunctions->createZeitgeistTable('entity_components');

		$entityname = uniqid();
		$entityid = $entitysystem->createEntity($entityname);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $entitysetup->createComponent($componentname, $componentdescription);
		
		$componentdataid = $entitysystem->addComponentToEntity($componentid, $entityid);
		$res = $this->database->query("ALTER TABLE `component_".$componentid."` ADD `testdata1` VARCHAR( 16 ) NOT NULL ");
		$res = $this->database->query("ALTER TABLE `component_".$componentid."` ADD `testdata2` VARCHAR( 16 ) NOT NULL ");
		$this->assertTrue($res);

		$testdata = array();
		$testdata1 = uniqid();
		$testdata2 = uniqid();
		$testdata['testdata1'] = $testdata1;
		$testdata['testdata2'] = $testdata2;

		$ret = $entitysystem->setComponentData($componentid, $entityid, $testdata);

		$ret = $entitysystem->getComponentData($componentid, $entityid);
		$this->assertEqual($ret['id'], $componentdataid);
		$this->assertEqual($ret['testdata1'], $testdata1);
		$this->assertEqual($ret['testdata2'], $testdata2);

		$testfunctions->dropZeitgeistTable('component_'.$componentid);
		$testfunctions->dropZeitgeistTable('components');
		$testfunctions->dropZeitgeistTable('entities');
		$testfunctions->dropZeitgeistTable('entity_components');

		unset($ret);
		unset($entitysystem);
    }


	// Try getting data from a component
	function test_getComponentListForEntity()
	{
		$entitysystem = new zgEntitysystem();
		$entitysetup = new zgEntitysetup();
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('components');
		$testfunctions->createZeitgeistTable('entities');
		$testfunctions->createZeitgeistTable('entity_components');

		$entityname = uniqid();
		$entityid = $entitysystem->createEntity($entityname);

		$componentname1 = uniqid();
		$componentdescription1 = uniqid();
		$componentid1 = $entitysetup->createComponent($componentname1, $componentdescription1);
		$componentdataid1 = $entitysystem->addComponentToEntity($componentid1, $entityid);

		$componentname2 = uniqid();
		$componentdescription2 = uniqid();
		$componentid2 = $entitysetup->createComponent($componentname2, $componentdescription2);
		$componentdataid2 = $entitysystem->addComponentToEntity($componentid2, $entityid);

		$componentlist = $entitysystem->getComponentListForEntity($entityid);
		$this->assertTrue($componentlist);
		$this->assertTrue($componentlist[$componentid1]);
		$this->assertTrue($componentlist[$componentid2]);

		$testfunctions->dropZeitgeistTable('component_'.$componentid1);
		$testfunctions->dropZeitgeistTable('component_'.$componentid2);
		$testfunctions->dropZeitgeistTable('components');
		$testfunctions->dropZeitgeistTable('entities');
		$testfunctions->dropZeitgeistTable('entity_components');

		unset($ret);
		unset($entitysystem);
    }

}

?>
