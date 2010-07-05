<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgUserroles test case.
 */
class zgUserrolesTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var zgUserroles
	 */
	private $zgUserroles;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp( )
	{
		parent::setUp( );
		$this->zgUserroles = new zgUserroles( /* parameters */ );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown( )
	{
		$this->zgUserroles = null;
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
	 * Tests zgUserroles->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgUserrolesTest->test__construct()
		$this->markTestIncomplete( "__construct test not implemented" );

		$this->zgUserroles->__construct( /* parameters */ );
	}


	/**
	 * Tests zgUserroles->loadUserroles()
	 */
	public function testLoadUserroles_NoUser( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userroles_to_users' );

		$ret = $this->zgUserroles->loadUserroles( '' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'userroles_to_users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserroles->loadUserroles()
	 */
	public function testLoadUserroles_NonExistantUser( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userroles_to_users' );

		$ret = $this->zgUserroles->loadUserroles( 1 );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'userroles_to_users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserroles->loadUserroles()
	 */
	public function testLoadUserroles_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userroles_to_users' );
		$testfunctions->createZeitgeistTable( 'userroles' );

		$testuser = rand( 1, 100 );
		$testrole1_id = rand( 1, 50 );
		$testrole1_name = uniqid( );
		$testrole1_desc = uniqid( );
		$testrole2_id = rand( 50, 100 );
		$testrole2_name = uniqid( );
		$testrole2_desc = uniqid( );

		$this->database->query( "INSERT INTO userroles(userrole_id, userrole_name, userrole_description) VALUES('" . $testrole1_id . "', '" . $testrole1_name . "', '" . $testrole1_desc . "')" );
		$this->database->query( "INSERT INTO userroles(userrole_id, userrole_name, userrole_description) VALUES('" . $testrole2_id . "', '" . $testrole2_name . "', '" . $testrole2_desc . "')" );

		$testroles = array();
		$testroles [$testrole1_id] = $testrole1_name;
		$testroles [$testrole2_id] = $testrole2_name;

		$this->zgUserroles->saveUserroles( $testuser, $testroles );
		$ret = $this->zgUserroles->loadUserroles( $testuser );

		$this->assertEquals( count( $ret ), 2 );
		$this->assertEquals( $ret [$testrole1_id], $testrole1_name );
		$this->assertEquals( $ret [$testrole2_id], $testrole2_name );

		$testfunctions->dropZeitgeistTable( 'userroles_to_users' );
		$testfunctions->dropZeitgeistTable( 'userroles' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserroles->saveUserroles()
	 */
	public function testSaveUserroles_NoData( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userroles_to_users' );

		$ret = $this->zgUserroles->saveUserroles( '', '' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'userroles_to_users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserroles->saveUserroles()
	 */
	public function testSaveUserroles_EmptyData( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userroles_to_users' );

		$testuser = rand( 1, 100 );
		$testroles = array();

		$ret = $this->zgUserroles->saveUserroles( $testuser, $testroles );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'userroles_to_users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserroles->saveUserroles()
	 */
	public function testSaveUserroles_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userroles_to_users' );

		$testuser = rand( 1, 100 );
		$testrole1_id = rand( 1, 50 );
		$testrole1_name = uniqid( );
		$testrole2_id = rand( 50, 100 );
		$testrole2_name = uniqid( );

		$testroles = array();
		$testroles [$testrole1_id] = $testrole1_name;
		$testroles [$testrole2_id] = $testrole2_name;

		$ret = $this->zgUserroles->saveUserroles( $testuser, $testroles );
		$this->assertTrue( $ret );

		$res = $this->database->query( "SELECT * FROM userroles_to_users" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 2 );

		$testfunctions->dropZeitgeistTable( 'userroles_to_users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserroles->identifyRole()
	 */
	public function testIdentifyRole_NoData( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userroles' );

		$ret = $this->zgUserroles->identifyRole( '' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'userroles' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserroles->identifyRole()
	 */
	public function testIdentifyRole_InvalidRole( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userroles' );

		$ret = $this->zgUserroles->identifyRole( 'false' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'userroles' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserroles->identifyRole()
	 */
	public function testIdentifyRole_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'userroles' );

		$testuser = rand( 1, 100 );
		$testrole1_id = rand( 1, 50 );
		$testrole1_name = uniqid( );
		$testrole1_desc = uniqid( );

		$this->database->query( "INSERT INTO userroles(userrole_id, userrole_name, userrole_description) VALUES('" . $testrole1_id . "', '" . $testrole1_name . "', '" . $testrole1_desc . "')" );

		$ret = $this->zgUserroles->identifyRole( $testrole1_name );
		$this->assertEquals( $ret, $testrole1_id );

		$testfunctions->dropZeitgeistTable( 'userroles' );
		$this->tearDown( );
	}
}

