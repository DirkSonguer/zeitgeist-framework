<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgConfiguration test case.
 */
class zgConfigurationTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var zgConfiguration
	 */
	private $zgConfiguration;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp( )
	{
		parent::setUp( );
		$this->zgConfiguration = zgConfiguration::init( );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown( )
	{
		$this->zgConfiguration = null;
		parent::tearDown( );
	}


	/**
	 * Constructs the test case.
	 */
	public function __construct( )
	{
		// TODO Auto-generated constructor
	}


	/**
	 * Tests zgConfiguration::init()
	 */
	public function testInit( )
	{
		// TODO Auto-generated zgConfigurationTest::testInit()
		$this->markTestIncomplete( "init test not implemented" );

		zgConfiguration::init( /* parameters */ );
	}


	/**
	 * Tests zgConfiguration->loadConfiguration()
	 */
	public function testLoadConfiguration_WrongFilename( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$ret = $this->zgConfiguration->loadConfiguration( $randomid, 'false' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->loadConfiguration()
	 */
	public function testLoadConfiguration_WithoutDatabase( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$randomid = uniqid( );

		$ret = $this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );
		$this->assertTrue( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->loadConfiguration()
	 */
	public function testLoadConfiguration_Success( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$ret = $this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );
		$this->assertTrue( $ret );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->loadConfiguration()
	 */
	public function testLoadConfiguration_ConfigurationAlreadyExists( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$ret = $this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );
		$ret = $this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration_include.ini' );
		$this->assertFalse( $ret );

		$ret = $this->zgConfiguration->getConfiguration( $randomid );
		$this->assertEquals( count( $ret ), 2 );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->loadConfiguration()
	 */
	public function testLoadConfiguration_Include( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$ret = $this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );
		$ret = $this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration_include.ini', true );
		$this->assertTrue( $ret );

		$ret = $this->zgConfiguration->getConfiguration( $randomid );
		$this->assertEquals( count( $ret ), 3 );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->loadConfiguration()
	 */
	public function testLoadConfiguration_NameCollision( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );
		$ret = $this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->loadConfiguration()
	 */
	public function testLoadConfiguration_ForcedOverwrite( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );
		$ret = $this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini', true );
		$this->assertTrue( $ret );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->getConfiguration()
	 */
	public function testGetConfiguration_EmptyConfiguration( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$ret = $this->zgConfiguration->getConfiguration( $randomid );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->getConfiguration()
	 */
	public function testGetConfiguration_GetCompleteConfiguration( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );

		$testblock1 = array('testvar1' => 'true', 'testvar2' => '1', 'testvar3' => 'test3');
		$testblock2 = array('testvar4' => 'false', 'testvar5' => '2', 'testvar6' => '1');
		$testconfiguration ['testblock1'] = $testblock1;
		$testconfiguration ['testblock2'] = $testblock2;

		$ret = $this->zgConfiguration->getConfiguration( $randomid );
		$this->assertEquals( $ret, $testconfiguration );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->getConfiguration()
	 */
	public function testGetConfiguration_EmptyBlock( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );

		$ret = $this->zgConfiguration->getConfiguration( $randomid, 'false' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->getConfiguration()
	 */
	public function testGetConfiguration_GetBlock( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );

		$ret = $this->zgConfiguration->loadConfiguration( $randomid, ZEITGEIST_ROOTDIRECTORY . 'tests/testdata/testconfiguration.ini' );
		$testblock1 = array('testvar1' => 'true', 'testvar2' => '1', 'testvar3' => 'test3');

		$ret = $this->zgConfiguration->getConfiguration( $randomid, 'testblock1' );
		$this->assertEquals( $ret, $testblock1 );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->getConfiguration()
	 */
	public function testGetConfiguration_EmptyVariable( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );

		$ret = $this->zgConfiguration->getConfiguration( $randomid, 'testblock2', 'false' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->getConfiguration()
	 */
	public function testGetConfiguration_GetVariable( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );

		$ret = $this->zgConfiguration->getConfiguration( $randomid, 'testblock1', 'testvar2' );
		$this->assertEquals( $ret, '1' );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}


	/**
	 * Tests zgConfiguration->getConfiguration()
	 */
	public function testGetConfiguration_GetReferencedVariable( )
	{
		$this->setUp( );

		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'configurationcache' );
		$randomid = uniqid( );

		$this->zgConfiguration->loadConfiguration( $randomid, ZG_TESTDATA_DIR . 'testconfiguration.ini' );

		$ret = $this->zgConfiguration->getConfiguration( $randomid, 'testblock2', 'testvar6' );
		$this->assertEquals( $ret, '1' );

		$testfunctions->dropZeitgeistTable( 'configurationcache' );
		$this->tearDown( );
	}
}

