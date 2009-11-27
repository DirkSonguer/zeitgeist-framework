<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');

class testEntitysetup extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$entitysetup = new zgEntitysetup();
		$this->assertNotNull($entitysetup);
		unset($entitysetup);
    }


	// Try to create a new component
	function test_createComponent()
	{
		$entitysetup = new zgEntitysetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('components');

		$componentname = uniqid();
		$componentdescription = uniqid();

		$componentid = $entitysetup->createComponent($componentname, $componentdescription);
		$this->assertTrue($componentid);

		// check database
		$res = $this->database->query("SELECT * FROM components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['component_id'], $componentid);
		$this->assertEqual($ret['component_name'], $componentname);
		$this->assertEqual($ret['component_description'], $componentdescription);

		// check database
		$res = $this->database->query("SELECT * FROM component_".$componentid);
		$this->assertTrue($res);

		$testfunctions->dropZeitgeistTable('component_'.$componentid);
		$testfunctions->dropZeitgeistTable('components');

		unset($ret);
		unset($entitysetup);
    }


	// Try to delete component
	function test_deleteComponent()
	{
		$entitysetup = new zgEntitysetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('components');

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $entitysetup->createComponent($componentname, $componentdescription);

		$ret = $entitysetup->deleteComponent($componentid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM component_".$componentid);
		$this->assertFalse($res);

		$testfunctions->dropZeitgeistTable('components');

		unset($ret);
		unset($entitysetup);
    }


	// Try to create a new assemblage
	function test_createAssemblage()
	{
		$entitysetup = new zgEntitysetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('assemblages');

		$assemblagename = uniqid();
		$assemblagedescription = uniqid();

		$assemblageid = $entitysetup->createAssemblage($assemblagename, $assemblagedescription);
		$this->assertTrue($assemblageid);

		// check database
		$res = $this->database->query("SELECT * FROM assemblages");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['assemblage_id'], $assemblageid);
		$this->assertEqual($ret['assemblage_name'], $assemblagename);
		$this->assertEqual($ret['assemblage_description'], $assemblagedescription);

		$testfunctions->dropZeitgeistTable('assemblages');

		unset($ret);
		unset($entitysetup);
    }


	// Try to add a new component to an existing assemblage
	function test_addComponentToAssemblage()
	{
		$entitysetup = new zgEntitysetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('assemblages');
		$testfunctions->createZeitgeistTable('components');
		$testfunctions->createZeitgeistTable('assemblage_components');

		$assemblagename = uniqid();
		$assemblagedescription = uniqid();
		$assemblageid = $entitysetup->createAssemblage($assemblagename, $assemblagedescription);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $entitysetup->createComponent($componentname, $componentdescription);
		
		$ret = $entitysetup->addComponentToAssemblage($componentid, $assemblageid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM assemblage_components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['assemblagecomponent_assemblage'], $assemblageid);
		$this->assertEqual($ret['assemblagecomponent_component'], $componentid);

		$testfunctions->dropZeitgeistTable('component_'.$componentid);
		$testfunctions->dropZeitgeistTable('components');
		$testfunctions->dropZeitgeistTable('assemblages');
		$testfunctions->dropZeitgeistTable('assemblage_components');

		unset($ret);
		unset($entitysystem);
    }

}

?>
