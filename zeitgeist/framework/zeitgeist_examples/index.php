<?php
/**
 * Zeitgeist Framework
 * http://www.zeitgeist-framework.com
 *
 * Zeitgeist Examples
 * 
 * @author Dirk Songür <dirk.songuer@zeitgeist-framework.com>
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZGEXAMPLES
 * @subpackage MAIN
 */


	// Sets a define that the application itself is running
	// By defining this, the modules & actions can't be called directly
	// if they each check for this define.
	define('ZGEXAMPLES_ACTIVE', true);

	// If DEBUGMODE is defined (with whatever content), debugging will be activated
	define('DEBUGMODE', true);

	// Load basic configuration for the application
	// See ./modules/configuration/configuration.module.php for details
	require_once('configuration/application.configuration.php');	
	
	// Define root of the framework as well as for the application.
	// This is used throughout the framework to determine paths
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', './zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './');

	// Include the framework itself
	include('zeitgeist/zeitgeist.php');

	// Define some misc classes
	// To know what each of these classes do, please refer to the according examples
	$debug = zgDebug::init();					// use debugging
	$message = zgMessages::init();				// use the message system
	$configuration = zgConfiguration::init();	// use configuration
	$error = zgErrorhandler::init();			// activate & use error handling
	$user = zgUserhandler::init();				// use user handling
	$eventhandler = new zgEventhandler();		// use the event handler

	// Set standard module if none is defined
	// Bascially modules are collections of actions. Each module is represented by
	// a class in the folder /modules/MODULENAME/
	if (isset($_GET['module']))
	{
		$module = $_GET['module'];
	}
	else
	{
		$module = 'main';
	}
	
	// Set standard action if none is defined
	// Actions are exactly what the name suggests: one specific action for the
	// application. They represent one method in their according module class.
	if (isset($_GET['action']))
	{
		$action = $_GET['action'];
	}
	else
	{
		$action = 'index';
	}	

	// This picks up a user session, if there is one
	// In this example you don't need this, but it's here for the sake of
	// completeness. 
	// For more information about user handling,
	// see ./modules/userhandler/userhandler.module.php
	$loggedin = $user->establishUserSession();

	// Call the event
	// This calls the Eventhandler (controller) which in turn does a lot of
	// magic (verifiy that the module and action exist, are active and that
	// the user has the right to call the action, filter the input and so on.
	$ret = $eventhandler->callEvent($module, $action);
	
	// If you don't know where to look next, take a look into
	// ./modules/main/main.php
	// This is where the eventhandler jumps if no further
	// parameters are used	

///*
	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();
//*/
?>
