<?php

	if (! defined('SIMPLE_TEST'))
	{
	    define('SIMPLE_TEST', 'simpletest/');
	}
	require_once(SIMPLE_TEST . 'autorun.php');
		
	define('DEBUGMODE', true);
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../');
	require_once('../zeitgeist.php');

	require_once('messages.test.php');
	require_once('database.test.php');

	$debug = zgDebug::init();

    $test = &new TestSuite('Zeitgeist Unit Tests');
    $test->addTestCase(new testMessages());
    $test->addTestCase(new testDatabase());
    $test->run(new HtmlReporter());

	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();
	
?>

