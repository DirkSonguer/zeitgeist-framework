<?php

class testSession extends UnitTestCase
{
	function test_init( )
	{
		$session = zgSession::init( );
		$this->assertNotNull( $session );
		unset( $session );
	}


	function test_setSessionVariable( )
	{
		$session = zgSession::init( );
		$session->setSessionVariable( 'test1', 1 );
		$session->setSessionVariable( 'test2', 'test' );

		$this->assertEqual( $_SESSION['test1'], 1 );
		$this->assertEqual( $_SESSION['test2'], 'test' );

		unset( $session );
	}


	function test_getSessionVariable( )
	{
		$session = zgSession::init( );

		$ret = $session->getSessionVariable( 'test1' );
		$this->assertEqual( $ret, 1 );

		$ret = $session->getSessionVariable( 'test2' );
		$this->assertEqual( $ret, 'test' );

		unset( $session );
	}


	function test_unsetSessionVariable( )
	{
		$session = zgSession::init( );

		$session->unsetSessionVariable( 'test1' );
		$this->assertNull( $_SESSION['test1'] );

		$session->unsetSessionVariable( 'test2' );
		$this->assertNull( $_SESSION['test2'] );

		unset( $session );
	}
}

?>
