<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Userroles class
 *
 * Manages roles that can be associated with a user
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERHANDLER
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

class zgUserroles
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
	 * Returns roles for a given user
	 * The userroles will be stored in an array with the userrole id as key
	 * and the name as value
	 *
	 * @param integer $userid id of the user
	 *
	 * @return array | boolean
	 */
	public function loadUserroles( $userid )
	{
		$this->debug->guard( );

		$userrolesToUsersTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userroles_to_users' );
		$userrolesTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userroles' );

		$sql = $this->database->prepare( "SELECT u2r.userroleuser_userrole, r.userrole_name FROM " . $userrolesToUsersTablename . " u2r LEFT JOIN " . $userrolesTablename . " r ON u2r.userroleuser_userrole = r.userrole_id WHERE u2r.userroleuser_user = ?" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem getting userrole for a user: could not read from userrole table', 'warning' );
			$this->messages->setMessage( 'Problem getting userrole for a user: could not read from userrole table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$ret = array( );
		while ( $row = $sql->fetch( PDO::FETCH_ASSOC ) )
		{
			var_dump($row);
			$ret[ $row[ 'userroleuser_userrole' ] ] = $row[ 'userrole_name' ];
		}

		if ( count( $ret ) == 0 )
		{
			$this->debug->write( 'Possible problem getting the roles of a user: there seems to be no roles assigned to the user', 'warning' );
			$this->messages->setMessage( 'Possible problem getting the roles of a user: there seems to be no roles assigned to the user', 'warning' );
		}

		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Save all userroles for the user
	 *
	 * @param integer $userid id of the user
	 * @param array $userroles array containing all roles
	 *
	 * @return boolean
	 */
	public function saveUserroles( $userid, $userroles )
	{
		$this->debug->guard( );

		if ( !is_array( $userroles ) || ( count( $userroles ) < 1 ) )
		{
			$this->debug->write( 'Problem setting the user roles: array not valid', 'warning' );
			$this->messages->setMessage( 'Problem setting the user roles: array not valid', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$userrolesTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userroles_to_users' );

		$sql = $this->database->prepare( "DELETE FROM " . $userrolesTablename . " WHERE userroleuser_user= ?" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem setting userrole for a user: could not clean up the roles table', 'warning' );
			$this->messages->setMessage( 'Problem setting userrole for a user: could not clean up the roles table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$sql = $this->database->prepare( "INSERT INTO " . $userrolesTablename . "(userroleuser_userrole, userroleuser_user) VALUES(?, ?)" );

		foreach ( $userroles as $key => $value )
		{
			$sql->bindParam( 1, $key );
			$sql->bindParam( 2, $userid );

			if ( !$sql->execute( ) )
			{
				$this->debug->write( 'Problem setting the user roles: could not insert the roles into the database', 'warning' );
				$this->messages->setMessage( 'Problem setting the user roles: could not insert the roles into the database', 'warning' );
				$this->debug->unguard( false );
				return false;
			}
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Gets the role id for a given rolename
	 *
	 * @param string $rolename name of the role
	 *
	 * @return integer
	 */
	public function identifyRole( $rolename )
	{
		$this->debug->guard( );

		$userrolesTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userroles' );

		$sql = $this->database->prepare( "SELECT userrole_id FROM " . $userrolesTablename . " WHERE userrole_name = ?" );
		$sql->bindParam( 1, $rolename );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem getting role id: could not access roles in database', 'warning' );
			$this->messages->setMessage( 'Problem getting role id: could not access roles in database', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		if ( $sql->rowCount( ) > 0 )
		{
			$row = $sql->fetch( PDO::FETCH_ASSOC );
			$ret = $row[ 'userrole_id' ];

			$this->debug->unguard( $ret );
			return $ret;
		}

		$this->debug->write( 'Problem getting role id: could not find the userrole', 'warning' );
		$this->messages->setMessage( 'Problem getting role id: could not find the userrole', 'warning' );
		$this->debug->unguard( false );
		return false;
	}
}

?>
