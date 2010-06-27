<?php
/**
 * Zeitgeist
 * A PHP based multipurpose framework for web applications
 * http://www.zeitgeist-framework.com
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST PROJECT
 * @subpackage ZEITGEIST PROJECT MAIN
 */

	// set define for application
	define('APPLICATION_ACTIVE', true);
	
	// activate debugging
	define('DEBUGMODE', true);

	// require basic configuration
	require_once('configuration/application.configuration.php');	
	
	// define zeitgeist specific path values
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', './zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './');

	// include framework
	include('zeitgeist/zeitgeist.php');
	
	// define some general classes
	$debug = zgDebug::init();
	$session = zgSession::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	
	// define a user and bind him to the application pipeline
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();
	$eventhandler->init($user);
	
	// load application configuration
	$configuration->loadConfiguration('application', 'configuration/application.ini');

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

	// establish user session if possible
	$activesession = $user->establishUserSession();

	// hand over to applicaton pipeline
	$ret = $eventhandler->callEvent($module, $action);

	// output all debug information
	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();

?>

