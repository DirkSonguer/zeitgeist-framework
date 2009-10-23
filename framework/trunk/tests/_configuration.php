<?php

if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', dirname(__FILE__).'/../');

// This should be an empty database without any tables
// The test cases will create the tables themselves as needed
define('ZG_DB_DBSERVER', 'localhost');
define('ZG_DB_USERNAME', 'root');
define('ZG_DB_USERPASS', '');
define('ZG_DB_DATABASE', 'zg_test');
define('ZG_DB_CONFIGURATIONCACHE', 'configurationcache');

require_once(ZEITGEIST_ROOTDIRECTORY.'zeitgeist.php');
require_once(dirname(__FILE__).'/_testfunctions.php');
	
?>
