<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../test_configuration.php');

class testMessages extends UnitTestCase
{

	function test_init()
	{
		$message = zgMessages::init();
		$this->assertNotNull($message);
		unset($message);
    }


	// Try setting a message without type
	function test_setMessage_without_type()
	{
		$message = zgMessages::init();
		$ret = $message->setMessage('hello world');
		$this->assertTrue($ret);

		unset($ret);
		unset($message);
    }


	// Try setting a message with type
	function test_setMessage_with_type()
	{
		$message = zgMessages::init();
		$ret = $message->setMessage('hello world', 'error');
		$this->assertTrue($ret);

		unset($ret);
		unset($message);
    }


	// Test getting all messages as clear array
	function test_getAllMessages_clear()
	{
		$message = zgMessages::init();
		$ret = $message->clearAllMessages();

		$ret = $message->getAllMessages();
		$this->assertIdentical($ret, array());

		unset($ret);
		unset($message);
	}
	

	// Test getting all messages
	function test_getAllMessages()
	{
		$message = zgMessages::init();
		$ret = $message->clearAllMessages();

 		$message->setMessage('test1', 'test');
		$message->setMessage('test2', 'test');

		$ret = $message->getAllMessages();
		$this->assertEqual($ret[0]->message, 'test1');
		$this->assertEqual($ret[1]->message, 'test2');

		unset($ret);
		unset($message);
	}


	// Test getting messages by type as clear array
	function test_getMessagesByType_clear()
	{
		$message = zgMessages::init();
		$message->clearAllMessages();
		
		$ret = $message->getMessagesByType();
		$this->assertIdentical($ret, array());

		unset($ret);
		unset($message);
	}


	// Test getting messages by type
	function test_getMessagesByType()
	{
		$message = zgMessages::init();
		$message->clearAllMessages();

		$message->setMessage('hello', 'test');
		$ret = $message->getMessagesByType('test');

		$this->assertEqual(count($ret), 1);
		$this->assertEqual($ret[0]->message, 'hello');

		unset($ret);
		unset($message);
	}

	
	// Test clearing all messages
	function test_clearAllMessages()
	{
		$message = zgMessages::init();
		$ret = $message->setMessage('hello world');

		$ret = $message->clearAllMessages();
		$this->assertTrue($ret);
		
		$ret = $message->getAllMessages();
		$this->assertEqual(count($ret), 0);
		
		unset($ret);
		unset($message);
    }	

	
	function test_importMessages_empty()
	{
		$message = zgMessages::init();
		$ret = $message->clearAllMessages();

		$ret = $message->importMessages('');
		$this->assertFalse($ret);

		unset($ret);
		unset($message);
    }	

	
	function test_importMessages()
	{
		$message = zgMessages::init();
		$ret = $message->clearAllMessages();

		$message->setMessage('test1', 'test');
		$message->setMessage('test2', 'test');

		$testMessages = $message->getAllMessages();
		$message->clearAllMessages();

		$ret = $message->importMessages($testMessages);
		$this->assertTrue($ret);
		unset($ret);

		$ret = $message->getAllMessages();
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret[0]->message, 'test1');
		$this->assertEqual($ret[1]->message, 'test2');
		unset($ret);

		unset($message);
	}
}

?>
