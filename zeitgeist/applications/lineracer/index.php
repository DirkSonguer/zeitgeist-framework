<?php
/**
 * Lineracer
 *
 * Based on the Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package LINERACER
 * @subpackage LINERACER CORE
 */

	define('LINERACER_ACTIVE', true);
	define('DEBUGMODE', true);

	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', './zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './');

	include('zeitgeist/zeitgeist.php');
	
	require_once('includes/lreventoverride.include.php');
	require_once('classes/lruserfunctions.class.php');
	require_once('classes/lrtemplate.class.php');
	require_once('classes/lrlobbyfunctions.class.php');
	require_once('classes/lrgameeventhandler.class.php');
	require_once('classes/lrstatisticfunctions.class.php');
	require_once('classes/lrachievementfunctions.class.php');
	require_once('classes/lrachievements.class.php');
	require_once('classes/lrgamestates.class.php');
	require_once('classes/lrgamecardfunctions.class.php');
	require_once('classes/lrmovementfunctions.class.php');
	require_once('classes/lrgamefunctions.class.php');

	require_once('prototype/classes/prototyperenderer.class.php');

	include('configuration/lineracer.config.php');
	
	spl_autoload_register ('__autoload');
	spl_autoload_register('lrEventoverride');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();

	// load configuration
	$configuration->loadConfiguration('lineracer', 'configuration/lineracer.ini');
	$configuration->loadConfiguration('gamedefinitions', 'configuration/gamedefinitions.ini');

	// test if user is logged in
	$user->establishUserSession();

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
	
	if ($ret != 1)
	{
		$tpl = new lrTemplate();
		$tpl->redirect($tpl->createLink('main', 'index'));
	}

	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
//	$debug->showQueryMessages();
	$debug->showGuardMessages();

?>
