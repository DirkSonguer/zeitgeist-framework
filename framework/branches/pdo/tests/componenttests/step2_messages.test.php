<?php

class testMessages extends UnitTestCase
{
	function test_init( )
	{
		$message = zgMessages::init( );
		$this->assertNotNull( $message );
		unset( $message );
	}


	// Try loading the messages from the database
	function test_loadMessagesFromDatabase( )
	{
		$messages = zgMessages::init( );

		$messages->loadMessagesFromSession( );
		$ret = $messages->getMessagesByType( 'cachetest' );
		$this->assertIdentical( $ret[0]->message, 'cache testing' );

		unset( $messages );
	}
}

?>
