<?php

	if (! defined('SIMPLE_TEST'))
	{
	    define('SIMPLE_TEST', 'simpletest/');
	}
	require_once(SIMPLE_TEST . 'autorun.php');
				
	define('DEBUGMODE', true);
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../zeitgeist/');

	define('ZG_DB_DBSERVER', 'localhost');
	define('ZG_DB_USERNAME', 'root');
	define('ZG_DB_USERPASS', '');
	define('ZG_DB_DATABASE', 'wellnesswelt');
	define('ZG_DB_CONFIGURATIONCACHE', 'configurationcache');

	require_once('../zeiteist/zeitgeist.php');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();
	$locale = zgLocale::init();
	
//	require_once('messages.test.php');
	
	$debug = zgDebug::init();

    $test = &new TestSuite('Zeitgeist Unit Tests');
//    $test->addTestCase(new testMessages());
    $test->run(new HtmlReporter());

	$debug->loadStylesheet('debug.css');
//	$debug->showInnerLoops = true;
//	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
//	$debug->showGuardMessages();
	
?>

