<?php

class testMessages extends UnitTestCase
{

	function test_init()
	{
		$message = zgMessages::init();
		$this->assertNotNull($message);
		unset($message);
    }

	function test_setMessage()
	{
		$message = zgMessages::init();
		$ret = $message->setMessage('hello world');
		$this->assertTrue($ret);

		$ret = $message->setMessage('hello world', 'error');
		$this->assertTrue($ret);
    }
	
	function test_clearAllMessages()
	{
		$message = zgMessages::init();
		$ret = $message->setMessage('hello world');

		$ret = $message->clearAllMessages();
		$this->assertTrue($ret);
    }	
	
	function test_getMessagesByType()
	{
		$message = zgMessages::init();
		$message->clearAllMessages();
		
		$ret = $message->getMessagesByType();
		$this->assertIdentical($ret, array());
		unset($ret);

		$ret = $message->getMessagesByType('');
		$this->assertIdentical($ret, array());
		unset($ret);

		$message->setMessage('Hallo', 'test');
		$ret = $message->getMessagesByType('test');
		$this->assertEqual(count($ret), 1);
		$this->assertEqual($ret[0]->message, 'Hallo');
	}
	
	function test_getAllMessages()
	{
		$message = zgMessages::init();
		$ret = $message->clearAllMessages();

		$ret = $message->getAllMessages();
		$this->assertIdentical($ret, array());
		unset($ret);

		$message->setMessage('test1', 'test');
		$message->setMessage('test2', 'test');

		$ret = $message->getAllMessages();
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret[0]->message, 'test1');
		$this->assertEqual($ret[1]->message, 'test2');
		unset($ret);
	}

	function test_importMessages()
	{
		$message = zgMessages::init();
		$ret = $message->clearAllMessages();

		$ret = $message->importMessages('');
		$this->assertFalse($ret);
		unset($ret);

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
	}
}

?>
