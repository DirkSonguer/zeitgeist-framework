<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgUserrights test case.
 */
class zgUserrightsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var zgUserrights
	 */
	private $zgUserrights;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp( )
	{
		parent::setUp( );
		$this->zgUserrights = new zgUserrights( /* parameters */ );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown( )
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
		$ret = $this->database->connect( );
	}


	/**
	 * Tests zgUserrights->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgUserrightsTest->test__construct()
		$this->markTestIncomplete( "__construct test not implemented" );

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
		$this->assertEquals( count( $ret ), 0 );

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
		$this->assertEquals( count( $ret ), 0 );

		$testfunctions->dropZeitgeistTable( 'userrights' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserrights->loadUserrights()
	 */
	public function testLoadUserrights_WithoutDatabase( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

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
		$this->assertEquals( count( $ret ), 2 );
		$this->assertEquals( $ret [$testright1], true );
		$this->assertEquals( $ret [$testright2], true );

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
		$this->assertEquals( count( $ret ), 4 );
		$this->assertEquals( $ret [$testright1], true );
		$this->assertEquals( $ret [$testright2], true );
		$this->assertEquals( $ret [$testright3], true );
		$this->assertEquals( $ret [$testright4], true );

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
		$testfunctions = new testFunctions( );

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
		$this->assertEquals( $ret, 2 );

		$testfunctions->dropZeitgeistTable( 'userrights' );
		$this->tearDown( );
	}
}

