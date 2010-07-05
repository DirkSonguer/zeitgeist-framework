<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgActionlog test case.
 */
class zgActionlogTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var zgActionlog
	 */
	private $zgActionlog;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp( )
	{
		parent::setUp( );
		$this->zgActionlog = new zgActionlog( /* parameters */ );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown( )
	{
		$this->zgActionlog = null;
		parent::tearDown( );
	}


	/**
	 * Constructs the test case.
	 */
	public function __construct( )
	{
		$this->database = new zgDatabase( );
		$ret = $this->database->connect( );
	}


	/**
	 * Tests zgActionlog->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgActionlogTest->test__construct()
		$this->markTestIncomplete( "__construct test not implemented" );

		$this->zgActionlog->__construct( /* parameters */ );
	}


	/**
	 * Tests zgActionlog->logAction()
	 */
	public function testLogAction_NoActionTable( )
	{
		$this->setUp( );

		$param1 = rand( 100, 5000 );
		$param2 = rand( 100, 5000 );
		$param3 = rand( 100, 5000 );

		$parameters = array();
		$parameters ['test1'] = 'test' . $param1;
		$parameters ['test2'] = 'test' . $param2;
		$ret = $this->zgActionlog->logAction( $param1, $param2, $parameters );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgActionlog->logAction()
	 */
	public function testLogAction_NoParameterTable( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'actionlog' );

		$param1 = rand( 100, 5000 );
		$param2 = rand( 100, 5000 );
		$param3 = rand( 100, 5000 );

		$parameters = array();
		$parameters ['test1'] = 'test' . $param1;
		$parameters ['test2'] = 'test' . $param2;
		$ret = $this->zgActionlog->logAction( $param1, $param2, $parameters );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'actionlog' );
		$this->tearDown( );
	}


	/**
	 * Tests zgActionlog->logAction()
	 */
	public function testLogAction_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'actionlog' );
		$testfunctions->createZeitgeistTable( 'actionlog_parameters' );

		$param1 = rand( 100, 5000 );
		$param2 = rand( 100, 5000 );
		$param3 = rand( 100, 5000 );

		$parameters = array();
		$parameters ['test1'] = 'test' . $param1;
		$parameters ['test2'] = 'test' . $param2;
		$ret = $this->zgActionlog->logAction( $param1, $param2, $parameters );
		$this->assertTrue( $ret );

		$res = $this->database->query( "SELECT * FROM actionlog" );

		// should be only one entry we just entered
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 1 );

		$ret = $this->database->fetchArray( $res );
		$this->assertEquals( $ret ['actionlog_module'], $param1 );
		$this->assertEquals( $ret ['actionlog_action'], $param2 );

		$res = $this->database->query( "SELECT * FROM actionlog_parameters" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 2 );

		$ret = $this->database->fetchArray( $res );
		$this->assertEquals( $ret ['actionparameter_key'], 'test1' );
		$this->assertEquals( $ret ['actionparameter_value'], 'test' . $param1 );

		$ret = $this->database->fetchArray( $res );
		$this->assertEquals( $ret ['actionparameter_key'], 'test2' );
		$this->assertEquals( $ret ['actionparameter_value'], 'test' . $param2 );

		$testfunctions->dropZeitgeistTable( 'actionlog' );
		$testfunctions->dropZeitgeistTable( 'actionlog_parameters' );
		$this->tearDown( );
	}
}

