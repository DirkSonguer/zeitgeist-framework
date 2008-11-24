<?php

class testObjects extends UnitTestCase
{

	function test_init()
	{
		$objectcache = zgObjects::init();
		$this->assertNotNull($objectcache);
		unset($objectcache);
    }

	function test_storeObject()
	{
		$objectcache = zgObjects::init();
		
		$testobj = 'testobj';
		$ret = $objectcache->storeObject('testobject', $testobj);
		$this->assertTrue($ret);

		$ret = $objectcache->storeObject('testobject', 'false');
		$this->assertFalse($ret);

		unset($objectcache);
    }

	function test_getObject()
	{
		$objectcache = zgObjects::init();
		
		$ret = $objectcache->getObject('testobject');
		$this->assertEqual($ret, 'testobj');

		unset($objectcache);
    }

	function test_deleteObject()
	{
		$objectcache = zgObjects::init();

		$ret = $objectcache->getObject('testobject');
		$this->assertEqual($ret, 'testobj');
		
		$ret = $objectcache->deleteObject('testobject');
		$this->assertTrue($ret);

		$ret = $objectcache->getObject('testobject');
		$this->assertFalse($ret);

		unset($objectcache);
    }
	
	function test_deleteAllObjects()
	{
		$objectcache = zgObjects::init();

		$testobj = 'testobj';
		$ret = $objectcache->storeObject('testobject1', $testobj);
		$ret = $objectcache->storeObject('testobject2', $testobj);

		$ret = $objectcache->getObject('testobject1');
		$this->assertTrue($ret);
		$ret = $objectcache->getObject('testobject2');
		$this->assertTrue($ret);

		$ret = $objectcache->deleteAllObjects();
		$this->assertTrue($ret);

		$ret = $objectcache->getObject('testobject1');
		$this->assertFalse($ret);
		$ret = $objectcache->getObject('testobject2');
		$this->assertFalse($ret);

		unset($objectcache);
    }
		
}

?>
