<?php

	if (! defined('SIMPLE_TEST'))
	{
	    define('SIMPLE_TEST', 'simpletest/');
	}

	require_once(SIMPLE_TEST . 'autorun.php');

	define('DEBUGMODE', true);
	define('APPLICATION_ACTIVE', true);
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', '../');

	define(ZG_DB_DBSERVER, 'localhost');
	define(ZG_DB_USERNAME, 'root');
	define(ZG_DB_USERPASS, '');
	define(ZG_DB_DATABASE, 'zeitgeist_project');
	define(ZG_DB_CONFIGURATIONCACHE, 'configurationcache');

	require_once('../zeitgeist/zeitgeist.php');
	include('../zeitgeist/zeitgeist.php');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();
	$locale = zgLocale::init();

	// load configuration
	$configuration->loadConfiguration('application', '../configuration/application.ini');

	require_once('database.test.php');

	$debug = zgDebug::init();

    $test = &new TestSuite('Application Unit Tests');
    $test->addTestCase(new testDatabase());

    $test->run(new HtmlReporter());

	$debug->loadStylesheet('debug.css');
//	$debug->showInnerLoops = true;
//	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
//	$debug->showGuardMessages();
	
?>

