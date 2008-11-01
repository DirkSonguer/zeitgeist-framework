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
	$database = new zgDatabase();
	$database->connect();
	
	// load configuration
	$configuration->loadConfiguration('lineracer', '../configuration/lineracer.ini');
	$configuration->loadConfiguration('gamedefinitions', '../configuration/gamedefinitions.ini');

	$sql = "TRUNCATE TABLE race_actions";
	$res = $database->query($sql);
	$sql = "TRUNCATE TABLE race_events";
	$res = $database->query($sql);
	$sql = "TRUNCATE TABLE users_to_gamecards";
	$res = $database->query($sql);
	$sql = "INSERT INTO users_to_gamecards(usergamecard_user, usergamecard_gamecard, usergamecard_count) VALUES('1', '1', '1'), ('1', '2', '1'), ('1', '3', '1')";
	$res = $database->query($sql);
	$sql = "INSERT INTO users_to_gamecards(usergamecard_user, usergamecard_gamecard, usergamecard_count) VALUES('2', '1', '1'), ('2', '2', '1'), ('2', '3', '1')";
	$res = $database->query($sql);
	$sql = "INSERT INTO users_to_gamecards(usergamecard_user, usergamecard_gamecard, usergamecard_count) VALUES('3', '1', '1'), ('3', '2', '1'), ('3', '3', '1')";
	$res = $database->query($sql);
	$sql = "INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('1', '1', '1', '150,370')";
	$res = $database->query($sql);
	$sql = "INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('1', '2', '1', '170,370')";
	$res = $database->query($sql);
	$sql = "INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('1', '3', '1', '190,370')";
	$res = $database->query($sql);
	$sql = "INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('1', '4', '1', '210,370')";
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
