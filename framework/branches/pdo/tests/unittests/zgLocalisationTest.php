<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgLocalisation test case.
 */
class zgLocalisationTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var zgLocalisation
	 */
	private $zgLocalisation;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp( )
	{
		parent::setUp( );
		$this->zgLocalisation = new zgLocalisation( /* parameters */ );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown( )
	{
		$this->zgLocalisation = null;
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
	 * Tests zgLocalisation->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgLocalisationTest->test__construct()
		$this->markTestIncomplete( "__construct test not implemented" );

		$this->zgLocalisation->__construct( /* parameters */ );
	}


	/**
	 * Tests zgLocalisation->loadLocale()
	 */
	public function testLoadLocale_InvalidLocale( )
	{
		$this->setUp( );

		$ret = $this->zgLocalisation->loadLocale( 'testlocale', 'false' );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgLocalisation->loadLocale()
	 */
	public function testLoadLocale_Success( )
	{
		$this->setUp( );

		$randomid = uniqid( );
		$ret = $this->zgLocalisation->loadLocale( $randomid, ZG_TESTDATA_DIR . 'testlocale.ini' );
		$this->assertTrue( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgLocalisation->setLocale()
	 */
	public function testSetLocale_Invalid( )
	{
		$this->setUp( );

		$randomid = uniqid( );
		$this->zgLocalisation->loadLocale( $randomid, ZG_TESTDATA_DIR . 'testlocale.ini' );
		$ret = $this->zgLocalisation->setLocale( 'false' );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgLocalisation->setLocale()
	 */
	public function testSetLocale_Success( )
	{
		$this->setUp( );

		$randomid = uniqid( );
		$this->zgLocalisation->loadLocale( $randomid, ZG_TESTDATA_DIR . 'testlocale.ini' );
		$ret = $this->zgLocalisation->setLocale( $randomid );
		$this->assertTrue( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgLocalisation->write()
	 */
	public function testWrite_NoLocale( )
	{
		$this->setUp( );

		$randomid = uniqid( );
		$this->zgLocalisation->loadLocale( $randomid, ZG_TESTDATA_DIR . 'testlocale.ini' );
		$ret = $this->zgLocalisation->write( 'hello world' );
		$this->assertEquals( $ret, 'hello world' );

		$this->tearDown( );
	}


	/**
	 * Tests zgLocalisation->write()
	 */
	public function testWrite_Success( )
	{
		$this->setUp( );

		$this->zgLocalisation->loadLocale( 'testlocale', ZG_TESTDATA_DIR . 'testlocale.ini' );
		$this->zgLocalisation->setLocale( 'testlocale' );
		$ret = $this->zgLocalisation->write( 'this is a test' );
		$this->assertEquals( $ret, 'das ist ein test' );

		$this->tearDown( );
	}
}

