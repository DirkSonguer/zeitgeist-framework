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

}

?>
