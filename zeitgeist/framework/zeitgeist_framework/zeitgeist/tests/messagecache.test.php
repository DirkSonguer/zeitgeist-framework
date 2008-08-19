<?php

class testMessagecache extends UnitTestCase
{
	
	function test_init()
	{
		$messagecache = zgMessagecache::init();
		$this->assertNotNull($messagecache);
		unset($messagecache);
    }

	function test_saveMessagesToDatabase()
	{
		$messagecache = zgMessagecache::init();
		$message = zgMessages::init();
		$ret = $message->setMessage('cache testing', 'cachetest');
		
		$messagecache->saveMessagesToDatabase();

		unset($message);
		unset($messagecache);
	}

}

?>
