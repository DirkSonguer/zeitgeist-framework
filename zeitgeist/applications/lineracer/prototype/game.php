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
	$gamestates = $gamefunctions->getGamestates(1);
	
	$renderer = new prototypeRenderer();
	$renderer->draw(1);

	$tpl = new lrTemplate();
	$tpl->load('game_index.tpl.html');
	
	if ($gamestates['activePlayer'] == 1) $tpl->assign('bgcolor', '#00ff00');
	elseif ($gamestates['activePlayer'] == 2) $tpl->assign('bgcolor', '#ff0000');
	elseif ($gamestates['activePlayer'] == 3) $tpl->assign('bgcolor', '#0000ff');
	else $tpl->assign('bgcolor', '#000000');
	$tpl->show();

	var_dump($gamestates);
		
	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();

?>
