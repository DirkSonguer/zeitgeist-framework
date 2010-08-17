<?php

// Those paths are relative to the test files
// As they are in the SVN as well, you should not need to change them
define( 'ZEITGEIST_ROOTDIRECTORY', dirname( __FILE__ ) . '/../' );
define( 'ZG_TESTDATA_DIR', dirname( __FILE__ ) . '/testdata/' );

// This should be an empty database without any tables
// The test cases will create the tables themselves as needed
define( 'ZG_DB_DBSERVER', 'localhost' );
define( 'ZG_DB_USERNAME', 'root' );
define( 'ZG_DB_USERPASS', '' );
define( 'ZG_DB_DATABASE', 'zg_test' );
define( 'ZG_DB_CONFIGURATIONCACHE', 'configurationcache' );

require_once( ZEITGEIST_ROOTDIRECTORY . 'zeitgeist.php' );
require_once dirname( __FILE__ ) . '/_testfunctions.php';

// SimpleTest is used for the tests however it is not part of the SVN
// Download SimpleTest from here: http://www.simpletest.org/
// Place it into a directory and change the path below to point there
if ( !defined( 'SIMPLETEST_DIR' ) )
{
	define( 'SIMPLETEST_DIR', dirname( __FILE__ ) . '/../../../../simpletest/' );
}
require_once( SIMPLETEST_DIR . 'autorun.php' );

?>