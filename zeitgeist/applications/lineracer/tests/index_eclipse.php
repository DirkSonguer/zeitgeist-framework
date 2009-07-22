<?php

	if (! defined('SIMPLE_TEST'))
	{
		define('SIMPLE_TEST', 'simpletest/');
	}

	require_once(SIMPLE_TEST . 'autorun.php');
	
	$basename = 'd:\webseiten\lineracer';

//	define('DEBUGMODE', true);
	define('LINERACER_ACTIVE', true);
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', $basename.'/zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', $basename.'/');

	define('ZG_DB_DBSERVER', 'localhost');
	define('ZG_DB_USERNAME', 'root');
	define('ZG_DB_USERPASS', '');
	define('ZG_DB_DATABASE', 'lineracer_test');
	define('ZG_DB_CONFIGURATIONCACHE', 'configurationcache');

	require_once(ZEITGEIST_ROOTDIRECTORY.'zeitgeist.php');
//	include('../zeitgeist/zeitgeist.php');

	require_once(APPLICATION_ROOTDIRECTORY.'includes/lreventoverride.include.php');
	require_once(APPLICATION_ROOTDIRECTORY.'classes/lruserfunctions.class.php');
	require_once(APPLICATION_ROOTDIRECTORY.'classes/lrtemplate.class.php');
	require_once(APPLICATION_ROOTDIRECTORY.'classes/lrgameeventhandler.class.php');
	require_once(APPLICATION_ROOTDIRECTORY.'classes/lrstatisticfunctions.class.php');
	require_once(APPLICATION_ROOTDIRECTORY.'classes/lrgamestates.class.php');
	require_once(APPLICATION_ROOTDIRECTORY.'classes/lrgamecardfunctions.class.php');
	require_once(APPLICATION_ROOTDIRECTORY.'classes/lrmovementfunctions.class.php');
	require_once(APPLICATION_ROOTDIRECTORY.'classes/lrgamefunctions.class.php');
	require_once(APPLICATION_ROOTDIRECTORY.'classes/lrlobbyfunctions.class.php');
	require_once(APPLICATION_ROOTDIRECTORY.'classes/lrachievements.class.php');
	require_once(APPLICATION_ROOTDIRECTORY.'classes/lrachievementfunctions.class.php');

	include(APPLICATION_ROOTDIRECTORY.'configuration/lineracer.config.php');

	spl_autoload_register ('__autoload');
	spl_autoload_register('lrEventoverride');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();
	$locale = zgLocale::init();

	// load configuration
	$configuration->loadConfiguration('lineracer', APPLICATION_ROOTDIRECTORY.'configuration/lineracer.ini');
	$configuration->loadConfiguration('gamedefinitions', APPLICATION_ROOTDIRECTORY.'configuration/gamedefinitions.ini');

	$user->logout();
	$user->login('test1', 'test');

	require_once('miscfunctions.php');
	require_once('database.test.php');
	require_once('lrgamestates.test.php');
	require_once('lrgamecardfunctions.test.php');
	require_once('lrmovementfunctions.test.php');
	require_once('lrmovementfunctions.test.php');
	require_once('lrgameeventhandler.test.php');
	require_once('lrgamecards.test.php');
	require_once('lrgamefunctions.test.php');
	require_once('lruserfunctions.test.php');
	require_once('lrlobbyfunctions.test.php');
	require_once('lrachievements.test.php');
	require_once('lrachievementfunctions.test.php');
	require_once('lrallachievements.test.php');
	require_once('lrstatisticfunctions.test.php');

	$debug = zgDebug::init();

	$test = &new TestSuite('Lineracer Unit Tests');
	$test->addTestCase(new testDatabase());
/*
	$test->addTestCase(new testLrgamestates());
	$test->addTestCase(new testLrgamecardfunctions());
	$test->addTestCase(new testLrmovementfunctions());
	$test->addTestCase(new testLrgameeventhandler());
	$test->addTestCase(new testLrgamefunctions());
	$test->addTestCase(new testLruserfunctions());
	$test->addTestCase(new testLrlobbyfunctions());
	$test->addTestCase(new testLrachievementfunctions());
	$test->addTestCase(new testLrachievements());
	$test->addTestCase(new testLrallachievements());
	$test->addTestCase(new testLrstatisticfunctions());
//*/
	$test->run(new HtmlReporter());

//	$debug->loadStylesheet('debug.css');
//	$debug->showInnerLoops = true;
//	$debug->showMiscInformation();
//	$debug->showDebugMessages();
//	$debug->showQueryMessages();
//	$debug->showGuardMessages();
	
?>

