<?php

class testUserhandler extends UnitTestCase
{
	public $database;


	function test_init( )
	{
		$this->database = new zgDatabase( );
		$this->database->connect( );

		$userhandler = zgUserhandler::init( );
		$this->assertNotNull( $userhandler );
		unset( $userhandler );
	}


	function test_login( )
	{
		$userhandler = zgUserhandler::init( );
		$userfunctions = new zgUserfunctions( );
		$this->database->query( 'TRUNCATE TABLE users' );

		$testid = $userfunctions->createUser( 'test', 'test' );
		$ret = $userfunctions->activateUser( $testid );

		$ret = $userhandler->login( 'test', 'test' );
		$this->assertTrue( $ret );

		$userid = $userhandler->getUserId( );
		$this->assertEqual( $userid, $testid );

		unset( $userhandler );
	}


	function test_isLoggedIn_true( )
	{
		$userhandler = zgUserhandler::init( );

		$ret = $userhandler->isLoggedIn( );
		$this->assertTrue( $ret );

		unset( $userhandler );
	}
}

?>
