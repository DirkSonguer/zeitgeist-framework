<?php

if ( !defined( 'MULTITEST' ) )
{
	include( dirname( __FILE__ ) . '/../_configuration.php' );
}

/**
 * zgMessages test case.
 */
class zgMessagesTest extends UnitTestCase
{
	/**
	 * @var zgMessages
	 */
	private $zgMessages;


	/**
	 * Prepares the environment before running a test.
	 */
	public function setUp( )
	{
		parent::setUp( );
		$this->zgMessages = zgMessages::init( );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	public function tearDown( )
	{
		$this->zgMessages = null;
		parent::tearDown( );
	}


	/**
	 * Constructs the test case.
	 */
	public function __construct( )
	{
		// TODO Auto-generated constructor
	}


	/**
	 * Tests zgMessages::init()
	 */
	public function testInit_Success( )
	{
		$this->setUp( );
		$this->assertNotNull( $this->zgMessages );
		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->setMessage()
	 */
	public function testSetMessage_WithoutType( )
	{
		$this->setUp( );

		$ret = $this->zgMessages->setMessage( 'hello world' );
		$this->assertTrue( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->setMessage()
	 */
	public function testSetMessage_WithType( )
	{
		$this->setUp( );

		$ret = $this->zgMessages->setMessage( 'hello world', 'message' );
		$this->assertTrue( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->getLastMessage()
	 */
	public function testGetLastMessage_NoMessage( )
	{
		$this->setUp( );

		$this->zgMessages->clearAllMessages( );
		$ret = $this->zgMessages->getLastMessage( );
		$this->assertNull( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->getLastMessage()
	 */
	public function testGetLastMessage_OneMessage( )
	{
		$this->setUp( );

		$this->zgMessages->clearAllMessages( );
		$this->zgMessages->setMessage( 'hello world' );
		$ret = $this->zgMessages->getLastMessage( );
		$this->assertEqual( $ret->message, 'hello world' );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->getLastMessage()
	 */
	public function testGetLastMessage_TwoMessages( )
	{
		$this->setUp( );

		$this->zgMessages->clearAllMessages( );
		$this->zgMessages->setMessage( 'hello world' );
		$this->zgMessages->setMessage( 'hello message' );
		$ret = $this->zgMessages->getLastMessage( );
		$this->assertEqual( $ret->message, 'hello message' );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->getMessagesByType()
	 */
	public function testGetMessagesByType_NoMessages( )
	{
		$this->setUp( );

		$this->zgMessages->clearAllMessages( );
		$ret = $this->zgMessages->getMessagesByType( );
		$this->assertEqual( $ret, array( ) );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->getMessagesByType()
	 */
	public function testGetMessagesByType_MessagesWithoutType( )
	{
		$this->setUp( );

		$this->zgMessages->clearAllMessages( );
		$this->zgMessages->setMessage( 'hello world' );

		$ret = $this->zgMessages->getMessagesByType( 'test' );
		$this->assertEqual( $ret, array( ) );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->getMessagesByType()
	 */
	public function testGetMessagesByType_GetDefault( )
	{
		$this->setUp( );

		$this->zgMessages->clearAllMessages( );
		$this->zgMessages->setMessage( 'hello world' );

		$ret = $this->zgMessages->getMessagesByType( 'message' );
		$this->assertEqual( count( $ret ), 1 );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->getMessagesByType()
	 */
	public function testGetMessagesByType_Success( )
	{
		$this->setUp( );

		$this->zgMessages->clearAllMessages( );
		$this->zgMessages->setMessage( 'hello world' );
		$this->zgMessages->setMessage( 'hello world', 'test' );
		$this->zgMessages->setMessage( 'hello world', 'other' );

		$ret = $this->zgMessages->getMessagesByType( 'test' );
		$this->assertEqual( count( $ret ), 1 );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->getAllMessages()
	 */
	public function testGetAllMessages_Success( )
	{
		$this->setUp( );

		$this->zgMessages->clearAllMessages( );
		$this->zgMessages->setMessage( 'hello world' );
		$this->zgMessages->setMessage( 'hello world', 'test' );
		$this->zgMessages->setMessage( 'hello world', 'other' );

		$ret = $this->zgMessages->getAllMessages( );
		$this->assertEqual( count( $ret ), 3 );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->clearAllMessages()
	 */
	public function testClearAllMessages( )
	{
		$this->setUp( );

		$this->zgMessages->setMessage( 'hello world' );

		$ret = $this->zgMessages->clearAllMessages( );
		$this->assertTrue( $ret );

		$ret = $this->zgMessages->getAllMessages( );
		$this->assertEqual( count( $ret ), 0 );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->importMessages()
	 */
	public function testImportMessages_NoArray( )
	{
		$this->setUp( );

		$this->zgMessages->clearAllMessages( );

		$ret = $this->zgMessages->importMessages( '' );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->importMessages()
	 */
	public function testImportMessages_EmptyArray( )
	{
		$this->setUp( );

		$this->zgMessages->clearAllMessages( );

		$ret = $this->zgMessages->importMessages( array( ) );
		$this->assertTrue( $ret );

		$ret = $this->zgMessages->getAllMessages( );
		$this->assertEqual( count( $ret ), 0 );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->importMessages()
	 */
	public function testImportMessages_Success( )
	{
		$this->setUp( );

		$this->zgMessages->clearAllMessages( );
		$this->zgMessages->setMessage( 'test1', 'test' );
		$this->zgMessages->setMessage( 'test2', 'test' );
		$testmessages = $this->zgMessages->getAllMessages( );

		$this->zgMessages->clearAllMessages( );
		$ret = $this->zgMessages->importMessages( $testmessages );
		$this->assertTrue( $ret );

		$ret = $this->zgMessages->getAllMessages( );
		$this->assertEqual( count( $ret ), 2 );
		$this->assertEqual( $ret, $testmessages );

		$this->tearDown( );
	}


	/**
	 * Tests zgMessages->saveMessagesToSession()
	 */
	public function testSaveMessagesToSession( )
	{
		// TODO Auto-generated zgMessagesTest->testSaveMessagesToSession()
		//		$this->zgMessages->saveMessagesToSession( /* parameters */ );
	}


	/**
	 * Tests zgMessages->loadMessagesFromSession()
	 */
	public function testLoadMessagesFromSession( )
	{
		// TODO Auto-generated zgMessagesTest->testLoadMessagesFromSession()
		//		$this->zgMessages->loadMessagesFromSession( /* parameters */ );
	}
}

if ( !defined( 'MULTITEST' ) )
{
	$test = &new TestSuite( 'zgMessagesTest Unit Tests' );

	$testfunctions = new testFunctions( );
	$test->addTestCase( new zgMessagesTest( ) );

	$test->run( new HtmlReporter( ) );
}