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
 * @subpackage ZEITGEIST GAMESYSTEM
 */

if (!defined('GAMESYSTEM_ACTIONDIRECTORY')) define('GAMESYSTEM_ACTIONDIRECTORY', 'gameactions/');

// include the gamesystem classes
require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/gamesystem/gameaction.interface.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/gamesystem/gamesetup.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/gamesystem/gamedata.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/gamesystem/gamehandler.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/gamesystem/gameautoloader.include.php');

spl_autoload_register ('zgGameautoloader');

?>