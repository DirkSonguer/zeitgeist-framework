<?php

if ( !defined( 'MULTITEST' ) )
{
	include( dirname( __FILE__ ) . '/../_configuration.php' );
}

/**
 * zgUserhandler test case.
 */
class zgUserhandlerTest extends UnitTestCase
{
	/**
	 * @var zgUserhandler
	 */
	private $zgUserhandler;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	public function setUp( )
	{
		parent::setUp( );
		$this->zgUserhandler = zgUserhandler::init( );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	public function tearDown( )
	{
		$this->zgUserhandler = null;
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
	 * Tests zgUserhandler::init()
	 */
	public function testInit( )
	{
		// TODO Auto-generated zgUserhandlerTest::testInit()
		zgUserhandler::init( /* parameters */ );
	}


	/**
	 * Tests zgUserhandler->establishUserSession()
	 */
	public function testEstablishUserSession( )
	{
		// TODO Auto-generated zgUserhandlerTest->testEstablishUserSession()
		$this->zgUserhandler->establishUserSession( /* parameters */ );
	}


	/**
	 * Tests zgUserhandler->login()
	 */
	public function testLogin_NoData( )
	{
		$this->setUp( );

		$ret = $this->zgUserhandler->login( '', '' );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserhandler->login()
	 */
	public function testLogin_WrongPassword( )
	{
		$this->setUp( );
		$userfunctions = new zgUserfunctions( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$userid = $userfunctions->createUser( $username, $password );
		$userfunctions->activateUser( $userid );

		$ret = $this->zgUserhandler->login( $username, 'false' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'users' );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserhandler->login()
	 */
	public function testLogin_WrongUser( )
	{
		$this->setUp( );
		$userfunctions = new zgUserfunctions( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$userid = $userfunctions->createUser( $username, $password );
		$userfunctions->activateUser( $userid );

		$ret = $this->zgUserhandler->login( 'false', $password );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'users' );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserhandler->login()
	 */
	public function testLogin_Success( )
	{
		$this->setUp( );
		$userfunctions = new zgUserfunctions( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$userid = $userfunctions->createUser( $username, $password );
		$userfunctions->activateUser( $userid );

		$ret = $this->zgUserhandler->login( $username, $password );
		$this->assertTrue( $ret );

		$testfunctions->dropZeitgeistTable( 'users' );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserhandler->logout()
	 */
	public function testLogout_Success( )
	{
		$this->setUp( );

		$ret = $this->zgUserhandler->logout( );
		$this->assertTrue( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserhandler->logout()
	 */
	public function testLogout_NotLoggedIn( )
	{
		$this->setUp( );

		$ret = $this->zgUserhandler->logout( );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserhandler->setLoginStatus()
	 */
	public function testSetLoginStatus( )
	{
		// TODO Auto-generated zgUserhandlerTest->testSetLoginStatus()
		$this->zgUserhandler->setLoginStatus( /* parameters */ );
	}


	/**
	 * Tests zgUserhandler->saveUserstates()
	 */
	public function testSaveUserstates( )
	{
		// TODO Auto-generated zgUserhandlerTest->testSaveUserstates()
		$this->zgUserhandler->saveUserstates( /* parameters */ );
	}


	/**
	 * Tests zgUserhandler->isLoggedIn()
	 */
	public function testIsLoggedIn_NotLoggedIn( )
	{
		$this->setUp( );

		$ret = $this->zgUserhandler->isLoggedIn( );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserhandler->isLoggedIn()
	 */
	public function testIsLoggedIn_LoggedIn( )
	{
		$this->setUp( );
		$userfunctions = new zgUserfunctions( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$userid = $userfunctions->createUser( $username, $password );
		$userfunctions->activateUser( $userid );

		$this->zgUserhandler->login( $username, $password );

		$ret = $this->zgUserhandler->isLoggedIn( );
		$this->assertTrue( $ret );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserhandler->getUserID()
	 */
	public function testGetUserID_NoUser( )
	{
		$this->setUp( );

		$this->zgUserhandler->logout( );
		$ret = $this->zgUserhandler->getUserID( );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserhandler->getUserID()
	 */
	public function testGetUserID_Success( )
	{
		$this->setUp( );
		$userfunctions = new zgUserfunctions( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$userid = $userfunctions->createUser( $username, $password );
		$userfunctions->activateUser( $userid );

		$this->zgUserhandler->login( $username, $password );

		$ret = $this->zgUserhandler->getUserID( );
		$this->assertEqual( $userid, $ret );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserhandler->getUsername()
	 */
	public function testGetUsername_NoUser( )
	{
		$this->setUp( );

		$this->zgUserhandler->logout( );
		$ret = $this->zgUserhandler->getUsername( );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserhandler->getUsername()
	 */
	public function testGetUsername_Success( )
	{
		$this->setUp( );
		$userfunctions = new zgUserfunctions( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$userid = $userfunctions->createUser( $username, $password );
		$userfunctions->activateUser( $userid );

		$this->zgUserhandler->login( $username, $password );

		$ret = $this->zgUserhandler->getUsername( );
		$this->assertEqual( $username, $ret );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserhandler->getUserKey()
	 */
	public function testGetUserKey( )
	{
		// TODO Auto-generated zgUserhandlerTest->testGetUserKey()
		//		$this->zgUserhandler->getUserKey( /* parameters */ );
	}


	/**
	 * Tests zgUserhandler->getUserdata()
	 */
	public function testGetUserdata( )
	{
		// TODO Auto-generated zgUserhandlerTest->testGetUserdata()
		//		$this->zgUserhandler->getUserdata( /* parameters */ );
	}


	/**
	 * Tests zgUserhandler->setUserdata()
	 */
	public function testSetUserdata( )
	{
		// TODO Auto-generated zgUserhandlerTest->testSetUserdata()
		//		$this->zgUserhandler->setUserdata( /* parameters */ );
	}


	/**
	 * Tests zgUserhandler->hasUserright()
	 */
	public function testHasUserright( )
	{
		// TODO Auto-generated zgUserhandlerTest->testHasUserright()
		//		$this->zgUserhandler->hasUserright( /* parameters */ );
	}


	/**
	 * Tests zgUserhandler->grantUserright()
	 */
	public function testGrantUserright( )
	{
		// TODO Auto-generated zgUserhandlerTest->testGrantUserright()
		//		$this->zgUserhandler->grantUserright( /* parameters */ );
	}


	/**
	 * Tests zgUserhandler->revokeUserright()
	 */
	public function testRevokeUserright( )
	{
		// TODO Auto-generated zgUserhandlerTest->testRevokeUserright()
		//		$this->zgUserhandler->revokeUserright( /* parameters */ );
	}


	/**
	 * Tests zgUserhandler->hasUserrole()
	 */
	public function testHasUserrole( )
	{
		// TODO Auto-generated zgUserhandlerTest->testHasUserrole()
		//		$this->zgUserhandler->hasUserrole( /* parameters */ );
	}


	/**
	 * Tests zgUserhandler->grantUserrole()
	 */
	public function testGrantUserrole( )
	{
		// TODO Auto-generated zgUserhandlerTest->testGrantUserrole()
		//		$this->zgUserhandler->grantUserrole( /* parameters */ );
	}


	/**
	 * Tests zgUserhandler->revokeUserrole()
	 */
	public function testRevokeUserrole( )
	{
		// TODO Auto-generated zgUserhandlerTest->testRevokeUserrole()
		//		$this->zgUserhandler->revokeUserrole( /* parameters */ );
	}
}

if ( !defined( 'MULTITEST' ) )
{
    // this is needed so that the session is initialized
    // before the first output by the test suite runner
    $tempUserhandler = zgUserhandler::init( );

	$test = &new TestSuite( 'zgUserhandlerTest Unit Tests' );

	$testfunctions = new testFunctions( );
	$test->addTestCase( new zgUserhandlerTest( ) );

	$test->run( new HtmlReporter( ) );
}