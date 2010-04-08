<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');

class testGamesetup extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$gamesetup = new zgGamesetup();
		$this->assertNotNull($gamesetup);
		unset($gamesetup);
    }


	// Try to create a new component
	function test_createComponent()
	{
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');

		$componentname = uniqid();
		$componentdescription = uniqid();

		$componentid = $gamesetup->createComponent($componentname, $componentdescription);
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
		unset($gamesetup);
    }


	// Try to delete component
	function test_deleteComponent()
	{
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_components');

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $gamesetup->createComponent($componentname, $componentdescription);

		$ret = $gamesetup->deleteComponent($componentid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$res = $this->database->query("SELECT * FROM game_component_".$componentid);
		$this->assertFalse($res);

		$testfunctions->dropZeitgeistTable('game_components');

		unset($ret);
		unset($gamesetup);
    }


	// Try to create a new assemblage
	function test_createAssemblage()
	{
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_assemblages');

		$assemblagename = uniqid();
		$assemblagedescription = uniqid();

		$assemblageid = $gamesetup->createAssemblage($assemblagename, $assemblagedescription);
		$this->assertTrue($assemblageid);

		// check database
		$res = $this->database->query("SELECT * FROM game_assemblages");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['assemblage_id'], $assemblageid);
		$this->assertEqual($ret['assemblage_name'], $assemblagename);
		$this->assertEqual($ret['assemblage_description'], $assemblagedescription);

		$testfunctions->dropZeitgeistTable('game_assemblages');

		unset($ret);
		unset($gamesetup);
    }


	// Try to add a new component to an existing assemblage
	function test_addComponentToAssemblage()
	{
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_assemblages');
		$testfunctions->createZeitgeistTable('game_components');
		$testfunctions->createZeitgeistTable('game_assemblage_components');

		$assemblagename = uniqid();
		$assemblagedescription = uniqid();
		$assemblageid = $gamesetup->createAssemblage($assemblagename, $assemblagedescription);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $gamesetup->createComponent($componentname, $componentdescription);
		
		$ret = $gamesetup->addComponentToAssemblage($componentid, $assemblageid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_assemblage_components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['assemblagecomponent_assemblage'], $assemblageid);
		$this->assertEqual($ret['assemblagecomponent_component'], $componentid);

		$testfunctions->dropZeitgeistTable('game_component_'.$componentid);
		$testfunctions->dropZeitgeistTable('game_components');
		$testfunctions->dropZeitgeistTable('game_assemblages');
		$testfunctions->dropZeitgeistTable('game_assemblage_components');

		unset($ret);
		unset($gamesetup);
    }


	// Try to remove a component from an existing assemblage
	function test_removeComponentFromAssemblage()
	{
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_assemblages');
		$testfunctions->createZeitgeistTable('game_components');
		$testfunctions->createZeitgeistTable('game_assemblage_components');

		$assemblagename = uniqid();
		$assemblagedescription = uniqid();
		$assemblageid = $gamesetup->createAssemblage($assemblagename, $assemblagedescription);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $gamesetup->createComponent($componentname, $componentdescription);
		
		$ret = $gamesetup->addComponentToAssemblage($componentid, $assemblageid);

		$ret = $gamesetup->removeComponentFromAssemblage($componentid, $assemblageid);
		$this->assertTrue($ret);

		// check database
		$res = $this->database->query("SELECT * FROM game_assemblage_components");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		$testfunctions->dropZeitgeistTable('game_component_'.$componentid);
		$testfunctions->dropZeitgeistTable('game_components');
		$testfunctions->dropZeitgeistTable('game_assemblages');
		$testfunctions->dropZeitgeistTable('game_assemblage_components');

		unset($ret);
		unset($gamesetup);
    }

}

?>
