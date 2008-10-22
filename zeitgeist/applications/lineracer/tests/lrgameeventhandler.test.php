<?php

class testLrgameeventhandler extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();

		$gameeventhandler = new lrGameeventhandler();
		$this->assertNotNull($gameeventhandler);
		unset($gameeventhandler);
    }

}

?>
