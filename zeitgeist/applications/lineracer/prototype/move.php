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

	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './prototype');
	include('../zeitgeist/zeitgeist.php');

	require_once('../classes/lrtemplate.class.php');
//	require_once('classes/lrpregamefunctions.class.php');
	require_once('../classes/lrgamefunctions.class.php');
//	require_once('classes/lruserfunctions.class.php');
	require_once('classes/prototyperenderer.class.php');

	include('../configuration/lineracer.config.php');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();

	// load configuration
	$configuration->loadConfiguration('lineracer', '../configuration/lineracer.ini');

	$gamefunctions = new lrGamefunctions();
	$gamestates = $gamefunctions->loadGamestates(1);
	
	if ($_REQUEST['action'] == '1')
	{
		$move = $gamefunctions->move($_REQUEST['position_x'], $_REQUEST['position_y']);
	}
	elseif ($_REQUEST['action'] == '3')
	{
		$move = $gamefunctions->playGamecard($_REQUEST['gamecard']);
	}

	$tpl = new lrTemplate();
	$tpl->redirect('127.0.0.1/lineracer/prototype/game.php');

	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();

?>
