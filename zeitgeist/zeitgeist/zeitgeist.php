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

// Define a constant to indicate that Zeitgeist is active
// Every file in this package should test for this constant to verify that it is called from the framework 
	define('ZEITGEIST_ACTIVE', true);

// definitions
	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', './zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './');

// configuration
	require_once (ZEITGEIST_ROOTDIRECTORY . 'configuration/zeitgeist.config.php');

// include core classes
	require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/debug.class.php');
	require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/messages.class.php');
	require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/database.class.php');
	require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/configuration.class.php');
	require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/session.class.php');
	require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/errorhandler.class.php');
	require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/template.class.php');
	
?>