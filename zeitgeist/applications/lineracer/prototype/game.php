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
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', '../');
	include('../zeitgeist/zeitgeist.php');

	require_once('../includes/lreventoverride.include.php');
	require_once('../classes/lrtemplate.class.php');
//	require_once('classes/lrpregamefunctions.class.php');
	require_once('../classes/lrgameeventhandler.class.php');
	require_once('../classes/lrgamestates.class.php');
	require_once('../classes/lrgamecardfunctions.class.php');
	require_once('../classes/lrmovementfunctions.class.php');
	require_once('../classes/lrgamefunctions.class.php');

	require_once('classes/prototyperenderer.class.php');

	include('../configuration/lineracer.config.php');

	spl_autoload_register ('__autoload');
	spl_autoload_register('lrEventoverride');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();

	// load configuration
	$configuration->loadConfiguration('lineracer', '../configuration/lineracer.ini');
	$configuration->loadConfiguration('gamedefinitions', '../configuration/gamedefinitions.ini');

	$gamestates = new lrGamestates();
	$currentGamestates = $gamestates->loadGamestates(1);

	$gamecardfunctions = new lrGamecardfunctions();
	
	$renderer = new prototypeRenderer();
	$renderer->draw();

	$tpl = new lrTemplate();
	$tpl->load('game_index.tpl.html');
	
	if ($currentGamestates['move']['currentPlayer'] == 1) $tpl->assign('bgcolor', '#00ff00');
	elseif ($currentGamestates['move']['currentPlayer'] == 2) $tpl->assign('bgcolor', '#ff0000');
	elseif ($currentGamestates['move']['currentPlayer'] == 3) $tpl->assign('bgcolor', '#0000ff');
	else $tpl->assign('bgcolor', '#000000');

	$tpl->assign('bgcolor_p1', '#00ff00');
	$tpl->assign('bgcolor_p2', '#ff0000');
	$tpl->assign('bgcolor_p3', '#0000ff');
	$tpl->assign('bgcolor_p4', '#ff6000');
/*
	// TODO: count moves
	$tpl->assign('moves_p1', count($gamefunctions->getMovement()));
	$tpl->assign('moves_p2', count($gamefunctions->getMovement()));
	$tpl->assign('moves_p3', count($gamefunctions->getMovement()));
	$tpl->assign('moves_p4', count($gamefunctions->getMovement()));

	if ($gamecardfunctions->checkRights('1', $currentGamestates['currentPlayer'])) $tpl->assign('bgcolor_card1', '#ffff00');
		else $tpl->assign('bgcolor_card1', '#cccc00');
	if ($gamecardfunctions->checkRights('2', $currentGamestates['currentPlayer'])) $tpl->assign('bgcolor_card2', '#ffff00');
		else $tpl->assign('bgcolor_card2', '#cccc00');
	if ($gamecardfunctions->checkRights('3', $currentGamestates['currentPlayer'])) $tpl->assign('bgcolor_card3', '#ffff00');
		else $tpl->assign('bgcolor_card3', '#cccc00');
*/
	$tpl->show();

	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();

?>
