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


	// Test date parameter
	function test_getSafeParameters_date()
	{
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$_GET['test_date_true'] = '01.01.1970';
		$_GET['test_date_false'] = 'false';

		$_POST['test_date_true'] = '01.01.1970';
		$_POST['test_date_false'] = 'false';						

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters_GET');
		$this->assertNull($ret['false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		unset($parameterhandler);
    }
/*
	// Test string
	function test_getSafeParameters()
	{
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$_GET['false'] = 'false';
		$_GET['test_text_true'] = 'Hallo';
		$_GET['test_text_false'] = '.^"!~';
		$_GET['test_reg_true'] = '1';
		$_GET['test_reg_false'] = 'false';
		$_GET['test_date_true'] = '01.01.1970';
		$_GET['test_date_false'] = 'false';

		$_POST['false'] = 'false';
		$_POST['test_text_true'] = 'Hallo';
		$_POST['test_text_false'] = '.^"!~';
		$_POST['test_reg_true'] = '1';
		$_POST['test_reg_false'] = 'false';
		$_POST['test_date_true'] = '01.01.1970';
		$_POST['test_date_false'] = 'false';
						

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['"false']);

		if ($this->assertNotNull($ret['test_text_true']))
		{
			$this->assertEqual($ret['test_text_true'], 'Hallo');
		}
		$this->assertNull($ret['"test_text_false']);

		if ($this->assertNotNull($ret['test_reg_true']))
		{
			$this->assertEqual($ret['test_reg_true'], '1');
		}
		$this->assertNull($ret['test_reg_false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		$ret = $parameterhandler->getSafeParameters('parameter', 'test_post');
		$this->assertNull($ret['"false']);

		if ($this->assertNotNull($ret['test_text_true']))
		{
			$this->assertEqual($ret['test_text_true'], 'Hallo');
		}
		$this->assertNull($ret['"test_text_false']);

		if ($this->assertNotNull($ret['test_reg_true']))
		{
			$this->assertEqual($ret['test_reg_true'], '1');
		}
		$this->assertNull($ret['test_reg_false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		unset($parameterhandler);
    }
	
	// Test string
	function test_getSafeParameters()
	{
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$_GET['false'] = 'false';
		$_GET['test_text_true'] = 'Hallo';
		$_GET['test_text_false'] = '.^"!~';
		$_GET['test_reg_true'] = '1';
		$_GET['test_reg_false'] = 'false';
		$_GET['test_date_true'] = '01.01.1970';
		$_GET['test_date_false'] = 'false';

		$_POST['false'] = 'false';
		$_POST['test_text_true'] = 'Hallo';
		$_POST['test_text_false'] = '.^"!~';
		$_POST['test_reg_true'] = '1';
		$_POST['test_reg_false'] = 'false';
		$_POST['test_date_true'] = '01.01.1970';
		$_POST['test_date_false'] = 'false';
						

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['"false']);

		if ($this->assertNotNull($ret['test_text_true']))
		{
			$this->assertEqual($ret['test_text_true'], 'Hallo');
		}
		$this->assertNull($ret['"test_text_false']);

		if ($this->assertNotNull($ret['test_reg_true']))
		{
			$this->assertEqual($ret['test_reg_true'], '1');
		}
		$this->assertNull($ret['test_reg_false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		$ret = $parameterhandler->getSafeParameters('parameter', 'test_post');
		$this->assertNull($ret['"false']);

		if ($this->assertNotNull($ret['test_text_true']))
		{
			$this->assertEqual($ret['test_text_true'], 'Hallo');
		}
		$this->assertNull($ret['"test_text_false']);

		if ($this->assertNotNull($ret['test_reg_true']))
		{
			$this->assertEqual($ret['test_reg_true'], '1');
		}
		$this->assertNull($ret['test_reg_false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		unset($parameterhandler);
    }
	
	// Test string
	function test_getSafeParameters()
	{
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$_GET['false'] = 'false';
		$_GET['test_text_true'] = 'Hallo';
		$_GET['test_text_false'] = '.^"!~';
		$_GET['test_reg_true'] = '1';
		$_GET['test_reg_false'] = 'false';
		$_GET['test_date_true'] = '01.01.1970';
		$_GET['test_date_false'] = 'false';

		$_POST['false'] = 'false';
		$_POST['test_text_true'] = 'Hallo';
		$_POST['test_text_false'] = '.^"!~';
		$_POST['test_reg_true'] = '1';
		$_POST['test_reg_false'] = 'false';
		$_POST['test_date_true'] = '01.01.1970';
		$_POST['test_date_false'] = 'false';
						

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['"false']);

		if ($this->assertNotNull($ret['test_text_true']))
		{
			$this->assertEqual($ret['test_text_true'], 'Hallo');
		}
		$this->assertNull($ret['"test_text_false']);

		if ($this->assertNotNull($ret['test_reg_true']))
		{
			$this->assertEqual($ret['test_reg_true'], '1');
		}
		$this->assertNull($ret['test_reg_false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		$ret = $parameterhandler->getSafeParameters('parameter', 'test_post');
		$this->assertNull($ret['"false']);

		if ($this->assertNotNull($ret['test_text_true']))
		{
			$this->assertEqual($ret['test_text_true'], 'Hallo');
		}
		$this->assertNull($ret['"test_text_false']);

		if ($this->assertNotNull($ret['test_reg_true']))
		{
			$this->assertEqual($ret['test_reg_true'], '1');
		}
		$this->assertNull($ret['test_reg_false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		unset($parameterhandler);
    }
	
	// Test string
	function test_getSafeParameters()
	{
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$_GET['false'] = 'false';
		$_GET['test_text_true'] = 'Hallo';
		$_GET['test_text_false'] = '.^"!~';
		$_GET['test_reg_true'] = '1';
		$_GET['test_reg_false'] = 'false';
		$_GET['test_date_true'] = '01.01.1970';
		$_GET['test_date_false'] = 'false';

		$_POST['false'] = 'false';
		$_POST['test_text_true'] = 'Hallo';
		$_POST['test_text_false'] = '.^"!~';
		$_POST['test_reg_true'] = '1';
		$_POST['test_reg_false'] = 'false';
		$_POST['test_date_true'] = '01.01.1970';
		$_POST['test_date_false'] = 'false';
						

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['"false']);

		if ($this->assertNotNull($ret['test_text_true']))
		{
			$this->assertEqual($ret['test_text_true'], 'Hallo');
		}
		$this->assertNull($ret['"test_text_false']);

		if ($this->assertNotNull($ret['test_reg_true']))
		{
			$this->assertEqual($ret['test_reg_true'], '1');
		}
		$this->assertNull($ret['test_reg_false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		$ret = $parameterhandler->getSafeParameters('parameter', 'test_post');
		$this->assertNull($ret['"false']);

		if ($this->assertNotNull($ret['test_text_true']))
		{
			$this->assertEqual($ret['test_text_true'], 'Hallo');
		}
		$this->assertNull($ret['"test_text_false']);

		if ($this->assertNotNull($ret['test_reg_true']))
		{
			$this->assertEqual($ret['test_reg_true'], '1');
		}
		$this->assertNull($ret['test_reg_false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		unset($parameterhandler);
    }
	
	// Test string
	function test_getSafeParameters()
	{
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$_GET['false'] = 'false';
		$_GET['test_text_true'] = 'Hallo';
		$_GET['test_text_false'] = '.^"!~';
		$_GET['test_reg_true'] = '1';
		$_GET['test_reg_false'] = 'false';
		$_GET['test_date_true'] = '01.01.1970';
		$_GET['test_date_false'] = 'false';

		$_POST['false'] = 'false';
		$_POST['test_text_true'] = 'Hallo';
		$_POST['test_text_false'] = '.^"!~';
		$_POST['test_reg_true'] = '1';
		$_POST['test_reg_false'] = 'false';
		$_POST['test_date_true'] = '01.01.1970';
		$_POST['test_date_false'] = 'false';
						

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['"false']);

		if ($this->assertNotNull($ret['test_text_true']))
		{
			$this->assertEqual($ret['test_text_true'], 'Hallo');
		}
		$this->assertNull($ret['"test_text_false']);

		if ($this->assertNotNull($ret['test_reg_true']))
		{
			$this->assertEqual($ret['test_reg_true'], '1');
		}
		$this->assertNull($ret['test_reg_false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		$ret = $parameterhandler->getSafeParameters('parameter', 'test_post');
		$this->assertNull($ret['"false']);

		if ($this->assertNotNull($ret['test_text_true']))
		{
			$this->assertEqual($ret['test_text_true'], 'Hallo');
		}
		$this->assertNull($ret['"test_text_false']);

		if ($this->assertNotNull($ret['test_reg_true']))
		{
			$this->assertEqual($ret['test_reg_true'], '1');
		}
		$this->assertNull($ret['test_reg_false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		unset($parameterhandler);
    }
	
	// Test string
	function test_getSafeParameters()
	{
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('testparameters', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testparameters.ini');

		$_GET['false'] = 'false';
		$_GET['test_text_true'] = 'Hallo';
		$_GET['test_text_false'] = '.^"!~';
		$_GET['test_reg_true'] = '1';
		$_GET['test_reg_false'] = 'false';
		$_GET['test_date_true'] = '01.01.1970';
		$_GET['test_date_false'] = 'false';

		$_POST['false'] = 'false';
		$_POST['test_text_true'] = 'Hallo';
		$_POST['test_text_false'] = '.^"!~';
		$_POST['test_reg_true'] = '1';
		$_POST['test_reg_false'] = 'false';
		$_POST['test_date_true'] = '01.01.1970';
		$_POST['test_date_false'] = 'false';
						

		$ret = $parameterhandler->getSafeParameters('testparameters', 'test_parameters');
		$this->assertNull($ret['"false']);

		if ($this->assertNotNull($ret['test_text_true']))
		{
			$this->assertEqual($ret['test_text_true'], 'Hallo');
		}
		$this->assertNull($ret['"test_text_false']);

		if ($this->assertNotNull($ret['test_reg_true']))
		{
			$this->assertEqual($ret['test_reg_true'], '1');
		}
		$this->assertNull($ret['test_reg_false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		$ret = $parameterhandler->getSafeParameters('parameter', 'test_post');
		$this->assertNull($ret['"false']);

		if ($this->assertNotNull($ret['test_text_true']))
		{
			$this->assertEqual($ret['test_text_true'], 'Hallo');
		}
		$this->assertNull($ret['"test_text_false']);

		if ($this->assertNotNull($ret['test_reg_true']))
		{
			$this->assertEqual($ret['test_reg_true'], '1');
		}
		$this->assertNull($ret['test_reg_false']);

		if ($this->assertNotNull($ret['test_date_true']))
		{
			$this->assertEqual($ret['test_date_true'], '01.01.1970');
		}
		$this->assertNull($ret['"test_date_false']);
		unset($ret);

		unset($parameterhandler);
    }
	*/
}

?>
