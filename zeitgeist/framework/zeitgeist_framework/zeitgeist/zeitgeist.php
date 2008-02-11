<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Main Zeitgeist file
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
if (!defined('DEBUGMODE'))
{
	require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/debugdummy.class.php');
}
else
{
	require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/debug.class.php');
}

require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/messages.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/database.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/configuration.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/objectcache.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/session.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/userhandler.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/errorhandler.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/template.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/parameterhandler.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/eventhandler.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/trafficlogger.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'classes/dataserver.class.php');

// include modules
require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/formcreator/formcreator.class.php');

// include misc functions
require_once (ZEITGEIST_ROOTDIRECTORY . 'includes/autoloader.include.php');


?>