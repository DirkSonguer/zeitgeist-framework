<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Pokendesign Webseite
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package POKENDESIGN
 * @subpackage POKENDESIGN MAIN
 */

	define('POKENDESIGN_ACTIVE', true);
	define('DEBUGMODE', true);

	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', './zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './');

	include('zeitgeist/zeitgeist.php');
	
	require_once('classes/pdtemplate.class.php');

	define(ZG_DB_DBSERVER, 'localhost');
	define(ZG_DB_USERNAME, 'root');
	define(ZG_DB_USERPASS, '');
	define(ZG_DB_DATABASE, 'pokendesign');
	define(ZG_DB_CONFIGURATIONCACHE, 'configurationcache');
	
	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();

	// load configuration
	$configuration->loadConfiguration('pokendesign', 'configuration/pokendesign.ini');

	// set module
	if (isset($_GET['module']))
	{
		$module = $_GET['module'];
	}
	else
	{
		$module = 'main';
	}
	
	// set action
	if (isset($_GET['action']))
	{
		$action = $_GET['action'];
	}
	else
	{
		$action = 'index';
	}	
	/*
	// test if user is logged in
	if(!$user->establishUserSession())
	{
		$module = 'main';
		$action = 'login';
	}
	*/
	// load event
	$ret = $eventhandler->callEvent($module, $action);
	
	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showGuardMessages();
	
?>
