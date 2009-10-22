<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');
	
class testConfiguration extends UnitTestCase
{

	function test_init()
	{
		$configuration = zgConfiguration::init();
		$this->assertNotNull($configuration);
		unset($configuration);
    }


	// Try to load a configuration from a non existant file
	function test_loadConfiguration_wrongfilename()
	{
		$configuration = zgConfiguration::init();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('configurationcache');
		$randomid = uniqid();

		$ret = $configuration->loadConfiguration($randomid, 'false');
		$this->assertFalse($ret);

		$testfunctions->dropZeitgeistTable('configurationcache');
		unset($ret);
		unset($configuration);
	}


	// Load a configuration without database
	function test_loadConfiguration_without_database()
	{
		$configuration = zgConfiguration::init();
		$randomid = uniqid();
		
		$ret = $configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');
		$this->assertTrue($ret);

		unset($ret);
		unset($configuration);
	}



	// Load a configuration from an existing and valid file
	function test_loadConfiguration_success()
	{
		$configuration = zgConfiguration::init();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('configurationcache');
		$randomid = uniqid();
		
		$ret = $configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');
		$this->assertTrue($ret);

		$testfunctions->dropZeitgeistTable('configurationcache');
		unset($ret);
		unset($configuration);
	}


	// Load a configuration from an existing file but into an already defined ID
	function test_loadConfiguration_namecollision()
	{
		$configuration = zgConfiguration::init();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('configurationcache');
		$randomid = uniqid();

		$configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');
		$ret = $configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');
		$this->assertFalse($ret);

		$testfunctions->dropZeitgeistTable('configurationcache');
		unset($ret);
		unset($configuration);
	}


	// Reload a configuration from an existing file into an already defined ID
	function test_loadConfiguration_forceoverwrite()
	{
		$configuration = zgConfiguration::init();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('configurationcache');
		$randomid = uniqid();

		$configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');
		$ret = $configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini', true);
		$this->assertTrue($ret);

		$testfunctions->dropZeitgeistTable('configurationcache');
		unset($ret);
		unset($configuration);
	}


	// Get the contents of the configuration
	function test_getConfiguration_getcompleteconfiguration()
	{
		$configuration = zgConfiguration::init();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('configurationcache');
		$randomid = uniqid();

		$ret = $configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');

		$testblock1 = array('testvar1' => 'true', 'testvar2' => '1', 'testvar3' => 'test3');
		$testblock2 = array('testvar4' => 'false', 'testvar5' => '2', 'testvar6' => '1');
		$testconfiguration['testblock1'] = $testblock1;
		$testconfiguration['testblock2'] = $testblock2;

		$ret = $configuration->getConfiguration($randomid);
		$this->assertEqual($ret, $testconfiguration);
		
		$testfunctions->dropZeitgeistTable('configurationcache');
		unset($ret);
		unset($configuration);
	}


	// Get the contents of the configuration blocks
	function test_getConfiguration_getblock()
	{
		$configuration = zgConfiguration::init();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('configurationcache');
		$randomid = uniqid();

		$ret = $configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');
		$testblock1 = array('testvar1' => 'true', 'testvar2' => '1', 'testvar3' => 'test3');

		$ret = $configuration->getConfiguration($randomid, 'testblock1');
		$this->assertEqual($ret, $testblock1);
		
		$testfunctions->dropZeitgeistTable('configurationcache');
		unset($ret);
		unset($configuration);
	}
	

	// Get the contents of a configuration variable
	function test_getConfiguration_getvariable()
	{
		$configuration = zgConfiguration::init();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('configurationcache');
		$randomid = uniqid();

		$ret = $configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');

		$ret = $configuration->getConfiguration($randomid, 'testblock1', 'testvar2');
		$this->assertEqual($ret, '1');
		
		$testfunctions->dropZeitgeistTable('configurationcache');
		unset($ret);
		unset($configuration);
	}
	

	// Get the contents of a referenced configuration variable
	function test_getConfiguration_getreferencedvariable()
	{
		$configuration = zgConfiguration::init();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('configurationcache');
		$randomid = uniqid();

		$ret = $configuration->loadConfiguration('testconfiguration', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');

		$ret = $configuration->getConfiguration('testconfiguration', 'testblock2', 'testvar6');
		$this->assertEqual($ret, '1');
		
		$testfunctions->dropZeitgeistTable('configurationcache');
		unset($ret);
		unset($configuration);
	}

}

?>
