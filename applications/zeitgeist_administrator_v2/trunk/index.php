<?php
/**
 * Zeitgeist Administrator v2
 *
 * The Zeitgeist Administrator v2 will help you create and manage
 * projects based on the Zeitgeist framework.
 * It is meant as a tool to help you develop applications with Zeitgeist
 * as well as manage your running applications.
 *
 * http://www.zeitgeist-framework.com
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST ADMINISTRATOR
 * @subpackage MAIN
 */

	// set define for application
	define('ZGADMIN_ACTIVE', true);

	// activate debugging by uncommenting the line below
	// define('DEBUGMODE', true);

	// require basic configuration
	require_once('configuration/application.configuration.php');

	// define zeitgeist specific path values
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', './zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './');

	// include framework
	include('zeitgeist/zeitgeist.php');

	// include application classes
	require_once('classes/zgatemplate.class.php');
	require_once('classes/zgasetupfunctions.class.php');
	require_once('classes/zgauserfunctions.class.php');
	require_once('classes/zgauserdata.class.php');
	require_once('classes/zgauserroles.class.php');
	require_once('classes/zgauserrights.class.php');
	require_once('classes/zgaprojectfunctions.class.php');

	// define some general classes
	$debug = zgDebug::init();
	$session = zgSession::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$controller = new zgController();

	// load application configuration
	$configuration->loadConfiguration('administrator', 'configuration/application.ini');

	// set module to input or defult value
	if (isset($_GET['module']))
	{
		$module = $_GET['module'];
	}
	else
	{
		$module = 'main';
	}

	// set action to input or defult value
	if (isset($_GET['action']))
	{
		$action = $_GET['action'];
	}
	else
	{
		$action = 'index';
	}

	// test if user is logged in
	if (!$user->establishUserSession())
	{
		$module = 'main';
		$action = 'login';
	}

	// establish user session if possible
	$activesession = $user->establishUserSession();

	// hand over to applicaton pipeline
	$ret = $controller->callEvent($module, $action);

	// save all debug information to file
	$debug->saveToFile('./_additional_material/debug.html', 'debug.css');

/*
	// output all debug information
	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showGuardMessages();
//*/

?>

