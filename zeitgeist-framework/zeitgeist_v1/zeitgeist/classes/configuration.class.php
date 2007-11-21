<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Configuration class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * @version 1.0.3 - 18.08.2007
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST CONFIGURATION
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgConfiguration::init();
 */
class zgConfiguration
{
	private static $instance = false;
	
	protected $configuration;
	
	protected $debug;
	protected $messages;
	protected $database;

	/**
	 * Class constructor
	 * 
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();

		$this->database = new zgDatabase();
		$this->database->connect();

		$this->configuration = array();
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
			self::$instance = new zgConfiguration();
			self::$instance->loadConfiguration('zeitgeist', ZEITGEIST_ROOTDIRECTORY . 'configuration/zeitgeist.ini');
		}

		return self::$instance;
	}


	/**
	 * Gets the configuration value(s) for a configuration, section or module
	 * If no configuration is given, the whole section is returned
	 * If no section is given, the whole module is returned
	 * 
	 * @param string $module name of the module
	 * @param string $section name of the section inside the module
 	 * @param string $configuration name of the configuration
	 * 
	 * @return string|array 
	 */
	public function getConfiguration($module, $section='', $configuration='')
	{
		$this->debug->guard(true);
		
		if ($section == '')
		{
			// return complete module
			if (empty($this->configuration[$module][$section][$configuration]))
			{
				$this->debug->write('Error reading the configuration: module not found', 'error');
				$this->messages->setMessage('Error reading the configuration: module not found', 'error');
				$this->debug->unguard(false);
				return false;	
			}
			
			$ret = $this->configuration[$module];
		}
		elseif ($configuration == '')
		{
			// return complete section
			if (empty($this->configuration[$module][$section]))
			{
				$this->debug->write('Error reading the configuration: section not found', 'error');
				$this->messages->setMessage('Error reading the configuration: section not found', 'error');
				$this->debug->unguard(false);
				return false;	
			}
			
			$ret = $this->configuration[$module][$section];
		}
		else
		{
			// return setting value
			if (empty($this->configuration[$module][$section][$configuration]))
			{
				$this->debug->write('Error reading the configuration: configuration not found', 'error');
				$this->messages->setMessage('Error reading the configuration: configuration not found', 'error');
				$this->debug->unguard(false);
				return false;	
			}
			
			$ret = $this->configuration[$module][$section][$configuration];
		}

		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * Loads the contents of a configuration file into a module container
	 * 
	 * @param string $modulename name of the module container
	 * @param string $filename name of the ini file to load
	 * 
	 * @return boolean 
	 */
	public function loadConfiguration($modulename, $filename)
	{
		$this->debug->guard();

		// check if module with this name is already loaded
		if (!empty($this->configuration[$modulename]))
		{
			$this->debug->write('Error loading the configuration: module already loaded', 'error');
			$this->messages->setMessage('Error loading the configuration: module already loaded', 'error');
			$this->debug->unguard(false);
			return false;	
		}

		// try to load the configuration
		$configuration = $this->_loadConfigurationFromDatabase($filename);
		if ($configuration !== false)
		{
			$this->debug->write('Configuration found and successfully loaded: '.$filename);
			$this->configuration[$modulename] = $configuration;
		}
		else
		{
			$configurationArray = $this->_readINIfile($filename);
			if (!$configurationArray)
			{	
				$this->debug->write('Error loading the configuration: no contents could be extracted', 'error');
				$this->messages->setMessage('Error loading the configuration: no contents could be extracted', 'error');
				$this->debug->unguard(false);
				return false;
			}
		
			$ret = $this->_saveConfigurationToDatabase($filename, $configurationArray);
			$this->configuration[$modulename] = $configurationArray;
		}

		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * Loads a configuration module from the database
	 * 
	 * @param string $filename name of the file/ module to load
	 * 
	 * @return array|boolean 
	 */
	protected function _loadConfigurationFromDatabase($filename)
	{
		$this->debug->guard();

		$res = $this->database->query("SELECT * FROM " . ZG_DB_CONFIGURATIONCACHE . " WHERE configurationcache_name = '".$filename."'");
	
		if ($this->database->numRows($res) == 1)
		{
			$row = $this->database->fetchArray($res);
			
			if ($row['configurationcache_timestamp'] == filemtime($filename))
			{
				$serializedConfiguration = $row['configurationcache_content'];
				$serializedConfiguration = base64_decode($serializedConfiguration);
				$configuration = unserialize($serializedConfiguration);

				if ($configuration === false)
				{
					$this->debug->write('Error unserializing configuration content from the database', 'error');
					$this->messages->setMessage('Error unserializing configuration content from the database', 'error');
					$this->debug->unguard(false);
					return false;
				}
			}
			else
			{
				$res = $this->database->query("DELETE FROM " . ZG_DB_CONFIGURATIONCACHE . " WHERE configurationcache_name = '".$filename."'");
				$this->debug->write('Configuration data in the database is outdated', 'warning');
				$this->messages->setMessage('Configuration data in the database is outdated', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		else
		{
			$this->debug->write('No configuration is stored in database for this module', 'warning');
			$this->messages->setMessage('No configuration is stored in database for this module', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($configuration);
		return $configuration;
	}
	
	
	/**
	 * Save a given configuration set into the database
	 * 
	 * @param string $filename name of the file
	 * @param string $configuration configuration content
	 * 
	 * @return boolean 
	 */
	protected function _saveConfigurationToDatabase($filename, $configuration)
	{
		$this->debug->guard();
		
		$serializedConfiguration = serialize($configuration);
		if ($serializedConfiguration == '')
		{
			$this->debug->unguard(false);
			return false;
		}
		
		$serializedConfiguration = base64_encode($serializedConfiguration);
		if ($serializedConfiguration === false)
		{
			$this->debug->unguard(false);
			return false;
		}
		
		$res = $this->database->query("INSERT INTO " . ZG_DB_CONFIGURATIONCACHE . 
		"(configurationcache_name, configurationcache_content, configurationcache_timestamp) " .
		"VALUES('" . $filename . "', '" . $serializedConfiguration . "', '" . filemtime($filename) . "')");		
		
		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Reads out the contents of an ini file into an array
	 * Also handles all references inside the configuration file
	 * 
	 * @param string $filename name of the ini file to load
	 * 
	 * @return array|boolean 
	 */
	protected function _readINIfile($filename)
	{
		$this->debug->guard();
		
		if (!file_exists($filename))
		{
			$this->debug->write('Error loading the configuration file '.$filename.': file not found', 'error');
			$this->messages->setMessage('Error loading the configuration file '.$filename.': file not found', 'error');
			$this->debug->unguard($retArray);
			return false;
		}

		$retArray = array();
		
		$fileArray = array();
		$fileArray = file($filename);
		
		if (!$fileArray)
		{
			$this->debug->write('Error loading the configuration file '.$filename.': file not a valid ini file', 'error');
			$this->messages->setMessage('Error loading the configuration file '.$filename.': file not a valid ini file', 'error');
			$this->debug->unguard(false);
			return false;
		}
		
		$currentSection = '';

		foreach ($fileArray as $fileData)
		{
			$fileData = trim($fileData);

			// check for comments
			if ( (substr($fileData, 0, 1) == ';') && ($fileData == '') ) continue;

			if ( (substr($fileData, 0, 1) == '[') && (substr($fileData, -1, 1) == ']') )
			{
				// parsing section
				$currentSection = substr($fileData, 1, -1);
			}
			else
			{
				// parsing key/value
				$delimiter = strpos($fileData, '=');
				$configurationKey = '';
				$configurationValue = '';

				if ($delimiter > 0)
				{
					$configurationKey = trim(substr($fileData, 0, $delimiter));
					$configurationValue = trim(substr($fileData, $delimiter + 1));
					
					// check if value is escaped. if so, cut the escape chars
					if ( (substr($configurationValue, 1, 1) == '"') && (substr($configurationValue, -1, 1) == '"') )
					{
						$configurationValue = substr($value, 1, -1);
					}

					$arrayvalue = false;
					if (substr($configurationKey, -2, 2) == '[]')
					{
						$arrayvalue = true;
						$configurationKey = substr($configurationKey, 0, (strlen($configurationKey)-2));
					}
					
					if (!$arrayvalue)
					{
						$retArray[$currentSection][$configurationKey] = stripcslashes($configurationValue);
					}
					else
					{
						$retArray[$currentSection][$configurationKey][] = stripcslashes($configurationValue);
					}
				}
			}
		}

		$this->debug->unguard($retArray);
		return $retArray;
	}

}
?>