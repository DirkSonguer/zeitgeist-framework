<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Userdata class
 *
 * Manages the additional userdata associated with a user
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERHANDLER
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

class zgUserdata
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;


	/**
	 * Class constructor
	 */
	public function __construct( )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );
		$this->configuration = zgConfiguration::init( );

		$this->database = new zgDatabasePDO( "mysql:host=" . ZG_DB_DBSERVER . ";dbname=" . ZG_DB_DATABASE, ZG_DB_USERNAME, ZG_DB_USERPASS );
	}


	/**
	 * Loads all userdata for a given user
	 *
	 * @param integer $userid id of the user
	 *
	 * @return boolean
	 */
	public function loadUserdata( $userid )
	{
		$this->debug->guard( );

		$userdataTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userdata' );

		$sql = $this->database->prepare( "SELECT * FROM " . $userdataTablename . " WHERE userdata_user = ?" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem getting userdata for a user: could not read from userdata table', 'warning' );
			$this->messages->setMessage( 'Problem getting userdata for a user: could not read from userdata table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$ret = array( );
		if ( $sql->rowCount( ) > 0 )
		{
			// a user can have only one assigned userdata, hence we only fetch once
			// change this if you want to have multiple userdata rows per user
			// see also username related methods in userfunctions.class.php
			$ret = $sql->fetch( PDO::FETCH_ASSOC );
		}
		else
		{
			$this->debug->write( 'The user seems to habe no assigned data. Userdata returned empty.', 'message' );
			$this->messages->setMessage( 'The user seems to habe no assigned data. Userdata returned empty.', 'message' );

			$sql = $this->database->prepare( "EXPLAIN " . $userdataTablename );
			if ( !$sql->execute( ) )
			{
				$this->debug->write( 'Problem getting userdata for a user: userdata table does not exist', 'warning' );
				$this->messages->setMessage( 'Problem getting userdata for a user: userdata table does not exist', 'warning' );

				$this->debug->unguard( false );
				return false;
			}

			while ( $row = $sql->fetch( PDO::FETCH_ASSOC ) )
			{
				$ret[ $row[ 'Field' ] ] = '';
			}
		}

		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Save the according userdata to the database
	 *
	 * @param integer $userid id of the user
	 * @param array $userdata array containing all user data
	 *
	 * @return boolean
	 */
	public function saveUserdata( $userid, $userdata )
	{
		$this->debug->guard( );

		if ( ( !is_array( $userdata ) ) || ( count( $userdata ) < 1 ) )
		{
			$this->debug->write( 'Problem setting the user data: array not valid', 'warning' );
			$this->messages->setMessage( 'Problem setting the user data: array not valid', 'warning' );
			
			$this->debug->unguard( false );
			return false;
		}


		$userdataTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userdata' );

		$sql = $this->database->prepare( "DELETE FROM " . $userdataTablename . " WHERE userdata_user = ?" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem saving the user data: could not clean up the data table', 'warning' );
			$this->messages->setMessage( 'Problem saving the user data: could not clean up the data table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// fill the control arrays and query strings with the userdata
		$sqlkeys = 'userdata_user,';
		$sqlvalues = ':userdata_user,';
		$userdatavalues[ 'userdata_user' ] = $userid;
		foreach ( $userdata as $key => $value )
		{
			if ( ( $key != 'userdata_timestamp' ) && ( $key != 'userdata_user' ) )
			{
				$sqlkeys .= $key . ',';
				$sqlvalues .= ":" . $key . ",";
				$userdatavalues[ $key ] = $value;
			}
		}

		// prepare the query strings by removing the last ','
		$sqlkeys = substr( $sqlkeys, 0, -1 );
		$sqlvalues = substr( $sqlvalues, 0, -1 );

		$sql = $this->database->prepare( "INSERT INTO " . $userdataTablename . "(" . $sqlkeys . ") VALUES(" . $sqlvalues . ")" );
		if ( !$sql->execute( $userdatavalues ) )
		{
			$this->debug->write( 'Problem saving the user data: could not write the data', 'warning' );
			$this->messages->setMessage( 'Problem saving the user data: could not write the data', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( true );
		return true;
	}
}

?>
