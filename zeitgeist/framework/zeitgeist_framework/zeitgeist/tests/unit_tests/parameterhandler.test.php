<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../test_configuration.php');

class testParameterhandler extends UnitTestCase
{
	
	function test_init()
	{
		$parameterhandler = new zgParameterhandler();
		$this->assertNotNull($parameterhandler);
		unset($parameterhandler);
    }


	// Test regexp parameter non-escaped with valid parameter
	function test_getSafeParameters_regexp_valid()
	{
		$_GET['test_regexp'] = '1234';
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNotNull($ret['test_regexp']);
		$this->assertEqual($ret['test_regexp'], '1234');

		unset($ret);
		unset($parameterhandler);
    }


	// Test regexp parameter non-escaped with invalid parameter
	function test_getSafeParameters_regexp_invalid()
	{
		$_GET['test_regexp'] = '12';
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNull($ret['test_regexp']);

		unset($ret);
		unset($parameterhandler);
    }


	// Test regexp parameter escaped with valid parameter
	function test_getSafeParameters_regexp_escaped_valid()
	{
		$_GET['test_regexp_escaped'] = "12'34";
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNotNull($ret['test_regexp_escaped']);
		$this->assertEqual($ret['test_regexp_escaped'], "12\'34");

		unset($ret);
		unset($parameterhandler);
    }


	// Test regexp parameter escaped with invalid parameter
	function test_getSafeParameters_regexp_escaped_invalid()
	{
		$_GET['test_regexp_escaped'] = '12';
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNull($ret['test_regexp_escaped']);

		unset($ret);
		unset($parameterhandler);
    }


	// Test text parameter with valid parameter
	function test_getSafeParameters_text_valid()
	{
		$_GET['test_text'] = "This is a test";
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNotNull($ret['test_text']);
		$this->assertEqual($ret['test_text'], "This is a test");

		unset($ret);
		unset($parameterhandler);
    }


	// Test text parameter with invalid parameter
	function test_getSafeParameters_text_invalid()
	{
		$_GET['test_text'] = "Illegal chars: ';";
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNull($ret['test_text']);

		unset($ret);
		unset($parameterhandler);
    }


	// Test string parameter with valid parameter
	function test_getSafeParameters_string_valid()
	{
		$_GET['test_string'] = "This is a test";
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNotNull($ret['test_string']);
		$this->assertEqual($ret['test_string'], "This is a test");

		unset($ret);
		unset($parameterhandler);
    }


	// Test string parameter with invalid parameter
	function test_getSafeParameters_string_invalid()
	{
		$_GET['test_string'] = "Illegal chars: \n\t";
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNull($ret['test_string']);

		unset($ret);
		unset($parameterhandler);
    }

	// Test date parameter with valid parameter
	function test_getSafeParameters_date_valid()
	{
		$_GET['test_date'] = "01.01.1970";
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNotNull($ret['test_date']);
		$this->assertEqual($ret['test_date'], "01.01.1970");

		unset($ret);
		unset($parameterhandler);
    }


	// Test string parameter escaped with invalid parameter
	function test_getSafeParameters_date_invalid()
	{
		$_GET['test_date'] = "001.001.1970";
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['false']);

		$this->assertNull($ret['test_date']);

		unset($ret);
		unset($parameterhandler);
    }

}

?>
