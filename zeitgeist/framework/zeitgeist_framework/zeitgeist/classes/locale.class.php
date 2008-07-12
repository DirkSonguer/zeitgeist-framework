<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Locale/Translator class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST LOCALE
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgLocale::init();
 */
class zgLocale
{
	private static $instance = false;

	protected $debug;
	protected $messages;
	protected $configuration;
	
	protected $currentLocale;

	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->currentLocale = '';
	}


	/**
	 * Initialize the singleton
	 *
	 * @return object
	 */
	public static function init()
	{
		if (self::$instance === false)
		{
			self::$instance = new zgLocale();
		}

		return self::$instance;
	}

	public function loadLocale($localeId, $localeFile)
	{
		$this->debug->guard(true);

		if (!$this->locales[$localeId] = $configuration->loadConfiguration('zglocale_' . $localeId, $localeFile))
		{
			$this->debug->write('Problem loading the locale: the given locale file could not be load', 'warning');
			$this->messages->setMessage('Problem loading the locale: the given locale file could not be load', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function setLocale($localeId)
	{
		$this->debug->guard(true);

		if (!$this->configuration->getConfiguration('zglocale_' . $localeId))
		{
			$this->debug->write('Problem changing locale: the given locale does not exist: ' . $localeId, 'warning');
			$this->messages->setMessage('Problem changing locale: the given locale does not exist: ' . $localeId, 'warning');
			$this->debug->unguard(false);
			return $message;
		}

		$this->currentLocale = $localeId;

		$this->debug->unguard(true);
		return true;
	}


	public function write($message)
	{
		$this->debug->guard(true);

		$localeMessage = '';
		$localeMessage = $this->configuration->getConfiguration('zglocale_' . $this->currentLocale, $message);		
		if ($localeMessage != '')
		{
			$this->debug->unguard($localeMessage);
			return $localeMessage;
		}
		
		$this->debug->write('Message string could not be found in the current locale', 'warning');
		$this->messages->setMessage('Message string could not be found in the current locale', 'warning');
		$this->debug->unguard(false);
		return $message;
	}
}
?>