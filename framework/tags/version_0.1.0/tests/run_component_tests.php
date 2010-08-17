<?php

	// SimpleTest is used for the tests however it is not part of the SVN
	// Download SimpleTest from here: http://www.simpletest.org/
	// Place it into a directory and change the path below to point there
	if (! defined('SIMPLE_TEST'))
	{
	    define('SIMPLE_TEST', 'simpletest/');
	}
	require_once(SIMPLE_TEST . 'autorun.php');
				
	define('DEBUGMODE', true);
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../');

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

	if ( (!empty($_GET['step'])) && ($_GET['step'] == 2))
	{
		require_once('./componenttests/step2_messages.test.php');
		require_once('./componenttests/step2_userhandler.test.php');
		echo "<h2><a href='run_component_tests.php?step=1'>Step 1</a></h2>";
	}
	else
	{
		require_once('./componenttests/step1_messages.test.php');
		require_once('./componenttests/step1_userhandler.test.php');
		echo "<h2><a href='run_component_tests.php?step=2'>Step 2</a></h2>";
	}

	$debug = zgDebug::init();

    $test = &new TestSuite('Zeitgeist Unit Tests');
    $test->addTestCase(new testMessages());
    $test->addTestCase(new testUserhandler());
    $test->run(new HtmlReporter());

	$debug->loadStylesheet('debug.css');
//	$debug->showInnerLoops = true;
//	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
//	$debug->showGuardMessages();
	
?>

