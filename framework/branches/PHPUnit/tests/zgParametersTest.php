<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgParameters test case.
 */
class zgParametersTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @var zgParameters
	 */
	private $zgParameters;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->zgParameters = new zgParameters(/* parameters */);
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->zgParameters = null;
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
	 * Tests zgParameters->__construct()
	 */
	public function test__construct()
	{
		// TODO Auto-generated zgParametersTest->test__construct()
		$this->markTestIncomplete( "__construct test not implemented" );
		
		$this->zgParameters->__construct(/* parameters */);
	
	}


	/**
	 * Tests zgParameters->getSafeParameters()
	 */
	public function testGetSafeParameters_RegexpValid()
	{
		$this->setUp();
		
		$_GET ['test_regexp'] = '1234';
		$configuration = zgConfiguration::init();
		$ret = $configuration->loadConfiguration( 'testparameters', ZG_TESTDATA_DIR . 'testparameters.ini' );
		
		$ret = $this->zgParameters->getSafeParameters( 'testparameters', 'test_parameters' );
		$this->assertNull( $ret ['false'] );
		$this->assertNotNull( $ret ['test_regexp'] );
		$this->assertEquals( $ret ['test_regexp'], '1234' );
		
		$objects = zgObjects::init();
		$objects->deleteAllObjects();
		
		$this->tearDown();
	}

}

