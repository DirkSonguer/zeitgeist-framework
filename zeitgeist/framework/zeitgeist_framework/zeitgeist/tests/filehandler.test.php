<?php

class testFilehandler extends UnitTestCase
{

	function test_init()
	{
		$filehandler = new zgFilehandler();
		$this->assertNotNull($filehandler);
		unset($filehandler);
    }

	function test_getFileContent()
	{
		$filehandler = new zgFilehandler();
		
		$ret = $filehandler->getFileContent('./testdata/testfile.txt');
		$this->assertEqual($ret, 'Hello World!');

		unset($filehandler);
    }

	function test_getDirectoryListing()
	{
		$filehandler = new zgFilehandler();
		
		$ret = $filehandler->getDirectoryListing('./testdata');
		$this->assertTrue(array_search('testfile.txt', $ret));

		unset($filehandler);
    }

}

?>
