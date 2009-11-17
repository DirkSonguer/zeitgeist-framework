<?php

class testMessages extends UnitTestCase
{

	function test_init()
	{
		$message = zgMessages::init();
		$this->assertNotNull($message);
		unset($message);
    }


	// Try saving the messages to database
	function test_saveMessagesToSession()
	{
		$messages = zgMessages::init();
		$ret = $messages->setMessage('cache testing', 'cachetest');
		
		$messages->saveMessagesToSession();

		unset($messages);
	}

}

?>
