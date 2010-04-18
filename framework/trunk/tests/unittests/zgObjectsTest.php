<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgObjects test case.
 */
class zgObjectsTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @var zgObjects
	 */
	private $zgObjects;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->zgObjects = zgObjects::init();
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->zgObjects = null;
		parent::tearDown();
	}


	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}


	/**
	 * Tests zgObjects::init()
	 */
	public function testInit()
	{
		$this->setUp();
		$this->assertNotNull( $this->zgObjects );
		$this->tearDown();
	}


	/**
	 * Tests zgObjects->storeObject()
	 */
	public function testStoreObject_Success()
	{
		$this->setUp();
		
		$randomid = uniqid();
		$testobj = uniqid();
		
		$ret = $this->zgObjects->storeObject( $randomid, $testobj );
		$this->assertTrue( $ret );
		
		$this->tearDown();
	}


	/**
	 * Tests zgObjects->storeObject()
	 */
	public function testStoreObject_NameCollision()
	{
		$this->setUp();
		
		$randomid = uniqid();
		$testobj = uniqid();
		
		$ret = $this->zgObjects->storeObject( $randomid, $testobj );
		$this->assertTrue( $ret );
		$ret = $this->zgObjects->storeObject( $randomid, $testobj );
		$this->assertFalse( $ret );
		
		$this->tearDown();
	}


	/**
	 * Tests zgObjects->storeObject()
	 */
	public function testStoreObject_OverwriteExisting()
	{
		$this->setUp();
		
		$randomid = uniqid();
		$testobj = uniqid();
		
		$ret = $this->zgObjects->storeObject( $randomid, $testobj );
		$this->assertTrue( $ret );
		$ret = $this->zgObjects->storeObject( $randomid, $testobj, true );
		$this->assertTrue( $ret );
		
		$this->tearDown();
	}


	/**
	 * Tests zgObjects->getObject()
	 */
	public function testGetObject_NoObject()
	{
		$this->setUp();
		
		$randomid = uniqid();
		
		$ret = $this->zgObjects->getObject( $randomid );
		$this->assertFalse( $ret );
		
		$this->tearDown();
	}


	/**
	 * Tests zgObjects->getObject()
	 */
	public function testGetObject_Success()
	{
		$this->setUp();
		
		$randomid = uniqid();
		$testobj = uniqid();
		
		$this->zgObjects->storeObject( $randomid, $testobj );
		$ret = $this->zgObjects->getObject( $randomid );
		$this->assertEquals( $ret, $testobj );
		
		$this->tearDown();
	}


	/**
	 * Tests zgObjects->deleteObject()
	 */
	public function testDeleteObject_NoObject()
	{
		$this->setUp();
		
		$randomid = uniqid();
		
		$ret = $this->zgObjects->deleteObject( $randomid );
		$this->assertFalse( $ret );
		
		$this->tearDown();
	}


	/**
	 * Tests zgObjects->deleteObject()
	 */
	public function testDeleteObject_Success()
	{
		$this->setUp();
		
		$randomid = uniqid();
		$testobj = uniqid();
		
		$this->zgObjects->storeObject( $randomid, $testobj );
		$this->zgObjects->storeObject( ($randomid . '1'), $testobj );
		$this->zgObjects->deleteObject( $randomid );
		
		$ret = $this->zgObjects->getObject( $randomid );
		$this->assertFalse( $ret );
		
		$ret = $this->zgObjects->getObject( ($randomid . '1') );
		$this->assertEquals( $ret, $testobj );
		
		$this->tearDown();
	}


	/**
	 * Tests zgObjects->deleteAllObjects()
	 */
	public function testDeleteAllObjects_Success()
	{
		$this->setUp();
		
		$randomid = uniqid();
		$testobj = uniqid();
		
		$this->zgObjects->storeObject( $randomid, $testobj );
		$this->zgObjects->storeObject( ($randomid . '1'), $testobj );
		$ret = $this->zgObjects->deleteAllObjects();
		$this->assertTrue( $ret );
		
		$ret = $this->zgObjects->getObject( $randomid );
		$this->assertFalse( $ret );
		
		$ret = $this->zgObjects->getObject( ($randomid . '1') );
		$this->assertFalse( $ret );
		
		$this->tearDown();
	}

}

