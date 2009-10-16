<?php

	if (! defined('SIMPLE_TEST'))
	{
	    define('SIMPLE_TEST', 'simpletest/');
	}

	require_once(SIMPLE_TEST . 'autorun.php');

	define('DEBUGMODE', true);
	define('TASKKUN_ACTIVE', true);
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../zeitgeist/');

	define('ZG_DB_DBSERVER', 'localhost');
	define('ZG_DB_USERNAME', 'root');
	define('ZG_DB_USERPASS', '');
	define('ZG_DB_DATABASE', 'taskkun_test');
	define('ZG_DB_CONFIGURATIONCACHE', 'configurationcache');

	require_once('../zeitgeist/zeitgeist.php');

	require_once('../classes/tktemplate.class.php');
	require_once('../classes/tktaskfunctions.class.php');
	require_once('../classes/tktasklogfunctions.class.php');
	require_once('../classes/tkworkflowfunctions.class.php');
	require_once('../classes/tkuserfunctions.class.php');
	require_once('../classes/tkgroupfunctions.class.php');
	require_once('../classes/tkinstancefunctions.class.php');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();
	$locale = zgLocale::init();
	
	require_once('database.test.php');
	require_once('tkuserfunctions.test.php');
	require_once('tkgroupfunctions.test.php');
	require_once('tkinstancefunctions.test.php');
	require_once('tktaskfunctions.test.php');
	
	$debug = zgDebug::init();

    $test = &new TestSuite('Taskkun Unit Tests');
    $test->addTestCase(new testDatabase());
    $test->addTestCase(new testTkuserfunctions());
    $test->addTestCase(new testTkgroupfunctions());
    $test->addTestCase(new testTkinstancefunctions());
    $test->addTestCase(new testTktaskfunctions());
    $test->run(new HtmlReporter());

//	echo "<h2><a href='index_step2.php'>Step 2</a></h2>";

	$debug->loadStylesheet('debug.css');
//	$debug->showInnerLoops = true;
//	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
//	$debug->showGuardMessages();
	
?>

