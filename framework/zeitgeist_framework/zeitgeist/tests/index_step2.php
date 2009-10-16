<?php

	if (! defined('SIMPLE_TEST'))
	{
	    define('SIMPLE_TEST', 'simpletest/');
	}
	require_once(SIMPLE_TEST . 'autorun.php');
				
	define('DEBUGMODE', true);
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../');

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
	$locale = zgLocalisation::init();
	
	require_once('userhandler_step2.test.php');
	require_once('messagecache_step2.test.php');
	require_once('userhandler_step3.test.php');

	$debug = zgDebug::init();

    $test = &new TestSuite('Zeitgeist Unit Tests');
    $test->addTestCase(new testUserhandler_s2());
    $test->addTestCase(new testMessagecache_s2());
    $test->addTestCase(new testUserhandler_s3());
    $test->run(new HtmlReporter());

	echo "<h2><a href='index.php'>Step 1</a></h2>";

	$debug->loadStylesheet('debug.css');
//	$debug->showInnerLoops = true;
//	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
//	$debug->showGuardMessages();
	
?>

