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

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$user = zgUserhandler::init();
	$userrights = zgUserrights::init();
	
	$debug->write('test');
	$message->setMessage('Test', 'test.php');
	
	$ret = $user->loginUser();
	echo $ret."<br />";

	$ret = $userrights->hasUserright();
	echo $ret."<br />";

	$debug->loadStylesheet('debug.css');
	$debug->showDebugMessages();
	$debug->showGuardMessages();
	
?>
