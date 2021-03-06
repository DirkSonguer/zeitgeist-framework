<?php

if ( !defined( 'MULTITEST' ) )
{
	include( dirname( __FILE__ ) . '/../_configuration.php' );
}

/**
 * zgParameters test case.
 */
class zgParametersTest extends UnitTestCase
{
	/**
	 * @var zgParameters
	 */
	private $zgParameters;


	/**
	 * Prepares the environment before running a test.
	 */
	public function setUp( )
	{
		parent::setUp( );
		$this->zgParameters = new zgParameters( /* parameters */ );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	public function tearDown( )
	{
		$this->zgParameters = null;
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
	 * Tests zgParameters->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgParametersTest->test__construct()
		$this->zgParameters->__construct( /* parameters */ );
	}


	/**
	 * Tests zgParameters->getSafeParameters()
	 */
	public function testGetSafeParameters_RegexpValid( )
	{
		$_GET ['test_regexp'] = '1234';
		$this->setUp( );

		$configuration = zgConfiguration::init( );
		$ret = $configuration->loadConfiguration( 'testparameters', ZG_TESTDATA_DIR . 'testparameters.ini' );

		$ret = $this->zgParameters->getSafeParameters( 'testparameters', 'test_parameters' );
		$this->assertNull( $ret ['false'] );
		$this->assertNotNull( $ret ['test_regexp'] );
		$this->assertEqual( $ret ['test_regexp'], '1234' );

		$objects = zgObjects::init( );
		$objects->deleteAllObjects( );

		$this->tearDown( );
	}


	/**
	 * Tests zgParameters->getSafeParameters()
	 */
	public function testGetSafeParameters_EscapedValid( )
	{
		$_GET ['test_regexp_escaped'] = "12'34";
		$this->setUp( );

		$configuration = zgConfiguration::init( );
		$ret = $configuration->loadConfiguration( 'testparameters', ZG_TESTDATA_DIR . 'testparameters.ini' );

		$ret = $this->zgParameters->getSafeParameters( 'testparameters', 'test_parameters' );
		$this->assertNull( $ret ['false'] );
		$this->assertNotNull( $ret ['test_regexp_escaped'] );
		$this->assertEqual( $ret ['test_regexp_escaped'], "12\'34" );

		$objects = zgObjects::init( );
		$objects->deleteAllObjects( );

		$this->tearDown( );
	}


	/**
	 * Tests zgParameters->getSafeParameters()
	 */
	public function testGetSafeParameters_RegexpEscapedInvalid( )
	{
		$_GET ['test_regexp_escaped'] = '12';
		$this->setUp( );

		$configuration = zgConfiguration::init( );
		$ret = $configuration->loadConfiguration( 'testparameters', ZG_TESTDATA_DIR . 'testparameters.ini' );

		$ret = $this->zgParameters->getSafeParameters( 'testparameters', 'test_parameters' );
		$this->assertNull( $ret ['false'] );
		$this->assertNull( $ret ['test_regexp_escaped'] );

		$objects = zgObjects::init( );
		$objects->deleteAllObjects( );

		$this->tearDown( );
	}


	/**
	 * Tests zgParameters->getSafeParameters()
	 */
	public function testGetSafeParameters_TextValid( )
	{
		$_GET ['test_text'] = "This is a test";
		$this->setUp( );

		$configuration = zgConfiguration::init( );
		$ret = $configuration->loadConfiguration( 'testparameters', ZG_TESTDATA_DIR . 'testparameters.ini' );

		$ret = $this->zgParameters->getSafeParameters( 'testparameters', 'test_parameters' );
		$this->assertNull( $ret ['false'] );
		$this->assertNotNull( $ret ['test_text'] );
		$this->assertEqual( $ret ['test_text'], "This is a test" );

		$objects = zgObjects::init( );
		$objects->deleteAllObjects( );

		$this->tearDown( );
	}


	/**
	 * Tests zgParameters->getSafeParameters()
	 */
	public function testGetSafeParameters_TextInvalid( )
	{
		$_GET ['test_text'] = "Illegal chars: ';";
		$this->setUp( );

		$configuration = zgConfiguration::init( );
		$ret = $configuration->loadConfiguration( 'testparameters', ZG_TESTDATA_DIR . 'testparameters.ini' );

		$ret = $this->zgParameters->getSafeParameters( 'testparameters', 'test_parameters' );
		$this->assertNull( $ret ['false'] );
		$this->assertNull( $ret ['test_text'] );

		$objects = zgObjects::init( );
		$objects->deleteAllObjects( );

		$this->tearDown( );
	}


	/**
	 * Tests zgParameters->getSafeParameters()
	 */
	public function testGetSafeParameters_StringValid( )
	{
		$_GET ['test_string'] = "This is a test";
		$this->setUp( );

		$configuration = zgConfiguration::init( );
		$ret = $configuration->loadConfiguration( 'testparameters', ZG_TESTDATA_DIR . 'testparameters.ini' );

		$ret = $this->zgParameters->getSafeParameters( 'testparameters', 'test_parameters' );
		$this->assertNull( $ret ['false'] );
		$this->assertEqual( $ret ['test_string'], "This is a test" );

		$objects = zgObjects::init( );
		$objects->deleteAllObjects( );

		$this->tearDown( );
	}


	/**
	 * Tests zgParameters->getSafeParameters()
	 */
	public function testGetSafeParameters_StringInvalid( )
	{
		$_GET ['test_string'] = "Illegal chars: \n\t";
		$this->setUp( );

		$configuration = zgConfiguration::init( );
		$ret = $configuration->loadConfiguration( 'testparameters', ZG_TESTDATA_DIR . 'testparameters.ini' );

		$ret = $this->zgParameters->getSafeParameters( 'testparameters', 'test_parameters' );
		$this->assertNull( $ret ['false'] );
		$this->assertNull( $ret ['test_string'] );

		$objects = zgObjects::init( );
		$objects->deleteAllObjects( );

		$this->tearDown( );
	}


	/**
	 * Tests zgParameters->getSafeParameters()
	 */
	public function testGetSafeParameters_DateValid( )
	{
		$_GET ['test_date'] = "01.01.1970";
		$this->setUp( );

		$configuration = zgConfiguration::init( );
		$ret = $configuration->loadConfiguration( 'testparameters', ZG_TESTDATA_DIR . 'testparameters.ini' );

		$ret = $this->zgParameters->getSafeParameters( 'testparameters', 'test_parameters' );
		$this->assertNull( $ret ['false'] );
		$this->assertEqual( $ret ['test_date'], "01.01.1970" );

		$objects = zgObjects::init( );
		$objects->deleteAllObjects( );

		$this->tearDown( );
	}


	/**
	 * Tests zgParameters->getSafeParameters()
	 */
	public function testGetSafeParameters_DateInvalid( )
	{
		$_GET ['test_date'] = "001.001.1970";
		$this->setUp( );

		$configuration = zgConfiguration::init( );
		$ret = $configuration->loadConfiguration( 'testparameters', ZG_TESTDATA_DIR . 'testparameters.ini' );

		$ret = $this->zgParameters->getSafeParameters( 'testparameters', 'test_parameters' );
		$this->assertNull( $ret ['false'] );
		$this->assertNull( $ret ['test_date'] );

		$objects = zgObjects::init( );
		$objects->deleteAllObjects( );

		$this->tearDown( );
	}
}

if ( !defined( 'MULTITEST' ) )
{
	$test = &new TestSuite( 'zgParametersTest Unit Tests' );

	$testfunctions = new testFunctions( );
	$test->addTestCase( new zgParametersTest( ) );

	$test->run( new HtmlReporter( ) );
}