<?php

class testMessagecache_s2 extends UnitTestCase
{
	
	function test_init()
	{
		$messagecache = zgMessagecache::init();
		$this->assertNotNull($messagecache);
		unset($messagecache);
    }

	function test_loadMessagesFromDatabase()
	{
		$messagecache = zgMessagecache::init();
		$message = zgMessages::init();
		
		$messagecache->loadMessagesFromDatabase();
		$ret = $message->getMessagesByType('cachetest');
		$this->assertIdentical($ret[0]->message, 'cache testing');

		unset($message);
		unset($messagecache);
	}

}

?>
