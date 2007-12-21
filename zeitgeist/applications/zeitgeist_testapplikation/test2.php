<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Main Zeitgeist file
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * @version 1.0.2 - 24.07.2007
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST CORE
 */

	include('zeitgeist/zeitgeist.php');

	define(ZG_DB_DBSERVER, 'localhost');
	define(ZG_DB_USERNAME, 'zeitgeist');
	define(ZG_DB_USERPASS, 'zeitgeist');
	define(ZG_DB_DATABASE, 'zeitgeist_test1');
	define(ZG_DB_CONFIGURATIONCACHE, 'configurationcache');
	
	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	
	$debug->write('test');
	$message->setMessage('Test', 'test.php');
	
	
	if(!$user->establishUserSession())
	{
		$debug->write('Could not relogin user. logging again', 'error');
		$ret = $user->loginUser('songuer', 'songuer');
		echo $ret."<br />";
	}
	
	echo "logged: ".$user->isLoggedIn()."<br />";

	$ret = $user->userrights->hasUserright('test');
	echo "Userrights: ".$ret."<br />";
	
	$ret = $user->userdata->getUserdata();
	echo "Userdata: ".$ret."<br />";
	
	$configuration->loadConfiguration('test', 'test1.ini');
	$testconfig = $configuration->getConfiguration('test', 'create', 'department_color');
	echo $testconfig . "<br />";
	$testconfig = $configuration->getConfiguration('zeitgeist', 'tables', 'table_users');
	echo $testconfig . "<br />";
	$testconfig = $configuration->getConfiguration('test', 'show', 'PreSnapIn');
	echo $testconfig . "<br />";
	
	foreach ($testconfig as $test)
	{
		echo $test."<br />";	
	}
	
	$user->saveUserstates();
	
	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showDebugMessages();
	$debug->showGuardMessages();
	
?>
