<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Session class
 * 
 * Simple session handling class
 * The sessioninformation will be handled in the database instead
 * of files
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST SESSION
 */

defined( 'ZEITGEIST_ACTIVE' ) or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgSession::init();
 */
class zgSession
{
	private static $instance = false;
	
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	
	protected $storageMode;
	protected $newSession;
	protected $boundIP;
	protected $lifetime;
	protected $sessionName;
	protected $sessionStarted;


	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	protected function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();
		
		$this->boundIP = '';
		$this->newSession = true;
		$this->storageMode = $this->configuration->getConfiguration( 'zeitgeist', 'session', 'session_storage' );
		$this->lifetime = $this->configuration->getConfiguration( 'zeitgeist', 'session', 'session_lifetime' );
		$this->sessionName = $this->configuration->getConfiguration( 'zeitgeist', 'session', 'session_name' );
		
		$this->sessionStarted = false;
	}


	/**
	 * Initialize the singleton
	 *
	 * @return object
	 */
	public static function init()
	{
		if( self::$instance === false )
		{
			self::$instance = new zgSession();
		}
		
		return self::$instance;
	}


	/**
	 * Starts or restarts a session
	 *
	 * @return boolean
	 */
	public function startSession()
	{
		$this->debug->guard();
		
		if( ! $this->sessionStarted )
		{
			if( $this->storageMode == 'database' )
			{
				$ret = session_set_save_handler( array (&$this, '_openSession' ), array (&$this, '_closeSession' ), array (&$this, '_readSession' ), array (&$this, '_writeSession' ), array (&$this, '_destroySession' ), array (&$this, '_cleanSession' ) );
				if( ! $ret )
				{
					$this->debug->write( 'Could not register session save handlers!', 'error' );
					$this->messages->setMessage( 'Could not register session save handlers!', 'error' );
				}
			}
			
			ini_set( 'session.use_cookies', 1 );
			ini_set( 'session.use_only_cookies', 1 );
			ini_set( 'session.use_trans_sid', 0 );
			ini_set( 'session.cookie_lifetime', $this->lifetime );
			ini_set( 'session.name', $this->sessionName );
			
			$ret = session_start();
			if( ! $ret )
			{
				$this->debug->write( 'Could not start session', 'error' );
				$this->messages->setMessage( 'Could not start session', 'error' );
			}
			
			$this->sessionStarted = true;
		}
		
		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Stops a session by destroying it
	 * All session data will be lost!
	 *
	 * @return boolean
	 */
	public function stopSession()
	{
		$this->debug->guard();
		
		if( $this->getSessionId() != false )
		{
			$ret = session_destroy();
		}
		
		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Sets a session variable with a given value
	 *
	 * @param string $key the name of the session variable
	 * @param string $value the value of the session variable
	 *
	 * @return boolean
	 */
	public function setSessionVariable($key, $value)
	{
		$this->debug->guard();
		
		$_SESSION [$key] = $value;
		
		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Gets the content of a session variable
	 *
	 * @param string $key name of the variable
	 *
	 * @return object
	 */
	public function getSessionVariable($key)
	{
		$this->debug->guard();
		
		if( isset( $_SESSION [$key] ) )
		{
			$this->debug->unguard( $_SESSION [$key] );
			return $_SESSION [$key];
		}
		
		$this->debug->unguard( false );
		return false;
	}


	/**
	 * Unset a session variable thus deleting it and its contents
	 *
	 * @param string $key name of the variable
	 * *
	 * @return boolean
	 */
	public function unsetSessionVariable($key)
	{
		$this->debug->guard();
		
		if( isset( $_SESSION [$key] ) )
		{
			unset( $_SESSION [$key] );
		}
		
		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Unsets all session variables, thus clearing the whole session
	 *
	 * @return boolean
	 */
	public function unsetAllSessionVariables()
	{
		$this->debug->guard();
		
		if( empty( $_SESSION ) )
		{
			$this->debug->unguard( true );
			return true;
		}
		
		foreach( $_SESSION as $key => $value )
		{
			if( isset( $_SESSION [$key] ) )
			{
				unset( $_SESSION [$key] );
			}
		}
		
		$this->debug->unguard( true );
		return true;
	}


	/**
	 * gets the session id for the current session
	 *
	 * @return integer
	 */
	public function getSessionId()
	{
		$this->debug->guard();
		
		$ret = session_id();
		
		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * gets the ip which is bound to the session id
	 *
	 * @return string
	 */
	public function getBoundIP()
	{
		$this->debug->guard();
		
		$ret = $this->boundIP;
		
		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Function is called if a session is started
	 * No work is needed as everything is initialized in the constructor
	 */
	public function _openSession()
	{
		$this->debug->guard();
		
		$this->debug->write( 'Starting Session through hook' );
		
		$this->debug->unguard( true );
	}


	/**
	 * Function is called if a session is destroyed
	 * No work is needed as everything is destroyed in the destructor
	 */
	public function _closeSession()
	{
		$this->debug->guard();
		
		$this->debug->write( 'Stopping Session through hook' );
		
		$this->debug->unguard( true );
	}


	/**
	 * Reads out the session data from the database if the session is started
	 *
	 * @param string $id session id of the session to read out
	 *
	 * @return array
	 */
	public function _readSession($id)
	{
		$this->debug->guard();
		
		$id = mysql_real_escape_string( $id );
		
		$sessionTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_sessiondata' );
		$sql = "SELECT sessiondata_content, sessiondata_ip FROM " . $sessionTablename . " WHERE sessiondata_id = '" . $id . "'";
		
		$res = $this->database->query( $sql );
		if( $res )
		{
			if( $this->database->numRows( $res ) )
			{
				$row = $this->database->fetchArray( $res );
				$sessiondata = $row ['sessiondata_content'];
				$this->boundIP = long2ip( $row ['sessiondata_ip'] );
				
				$this->newSession = false;
				
				$this->debug->guard( $sessiondata );
				return $sessiondata;
			}
			else
			{
				$this->newSession = true;
			}
		}
		else
		{
			$this->debug->write( 'Stopping Session through hook' );
		}
		
		$this->debug->guard( '' );
		return '';
	}


	/**
	 * Writes the session data to the database
	 *
	 * @param string $id session id
	 * @param string $data derialized session data
	 *
	 * @return resource
	 */
	public function _writeSession($id, $data)
	{
		$this->debug->guard();
		
		$currentTime = time();
		
		$id = mysql_escape_string( $id );
		$data = mysql_escape_string( $data );
		
		$sessionTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_sessiondata' );
		
		if( $this->newSession )
		{
			$startTime = time();
			$sql = "INSERT INTO " . $sessionTablename . " VALUES  ('" . $id . "', '" . $startTime . "', '" . $currentTime . "', '" . $data . "', INET_ATON('" . getenv( 'REMOTE_ADDR' ) . "'))";
		}
		else
		{
			$sql = "UPDATE " . $sessionTablename . " SET " . $sessionTablename . "_lastupdate = '" . $currentTime . "', " . $sessionTablename . "_content = '" . $data . "'" . " WHERE " . $sessionTablename . "_id = '" . $id . "'";
		}
		
		$ret = $this->database->query( $sql );
		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Function is called if the session is destroyed
	 * All data according to the given session id is deleted
	 *
	 * @param string $id session id
	 *
	 * @return resource
	 */
	public function _destroySession($id)
	{
		$this->debug->guard();
		
		$id = mysql_escape_string( $id );
		
		$sessionTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_sessiondata' );
		$sql = "DELETE FROM " . $sessionTablename . " WHERE sessiondata_id = '" . $id . "'";
		
		$ret = $this->database->query( $sql );
		$this->debug->guard( $ret );
		return $ret;
	}


	/**
	 * Clean the session table of the database
	 * Old session data will be deleted from the table
	 *
	 * @param integer $max max lifetime of a session
	 *
	 * @return resource
	 */
	public function _cleanSession($max)
	{
		$this->debug->guard();
		
		$old = time() - $max;
		$old = mysql_escape_string( $old );
		
		$sessionTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_sessiondata' );
		$sql = "DELETE FROM " . $sessionTablename . " WHERE sessiondata_lastupdate < '" . $old . "'";
		
		$ret = $this->database->query( $sql );
		$this->debug->guard( $ret );
		return $ret;
	}

}
?>
