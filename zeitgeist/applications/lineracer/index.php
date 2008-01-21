<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Zeitgeist Administrator Tool
 *
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST ADMINISTRATOR
 */

	define('LINERACER_ACTIVE', true);

	include('zeitgeist/zeitgeist.php');

	require_once('classes/lrtemplate.class.php');
	require_once('classes/lrgamefunctions.class.php');

	define(ZG_DB_DBSERVER, 'localhost');
	define(ZG_DB_USERNAME, 'lineracer');
	define(ZG_DB_USERPASS, 'lineracer');
	define(ZG_DB_DATABASE, 'lineracer');
	define(ZG_DB_CONFIGURATIONCACHE, 'configurationcache');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();


	// load configuration
	$configuration->loadConfiguration('lineracer', 'configuration/lineracer.ini');

	// test if user is logged in
	if(!$user->establishUserSession())
	{
		$user->loginUser('songuer', 'songuer');
	}

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

	// load event
	$ret = $eventhandler->callEvent($module, $action);

	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showGuardMessages();

?>
