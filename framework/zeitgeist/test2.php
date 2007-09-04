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
	define(ZG_DB_DATABASE, 'zeitgeist');
	define(ZG_DB_CONFIGURATIONCACHE, 'configurationcache');
	
	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	
	$debug->write('test');
	$message->setMessage('Test', 'test.php');
	
	$ret = $user->loginUser();
	echo $ret."<br />";

	$ret = $user->rights->hasUserright();
	echo $ret."<br />";

	$configuration->loadConfiguration('test', 'test1.ini');
	$testconfig = $configuration->getConfiguration('test', 'create', 'department_color');
	echo $testconfig . "<br />";
	$testconfig = $configuration->getConfiguration('zeitgeist', 'userhandler', 'table_users');
	echo $testconfig . "<br />";
	$testconfig = $configuration->getConfiguration('test', 'show', 'PreSnapin');
	echo $testconfig . "<br />";
	
	foreach ($testconfig as $test)
	{
		echo $test."<br />";	
	}
	
	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showDebugMessages();
	$debug->showGuardMessages();
	
?>
