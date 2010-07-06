<?php

if ( !defined( 'MULTITEST' ) )
{
	include( dirname( __FILE__ ) . '/../_configuration.php' );
}

/**
 * zgUserfunctions test case.
 */
class zgUserfunctionsTest extends UnitTestCase
{
	/**
	 * @var zgUserfunctions
	 */
	private $zgUserfunctions;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	public function setUp( )
	{
		parent::setUp( );
		$this->zgUserfunctions = new zgUserfunctions( /* parameters */ );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	public function tearDown( )
	{
		$this->zgUserfunctions = null;
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
	 * Tests zgUserfunctions->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgUserfunctionsTest->test__construct()
		$this->zgUserfunctions->__construct( /* parameters */ );
	}


	/**
	 * Tests zgUserfunctions->createUser()
	 */
	public function testCreateUser_WithoutData( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$newuserid = $this->zgUserfunctions->createUser( '', '' );
		$this->assertFalse( $newuserid );

		// check database content
		$res = $this->database->query( "SELECT * FROM users WHERE user_id='" . $newuserid . "'" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 0 );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->createUser()
	 */
	public function testCreateUser_WithoutDatabase( )
	{
		$this->setUp( );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$this->assertFalse( $newuserid );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->createUser()
	 */
	public function testCreateUser_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$this->assertTrue( ( !empty( $newuserid ) ) );

		// check database content
		$res = $this->database->query( "SELECT * FROM users WHERE user_id='" . $newuserid . "'" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->createUser()
	 */
	public function testCreateUser_Twice( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$this->assertTrue( ( !empty( $newuserid ) ) );

		$seconduser = $this->zgUserfunctions->createUser( $username, $password );
		$this->assertFalse( $seconduser );

		// check database content
		$res = $this->database->query( "SELECT * FROM users WHERE user_id='" . $newuserid . "'" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->deleteUser()
	 */
	public function testDeleteUser_NoUser( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );

		$ret = $this->zgUserfunctions->deleteUser( ( $newuserid + 1 ) );
		$this->assertTrue( $ret );

		// The delete should leave the existing user alone
		$res = $this->database->query( "SELECT * FROM users WHERE user_id='" . $newuserid . "'" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->deleteUser()
	 */
	public function testDeleteUser_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );

		$ret = $this->zgUserfunctions->deleteUser( $newuserid );
		$this->assertTrue( $ret );

		// The delete should leave the existing user alone
		$res = $this->database->query( "SELECT * FROM users WHERE user_id='" . $newuserid . "'" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 0 );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->login()
	 */
	public function testLogin_NoData( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$ret = $this->zgUserfunctions->login( '', '' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->login()
	 */
	public function testLogin_WithoutDatabase( )
	{
		$this->setUp( );

		$ret = $this->zgUserfunctions->login( '', '' );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->login()
	 */
	public function testLogin_WrongPassword( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$this->zgUserfunctions->activateUser( $newuserid );

		$ret = $this->zgUserfunctions->login( $username, $password . '1' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->login()
	 */
	public function testLogin_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$this->zgUserfunctions->activateUser( $newuserid );

		$ret = $this->zgUserfunctions->login( $username, $password );
		$this->assertEqual( $ret, $newuserid );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->changePassword()
	 */
	public function testChangePassword_NoData( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$ret = $this->zgUserfunctions->changePassword( $newuserid, '' );
		$this->assertFalse( $ret );

		$res = $this->database->query( "SELECT * FROM users WHERE user_password='" . md5( $password ) . "'" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->changePassword()
	 */
	public function testChangePassword_WithoutDatabase( )
	{
		$this->setUp( );

		$ret = $this->zgUserfunctions->changePassword( '1', 'test' );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->changePassword()
	 */
	public function testChangePassword_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$ret = $this->zgUserfunctions->changePassword( $newuserid, $password . '1' );
		$this->assertTrue( $ret );

		$res = $this->database->query( "SELECT * FROM users WHERE user_password='" . md5( $password . '1' ) . "'" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->changeUsername()
	 */
	public function testChangeUsername_NoData( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$ret = $this->zgUserfunctions->changeUsername( $newuserid, '' );
		$this->assertFalse( $ret );

		$res = $this->database->query( "SELECT * FROM users WHERE user_username='" . $username . "'" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->changeUsername()
	 */
	public function testChangeUsername_ExistingUsername( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$this->assertTrue( ( !empty( $newuserid ) ) );

		$newuserid = $this->zgUserfunctions->createUser( $username . '1', $password . '1' );
		$this->assertTrue( ( !empty( $newuserid ) ) );

		$ret = $this->zgUserfunctions->changeUsername( $newuserid, $username );
		$this->assertFalse( $ret );

		$res = $this->database->query( "SELECT * FROM users WHERE user_username='" . $username . "'" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->changeUsername()
	 */
	public function testChangeUsername_WithoutDatabase( )
	{
		$this->setUp( );

		$ret = $this->zgUserfunctions->changeUsername( '1', 'test' );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->changeUsername()
	 */
	public function testChangeUsername_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$ret = $this->zgUserfunctions->changeUsername( $newuserid, $username . '1' );
		$this->assertTrue( $ret );

		$res = $this->database->query( "SELECT * FROM users WHERE user_username='" . ( $username . '1' ) . "'" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->getInformation()
	 */
	public function testGetInformation( )
	{
		$this->setUp( );

		$ret = $this->zgUserfunctions->changeUsername( '1', 'test' );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->getConfirmationKey()
	 */
	public function testGetConfirmationKey_InvalidUser( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );
		$testfunctions->createZeitgeistTable( 'userconfirmation' );

		$ret = $this->zgUserfunctions->getConfirmationKey( 1 );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'users' );
		$testfunctions->dropZeitgeistTable( 'userconfirmation' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->getConfirmationKey()
	 */
	public function testGetConfirmationKey_WithoutDatabase( )
	{
		$this->setUp( );

		$ret = $this->zgUserfunctions->getConfirmationKey( 1 );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->getConfirmationKey()
	 */
	public function testGetConfirmationKey_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );
		$testfunctions->createZeitgeistTable( 'userconfirmation' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$confirmationkey = $this->zgUserfunctions->getConfirmationKey( $newuserid );
		$this->assertTrue( ( !empty( $confirmationkey ) ) );

		$res = $this->database->query( "SELECT * FROM userconfirmation WHERE userconfirmation_user='" . $newuserid . "'" );
		$ret = $this->database->fetchArray( $res );
		$this->assertEqual( $ret ['userconfirmation_key'], $confirmationkey );

		$testfunctions->dropZeitgeistTable( 'users' );
		$testfunctions->dropZeitgeistTable( 'userconfirmation' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->checkConfirmation()
	 */
	public function testCheckConfirmation_InvalidKey( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );
		$testfunctions->createZeitgeistTable( 'userconfirmation' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$confirmationkey = $this->zgUserfunctions->getConfirmationKey( $newuserid );

		$ret = $this->zgUserfunctions->checkConfirmation( $confirmationkey . '1' );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'users' );
		$testfunctions->dropZeitgeistTable( 'userconfirmation' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->checkConfirmation()
	 */
	public function testCheckConfirmation_WithoutDatabase( )
	{
		$this->setUp( );

		$ret = $this->zgUserfunctions->checkConfirmation( '1' );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->checkConfirmation()
	 */
	public function testCheckConfirmation_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );
		$testfunctions->createZeitgeistTable( 'userconfirmation' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$confirmationkey = $this->zgUserfunctions->getConfirmationKey( $newuserid );

		$ret = $this->zgUserfunctions->checkConfirmation( $confirmationkey );
		$this->assertEqual( $ret, $newuserid );

		$testfunctions->dropZeitgeistTable( 'users' );
		$testfunctions->dropZeitgeistTable( 'userconfirmation' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->activateUser()
	 */
	public function testActivateUser_InvalidUser( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$ret = $this->zgUserfunctions->activateUser( 1 );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->activateUser()
	 */
	public function testActivateUser_WithoutDatabase( )
	{
		$this->setUp( );

		$ret = $this->zgUserfunctions->activateUser( 1 );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->activateUser()
	 */
	public function testActivateUser_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );
		$testfunctions->createZeitgeistTable( 'userconfirmation' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );

		$ret = $this->zgUserfunctions->activateUser( $newuserid );
		$this->assertTrue( $ret );

		$res = $this->database->query( "SELECT * FROM users WHERE user_username='" . $username . "' and user_active='1'" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$res = $this->database->query( "SELECT * FROM userconfirmation" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 0 );

		$testfunctions->dropZeitgeistTable( 'users' );
		$testfunctions->dropZeitgeistTable( 'userconfirmation' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->deactivateUser()
	 */
	public function testDeactivateUser_InvalidUser( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );

		$ret = $this->zgUserfunctions->deactivateUser( 1 );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'users' );
		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->deactivateUser()
	 */
	public function testDeactivateUser_WithoutDatabase( )
	{
		$this->setUp( );

		$ret = $this->zgUserfunctions->deactivateUser( 1 );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgUserfunctions->deactivateUser()
	 */
	public function testDeactivateUser_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'users' );
		$testfunctions->createZeitgeistTable( 'userconfirmation' );

		$username = uniqid( );
		$password = uniqid( );
		$newuserid = $this->zgUserfunctions->createUser( $username, $password );
		$this->zgUserfunctions->activateUser( $newuserid );

		$ret = $this->zgUserfunctions->deactivateUser( $newuserid );
		$this->assertTrue( $ret );

		$res = $this->database->query( "SELECT * FROM users WHERE user_username='" . $username . "' and user_active='0'" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$res = $this->database->query( "SELECT * FROM userconfirmation" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$testfunctions->dropZeitgeistTable( 'users' );
		$testfunctions->dropZeitgeistTable( 'userconfirmation' );
		$this->tearDown( );
	}
}

if ( !defined( 'MULTITEST' ) )
{
	$test = &new TestSuite( 'zgUserfunctionsTest Unit Tests' );

	$testfunctions = new testFunctions( );
	$test->addTestCase( new zgUserfunctionsTest( ) );

	$test->run( new HtmlReporter( ) );
}