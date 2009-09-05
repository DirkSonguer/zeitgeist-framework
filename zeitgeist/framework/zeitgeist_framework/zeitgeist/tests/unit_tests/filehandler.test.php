<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../test_configuration.php');

class testFilehandler extends UnitTestCase
{

	function test_init()
	{
		$filehandler = new zgFilehandler();
		$this->assertNotNull($filehandler);
		unset($filehandler);
    }


	// Try to load the contents from a nonexistant file
	function test_getFileContent_wrongfilename()
	{
		$filehandler = new zgFilehandler();
		
		$ret = $filehandler->getFileContent('false');
		$this->assertFalse($ret);
		unset($ret);

		unset($filehandler);
    }


	// Try to load the contents of an existing file
	function test_getFileContent()
	{
		$filehandler = new zgFilehandler();
		
		$ret = $filehandler->getFileContent(ZEITGEIST_ROOTDIRECTORY.'/tests/testdata/testfile.txt');
		$this->assertEqual($ret, 'Hello World!');
		unset($ret);

		unset($filehandler);
    }


	// Try to load the contents of an nonexisting directory
	function test_getDirectoryListing_wrongdirname()
	{
		$filehandler = new zgFilehandler();
		
		$ret = $filehandler->getDirectoryListing('false');
		$this->assertFalse($ret);
		unset($ret);

		unset($filehandler);
    }


	// Try to load the contents of an existing directory
	function test_getDirectoryListing()
	{
		$filehandler = new zgFilehandler();
		
		$ret = $filehandler->getDirectoryListing(ZEITGEIST_ROOTDIRECTORY.'/tests/testdata');
		$this->assertTrue(array_search('testfile.txt', $ret));
		unset($ret);

		unset($filehandler);
    }

}

?>
