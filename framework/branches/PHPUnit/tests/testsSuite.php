<?php

define( 'DEBUGMODE', true );
require_once 'tests\_configuration.php';

require_once 'PHPUnit\Framework\TestSuite.php';

require_once 'tests\zgConfigurationTest.php';
require_once 'tests\zgDatabaseTest.php';
require_once 'tests\zgFilesTest.php';
require_once 'tests\zgGamedataTest.php';
require_once 'tests\zgGamehandlerTest.php';
require_once 'tests\zgGamesetupTest.php';
require_once 'tests\zgMessagesTest.php';
require_once 'tests\zgObjectsTest.php';
require_once 'tests\zgParametersTest.php';

/**
 * Static test suite.
 */
class testsSuite extends PHPUnit_Framework_TestSuite
{


	/**
	 * Constructs the test suite handler.
	 */
	public function __construct()
	{
		$this->setName( 'testsSuite' );
		
		$this->addTestSuite( 'zgConfigurationTest' );
		$this->addTestSuite( 'zgDatabaseTest' );
		$this->addTestSuite( 'zgFilesTest' );
		$this->addTestSuite( 'zgGamedataTest' );
		$this->addTestSuite( 'zgGamehandlerTest' );
		$this->addTestSuite( 'zgGamesetupTest' );
		$this->addTestSuite( 'zgMessagesTest' );
		$this->addTestSuite( 'zgObjectsTest' );
		$this->addTestSuite( 'zgParametersTest' );
	}


	/**
	 * Creates the suite.
	 */
	public static function suite()
	{
		return new self();
	}
}

