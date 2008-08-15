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
//	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();
	$locale = zgLocale::init();
	
	require_once('messages.test.php');
	require_once('database.test.php');
	require_once('configuration.test.php');
	require_once('locale.test.php');
	require_once('userroles.test.php');
	require_once('parameterhandler.test.php');

	$debug = zgDebug::init();

    $test = &new TestSuite('Zeitgeist Unit Tests');
    $test->addTestCase(new testMessages());
    $test->addTestCase(new testDatabase());
    $test->addTestCase(new testConfiguration());
    $test->addTestCase(new testLocale());
    $test->addTestCase(new testUserroles());
    $test->addTestCase(new testParameterhandler());
    $test->run(new HtmlReporter());

	$debug->loadStylesheet('debug.css');
//	$debug->showInnerLoops = true;
//	$debug->showMiscInformation();
//	$debug->showDebugMessages();
	$debug->showQueryMessages();
//	$debug->showGuardMessages();
	
?>

