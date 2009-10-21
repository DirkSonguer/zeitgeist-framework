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
		$randomid = uniqid();

		$ret = $configuration->loadConfiguration($randomid, 'false');
		$this->assertFalse($ret);

		unset($ret);
		unset($configuration);
	}


	// Load a configuration from an existing and valid file
	function test_loadConfiguration_success()
	{
		$configuration = zgConfiguration::init();
		$randomid = uniqid();
		
		$ret = $configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');
		$this->assertTrue($ret);

		unset($ret);
		unset($configuration);
	}


	// Load a configuration from an existing file but into an already defined ID
	function test_loadConfiguration_namecollision()
	{
		$configuration = zgConfiguration::init();
		$randomid = uniqid();

		$configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');
		$ret = $configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');
		$this->assertFalse($ret);

		unset($ret);
		unset($configuration);
	}


	// Reload a configuration from an existing file into an already defined ID
	function test_loadConfiguration_forceoverwrite()
	{
		$configuration = zgConfiguration::init();
		$randomid = uniqid();

		$configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');
		$ret = $configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini', true);
		$this->assertTrue($ret);

		unset($ret);
		unset($configuration);
	}
	
	
	// Check if the contents of the test configuration are ok
	function test_getConfiguration_correctitems()
	{
		$configuration = zgConfiguration::init();
		$randomid = uniqid();

		$ret = $configuration->loadConfiguration($randomid, ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testconfiguration.ini');

		$testblock1 = array('testvar1' => 'true', 'testvar2' => '1', 'testvar3' => 'test3');
		$testblock2 = array('testvar4' => 'false', 'testvar5' => '2', 'testvar6' => 'test4');
		$testconfiguration['testblock1'] = $testblock1;
		$testconfiguration['testblock2'] = $testblock2;

		$ret = $configuration->getConfiguration($randomid);
		$this->assertEqual($ret, $testconfiguration);
		
		unset($ret);
		unset($configuration);
	}

}

?>
