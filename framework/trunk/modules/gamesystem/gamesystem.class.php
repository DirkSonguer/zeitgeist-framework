<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Game System
 *
 * The main entry point for the game system
 * The game system provides an eventhandler as well as data
 * management for a game project
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST GAMESYSTEM
 */

if ( !defined( 'GAMESYSTEM_ACTIONDIRECTORY' ) )
{
	define( 'GAMESYSTEM_ACTIONDIRECTORY', 'gameactions/' );
}

// include the gamesystem classes
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/gamesystem/gameaction.interface.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/gamesystem/gamesetup.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/gamesystem/gamedata.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/gamesystem/gamehandler.class.php' );
require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/gamesystem/gameautoloader.include.php' );

spl_autoload_register( 'zgGameautoloader' );

?>