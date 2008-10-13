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
	$database = new zgDatabase();
	$database->connect();

	// load configuration
	$configuration->loadConfiguration('lineracer', '../configuration/lineracer.ini');

	$sql = "TRUNCATE TABLE race_moves";
	$res = $database->query($sql);
	$sql = "TRUNCATE TABLE race_eventhandler";
	$res = $database->query($sql);
	$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('1', '1', '1', '150,370')";
	$res = $database->query($sql);
	$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('1', '2', '1', '170,370')";
	$res = $database->query($sql);
	$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('1', '3', '1', '190,370')";
	$res = $database->query($sql);
	$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('1', '4', '1', '210,370')";
	$res = $database->query($sql);
	$sql = "UPDATE races SET race_currentround='1', race_activeplayer='1' WHERE race_id='1'";
	$res = $database->query($sql);

	$tpl = new lrTemplate();
	$tpl->redirect('127.0.0.1/lineracer/prototype/game.php');

	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();

?>
