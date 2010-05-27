<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgUserdata test case.
 */
class zgUserdataTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var zgUserdata
	 */
	private $zgUserdata;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp( )
	{
		parent::setUp( );
		$this->zgUserdata = new zgUserdata( /* parameters */ );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown( )
	{
		$this->zgUserdata = null;
		parent::tearDown( );
	}


	/**
	 * Constructs the test case.
	 */
	public function __construct( )
	{
		$this->database = new zgDatabase( );
		$ret = $this->database->connect( );
	}


	/**
	 * Tests zgUserdata->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgUserdataTest->test__construct()
		$this->markTestIncomplete( "__construct test not implemented" );

		$this->zgUserdata->__construct( /* parameters */ );
	}


	/**
	 * Tests zgUserdata->loadUserdata()
	 */
	public function testLoadUserdata_EmptyUser( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userdata' );

		$ret = $this->zgUserdata->loadUserdata( '' );
		$this->assertTrue( is_array( $ret ) );
		$this->assertEquals( count( $ret ), 5 );

		$testfunctions->dropZeitgeistTable( 'userdata' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserdata->loadUserdata()
	 */
	public function testLoadUserdata_NonExistantUser( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userdata' );

		$ret = $this->zgUserdata->loadUserdata( 1 );
		$this->assertTrue( is_array( $ret ) );
		$this->assertEquals( count( $ret ), 5 );

		$testfunctions->dropZeitgeistTable( 'userdata' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserdata->loadUserdata()
	 */
	public function testLoadUserdata_WithoutDatabase( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$ret = $this->zgUserdata->loadUserdata( 1 );
		$this->assertFalse( is_array( $ret ) );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserdata->loadUserdata()
	 */
	public function testLoadUserdata_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userdata' );

		$testuser = rand( 100, 200 );
		$testdata = uniqid( );

		$this->database->query( "INSERT INTO userdata(userdata_user, userdata_username) VALUES('" . $testuser . "', '" . $testdata . "')" );

		$ret = $this->zgUserdata->loadUserdata( $testuser );
		$this->assertEquals( count( $ret ), 5 );
		$this->assertEquals( $ret ['userdata_username'], $testdata );

		$testfunctions->dropZeitgeistTable( 'userdata' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserdata->saveUserdata()
	 */
	public function testSaveUserdata_NoUserdata( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userdata' );

		$ret = $this->zgUserdata->saveUserdata( '', '' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'userdata' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserdata->saveUserdata()
	 */
	public function testSaveUserdata_WithoutUserdata( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userdata' );

		$testuser = rand( 100, 200 );
		$testdata = array();

		$ret = $this->zgUserdata->saveUserdata( $testuser, $testdata );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'userdata' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserdata->saveUserdata()
	 */
	public function testSaveUserdata_WithoutDatabase( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testuser = rand( 100, 200 );
		$testdata = array();

		$ret = $this->zgUserdata->saveUserdata( $testuser, $testdata );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserdata->saveUserdata()
	 */
	public function testSaveUserdata_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userdata' );

		$testuser = rand( 100, 200 );
		$testdata = array();
		$testdata ['userdata_username'] = uniqid( );

		$ret = $this->zgUserdata->saveUserdata( $testuser, $testdata );
		$this->assertTrue( $ret );

		$res = $this->database->query( "SELECT * FROM userdata" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 1 );

		$ret = $this->database->fetchArray( $res );
		$this->assertEquals( $ret ['userdata_username'], $testdata ['userdata_username'] );

		$testfunctions->dropZeitgeistTable( 'userdata' );
		$this->tearDown( );
	}
}

