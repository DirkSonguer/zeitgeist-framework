<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Configuration class
 *
 * This class acts as a container for all configuration data of
 * a Zeitgeist project
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST CONFIGURATION
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

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
	protected function __construct( )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );

		$this->database = new zgDatabasePDO( "mysql:host=" . ZG_DB_DBSERVER . ";dbname=" . ZG_DB_DATABASE, ZG_DB_USERNAME, ZG_DB_USERPASS );

		$this->configuration = array( );
	}


	/**
	 * Initialize the singleton
	 *
	 * @return zgConfiguration
	 */
	public static function init( )
	{
		if ( self::$instance === false )
		{
			self::$instance = new zgConfiguration( );

			// try to load zeitgeist default configuration as this contains several global configuration data
			if ( file_exists( ZEITGEIST_ROOTDIRECTORY . 'configuration/zeitgeist.ini' ) )
			{
				self::$instance->loadConfiguration( 'zeitgeist', ZEITGEIST_ROOTDIRECTORY . 'configuration/zeitgeist.ini' );
			}

			// try to load zeitgeist configuration in the application configuration directory
			// the application configuration will overwrite the default values and act as a project configuration file
			if ( file_exists( APPLICATION_ROOTDIRECTORY . 'configuration/zeitgeist.ini' ) )
			{
				self::$instance->loadConfiguration( 'zeitgeist', APPLICATION_ROOTDIRECTORY . 'configuration/zeitgeist.ini', true );
			}
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
	public function getConfiguration( $module, $section = '', $configuration = '' )
	{
		$this->debug->guard( true );

		if ( $section == '' )
		{
			// try to return the complete configuration if not empty
			if ( empty( $this->configuration[ $module ] ) )
			{
				$this->debug->write( 'Problem reading the configuration: module not found', 'warning' );
				$this->messages->setMessage( 'Problem reading the configuration: module not found', 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			$ret = $this->configuration[ $module ];
			array_walk_recursive( $ret, array( $this, '_replaceReferences' ) );
		}
		elseif ( $configuration == '' )
		{
			// try to return the section configuration if not empty
			if ( empty( $this->configuration[ $module ][ $section ] ) )
			{
				$this->debug->write( 'Problem reading the configuration: section not found', 'warning' );
				$this->messages->setMessage( 'Problem reading the configuration: section not found', 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			$ret = $this->configuration[ $module ][ $section ];
			array_walk_recursive( $ret, array( $this, '_replaceReferences' ) );
		}
		else
		{
			// try to return the configuration value if it's not empty
			if ( !isset( $this->configuration[ $module ][ $section ][ $configuration ] ) )
			{
				$this->debug->write( 'Problem reading the configuration: configuration not found (' . $module . ' - ' . $section . ' - ' . $configuration . ')', 'warning' );
				$this->messages->setMessage( 'Problem reading the configuration: configuration not found (' . $module . ' - ' . $section . ' - ' . $configuration . ')', 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			$ret = $this->configuration[ $module ][ $section ][ $configuration ];
			if ( !is_array( $ret ) )
			{
				$this->_replaceReferences( $ret, '' );
			}
			else
			{
				array_walk_recursive( $ret, array( $this, '_replaceReferences' ) );
			}
		}

		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Loads the contents of a configuration file into a module container
	 *
	 * @param string $modulename name of the module container
	 * @param string $filename name of the ini file to load
	 * @param boolean $overwrite flag if an existing module with that name should be ignored and replaced
	 *
	 * @return boolean
	 */
	public function loadConfiguration( $modulename, $filename, $overwrite = false )
	{
		$this->debug->guard( );

		// check if module with this name is already loaded
		if ( ( !empty( $this->configuration[ $modulename ] ) ) && ( $overwrite == false ) )
		{
			$this->debug->write( 'Problem loading the configuration: module already loaded', 'warning' );
			$this->messages->setMessage( 'Problem loading the configuration: module already loaded', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// try to load the configuration from the database
		// if this succeeds, $configurationArray will contain the configuration array
		$configurationArray = $this->_loadConfigurationFromDatabase( $filename );

		if ( is_array( $configurationArray ) )
		{
			$this->debug->write( 'Configuration found and successfully loaded: ' . $filename );
			if ( !$overwrite )
			{
				$this->configuration[ $modulename ] = $configurationArray;
			}
			else
			{
				if ( is_array( $this->configuration[ $modulename ] ) )
				{
					$this->configuration[ $modulename ] = array_merge( $this->configuration[ $modulename ], $configurationArray );
				}
				else
				{
					$this->configuration[ $modulename ] = $configurationArray;
				}
			}
		}
		else
		{
			// no configuration could be found in the database
			// thus we have to load the data from the file system
			$configurationArray = $this->_readINIfile( $filename );

			if ( !is_array( $configurationArray ) )
			{
				$this->debug->write( 'Problem loading the configuration: no contents could be extracted', 'warning' );
				$this->messages->setMessage( 'Problem loading the configuration: no contents could be extracted', 'warning' );
				$this->debug->unguard( false );
				return false;
			}

			// after successfully loading the data, store it in the database
			$ret = $this->_saveConfigurationToDatabase( $filename, $configurationArray );

			if ( !$overwrite )
			{
				// as we checked at the beginning of this method that there is no
				// configuration with this id already, we can just fill the array
				$this->configuration[ $modulename ] = $configurationArray;
			}
			else
			{
				if ( is_array( $this->configuration[ $modulename ] ) )
				{
					$this->configuration[ $modulename ] = array_merge( $this->configuration[ $modulename ], $configurationArray );
				}
				else
				{
					$this->configuration[ $modulename ] = $configurationArray;
				}
			}
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Loads a configuration module from the database
	 *
	 * @access protected
	 *
	 * @param string $filename name of the file/ module to load
	 *
	 * @return array|boolean
	 */
	protected function _loadConfigurationFromDatabase( $filename )
	{
		$this->debug->guard( );

		$sql = $this->database->prepare( "SELECT configurationcache_content, configurationcache_timestamp FROM " . ZG_DB_CONFIGURATIONCACHE . " WHERE configurationcache_name = ?" );
		$sql->bindParam( 1, $filename );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem loading the configuration from database: could not read from configuration table', 'warning' );
			$this->messages->setMessage( 'Problem loading the configuration from database: could not read from configuration table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// only one row is expected as the module names should be unique
		if ( $sql->rowCount( ) == 1 )
		{
			// as there is only one row, we only need to fetch once
			$row = $sql->fetch( PDO::FETCH_ASSOC );

			// check if the timestamp of the file matches the timestamp in the database
			if ( $row[ 'configurationcache_timestamp' ] == filemtime( $filename ) )
			{
				// decode and unserialze it
				$serializedConfiguration = $row[ 'configurationcache_content' ];
				$serializedConfiguration = base64_decode( $serializedConfiguration );
				$configuration = unserialize( $serializedConfiguration );

				if ( $configuration === false )
				{
					$this->debug->write( 'Problem loading the configuration from database: error unserializing configuration content from the database', 'warning' );
					$this->messages->setMessage( 'Problem loading the configuration from database: error unserializing configuration content from the database', 'warning' );
					$this->debug->unguard( false );
					return false;
				}
			}
			else
			{
				// the configuration in the database is outdated thus it can be deleted
				$sql = $this->database->prepare( "DELETE FROM " . ZG_DB_CONFIGURATIONCACHE . " WHERE configurationcache_name = ?" );
				$sql->bindParam( 1, $filename );

				if ( !$sql->execute( ) )
				{
					$this->debug->write( 'Problem loading the configuration from database: could not delete outdated data from configuration table', 'warning' );
					$this->messages->setMessage( 'Problem loading the configuration from database: could not delete outdated data from configuration table', 'warning' );

					$this->debug->unguard( false );
					return false;
				}

				$this->debug->write( 'Info while loading the configuration from database: configuration data in the database is outdated', 'warning' );
				$this->messages->setMessage( 'Info while loading the configuration from database: configuration data in the database is outdated', 'warning' );
				$this->debug->unguard( false );
				return false;
			}
		}
		else
		{
			$this->debug->write( 'Info while loading the configuration from database: no configuration is stored in database for this module', 'warning' );
			$this->messages->setMessage( 'Info while loading the configuration from database: no configuration is stored in database for this module', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( $configuration );
		return $configuration;
	}


	/**
	 * Saves a given configuration set into the database
	 *
	 * @access protected
	 *
	 * @param string $filename name of the file
	 * @param string $configuration configuration content
	 *
	 * @return boolean
	 */
	protected function _saveConfigurationToDatabase( $filename, $configuration )
	{
		$this->debug->guard( );

		$serializedConfiguration = serialize( $configuration );
		if ( $serializedConfiguration == '' )
		{
			$this->debug->unguard( false );
			return false;
		}

		// we are encoding the serialzed data string with base64
		// this is due to an PHP bug in an early version of PHP5 where sometimes
		// serialized objects could not be saved and retrieved from the database correctly
		// normally you would not need this as the bug is fixed by now
		$serializedConfiguration = base64_encode( $serializedConfiguration );
		if ( $serializedConfiguration === false )
		{
			$this->debug->unguard( false );
			return false;
		}

		$sql = $this->database->prepare( "INSERT INTO " . ZG_DB_CONFIGURATIONCACHE . "(configurationcache_name, configurationcache_content, configurationcache_timestamp) " . "VALUES(?, ?, ?)" );
		$sql->bindParam( 1, $filename );
		$sql->bindParam( 2, $serializedConfiguration );
		$sql->bindParam( 3, filemtime( $filename ) );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem saving the configuration to database: could not insert data into configuration table', 'warning' );
			$this->messages->setMessage( 'Problem saving the configuration to database: could not insert data into configuration table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Replaces the references in a configuration array
	 * This function is called recursively by walk_array_recursive in the getConfiguration method
	 *
	 * @access protected
	 *
	 * @param string $configurationValue value of the configuration
	 * @param string $configurationKey key of the configuration
	 */
	protected function _replaceReferences( &$configurationValue, $configurationKey )
	{
		// check for references
		if ( ( strpos( $configurationValue, '[[' ) !== false ) && ( strpos( $configurationValue, ']]' ) !== false ) )
		{
			// get start and end of the reference id
			$referenceStart = strpos( $configurationValue, '[[' );
			$referenceEnd = strpos( $configurationValue, ']]' );

			// extract reference id
			$referenceString = substr( $configurationValue, $referenceStart, $referenceEnd - $referenceStart + 2 );
			$reference = substr( $referenceString, 2, -2 );

			// get individual information from the reference id
			// this points to the configuration that need to be already loaded
			$referenceArray = explode( '.', $reference );
			if ( count( $referenceArray ) == 3 )
			{
				if ( $this->getConfiguration( $referenceArray[ 0 ], $referenceArray[ 1 ], $referenceArray[ 2 ] ) )
				{
					$referenceValue = $this->getConfiguration( $referenceArray[ 0 ], $referenceArray[ 1 ], $referenceArray[ 2 ] );
					$configurationValue = str_replace( $referenceString, $referenceValue, $configurationValue );
				}
				else
				{
					$this->debug->write( 'A reference (' . $reference . ') could not be resolved', 'warning' );
					$this->messages->setMessage( 'A reference (' . $reference . ') could not be resolved', 'warning' );
				}
			}
			else
			{
				$this->debug->write( 'A reference (' . $reference . ') is not stated correctly', 'warning' );
				$this->messages->setMessage( 'A reference (' . $reference . ') is not stated correctly', 'warning' );
			}
		}
	}


	/**
	 * Reads out the contents of an ini file into an array
	 * Also handles all references inside the configuration file
	 *
	 * @access protected
	 *
	 * @param string $filename name of the ini file to load
	 *
	 * @return array|boolean
	 */
	protected function _readINIfile( $filename )
	{
		$this->debug->guard( );

		if ( !file_exists( $filename ) )
		{
			$this->debug->write( 'Problem loading the configuration file ' . $filename . ': file not found', 'warning' );
			$this->messages->setMessage( 'Problem loading the configuration file ' . $filename . ': file not found', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$retArray = array( );

		$fileArray = array( );
		$fileArray = file( $filename );

		if ( !$fileArray )
		{
			$this->debug->write( 'Problem loading the configuration file ' . $filename . ': file not a valid ini file', 'warning' );
			$this->messages->setMessage( 'Problem loading the configuration file ' . $filename . ': file not a valid ini file', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$currentSection = '';

		foreach ( $fileArray as $fileData )
		{
			$fileData = trim( $fileData );

			// check for comments
			// if comment is found, continue to next line
			if ( ( substr( $fileData, 0, 1 ) == ';' ) && ( $fileData == '' ) ) continue;

			// check if the line is the beginn of a new section
			if ( ( substr( $fileData, 0, 1 ) == '[' ) && ( substr( $fileData, -1, 1 ) == ']' ) )
			{
				// parsing section name and set it as the new section name
				$currentSection = substr( $fileData, 1, -1 );
			}
			else
			{
				// parsing key/value
				// information will be separated into key and value
				$delimiter = strpos( $fileData, '=' );
				$configurationKey = '';
				$configurationValue = '';

				// check if the delemiter was found
				// if not, it's an invalid line, thus it does not need to be processed
				if ( $delimiter > 0 )
				{
					// separate line into key and value
					$configurationKey = trim( substr( $fileData, 0, $delimiter ) );
					$configurationValue = trim( substr( $fileData, $delimiter + 1 ) );

					// check if value is escaped
					// if so, cut the escape chars
					if ( ( substr( $configurationValue, 1, 1 ) == '"' ) && ( substr( $configurationValue, -1, 1 ) == '"' ) )
					{
						$configurationValue = substr( $configurationValue, 1, -1 );
					}

					// check if the value is an array
					$arrayvalue = false;
					if ( ( strpos( $configurationKey, '[' ) !== false ) && ( strpos( $configurationKey, ']' ) !== false ) )
					{
						$keystart = strpos( $configurationKey, '[' );
						$arrayvalue = true;
						$arrayKey = substr( $configurationKey, $keystart + 1, -1 );
						$configurationKey = substr( $configurationKey, 0, $keystart );
					}

					if ( !$arrayvalue )
					{
						$retArray[ $currentSection ][ $configurationKey ] = $configurationValue;
						$arrayKey = false;
					}
					else
					{
						if ( !$arrayKey )
						{
							$retArray[ $currentSection ][ $configurationKey ][ ] = $configurationValue;
						}
						else
						{
							$retArray[ $currentSection ][ $configurationKey ][ $arrayKey ] = $configurationValue;
						}
					}
				}
			}
		}

		$this->debug->unguard( $retArray );
		return $retArray;
	}
}

?>