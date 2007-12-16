<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Autoloader
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * @version 1.0.1 - 18.11.2007
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST AUTOLOADER
 */

defined('ZEITGEIST_ACTIVE') or die();


	/**
	 * This is an autoloader superfunction
	 * It tries to catch unknown classes that are called and tries to load them
	 * It is used by the eventhandler to load snapins and modules
	 * 
	 * Note that if this function fails, a fatal error will occur!
	 * 
	 * @param string $class name of the class that was called
	 * 
	 * @return NULL 
	 */
	function __autoload($class)
	{
		$debug = zgDebug::init();
		$message = zgMessages::init();
		
		$debug->guard();
		
		if (file_exists(APPLICATION_ROOTDIRECTORY . 'modules/' . $class . '/' . $class . '.module.php'))
		{
			require_once(APPLICATION_ROOTDIRECTORY . 'modules/' . $class . '/' . $class . '.module.php');
			$debug->unguard('Class '.APPLICATION_ROOTDIRECTORY . 'modules/' . $class . '/' . $class . '.module.php loaded');
			return;
		}
		
		if (file_exists(APPLICATION_ROOTDIRECTORY . 'snapins/' . $class . '.snapin.php'))
		{
			require_once(APPLICATION_ROOTDIRECTORY . 'snapins/' . $class . '.snapin.php');
			$debug->unguard('Class '.APPLICATION_ROOTDIRECTORY . 'snapins/' . $class . '.snapin.php loaded');
			return;
		}		
		
		$debug->write('Error autoloading class: Class ' . $class . ' not found', 'error');
		$message->setMessage('Error autoloading class: Class ' . $class . ' not found', 'error');
		
		$debug->unguard(false);
	}
	
	
?>