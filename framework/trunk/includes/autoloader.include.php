<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Autoloader
 *
 * The autoloader is needed to route the incoming requests to the
 * according modules and actions
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST AUTOLOADER
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

/**
 * This is an autoloader superfunction
 * It tries to catch unknown classes that are called and tries to load them
 * It is used by the controller to load modules
 *
 * Note that if this function fails, a fatal error will occur!
 *
 * @param string $class name of the class that was called
 *
 * @return NULL
 */
function zgAutoload( $class )
{
	$debug = zgDebug::init( );
	$message = zgMessages::init( );

	$debug->guard( );

	if ( file_exists( APPLICATION_ROOTDIRECTORY . 'modules/' . $class . '/' . $class . '.module.php' ) )
	{
		require_once( APPLICATION_ROOTDIRECTORY . 'modules/' . $class . '/' . $class . '.module.php' );
		$debug->unguard( 'Class ' . APPLICATION_ROOTDIRECTORY . 'modules/' . $class . '/' . $class . '.module.php loaded' );
		return;
	}

	$debug->write( 'Error autoloading class: Class ' . $class . ' not found', 'warning' );
	$message->setMessage( 'Error autoloading class: Class ' . $class . ' not found', 'warning' );

	$debug->unguard( false );
}


?>
