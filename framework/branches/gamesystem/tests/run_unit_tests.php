<?php

	define('MULTITEST', true);

	if (!defined('SIMPLE_TEST'))
	{
	    define('SIMPLE_TEST', './simpletest/');
	}
	require_once(SIMPLE_TEST . 'autorun.php');

//	define('DEBUGMODE', true);
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../');
	if (!defined('GAMESYSTEM_ACTIONDIRECTORY')) define('GAMESYSTEM_ACTIONDIRECTORY', ZEITGEIST_ROOTDIRECTORY . 'tests/testdata/');


	// This should be an empty database without any tables
	// The test cases will create the tables themselves as needed
	define('ZG_DB_DBSERVER', 'localhost');
	define('ZG_DB_USERNAME', 'root');
	define('ZG_DB_USERPASS', '');
	define('ZG_DB_DATABASE', 'zg_test');
	define('ZG_DB_CONFIGURATIONCACHE', 'configurationcache');

	require_once('../zeitgeist.php');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$controller = new zgController();

	require_once('./_testfunctions.php');
	
	require_once('./unittests/messages.test.php');
	require_once('./unittests/objects.test.php');
	require_once('./unittests/configuration.test.php');
	require_once('./unittests/files.test.php');
	require_once('./unittests/actionlog.test.php');
	require_once('./unittests/parameters.test.php');
	require_once('./unittests/userfunctions.test.php');
	require_once('./unittests/userdata.test.php');
	require_once('./unittests/userroles.test.php');
	require_once('./unittests/userrights.test.php');
	require_once('./unittests/userhandler.test.php');
	require_once('./unittests/localisation.test.php');
	require_once('./unittests/gamedata.test.php');
	require_once('./unittests/gamesetup.test.php');
	require_once('./unittests/gamehandler.test.php');
	
	$debug = zgDebug::init();

    $test = &new TestSuite('Zeitgeist Unit Tests');

	$test->addTestCase(new testMessages());
	$test->addTestCase(new testObjects());
	$test->addTestCase(new testConfiguration());
	$test->addTestCase(new testFiles());
	$test->addTestCase(new testactionlog());
	$test->addTestCase(new testParameters());
	$test->addTestCase(new testUserfunctions());
	$test->addTestCase(new testUserdata());
	$test->addTestCase(new testUserroles());
	$test->addTestCase(new testUserrights());
	$test->addTestCase(new testUserhandler());
	$test->addTestCase(new testLocalisation());
	$test->addTestCase(new testGamesetup());
	$test->addTestCase(new testGamedata());
	$test->addTestCase(new testGamehandler());
	
	$test->run(new HtmlReporter());

	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
//	$debug->showGuardMessages();
	
?>

