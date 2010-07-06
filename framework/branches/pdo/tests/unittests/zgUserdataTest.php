<?php

if ( !defined( 'MULTITEST' ) )
{
	include( dirname( __FILE__ ) . '/../_configuration.php' );
}

/**
 * zgUserdata test case.
 */
class zgUserdataTest extends UnitTestCase
{
	/**
	 * @var zgUserdata
	 */
	private $zgUserdata;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	public function setUp( )
	{
		parent::setUp( );
		$this->zgUserdata = new zgUserdata( /* parameters */ );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	public function tearDown( )
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
		$this->database->connect( );
	}


	/**
	 * Tests zgUserdata->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgUserdataTest->test__construct()
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
		$this->assertEqual( count( $ret ), 5 );

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
		$this->assertEqual( count( $ret ), 5 );

		$testfunctions->dropZeitgeistTable( 'userdata' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserdata->loadUserdata()
	 */
	public function testLoadUserdata_WithoutDatabase( )
	{
		$this->setUp( );

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
		$this->assertEqual( count( $ret ), 5 );
		$this->assertEqual( $ret ['userdata_username'], $testdata );

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
		$this->assertEqual( $ret, 1 );

		$ret = $this->database->fetchArray( $res );
		$this->assertEqual( $ret ['userdata_username'], $testdata ['userdata_username'] );

		$testfunctions->dropZeitgeistTable( 'userdata' );
		$this->tearDown( );
	}
}

if ( !defined( 'MULTITEST' ) )
{
	$test = &new TestSuite( 'zgUserdataTest Unit Tests' );

	$testfunctions = new testFunctions( );
	$test->addTestCase( new zgUserdataTest( ) );

	$test->run( new HtmlReporter( ) );
}