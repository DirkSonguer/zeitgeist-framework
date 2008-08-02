<?php
	
class testConfiguration extends UnitTestCase
{

	function test_init()
	{
		$configuration = zgConfiguration::init();

		$this->assertNotNull($configuration);
    }
	
	function test_loadConfiguration()
	{
		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('test', 'false');
		$this->assertFalse($ret);
		unset($ret);

		$ret = $configuration->loadConfiguration('test', 'testdata/test.ini');
		$this->assertTrue($ret);
		unset($ret);

		$ret = $configuration->loadConfiguration('test', 'testdata/test.ini', false);
		$this->assertFalse($ret);
		unset($ret);

		$ret = $configuration->loadConfiguration('test', 'testdata/test.ini', true);
		$this->assertTrue($ret);
		unset($ret);
	}
	
	function test_getConfiguration()
	{
		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration('test', 'testdata/test.ini');

		$ret = $configuration->getConfiguration('test');
		$this->assertEqual(count($ret), 2);
		unset($ret);
		
		$ret = $configuration->getConfiguration('test', 'test1');
		$this->assertEqual(count($ret), 3);
		$this->assertEqual($ret['test1'], 'true');
		$this->assertEqual($ret['test2'], '1');
		$this->assertEqual($ret['test3'], 'test3');

		$ret = $configuration->getConfiguration('test', 'test2', 'test1');
		$this->assertEqual($ret, 'true');
	}
}

?>
