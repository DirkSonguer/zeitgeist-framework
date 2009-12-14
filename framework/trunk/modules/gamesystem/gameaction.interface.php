<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Gameaction interface
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST GAMEACTION
 */

defined('ZEITGEIST_ACTIVE') or die();

interface zgGameaction
{

	public function execute($eventdata, $time);

}

?>