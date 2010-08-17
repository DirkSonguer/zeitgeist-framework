<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Main Zeitgeist file
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST CORE
 *
 */

// Define a constant to indicate that Zeitgeist is active
// Every file in this package should test for this constant to verify that it is called from the framework
define( 'ZEITGEIST_ACTIVE', true );

// definitions
if ( !defined( 'ZEITGEIST_ROOTDIRECTORY' ) )
{
	define( 'ZEITGEIST_ROOTDIRECTORY', './zeitgeist/' );
}
if ( !defined( 'APPLICATION_ROOTDIRECTORY' ) )
{
	define( 'APPLICATION_ROOTDIRECTORY', './' );
}

// configuration
require_once ( ZEITGEIST_ROOTDIRECTORY . 'configuration/zeitgeist.config.php' );

// check for debug mode
if ( !defined( 'DEBUGMODE' ) )
{
	require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/debugdummy.class.php' );
}
else
{
	require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/debug.class.php' );
}

// include core classes
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/messages.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/database.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/database_pdo.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/configuration.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/localisation.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/objects.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/session.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/errorhandler.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/parameters.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/controller.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/template.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/actionlog.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/dataserver.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'classes/files.class.php' );

// include misc functions
require_once ( ZEITGEIST_ROOTDIRECTORY . 'includes/autoloader.include.php' );
spl_autoload_register( 'zgAutoload' );

// include modules
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/user/userroles.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/user/userrights.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/user/userdata.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/user/userfunctions.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/user/userhandler.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/facebook/facebookuserhandler.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/form/form.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/gamesystem/gamesystem.class.php' );

?>