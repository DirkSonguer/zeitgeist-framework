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


	// sets a define that the application itself is running
	// by defining this, the modules & actions can't be called directly
	define('ZGEXAMPLES_ACTIVE', true);

	// if DEBUGMODE is defined (with whatever content), debugging will be activated
	define('DEBUGMODE', true);

	// load basic configuration for the application
	require_once('configuration/application.configuration.php');	
	
	// define root of the framework as well as for the application
	// this is used throughout the framework to determine paths
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', './zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './');

	// include the framework itself
	include('zeitgeist/zeitgeist.php');

	// define some misc classes
	// to know what each of these classes do, please refer to the according examples
	$debug = zgDebug::init();					// use debugging
	$message = zgMessages::init();				// use the message system
	$configuration = zgConfiguration::init();	// use configuration
	$error = zgErrorhandler::init();			// activate & use error handling
	$user = zgUserhandler::init();				// use user handling
	$eventhandler = new zgEventhandler();		// use the event handler

	// set standard module if none is defined
	// for more information about module parameters, see eventhandler
	if (isset($_GET['module']))
	{
		$module = $_GET['module'];
	}
	else
	{
		$module = 'main';
	}
	
	// set standard action if none is defined
	// for more information about action parameters, see eventhandler
	if (isset($_GET['action']))
	{
		$action = $_GET['action'];
	}
	else
	{
		$action = 'index';
	}	

	// this picks up a user session, if there is one
	// for more information about user handling, see userhandler
	$loggedin = $user->establishUserSession();

	// call the event
	// for more information about this parameters, see eventhandler
	$ret = $eventhandler->callEvent($module, $action);
	
	// If you don't know where to look next, take a look into
	// ./modules/main/main.php
	// This is where the eventhandler jumps next if no further
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
