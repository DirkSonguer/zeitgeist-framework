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

	require_once('configuration/application.configuration.php');	
	
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', './zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './');

	include('zeitgeist/zeitgeist.php');
	
	require_once('classes/pdtemplate.class.php');
	require_once('classes/pduserfunctions.class.php');
	require_once('classes/pdcards.class.php');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgFacebookUserhandler::init();
	$eventhandler = new zgEventhandler();
	$locale = zgLocale::init();

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
	$facebook = new zgFacebook(); //start a new instance of the facebook object
	$fb_id= $facebook->getUserID();  //get the user’s Facebook ID
		
	$tpl = new zgTemplate();
	$tpl->load('fbtest.tpl.html');
	$tpl->show();

	echo "<br />User ID: ".$fb_id."<br />";

//	$loggedin = $user->establishUserSession();
	
	$facebookuser = zgFacebookUserhandler::init();

	$test = $facebookuser->login();
	$test = $facebookuser->establishUserSession();
	
	echo "<br />Test: ".$test."<br />";

	$test = $facebookuser->storeFacebookUserdata();
*/

	$loggedin = $user->establishUserSession();

	$session = zgSession::init();
	$userid = $session->getSessionVariable('user_userid');
	$key = $session->getSessionVariable('user_key');
	$name = $session->getSessionVariable('user_username');

	$test = $user->isLoggedIn();
	$debug->write('Logged in: '.$test.' user: '.$userid.' key: '.$key.' name: '.$name, 'message');

	// load event
	$ret = $eventhandler->callEvent($module, $action);

	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();

?>

