<?php

	define('MULTITEST', true);

	if (!defined('SIMPLE_TEST'))
	{
	    define('SIMPLE_TEST', './simpletest/');
	}
	require_once(SIMPLE_TEST . 'autorun.php');

	define('DEBUGMODE', true);
	define('LINERACER_ACTIVE', true);
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', '../');

	define('ZG_DB_DBSERVER', 'localhost');
	define('ZG_DB_USERNAME', 'root');
	define('ZG_DB_USERPASS', '');
	define('ZG_DB_DATABASE', 'lineracer_test');
	define('ZG_DB_CONFIGURATIONCACHE', 'configurationcache');

	require_once('../zeitgeist/zeitgeist.php');

	require_once('../includes/lreventoverride.include.php');
	require_once('../classes/lruserfunctions.class.php');
	require_once('../classes/lrtemplate.class.php');
	require_once('../classes/lrgameeventhandler.class.php');
	require_once('../classes/lrstatisticfunctions.class.php');
	require_once('../classes/lrgamestates.class.php');
	require_once('../classes/lrgamecardfunctions.class.php');
	require_once('../classes/lrmovementfunctions.class.php');
	require_once('../classes/lrgamefunctions.class.php');
	require_once('../classes/lrlobbyfunctions.class.php');
	require_once('../classes/lrachievements.class.php');
	require_once('../classes/lrachievementfunctions.class.php');

	include('../configuration/lineracer.config.php');
	
	spl_autoload_register ('__autoload');
	spl_autoload_register('lrEventoverride');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$controller = new zgController();

	// load configuration
	$configuration->loadConfiguration('lineracer', '../configuration/lineracer.ini');
	$configuration->loadConfiguration('gamedefinitions', '../configuration/gamedefinitions.ini');
	
	require_once('miscfunctions.php');
	require_once('./unittests/database.test.php');
	require_once('./unittests/lrgamestates.test.php');
	require_once('./unittests/lrgamecardfunctions.test.php');
	require_once('./unittests/lrmovementfunctions.test.php');
	require_once('./unittests/lrmovementfunctions.test.php');
	require_once('./unittests/lrgameeventhandler.test.php');
	require_once('./unittests/lrgamecards.test.php');
	require_once('./unittests/lrgamefunctions.test.php');
	require_once('./unittests/lruserfunctions.test.php');
	require_once('./unittests/lrlobbyfunctions.test.php');
	require_once('./unittests/lrachievements.test.php');
	require_once('./unittests/lrachievementfunctions.test.php');
	require_once('./unittests/lrallachievements.test.php');
	require_once('./unittests/lrstatisticfunctions.test.php');
	
	
	$user->logout();
	$user->login('test1', 'test');

	$debug = zgDebug::init();

	$test = &new TestSuite('Lineracer Unit Tests');
//	$test->addTestCase(new testDatabase());

	$test->addTestCase(new testLrgamestates());
	$test->addTestCase(new testLrgamecardfunctions());
	$test->addTestCase(new testLrmovementfunctions());
	$test->addTestCase(new testLrgameeventhandler());
	$test->addTestCase(new testLrgamefunctions());
	$test->addTestCase(new testLrgamecards());
	$test->addTestCase(new testLruserfunctions());
	$test->addTestCase(new testLrlobbyfunctions());
	$test->addTestCase(new testLrachievementfunctions());
	$test->addTestCase(new testLrachievements());
	$test->addTestCase(new testLrallachievements());
	$test->addTestCase(new testLrstatisticfunctions());

	$test->run(new HtmlReporter());

	$debug->loadStylesheet('debug.css');
//	$debug->showInnerLoops = true;
//	$debug->showMiscInformation();
	$debug->showDebugMessages();
//	$debug->showQueryMessages();
//	$debug->showGuardMessages();
	
	
?>

