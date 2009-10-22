<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../_configuration.php');

class testFiles extends UnitTestCase
{

	function test_init()
	{
		$filehandler = new zgFiles();
		$this->assertNotNull($filehandler);
		unset($filehandler);
    }


	// Try to load the contents from a nonexistant file
	function test_getFileContent_wrongfilename()
	{
		$filehandler = new zgFiles();
		
		$ret = $filehandler->getFileContent('false');
		$this->assertFalse($ret);
		unset($ret);

		unset($filehandler);
    }


	// Try to load the contents of an existing file
	function test_getFileContent_success()
	{
		$filehandler = new zgFiles();
		
		$ret = $filehandler->getFileContent(ZEITGEIST_ROOTDIRECTORY.'/tests/testdata/testfile.txt');
		$this->assertEqual($ret, 'Hello World!');
		unset($ret);

		unset($filehandler);
    }


	// Try to load the contents of an nonexisting directory
	function test_getDirectoryListing_wrongdirname()
	{
		$filehandler = new zgFiles();
		
		$ret = $filehandler->getDirectoryListing('false');
		$this->assertFalse($ret);
		unset($ret);

		unset($filehandler);
    }


	// Try to load the contents of an existing directory
	function test_getDirectoryListing_success()
	{
		$filehandler = new zgFiles();
		
		$ret = $filehandler->getDirectoryListing(ZEITGEIST_ROOTDIRECTORY.'/tests/testdata');
		$this->assertTrue(array_search('testfile.txt', $ret));
		unset($ret);

		unset($filehandler);
    }


	// Try to store nonexistant uploaded file
	function test_storeUploadedFile_nouploadfile()
	{
		$filehandler = new zgFiles();
		
		$ret = $filehandler->storeUploadedFile('./tests/testdata/');
		$this->assertFalse($ret);
		unset($ret);

		unset($filehandler);
    }


	// Try to store uploaded file with errors
	function test_storeUploadedFile_fileerror()
	{
		$filehandler = new zgFiles();

		$_FILES["file"]["error"] = 1;
		$ret = $filehandler->storeUploadedFile('./tests/testdata/');
		$this->assertFalse($ret);
		unset($ret);

		unset($filehandler);
    }


	// Try to store uploaded file over existing file
	function test_storeUploadedFile_existingfile()
	{
		$filehandler = new zgFiles();

		$_FILES["file"]["error"] = 0;
		$_FILES["file"]["name"] = 'testfile.txt';
		$ret = $filehandler->storeUploadedFile('./tests/testdata/');
		$this->assertFalse($ret);
		unset($ret);

		unset($filehandler);
    }

}

?>
