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
	$session = zgSession::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$locale = zgLocale::init();
	
	$user = zgFacebookUserhandler::init();
	$eventhandler = new zgEventhandler();
	$eventhandler->init($user);
	
	$pduser = new pdUserfunctions();

	// load configuration
	$configuration->loadConfiguration('pokendesign', 'configuration/pokendesign.ini');

	$language = $session->getSessionVariable('language');
	if (!$language)
	{
		$url = $_SERVER["SERVER_NAME"];
		if (strpos(strtolower($url), 'design.de') !== false)
		{
			$session->setSessionVariable('language', '_de');
		}
		else
		{
			$session->setSessionVariable('language', '_en');
		}
	}

	$user->connectToFacebook($configuration->getConfiguration('facebook','api'.$session->getSessionVariable('language'),'api_key'), $configuration->getConfiguration('facebook','api'.$session->getSessionVariable('language'),'secret_key'));

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

	$activesession = $user->establishUserSession();
	
	if (!$activesession)
	{
		if ($user->getFacebookID())
		{
			$loggedin = $user->login();
			if (!$loggedin)
			{
				
			}
			
			$message->setMessage('Dein Account wurde angelegt. Viel Vergnügen bei Pokendesign.', 'usermessage');
		}
	}


///*
	$session = zgSession::init();
	$userid = $session->getSessionVariable('user_userid');
	$key = $session->getSessionVariable('user_key');
	$name = $session->getSessionVariable('user_username');

	$test = $user->isLoggedIn();
	$debug->write('Logged in: '.$test.' user: '.$userid.' key: '.$key.' name: '.$name, 'message');
//*/

	// load event
	$ret = $eventhandler->callEvent($module, $action);

	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();

?>

