<?php

class testMessagecache extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$messagecache = zgMessagecache::init();
		$this->assertNotNull($messagecache);
		unset($messagecache);
    }

	function test_saveMessagesToDatabase()
	{
		$res = $this->database->query("TRUNCATE TABLE messagecache");
		
		$messagecache = zgMessagecache::init();
		$message = zgMessages::init();
		$ret = $message->setMessage('cache testing', 'cachetest');
		
		$messagecache->saveMessagesToDatabase();

		unset($message);
		unset($messagecache);
	}

}

?>
