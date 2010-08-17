<?php

if ( !defined( 'MULTITEST' ) )
{
	include( dirname( __FILE__ ) . '/../_configuration.php' );
}

/**
 * zgController test case.
 */
class zgControllerTest extends UnitTestCase
{
	/**
	 * @var zgController
	 */
	private $zgController;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	public function setUp( )
	{
		parent::setUp( );
		$this->zgController = new zgController( /* parameters */ );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	public function tearDown( )
	{
		$this->zgController = null;
		parent::tearDown( );
	}


	/**
	 * Constructs the test case.
	 */
	public function __construct( )
	{
		$this->database = new zgDatabase( );
		$this->database->connect( );
	}


	/**
	 * Tests zgController->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgUserrightsTest->test__construct()
		$this->zgController->__construct( /* parameters */ );
	}


	/**
	 * Tests zgController->callEvent()
	 */
	public function testCallEvent_NoDatabase( )
	{
		$this->setUp( );

		$ret = $this->zgController->callEvent( 'test', 'test' );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgController->callEvent()
	 */
	public function testCallEvent_ModuleNotFound( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'modules' );

		$testmodulename = uniqid( );

		$messages = zgMessages::init( );
		$messages->clearAllMessages( );

		$ret = $this->zgController->callEvent( $testmodulename, 'test' );
		$this->assertFalse( $ret );

		$testMessages = $messages->getAllMessages( 'controller.class.php' );
		$this->assertEqual( count( $testMessages ), 1 );
		$this->assertEqual( $testMessages[0]->message, "Problem loading the module: Module is not found / installed: " . $testmodulename );

		$testfunctions->dropZeitgeistTable( 'modules' );
		$this->tearDown( );
	}


	/**
	 * Tests zgController->callEvent()
	 */
	public function testCallEvent_ModuleInactive( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );
		$testfunctions->createZeitgeistTable( 'modules' );

		$testmoduleid = rand( 1, 100 );
		$testmodulename = uniqid( );

		$this->database->query( "INSERT INTO modules(module_id, module_name, module_description, module_active) VALUES('" . $testmoduleid . "', '" . $testmodulename . "', 'test', '0')" );

		$messages = zgMessages::init( );
		$messages->clearAllMessages( );

		$ret = $this->zgController->callEvent( $testmodulename, 'test' );
		$this->assertFalse( $ret );

		$testMessages = $messages->getAllMessages( 'controller.class.php' );
		$this->assertEqual( count( $testMessages ), 1 );
		$this->assertEqual( $testMessages[0]->message, "Problem loading the module: Module is not active: " . $testmodulename );

		$testfunctions->dropZeitgeistTable( 'modules' );
		$this->tearDown( );
	}
}

if ( !defined( 'MULTITEST' ) )
{
	$test = &new TestSuite( 'zgController Unit Tests' );

	$testfunctions = new testFunctions( );
	$test->addTestCase( new zgControllerTest( ) );

	$test->run( new HtmlReporter( ) );
}