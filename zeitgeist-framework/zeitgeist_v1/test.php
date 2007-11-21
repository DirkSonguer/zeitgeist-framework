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
	$session = zgSession::init();
	$error = zgErrorhandler::init();

	$session->startSession();

	$test = new zgTemplate();
	$test->load('test.tpl.html');
	
	$debug->write('test');
	$message->setMessage('Test', 'test.php');
	
//	$database = new zgDatabase();
//	$database->connect();
//	$ret = $database->query('SELECT * FROM test');
	
//	while ($row = $database->fetchArray($ret))
//	{
//		echo $row['test_content'].'<br />';
//	}

	$configuration->loadConfiguration('test', 'test1.ini');
	$testconfig = $configuration->getConfiguration('test', 'create', 'department_color');
	echo $testconfig . "<br />";
	$testconfig = $configuration->getConfiguration('zeitgeist', 'tables', 'table_users');
	echo $testconfig . "<br />";

	$sesstest = $session->getSessionVariable('test');
	echo $sesstest . "<br />";
	
	if (!$sesstest = $session->getSessionVariable('test'))
	{
		$session->setSessionVariable('test', 1);
	}
	else
	{
		$session->setSessionVariable('test', $sesstest+1);		
	}
	
	$sesstest = $session->getSessionVariable('test');
	echo $sesstest . "<br />";

	if ($sesstest > 9)
	{
		$session->stopSession();
	}

	$test->assign('durchgang', $sesstest);
	$test->insertBlock('main');
	
	$test->show();
	
	$test = $message->getMessages();
	
	$debug->loadStylesheet('debug.css');
	$debug->showDebugMessages();
	$debug->showGuardMessages();
	
?>
