<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Localisation class
 * 
 * This class handles multiple language variants of system messages
 * and is able to convert one variant into another
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST LOCALISATION
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgLocalisation
{
	protected $debug;
	protected $messages;
	protected $configuration;
	
	protected $currentLocale;

	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		
		$this->currentLocale = '';
	}


	/**
	 * Function to load a locale definition file
	 *
	 * @param string $id id of the new locale
	 * @param string $filename file that contains the locale definition
	 * 
	 * @return boolean
	 */
	public function loadLocale($id, $filename, $overwrite=false)
	{
		$this->debug->guard(true);

		if (!$this->locales[$id] = $this->configuration->loadConfiguration('zglocale_' . $id, $filename, $overwrite))
		{
			$this->debug->write('Problem loading the locale: the given locale file could not be load', 'warning');
			$this->messages->setMessage('Problem loading the locale: the given locale file could not be load', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Sets the current locale
	 *
	 * @param string $id id of the locale to activate
	 *
	 * @return boolean
	 */
	public function setLocale($id)
	{
		$this->debug->guard(true);

		if (!$this->configuration->getConfiguration('zglocale_' . $id))
		{
			$this->debug->write('Problem changing locale: the given locale does not exist: ' . $id, 'warning');
			$this->messages->setMessage('Problem changing locale: the given locale does not exist: ' . $id, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->currentLocale = $id;

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Translates a message into the current locale
	 *
	 * @param string $message message to translate
	 *
	 * @return string|boolean
	 */
	public function write($message)
	{
		$this->debug->guard(true);

		if ($this->currentLocale == '')
		{
			$this->debug->write('No locale active. Returning original text', 'message');
			$this->messages->setMessage('No locale active. Returning original text', 'message');
			$this->debug->unguard(false);
			return $message;
		}
		
		$localeMessage = '';
		$localeMessage = $this->configuration->getConfiguration('zglocale_' . $this->currentLocale, $this->currentLocale, $message);		
		if ($localeMessage != '')
		{
			$this->debug->unguard($localeMessage);
			return $localeMessage;
		}
		
		$this->debug->write('Message string could not be found in the current locale. Returning original text', 'warning');
		$this->messages->setMessage('Message string could not be found in the current locale. Returning original text', 'warning');
		$this->debug->unguard(false);
		return $message;
	}
}
?>