<?php

define('ZEITGEIST_ROOTDIRECTORY', '');

// This should be an empty database without any tables
// The test cases will create the tables themselves as needed
define('ZG_DB_DBSERVER', 'localhost');
define('ZG_DB_USERNAME', 'root');
define('ZG_DB_USERPASS', '');
define('ZG_DB_DATABASE', 'zg_test');
define('ZG_DB_CONFIGURATIONCACHE', 'configurationcache');

require_once('zeitgeist.php');
	
?>

