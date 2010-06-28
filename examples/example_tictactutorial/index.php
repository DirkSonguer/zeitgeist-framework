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
    // this define should be checked by every php file that
    // is executed afterwards
	define('TICTACTUTORIAL_ACTIVE', true);

	// activate debugging with this line
    // to deactivate, simply comment it out
	define('DEBUGMODE', true);

	// require basic configuration
    // this provides application specific configuration data
    // this will not be handled by the configuration class
    // so use this file only to define data you can't define
    // in the application itself (e.g. database access etc.)
	require_once('configuration/application.configuration.php');

	// define zeitgeist specific path values
    // if you located your zeitgeist directory somewhere else (not in the root of your
    // application) change this directory to it's location
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', './zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './');

	// include framework
	include(ZEITGEIST_ROOTDIRECTORY . 'zeitgeist.php');

	// define some general classes
    // you may or may not need those, so use them
    // according to your application
	$debug = zgDebug::init();
	$session = zgSession::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();

	// load application configuration
    // this contains the application specific data and is
    // handled by the configuration class
    // all configuration data should be defined in there
	$configuration->loadConfiguration('application', 'configuration/application.ini');

    // pick up the session for the user
    // this requires cookies to work
    $user = zgUserhandler::init();
    $user->establishUserSession();

    // get the module name to load
    // the module name will be verified against existing values, so no escape needed
    // if no module has been specified, load the main module
	if (isset($_GET['module']))
	{
		$module = $_GET['module'];
	}
	else
	{
		$module = 'main';
	}

    // get the action to load
    // the action name will be verified against existing values, so no escape needed
    // if no action has been specified, load the index
	if (isset($_GET['action']))
	{
		$action = $_GET['action'];
	}
	else
	{
		$action = 'index';
	}

    // call the controller with the defined module and action
    // this executes the action method in the module class
    $controller = new zgController();
	$ret = $controller->callEvent($module, $action);

	// output all debug information
	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();

?>

