<?php

define( 'DEBUGMODE', true );
define( 'MULTITEST', true );
include( dirname( __FILE__ ) . '/_configuration.php' );

$debug = zgDebug::init( );

require_once 'unittests\zgActionlogTest.php';
require_once 'unittests\zgConfigurationTest.php';
require_once 'unittests\zgDatabaseTest.php';
require_once 'unittests\zgFilesTest.php';
require_once 'unittests\zgGamedataTest.php';
require_once 'unittests\zgGamehandlerTest.php';
require_once 'unittests\zgGamesetupTest.php';
require_once 'unittests\zgLocalisationTest.php';
require_once 'unittests\zgMessagesTest.php';
require_once 'unittests\zgObjectsTest.php';
require_once 'unittests\zgParametersTest.php';
require_once 'unittests\zgUserdataTest.php';
require_once 'unittests\zgUserfunctionsTest.php';
require_once 'unittests\zgUserhandlerTest.php';
require_once 'unittests\zgUserrightsTest.php';
require_once 'unittests\zgUserrolesTest.php';

$test = &new TestSuite( 'Zeitgeist Unit Tests' );

$testfunctions = new testFunctions( );
$test->addTestCase( new zgActionlogTest( ) );
$test->addTestCase( new zgConfigurationTest( ) );
$test->addTestCase( new zgFilesTest( ) );
$test->addTestCase( new zgGamedataTest( ) );
$test->addTestCase( new zgGamehandlerTest( ) );
$test->addTestCase( new zgGamesetupTest( ) );
$test->addTestCase( new zgLocalisationTest( ) );
$test->addTestCase( new zgMessagesTest( ) );
$test->addTestCase( new zgObjectsTest( ) );
$test->addTestCase( new zgParametersTest( ) );
$test->addTestCase( new zgUserdataTest( ) );
$test->addTestCase( new zgUserfunctionsTest( ) );
$test->addTestCase( new zgUserhandlerTest( ) );
$test->addTestCase( new zgUserrightsTest( ) );
$test->addTestCase( new zgUserrolesTest( ) );
$test->addTestCase( new zgDatabaseTest( ) );

$test->run( new HtmlReporter( ) );

$debug->loadStylesheet( 'debug.css' );
//	$debug->showInnerLoops = true;
$debug->showMiscInformation();
$debug->showDebugMessages( );
$debug->showQueryMessages();
$debug->showGuardMessages();