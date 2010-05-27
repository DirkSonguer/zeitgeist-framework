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

		$this->database = new zgDatabase( );
		$this->database->connect( );
	}


	/**
	 * Returns roles for a given user
	 *
	 * @param integer $userid id of the user
	 *
	 * @return boolean
	 */
	public function loadUserroles( $userid )
	{
		$this->debug->guard( );

		$userrolesToUsersTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userroles_to_users' );
		$userrolesTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userroles' );
		$sql = "SELECT u2r.userroleuser_userrole, r.userrole_name FROM " . $userrolesToUsersTablename . " u2r ";
		$sql .= "LEFT JOIN " . $userrolesTablename . " r ON u2r.userroleuser_userrole = r.userrole_id ";
		$sql .= "WHERE u2r.userroleuser_user = '" . $userid . "'";

		$res = $this->database->query( $sql );
		if ( $res )
		{
			$ret = array();
			while ( ( $row = $this->database->fetchArray( $res ) ) !== false )
			{
				if ( $row ['userrole_name'] == NULL ) $row ['userrole_name'] = true;
				$ret [$row ['userroleuser_userrole']] = $row ['userrole_name'];
			}

			if ( count( $ret ) == 0 )
			{
				$this->debug->write( 'Possible problem getting the roles of a user: there seems to be no roles assigned to the user', 'warning' );
				$this->messages->setMessage( 'Possible problem getting the roles of a user: there seems to be no roles assigned to the user', 'warning' );
			}

			$this->debug->unguard( $ret );
			return $ret;
		}
		else
		{
			$this->debug->write( 'Problem getting userrole for a user: could not find the userrole', 'warning' );
			$this->messages->setMessage( 'Problem getting userrole for a user: could not find the userrole', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( false );
		return false;
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
		$sql = 'DELETE FROM ' . $userrolesTablename . " WHERE userroleuser_user='" . $userid . "'";
		$res = $this->database->query( $sql );
		if ( !$res )
		{
			$this->debug->write( 'Problem setting the user r: lescould not clean up the roles table', 'warning' );
			$this->messages->setMessage( 'Problem setting the user roles: could not clean up the roles table', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		foreach ( $userroles as $key => $value )
		{
			$sql = 'INSERT INTO ' . $userrolesTablename . "(userroleuser_userrole, userroleuser_user) VALUES('" . $key . "', '" . $userid . "')";
			$res = $this->database->query( $sql );
			if ( !$res )
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
		$sql = "SELECT userrole_id FROM " . $userrolesTablename . " WHERE userrole_name = '" . $rolename . "'";

		$res = $this->database->query( $sql );
		if ( $res )
		{
			if ( $this->database->numRows( $res ) )
			{
				$row = $this->database->fetchArray( $res );
				$ret = $row ['userrole_id'];

				$this->debug->unguard( $ret );
				return $ret;
			}
			else
			{
				$this->debug->write( 'Problem getting role id: could not find the userrole', 'warning' );
				$this->messages->setMessage( 'Problem getting role id: could not find the userrole', 'warning' );
				$this->debug->unguard( false );
				return false;
			}
		}
		else
		{
			$this->debug->write( 'Problem getting role id: could not access roles in database', 'warning' );
			$this->messages->setMessage( 'Problem getting role id: could not access roles in database', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( false );
		return false;
	}
}

?>
