<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../test_configuration.php');

class testObjects extends UnitTestCase
{

	function test_init()
	{
		$objectcache = zgObjects::init();
		$this->assertNotNull($objectcache);
		unset($objectcache);
    }


	// Test store object
	function test_storeObject()
	{
		$objectcache = zgObjects::init();
		
		$randomid = uniqid();		
		$testobj = 'testobj';

		$ret = $objectcache->storeObject($randomid, $testobj);
		$this->assertTrue($ret);

		unset($ret);
		unset($objectcache);
    }


	// Test store object with already existig name
	function test_storeObject_namecollision()
	{
		$objectcache = zgObjects::init();

		$randomid = uniqid();		
		$testobj = 'testobj';

		$ret = $objectcache->storeObject($randomid, $testobj);
		$this->assertTrue($ret);
		$ret = $objectcache->storeObject($randomid, $testobj);
		$this->assertFalse($ret);

		unset($ret);
		unset($objectcache);
    }


	// Test updating object 
	function test_storeObject_overwrite()
	{
		$objectcache = zgObjects::init();

		$randomid = uniqid();		
		$testobj = 'testobj';

		$ret = $objectcache->storeObject($randomid, $testobj);
		$this->assertTrue($ret);
		$ret = $objectcache->storeObject($randomid, $testobj, true);
		$this->assertTrue($ret);

		unset($ret);
		unset($objectcache);
    }


	// Testing getting object data back
	function test_getObject()
	{
		$objectcache = zgObjects::init();
		
		$randomid = uniqid();		
		$testobj = 'testobj';
		
		$objectcache->storeObject($randomid, true);
		$ret = $objectcache->getObject($randomid);
		
		$this->assertEqual($ret, 'testobj');
		unset($ret);

		unset($objectcache);
    }


	// Testing deleting object data
	function test_deleteObject()
	{
		$objectcache = zgObjects::init();

		$randomid = uniqid();
		$testobj = 'testobj';

		$objectcache->storeObject($randomid, true);
		$objectcache->getObject($randomid);

		$ret = $objectcache->deleteObject($randomid);
		$this->assertTrue($ret);

		$ret = $objectcache->getObject($randomid);
		$this->assertFalse($ret);

		unset($ret);
		unset($objectcache);
    }
	
	
	// Test deleting all objects from the system
	function test_deleteAllObjects()
	{
		$objectcache = zgObjects::init();

		$testobj = 'testobj';

		$randomid1 = uniqid();
		$objectcache->storeObject($randomid1, $testobj);

		$randomid2 = uniqid();
		$objectcache->storeObject($randomid2, $testobj);

		$ret = $objectcache->deleteAllObjects();
		$this->assertTrue($ret);

		$ret = $objectcache->getObject($randomid1);
		$this->assertFalse($ret);

		$ret = $objectcache->getObject($randomid2);
		$this->assertFalse($ret);

		unset($ret);
		unset($objectcache);
    }
		
}

?>
