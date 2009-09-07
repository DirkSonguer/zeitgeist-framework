<?php

class testParameterhandler extends UnitTestCase
{
	
	function test_init()
	{
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
						
		$parameterhandler = new zgParameterhandler();
		$this->assertNotNull($parameterhandler);
		unset($parameterhandler);
    }

	function test_getSafeParameters()
	{
		$parameterhandler = new zgParameterhandler();

		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('parameter', 'testdata/testparameter.ini');
		
		$ret = $parameterhandler->getSafeParameters('parameter', 'test_get');
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
	
}

?>
