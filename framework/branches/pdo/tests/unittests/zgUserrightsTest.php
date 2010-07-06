<?php

if ( !defined( 'MULTITEST' ) )
{
	include( dirname( __FILE__ ) . '/../_configuration.php' );
}

/**
 * zgUserrights test case.
 */
class zgUserrightsTest extends UnitTestCase
{
	/**
	 * @var zgUserrights
	 */
	private $zgUserrights;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	public function setUp( )
	{
		parent::setUp( );
		$this->zgUserrights = new zgUserrights( /* parameters */ );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	public function tearDown( )
	{
		$this->zgUserrights = null;
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
	 * Tests zgUserrights->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgUserrightsTest->test__construct()
		$this->zgUserrights->__construct( /* parameters */ );
	}


	/**
	 * Tests zgUserrights->loadUserrights()
	 */
	public function testLoadUserrights_NoUser( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userrights' );

		$ret = $this->zgUserrights->loadUserrights( '' );
		$this->assertEqual( count( $ret ), 0 );

		$testfunctions->dropZeitgeistTable( 'userrights' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserrights->loadUserrights()
	 */
	public function testLoadUserrights_NonExistantUser( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userrights' );

		$ret = $this->zgUserrights->loadUserrights( 1 );
		$this->assertEqual( count( $ret ), 0 );

		$testfunctions->dropZeitgeistTable( 'userrights' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserrights->loadUserrights()
	 */
	public function testLoadUserrights_WithoutDatabase( )
	{
		$this->setUp( );

		$ret = $this->zgUserrights->loadUserrights( 1 );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserrights->loadUserrights()
	 */
	public function testLoadUserrights_SuccessNoRoles( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userrights' );

		$testuser = rand( 1, 100 );
		$testright1 = rand( 1, 50 );
		$testright2 = rand( 50, 100 );

		$this->database->query( "INSERT INTO userrights(userright_user, userright_action) VALUES('" . $testuser . "', '" . $testright1 . "')" );
		$this->database->query( "INSERT INTO userrights(userright_user, userright_action) VALUES('" . $testuser . "', '" . $testright2 . "')" );

		$ret = $this->zgUserrights->loadUserrights( $testuser );
		$this->assertEqual( count( $ret ), 2 );
		$this->assertEqual( $ret [$testright1], true );
		$this->assertEqual( $ret [$testright2], true );

		$testfunctions->dropZeitgeistTable( 'userrights' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserrights->loadUserrights()
	 */
	public function testLoadUserrights_WithRoles( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userrights' );
		$testfunctions->createZeitgeistTable( 'userroles_to_users' );
		$testfunctions->createZeitgeistTable( 'userroles_to_actions' );
		$testfunctions->createZeitgeistTable( 'userroles' );

		$testuser = rand( 1, 100 );
		$testrole = rand( 1, 100 );
		$testright1 = rand( 1, 50 );
		$testright2 = rand( 50, 100 );
		$testright3 = rand( 100, 150 );
		$testright4 = rand( 150, 200 );

		$this->database->query( "INSERT INTO userrights(userright_user, userright_action) VALUES('" . $testuser . "', '" . $testright1 . "')" );
		$this->database->query( "INSERT INTO userrights(userright_user, userright_action) VALUES('" . $testuser . "', '" . $testright2 . "')" );

		$this->database->query( "INSERT INTO userroles(userrole_id, userrole_name, userrole_description) VALUES('" . $testrole . "','test', 'test')" );

		$this->database->query( "INSERT INTO userroles_to_users(userroleuser_user, userroleuser_userrole) VALUES($testuser, $testrole)" );
		$this->database->query( "INSERT INTO userroles_to_actions(userroleaction_userrole, userroleaction_action) VALUES($testrole, $testright3)" );
		$this->database->query( "INSERT INTO userroles_to_actions(userroleaction_userrole, userroleaction_action) VALUES($testrole, $testright4)" );

		$ret = $this->zgUserrights->loadUserrights( $testuser );
		$this->assertEqual( count( $ret ), 4 );
		$this->assertEqual( $ret [$testright1], true );
		$this->assertEqual( $ret [$testright2], true );
		$this->assertEqual( $ret [$testright3], true );
		$this->assertEqual( $ret [$testright4], true );

		$testfunctions->dropZeitgeistTable( 'userrights' );
		$testfunctions->dropZeitgeistTable( 'userroles_to_users' );
		$testfunctions->dropZeitgeistTable( 'userroles_to_actions' );
		$testfunctions->dropZeitgeistTable( 'userroles' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserrights->saveUserrights()
	 */
	public function testSaveUserrights_Emptydata( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userrights' );

		$testuser = rand( 1, 100 );
		$testrights = array();

		$ret = $this->zgUserrights->saveUserrights( $testuser, $testrights );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'userrights' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserrights->saveUserrights()
	 */
	public function testSaveUserrights_WithoutDatabase( )
	{
		$this->setUp( );

		$testuser = rand( 1, 100 );
		$testrights = array();

		$ret = $this->zgUserrights->saveUserrights( $testuser, $testrights );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserrights->saveUserrights()
	 */
	public function testSaveUserrights_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userrights' );

		$testuser = rand( 1, 100 );
		$testright1 = rand( 1, 50 );
		$testright2 = rand( 50, 100 );

		$testrights = array();
		$testrights [$testright1] = true;
		$testrights [$testright2] = true;

		$ret = $this->zgUserrights->saveUserrights( $testuser, $testrights );
		$this->assertTrue( $ret );

		$res = $this->database->query( "SELECT * FROM userrights" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 2 );

		$testfunctions->dropZeitgeistTable( 'userrights' );
		$this->tearDown( );
	}
}

if ( !defined( 'MULTITEST' ) )
{
	$test = &new TestSuite( 'zgUserrightsTest Unit Tests' );

	$testfunctions = new testFunctions( );
	$test->addTestCase( new zgUserrightsTest( ) );

	$test->run( new HtmlReporter( ) );
}