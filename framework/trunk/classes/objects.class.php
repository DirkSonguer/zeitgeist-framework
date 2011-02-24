<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Objecthandler class
 *
 * The object class is comparable to the message class but instead of
 * managing messages, it manages application objects
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST OBJECTS
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgObjects::init();
 */
class zgObjects
{
	private static $instance = false;
	protected $debug;
	protected $messages;
	protected $objects;


	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	protected function __construct( )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );

		$this->objects = array( );
	}


	/**
	 * Initialize the singleton
	 *
	 * @return zgObjects
	 */
	public static function init( )
	{
		if ( self::$instance === false )
		{
			self::$instance = new zgObjects( );
		}

		return self::$instance;
	}


	/**
	 * Adds an object to the cache
	 *
	 * @param string $name name of the object to store
	 * @param object $object whatever to store into the cache
	 * @param boolean $overwrite flag if an existing module with that name should be ignored and replaced
	 *
	 * @return boolean
	 */
	public function storeObject( $name, $object, $overwrite = false )
	{
		$this->debug->guard( );

		if ( ( !empty( $this->objects[ $name ] ) ) && ( !$overwrite ) )
		{
			$this->debug->write( 'Problem storing object: an object of this name ("' . $name . '") already exists', 'warning' );
			$this->messages->setMessage( 'Problem storing object: an object of this name ("' . $name . '") already exists', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->objects[ $name ] = $object;

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Reads out a stored object
	 *
	 * @param string $objectname name of the object
	 *
	 * @return object
	 */
	public function getObject( $objectname )
	{
		$this->debug->guard( );

		if ( empty( $this->objects[ $objectname ] ) )
		{
			$this->debug->write( 'Problem getting object: object with name ' . $objectname . ' not found', 'warning' );
			$this->messages->setMessage( 'Problem getting object: object with name ' . $objectname . ' not found', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$ret = $this->objects[ $objectname ];

		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Deletes an object from the cache
	 *
	 * @param string $objectname name of the object
	 *
	 * @return boolean
	 */
	public function deleteObject( $objectname )
	{
		$this->debug->guard( );

		if ( empty( $this->objects[ $objectname ] ) )
		{
			$this->debug->write( 'Problem deleting object: Object with name ' . $objectname . ' not found', 'warning' );
			$this->messages->setMessage( 'Problem deleting object: Object with name ' . $objectname . ' not found', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		unset( $this->objects[ $objectname ] );

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Clears the entire cache
	 * All objects will be lost
	 *
	 * @return boolean
	 */
	public function deleteAllObjects( )
	{
		$this->debug->guard( );

		$this->objects = array( );

		$this->debug->unguard( true );
		return true;
	}
}

?>
