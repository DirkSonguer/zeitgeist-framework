<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Database class
 *
 * A simple database abstraction class
 * It serves as a small example implementation that adds function
 * guarding. Most likely you will use your own or an already
 * existing database layer that you can extend with guarding.
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST DATABASE
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

class zgDatabase
{
	protected $debug;
	protected $messages;
	protected $dblink;
	protected $persistent;


	/**
	 * Class constructor
	 */
	public function __construct( )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );

		$this->persistent = false;
		$this->dblink = null;
	}


	/**
	 * Class destructor
	 */
	public function __destruct( )
	{
		if ( ( $this->dblink ) && ( $this->persistent ) )
		{
			$this->close( );
		}
	}


	/**
	 * Connect to the database
	 * If no connection info is given, the standard defines are used
	 * These should be defined in the application config
	 *
	 * @param string $servername servername to connect to
	 * @param string $username username to connect with
	 * @param string $userpass userpassword to connect with
	 * @param string $database name of the database to connect to
	 * @param boolean $persistent set true if connection should be persistent
	 * @param boolean $newconnection set true if new link should be created for this connection
	 *
	 * @return boolean
	 */
	public function connect( $servername = ZG_DB_DBSERVER, $username = ZG_DB_USERNAME, $userpass = ZG_DB_USERPASS, $database = ZG_DB_DATABASE, $persistent = false, $newconnection = false )
	{
		$this->debug->guard( );

		if ( $persistent )
		{
			$this->persistent = true;
			$this->dblink = @mysql_pconnect( $servername, $username, $userpass );
		}
		else
		{
			$this->dblink = @mysql_connect( $servername, $username, $userpass, $newconnection );
		}

		if ( !$this->dblink )
		{
			$this->debug->write( 'Problem connecting to database server: ' . mysql_error( ), 'warning' );
			$this->messages->setMessage( 'Problem connecting to database server: ' . mysql_error( ), 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		if ( !mysql_select_db( $database, $this->dblink ) )
		{
			$this->debug->write( 'Problem connecting to database: ' . mysql_error( ), 'warning' );
			$this->messages->setMessage( 'Problem connecting to database: ' . mysql_error( ), 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->setDBCharset( 'utf8' );

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Close the database connection
	 *
	 * @return boolean
	 */
	public function close( )
	{
		$this->debug->guard( );

		$ret = false;

		if ( ( $this->dblink ) && ( !$this->persistent ) )
		{
			$ret = mysql_close( $this->dblink );
		}

		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Set the charset for connection, results and client
	 *
	 * @param string $charset charset to use for the connection
	 *
	 * @return boolean
	 */
	public function setDBCharset( $charset )
	{
		$this->debug->guard( );

		$res = mysql_query( "SET character_set_connection = " . $charset, $this->dblink );
		if ( !$res )
		{
			$this->debug->write( 'Problem setting charset for connection: ' . mysql_error( ), 'warning' );
			$this->messages->setMessage( 'Problem setting charset for connection: ' . mysql_error( ), 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$res = mysql_query( "SET character_set_results = " . $charset, $this->dblink );
		if ( !$res )
		{
			$this->debug->write( 'Problem setting charset for results: ' . mysql_error( ), 'warning' );
			$this->messages->setMessage( 'Problem setting charset for results: ' . mysql_error( ), 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$res = mysql_query( "SET character_set_client = " . $charset, $this->dblink );
		if ( !$res )
		{
			$this->debug->write( 'Problem setting charset for client: ' . mysql_error( ), 'warning' );
			$this->messages->setMessage( 'Problem setting charset for client: ' . mysql_error( ), 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Send a query to the database
	 *
	 * @param string $query contains the query to be sent
	 *
	 * @return resource
	 */
	public function query( $query )
	{
		$this->debug->guard( );

		$this->debug->beginSQLStatement( );
		$result = mysql_query( $query, $this->dblink );
		$this->debug->storeSQLStatement( $query, $result );

		if ( !$result )
		{
			$this->debug->write( 'Problem executing query: ' . mysql_error( ) . ' Query was: "' . $query . '"', 'warning' );
			$this->messages->setMessage( 'Problem executing query: ' . mysql_error( ) . ' Query was: "' . $query . '"', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( $result );
		return $result;
	}


	/**
	 * Fetches a row from the sql result
	 * Standard type is MYSQL_ASSOC, so an associative array is returned
	 *
	 * @param resource $result resource id of the query
	 * @param int $type type of array to be fetched
	 *
	 * @return array|boolean
	 */
	public function fetchArray( $result, $type = MYSQL_ASSOC )
	{
		$this->debug->guard( true );

		$resulttype = gettype( $result );
		if ( $resulttype != 'resource' )
		{
			$this->debug->write( 'Problem fetching result: The given result was not a resource: "' . $result . '"', 'warning' );
			$this->messages->setMessage( 'Problem fetching result: The given result was not a resource: "' . $result . '"', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$ret = mysql_fetch_array( $result, $type );

		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Returns the number of rows for a resource
	 *
	 * @param resource $result resource id of the query
	 *
	 * @return integer
	 */
	public function numRows( $result )
	{
		$this->debug->guard( );

		$resulttype = gettype( $result );
		if ( $resulttype != 'resource' )
		{
			$this->debug->write( 'Problem counting rows: The given result was not a resource: "' . $result . '"', 'warning' );
			$this->messages->setMessage( 'Problem counting rows: The given result was not a resource: "' . $result . '"', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$ret = mysql_num_rows( $result );

		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Returns the number of affected rows by the last operation
	 *
	 * @return integer
	 */
	public function affectedRows( )
	{
		$this->debug->guard( );

		$ret = mysql_affected_rows( $this->dblink );

		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Gets the last id generated by an insert operation
	 *
	 * @return integer
	 */
	public function insertId( )
	{
		$this->debug->guard( );

		$ret = mysql_insert_id( $this->dblink );

		if ( !$ret )
		{
			$this->debug->write( 'Problem geting the last insert id: ' . mysql_error( ), 'warning' );
			$this->messages->setMessage( 'Problem geting the last insert id: ' . mysql_error( ), 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( $ret );
		return $ret;
	}
}

?>