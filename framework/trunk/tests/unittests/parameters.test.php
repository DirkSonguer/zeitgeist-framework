<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');

class testParameters extends UnitTestCase
{
	
	function test_init()
	{
		$parameters = new zgParameters();
		$this->assertNotNull($parameters);
		unset($parameters);
    }


	// Test regexp parameter non-escaped with valid parameter
	function test_getSafeParameters_regexp_valid()
	{
		$_GET['test_regexp'] = '1234';
		$parameters = new zgParameters();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameters->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNotNull($ret['test_regexp']);
		$this->assertEqual($ret['test_regexp'], '1234');

		$objects = zgObjects::init();
		$objects->deleteAllObjects();
		unset($ret);
		unset($parameters);
    }


	// Test regexp parameter non-escaped with invalid parameter
	function test_getSafeParameters_regexp_invalid()
	{
		$_GET['test_regexp'] = '12';
		$parameters = new zgParameters();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameters->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNull($ret['test_regexp']);

		$objects = zgObjects::init();
		$objects->deleteAllObjects();
		unset($ret);
		unset($parameters);
    }


	// Test regexp parameter escaped with valid parameter
	function test_getSafeParameters_regexp_escaped_valid()
	{
		$_GET['test_regexp_escaped'] = "12'34";
		$parameters = new zgParameters();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameters->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNotNull($ret['test_regexp_escaped']);
		$this->assertEqual($ret['test_regexp_escaped'], "12\'34");

		$objects = zgObjects::init();
		$objects->deleteAllObjects();
		unset($ret);
		unset($parameters);
    }


	// Test regexp parameter escaped with invalid parameter
	function test_getSafeParameters_regexp_escaped_invalid()
	{
		$_GET['test_regexp_escaped'] = '12';
		$parameters = new zgParameters();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameters->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNull($ret['test_regexp_escaped']);

		$objects = zgObjects::init();
		$objects->deleteAllObjects();
		unset($ret);
		unset($parameters);
    }


	// Test text parameter with valid parameter
	function test_getSafeParameters_text_valid()
	{
		$_GET['test_text'] = "This is a test";
		$parameters = new zgParameters();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameters->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNotNull($ret['test_text']);
		$this->assertEqual($ret['test_text'], "This is a test");

		$objects = zgObjects::init();
		$objects->deleteAllObjects();
		unset($ret);
		unset($parameters);
    }


	// Test text parameter with invalid parameter
	function test_getSafeParameters_text_invalid()
	{
		$_GET['test_text'] = "Illegal chars: ';";
		$parameters = new zgParameters();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameters->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNull($ret['test_text']);

		$objects = zgObjects::init();
		$objects->deleteAllObjects();
		unset($ret);
		unset($parameters);
    }


	// Test string parameter with valid parameter
	function test_getSafeParameters_string_valid()
	{
		$_GET['test_string'] = "This is a test";
		$parameters = new zgParameters();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameters->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNotNull($ret['test_string']);
		$this->assertEqual($ret['test_string'], "This is a test");

		$objects = zgObjects::init();
		$objects->deleteAllObjects();
		unset($ret);
		unset($parameters);
    }


	// Test string parameter with invalid parameter
	function test_getSafeParameters_string_invalid()
	{
		$_GET['test_string'] = "Illegal chars: \n\t";
		$parameters = new zgParameters();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameters->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNull($ret['test_string']);

		$objects = zgObjects::init();
		$objects->deleteAllObjects();
		unset($ret);
		unset($parameters);
    }

	// Test date parameter with valid parameter
	function test_getSafeParameters_date_valid()
	{
		$_GET['test_date'] = "01.01.1970";
		$parameters = new zgParameters();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameters->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNotNull($ret['test_date']);
		$this->assertEqual($ret['test_date'], "01.01.1970");

		$objects = zgObjects::init();
		$objects->deleteAllObjects();
		unset($ret);
		unset($parameters);
    }


	// Test string parameter escaped with invalid parameter
	function test_getSafeParameters_date_invalid()
	{
		$_GET['test_date'] = "001.001.1970";
		$parameters = new zgParameters();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameters->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNull($ret['test_date']);

		$objects = zgObjects::init();
		$objects->deleteAllObjects();
		unset($ret);
		unset($parameters);
    }

}

?>
