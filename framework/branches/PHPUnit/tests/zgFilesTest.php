<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgFiles test case.
 */
class zgFilesTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @var zgFiles
	 */
	private $zgFiles;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->zgFiles = new zgFiles(/* parameters */);
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->zgFiles = null;
		parent::tearDown();
	}


	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}


	/**
	 * Tests zgFiles->__construct()
	 */
	public function test__construct()
	{
		// TODO Auto-generated zgFilesTest->test__construct()
		$this->markTestIncomplete( "__construct test not implemented" );
		
		$this->zgFiles->__construct(/* parameters */);
	
	}


	/**
	 * Tests zgFiles->getFileContent()
	 */
	public function testGetFileContent_WrongFilename()
	{
		$this->setUp();
		
		$ret = $this->zgFiles->getFileContent( 'false' );
		$this->assertFalse( $ret );
		
		$this->tearDown();
	}


	/**
	 * Tests zgFiles->getFileContent()
	 */
	public function testGetFileContent_Success()
	{
		$this->setUp();
		
		$ret = $this->zgFiles->getFileContent( ZG_TESTDATA_DIR . 'testfile.txt' );
		$this->assertEquals( $ret, 'Hello World!' );
		
		$this->tearDown();
	}


	/**
	 * Tests zgFiles->getDirectoryListing()
	 */
	public function testGetDirectoryListing_WrongDirectoryName()
	{
		$this->setUp();
		
		$ret = $this->zgFiles->getDirectoryListing( 'false' );
		$this->assertFalse( $ret );
		
		$this->tearDown();
	}


	/**
	 * Tests zgFiles->getDirectoryListing()
	 */
	public function testGetDirectoryListing_Success()
	{
		$this->setUp();
		
		$ret = $this->zgFiles->getFileContent( 'tests/testdata/' );
		$this->assertTrue( array_search( 'testfile.txt', $ret ) );
		//array_search('testfile.txt', $ret)
		$this->tearDown();
	}


	/**
	 * Tests zgFiles->storeUploadedFile()
	 */
	public function testStoreUploadedFile_NoUploadFile()
	{
		$this->setUp();
		
		$ret = $this->zgFiles->storeUploadedFile( ZG_TESTDATA_DIR );
		$this->assertFalse( $ret );
		
		$this->tearDown();
	
	}


	/**
	 * Tests zgFiles->storeUploadedFile()
	 */
	public function testStoreUploadedFile_Fileerror()
	{
		$this->setUp();
		
		$_FILES ["file"] ["error"] = 1;
		$ret = $this->zgFiles->storeUploadedFile( ZG_TESTDATA_DIR );
		$this->assertFalse( $ret );
		
		$this->tearDown();
	
	}


	/**
	 * Tests zgFiles->storeUploadedFile()
	 */
	public function testStoreUploadedFile_FileAlreadyExists()
	{
		$this->setUp();
		
		$_FILES ["file"] ["error"] = 0;
		$_FILES ["file"] ["name"] = 'testfile.txt';
		$ret = $this->zgFiles->storeUploadedFile( ZG_TESTDATA_DIR );
		$this->assertFalse( $ret );
		
		$this->tearDown();
	
	}

}

