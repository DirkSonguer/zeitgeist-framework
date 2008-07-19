<?php
/**
 * Taskkun - Agile Project Management Tool
 *
 * Based on the Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * @author Dirk Songr <songuer@zeitgeist-framework.com>
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package TASKKUN
 * @subpackage TASKKUN CORE
 */

	define('TASKKUN_ACTIVE', true);
//	define('DEBUGMODE', true);

	include('zeitgeist/zeitgeist.php');

	require_once('classes/tktemplate.class.php');
	require_once('classes/tktaskfunctions.class.php');
	require_once('classes/tktasklogfunctions.class.php');
	require_once('classes/tktasktypefunctions.class.php');
	require_once('classes/tkuserfunctions.class.php');
	require_once('classes/tkgroupfunctions.class.php');
	require_once('classes/tkinstancefunctions.class.php');

	include_once('includes/open-flash-chart/open_flash_chart_object.php');
	include_once('includes/open-flash-chart/open-flash-chart.php');

	define('ZG_DB_DBSERVER', 'localhost');
	define('ZG_DB_USERNAME', 'taskkun');
	define('ZG_DB_USERPASS', 'taskkun');
	define('ZG_DB_DATABASE', 'taskkun');
	define('ZG_DB_CONFIGURATIONCACHE', 'configurationcache');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();
	$locale = zgLocale::init();
	
	$locale->setLocale('de_DE');

	// load configuration
	$configuration->loadConfiguration('taskkun', 'configuration/taskkun.ini');

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

	// test if user is logged in
	if ( (!$user->establishUserSession()) && ($module != 'register')  && ($module != 'help') )
	{
		$module = 'main';
		$action = 'login';
	}
	
	$systemwarnings = $message->getMessagesByType('warning');
	foreach($systemwarnings as $warnings)
	{
		if ($warnings->message == 'Problem validating the user session: IP does not match the session')
		{
			$message->setMessage('Ihre Sitzung ist abgelaufen. Sie wurden aus Sicherheitsgründen abgemeldet', 'usererror');
		}
	}

	// load event
	$ret = $eventhandler->callEvent($module, $action);

	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();

?>
