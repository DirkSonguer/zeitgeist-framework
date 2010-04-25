<?php

require_once 'PHPUnit\Framework\TestSuite.php';

require_once 'tests\unittests\zgActionlogTest.php';

require_once 'tests\unittests\zgConfigurationTest.php';

require_once 'tests\unittests\zgDatabaseTest.php';

require_once 'tests\unittests\zgFilesTest.php';

require_once 'tests\unittests\zgGamedataTest.php';

require_once 'tests\unittests\zgGamehandlerTest.php';

require_once 'tests\unittests\zgGamesetupTest.php';

require_once 'tests\unittests\zgLocalisationTest.php';

require_once 'tests\unittests\zgMessagesTest.php';

require_once 'tests\unittests\zgObjectsTest.php';

require_once 'tests\unittests\zgParametersTest.php';

require_once 'tests\unittests\zgUserdataTest.php';

require_once 'tests\unittests\zgUserfunctionsTest.php';

require_once 'tests\unittests\zgUserhandlerTest.php';

require_once 'tests\unittests\zgUserrightsTest.php';

require_once 'tests\unittests\zgUserrolesTest.php';

/**
 * Static test suite.
 */
class runUnitTests extends PHPUnit_Framework_TestSuite
{


	/**
	 * Constructs the test suite handler.
	 */
	public function __construct()
	{
		$this->setName( 'runUnitTests' );
		
		$this->addTestSuite( 'zgActionlogTest' );
		
		$this->addTestSuite( 'zgConfigurationTest' );

		$this->addTestSuite( 'zgDatabaseTest' );
		
		$this->addTestSuite( 'zgFilesTest' );
		
		$this->addTestSuite( 'zgGamedataTest' );
		
		$this->addTestSuite( 'zgGamehandlerTest' );
		
		$this->addTestSuite( 'zgGamesetupTest' );
		
		$this->addTestSuite( 'zgLocalisationTest' );
		
		$this->addTestSuite( 'zgMessagesTest' );
		
		$this->addTestSuite( 'zgObjectsTest' );
		
		$this->addTestSuite( 'zgParametersTest' );
		
		$this->addTestSuite( 'zgUserdataTest' );
		
		$this->addTestSuite( 'zgUserfunctionsTest' );
		
		$this->addTestSuite( 'zgUserhandlerTest' );
		
		$this->addTestSuite( 'zgUserrightsTest' );
		
		$this->addTestSuite( 'zgUserrolesTest' );

	}


	/**
	 * Creates the suite.
	 */
	public static function suite()
	{
		return new self();
	}
}

