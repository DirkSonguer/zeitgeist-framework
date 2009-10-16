<?php

	if (! defined('SIMPLE_TEST'))
	{
	    define('SIMPLE_TEST', 'simpletest/');
	}

	require_once(SIMPLE_TEST . 'autorun.php');

	define('DEBUGMODE', true);
	define('POKENDESIGN_ACTIVE', true);
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', '../');

	define(ZG_DB_DBSERVER, 'localhost');
	define(ZG_DB_USERNAME, 'root');
	define(ZG_DB_USERPASS, '');
	define(ZG_DB_DATABASE, 'pokendesign_test');
	define(ZG_DB_CONFIGURATIONCACHE, 'configurationcache');

	require_once('../zeitgeist/zeitgeist.php');
	include('../zeitgeist/zeitgeist.php');

	require_once('../classes/pdcards.class.php');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgFacebookUserhandler::init();
	$eventhandler = new zgEventhandler();
	$locale = zgLocale::init();

	// load configuration
	$configuration->loadConfiguration('pokendesign', '../configuration/pokendesign.ini');

	require_once('database.test.php');
	require_once('pdcards.test.php');

	$debug = zgDebug::init();

    $test = &new TestSuite('Pokendesign Unit Tests');
    $test->addTestCase(new testDatabase());
    $test->addTestCase(new testPdcards());

    $test->run(new HtmlReporter());

	$debug->loadStylesheet('debug.css');
//	$debug->showInnerLoops = true;
//	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
//	$debug->showGuardMessages();
	
?>

