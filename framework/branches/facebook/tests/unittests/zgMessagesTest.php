<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgMessages test case.
 */
class zgMessagesTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @var zgMessages
	 */
	private $zgMessages;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->zgMessages = zgMessages::init();
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->zgMessages = null;
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
	 * Tests zgMessages::init()
	 */
	public function testInit_Success()
	{
		$this->setUp();
		$this->assertNotNull( $this->zgMessages );
		$this->tearDown();
	}


	/**
	 * Tests zgMessages->setMessage()
	 */
	public function testSetMessage_WithoutType()
	{
		$this->setUp();
		
		$ret = $this->zgMessages->setMessage( 'hello world' );
		$this->assertTrue( $ret );
		
		$this->tearDown();
	}


	/**
	 * Tests zgMessages->setMessage()
	 */
	public function testSetMessage_WithType()
	{
		$this->setUp();
		
		$ret = $this->zgMessages->setMessage( 'hello world', 'message' );
		$this->assertTrue( $ret );
		
		$this->tearDown();
	}


	/**
	 * Tests zgMessages->getMessagesByType()
	 */
	public function testGetMessagesByType_NoMessages()
	{
		$this->setUp();
		
		$this->zgMessages->clearAllMessages();
		$ret = $this->zgMessages->getMessagesByType();
		$this->assertEquals( $ret, array () );
		
		$this->tearDown();
	}


	/**
	 * Tests zgMessages->getMessagesByType()
	 */
	public function testGetMessagesByType_MessagesWithoutType()
	{
		$this->setUp();
		
		$this->zgMessages->clearAllMessages();
		$this->zgMessages->setMessage( 'hello world' );
		
		$ret = $this->zgMessages->getMessagesByType( 'test' );
		$this->assertEquals( $ret, array () );
		
		$this->tearDown();
	}


	/**
	 * Tests zgMessages->getMessagesByType()
	 */
	public function testGetMessagesByType_GetDefault()
	{
		$this->setUp();
		
		$this->zgMessages->clearAllMessages();
		$this->zgMessages->setMessage( 'hello world' );
		
		$ret = $this->zgMessages->getMessagesByType( 'message' );
		$this->assertEquals( count( $ret ), 1 );
		
		$this->tearDown();
	}


	/**
	 * Tests zgMessages->getMessagesByType()
	 */
	public function testGetMessagesByType_Success()
	{
		$this->setUp();
		
		$this->zgMessages->clearAllMessages();
		$this->zgMessages->setMessage( 'hello world' );
		$this->zgMessages->setMessage( 'hello world', 'test' );
		$this->zgMessages->setMessage( 'hello world', 'other' );
		
		$ret = $this->zgMessages->getMessagesByType( 'test' );
		$this->assertEquals( count( $ret ), 1 );
		
		$this->tearDown();
	}


	/**
	 * Tests zgMessages->getAllMessages()
	 */
	public function testGetAllMessages_Success()
	{
		$this->setUp();
		
		$this->zgMessages->clearAllMessages();
		$this->zgMessages->setMessage( 'hello world' );
		$this->zgMessages->setMessage( 'hello world', 'test' );
		$this->zgMessages->setMessage( 'hello world', 'other' );
		
		$ret = $this->zgMessages->getAllMessages();
		$this->assertEquals( count( $ret ), 3 );
		
		$this->tearDown();
	}


	/**
	 * Tests zgMessages->clearAllMessages()
	 */
	public function testClearAllMessages()
	{
		$this->setUp();
		
		$this->zgMessages->setMessage( 'hello world' );
		
		$ret = $this->zgMessages->clearAllMessages();
		$this->assertTrue( $ret );
		
		$ret = $this->zgMessages->getAllMessages();
		$this->assertEquals( count( $ret ), 0 );
		
		$this->tearDown();
	}


	/**
	 * Tests zgMessages->importMessages()
	 */
	public function testImportMessages_NoArray()
	{
		$this->setUp();
		
		$this->zgMessages->clearAllMessages();
		
		$ret = $this->zgMessages->importMessages( '' );
		$this->assertFalse( $ret );
		
		$this->tearDown();
	}


	/**
	 * Tests zgMessages->importMessages()
	 */
	public function testImportMessages_EmptyArray()
	{
		$this->setUp();
		
		$this->zgMessages->clearAllMessages();
		
		$ret = $this->zgMessages->importMessages( array () );
		$this->assertTrue( $ret );
		
		$ret = $this->zgMessages->getAllMessages();
		$this->assertEquals( count( $ret ), 0 );
		
		$this->tearDown();
	}


	/**
	 * Tests zgMessages->importMessages()
	 */
	public function testImportMessages_Success()
	{
		$this->setUp();
		
		$this->zgMessages->clearAllMessages();
		$this->zgMessages->setMessage( 'test1', 'test' );
		$this->zgMessages->setMessage( 'test2', 'test' );
		$testmessages = $this->zgMessages->getAllMessages();
		
		$this->zgMessages->clearAllMessages();
		$ret = $this->zgMessages->importMessages( $testmessages );
		$this->assertTrue( $ret );
		
		$ret = $this->zgMessages->getAllMessages();
		$this->assertEquals( count( $ret ), 2 );
		$this->assertEquals( $ret, $testmessages );
		
		$this->tearDown();
	}


	/**
	 * Tests zgMessages->saveMessagesToSession()
	 */
	public function testSaveMessagesToSession()
	{
		// TODO Auto-generated zgMessagesTest->testSaveMessagesToSession()
		$this->markTestIncomplete( "saveMessagesToSession test not implemented" );
		
		$this->zgMessages->saveMessagesToSession(/* parameters */);
	
	}


	/**
	 * Tests zgMessages->loadMessagesFromSession()
	 */
	public function testLoadMessagesFromSession()
	{
		// TODO Auto-generated zgMessagesTest->testLoadMessagesFromSession()
		$this->markTestIncomplete( "loadMessagesFromSession test not implemented" );
		
		$this->zgMessages->loadMessagesFromSession(/* parameters */);
	
	}

}

