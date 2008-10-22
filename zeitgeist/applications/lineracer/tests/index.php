<?php

	if (! defined('SIMPLE_TEST'))
	{
	    define('SIMPLE_TEST', 'simpletest/');
	}

	require_once(SIMPLE_TEST . 'autorun.php');

	define('DEBUGMODE', true);
	define('LINERACER_ACTIVE', true);
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './prototype');

	define('ZG_DB_DBSERVER', 'localhost');
	define('ZG_DB_USERNAME', 'root');
	define('ZG_DB_USERPASS', '');
	define('ZG_DB_DATABASE', 'lineracer_test');
	define('ZG_DB_CONFIGURATIONCACHE', 'configurationcache');

	require_once('../zeitgeist/zeitgeist.php');
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './prototype');
	include('../zeitgeist/zeitgeist.php');

	require_once('../classes/lrtemplate.class.php');
//	require_once('classes/lrpregamefunctions.class.php');
	require_once('../classes/lrgameeventhandler.class.php');
	require_once('../classes/lrgamestates.class.php');
	require_once('../classes/lrgamecardfunctions.class.php');
	require_once('../classes/lrmovementfunctions.class.php');
	require_once('../classes/lrgamefunctions.class.php');
//	require_once('classes/lruserfunctions.class.php');

	include('../configuration/lineracer.config.php');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();
	$locale = zgLocale::init();
	
	require_once('database.test.php');
	require_once('lrgamecardfunctions.test.php');
	require_once('lrmovementfunctions.test.php');
	require_once('lrmovementfunctions.test.php');
	require_once('lrgamestates.test.php');
	require_once('lrgameeventhandler.test.php');
	
	$debug = zgDebug::init();

    $test = &new TestSuite('Lineracer Unit Tests');
    $test->addTestCase(new testDatabase());
    $test->addTestCase(new testLrgamecardfunctions());
    $test->addTestCase(new testLrgamestates());
    $test->addTestCase(new testLrmovementfunctions());
    $test->addTestCase(new testLrgameeventhandler());
    $test->run(new HtmlReporter());

//	echo "<h2><a href='index_step2.php'>Step 2</a></h2>";

	$debug->loadStylesheet('debug.css');
//	$debug->showInnerLoops = true;
//	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
//	$debug->showGuardMessages();
	
?>

