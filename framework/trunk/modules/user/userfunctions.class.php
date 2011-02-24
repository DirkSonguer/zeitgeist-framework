<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * User class
 *
 * Provides general user handling functionalities like ctreating and
 * editing users, opt-is as well as validating a login
 * However it does manage a specific user session (see userhandler.class.php)
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERHANDLER
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

class zgUserfunctions
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;


	/**
	 * Class constructor
	 *
	 */
	public function __construct( )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );
		$this->configuration = zgConfiguration::init( );

		$this->database = new zgDatabasePDO( "mysql:host=" . ZG_DB_DBSERVER . ";dbname=" . ZG_DB_DATABASE, ZG_DB_USERNAME, ZG_DB_USERPASS );

		$this->userrights = array( );
		$this->userrightsLoaded = false;

		$this->userdata = array( );
		$this->userdataLoaded = false;

		$this->userroles = array( );
		$this->userrolesLoaded = false;
	}


	/**
	 * Creates a new user with a given name and password
	 * Returns the id of the new user
	 *
	 * @param string $name name of the user
	 * @param string $password password of the user
	 *
	 * @return integer
	 */
	public function createUser( $name, $password )
	{
		$this->debug->guard( );

		// check if name and password are given
		// they are mandatory for a new user
		if ( ( empty( $name ) ) or ( empty( $password ) ) )
		{
			$this->debug->write( 'Problem creating a new user: no name or password was given for the user', 'warning' );
			$this->messages->setMessage( 'Problem creating a new user: no name or password was given for the user', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// begin transaction as we have multiple inserts depending on each other
		if ( !$this->database->beginTransaction( ) )
		{
			$this->debug->write( 'Problem creating a new user: could no begin database transaction', 'warning' );
			$this->messages->setMessage( 'Problem creating a new user: could no begin database transaction', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// check if a user with that name already exists
		$sql = $this->database->prepare( "SELECT * FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " WHERE user_username = ?" );
		$sql->bindParam( 1, $name );

		if ( !$sql->execute( ) )
		{
			$this->database->rollBack( );
			$this->debug->write( 'Problem creating a new user: could not read from user table', 'warning' );
			$this->messages->setMessage( 'Problem creating a new user: could not read from user table', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// if you want to allow multiple users with the same name, change the following check
		if ( $sql->rowCount( ) > 0 )
		{
			$this->database->rollBack( );
			$this->debug->write( 'Problem creating a new user: a user with this name already exists in the database. Please choose another username.', 'warning' );
			$this->messages->setMessage( 'Problem creating a new user: a user with this name already exists in the database. Please choose another username.', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// define some default values
		$active = 1;
		$key = md5( uniqid( mt_rand( ), true ) );
		if ( $this->configuration->getConfiguration( 'zeitgeist', 'userhandler', 'use_doubleoptin' ) == '1' )
		{
			$active = 0;
		}

		$sql = $this->database->prepare( "INSERT INTO " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . "(user_username, user_key, user_password, user_active) VALUES(?, ?, ?, ?)" );
		$sql->bindParam( 1, $name );
		$sql->bindParam( 2, $key );
		$sql->bindParam( 3, md5( $password ) );
		$sql->bindParam( 4, $active );

		if ( !$sql->execute( ) )
		{
			$this->database->rollBack( );
			$this->debug->write( 'Problem creating a new user: could not insert the user into the database', 'warning' );
			$this->messages->setMessage( 'Problem creating a new user: could not insert the user into the database', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$currentId = $this->database->lastInsertId( );

		// insert confirmation key
		// this is used only if the double opt in is used
		if ( $this->configuration->getConfiguration( 'zeitgeist', 'userhandler', 'use_doubleoptin' ) == '1' )
		{
			$confirmationkey = md5( uniqid( mt_rand( ), true ) );

			$sql = $this->database->prepare( "INSERT INTO " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userconfirmation' ) . "(userconfirmation_user, userconfirmation_key) VALUES(?, ?)" );
			$sql->bindParam( 1, $currentId );
			$sql->bindParam( 2, $confirmationkey );

			if ( !$sql->execute( ) )
			{
				$this->database->rollBack( );
				$this->debug->write( 'Problem creating a new user: could not insert the user confirmation key into the database', 'warning' );
				$this->messages->setMessage( 'Problem creating a new user: could not insert the user confirmation key into the database', 'warning' );
				$this->debug->unguard( false );
				return false;
			}
		}

		// commit inserts into database
		$this->database->commit( );

		$this->debug->unguard( $currentId );
		return $currentId;
	}


	/**
	 * Deletes a user from the database
	 *
	 * @param integer $userid id of the user to delete
	 *
	 * @return boolean
	 */
	public function deleteUser( $userid )
	{
		$this->debug->guard( );

		// user
		$sql = $this->database->prepare( "DELETE FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " WHERE user_id = ?" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem deleting user: could not delete the user from database', 'warning' );
			$this->messages->setMessage( 'Problem deleting user: could not delete the user from database', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// userdata
		$sql = $this->database->prepare( "DELETE FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userdata' ) . " WHERE userdata_user = ?" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem deleting user: could not delete the userdata from database', 'warning' );
			$this->messages->setMessage( 'Problem deleting user: could not delete the userdata from database', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// userrights
		$sql = $this->database->prepare( "DELETE FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userrights' ) . " WHERE userright_user = ?" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem deleting user: could not delete the userrights from database', 'warning' );
			$this->messages->setMessage( 'Problem deleting user: could not delete the userrights from database', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// userroles
		$sql = $this->database->prepare( "DELETE FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userroles_to_users' ) . " WHERE userroleuser_user = ?" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem deleting user: could not delete the userroles from database', 'warning' );
			$this->messages->setMessage( 'Problem deleting user: could not delete the userroles from database', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// userconfirmation
		$sql = $this->database->prepare( "DELETE FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userconfirmation' ) . " WHERE userconfirmation_user = ?" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem deleting user: could not delete the userconfirmation from database', 'warning' );
			$this->messages->setMessage( 'Problem deleting user: could not delete the userconfirmation from database', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Login a user with username and password
	 * Returns user specific data of the found user or false if no user was found
	 *
	 * @param string $username name of the user
	 * @param string $password supposed password of the user
	 *
	 * @return boolean|array
	 */
	public function login( $username, $password )
	{
		$this->debug->guard( );

		$sql = $this->database->prepare( "SELECT user_id, user_key, user_username FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " WHERE user_username = ? AND user_password = ? AND user_active = '1'" );
		$sql->bindParam( 1, $username );
		$sql->bindParam( 2, md5( $password ) );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem logging in: could not read user information from user table', 'warning' );
			$this->messages->setMessage( 'Problem logging in: could not read user information from user table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// check if there is a user with the given name and password in the users table
		if ( $sql->rowCount( ) > 0 )
		{
			// as we expect a unique name / password combination, we only need to get the first row
			// if multiple users with the same username are allowed you need to change this
			// see also method loadUserdata() in userfunctions.class.php
			$row = $sql->fetch( PDO::FETCH_ASSOC );
			$ret = $row[ 'user_id' ];

			$this->debug->unguard( $ret );
			return $ret;
		}
		else
		{
			$this->debug->write( 'Problem logging in: user not found/is inactive or password is wrong', 'warning' );
			$this->messages->setMessage( 'Problem logging in: user not found/is inactive or password is wrong', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( false );
		return false;
	}


	/**
	 * Changes the password of the user to a given one
	 *
	 * @param integer $userid id of the user
	 * @param string $password the new password of the user
	 *
	 * @return boolean
	 */
	public function changePassword( $userid, $password )
	{
		$this->debug->guard( );

		if ( empty( $password ) )
		{
			$this->debug->write( 'Problem changing the password of a user: no password was given for the user', 'warning' );
			$this->messages->setMessage( 'Problem changing the password of a user: no password was given for the user', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$sql = $this->database->prepare( "UPDATE " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " SET user_password = ? WHERE user_id = ?" );
		$sql->bindParam( 1, md5( $password ) );
		$sql->bindParam( 2, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem changing the password of a user: could not write to database', 'warning' );
			$this->messages->setMessage( 'Problem changing the password of a user: could not write to database', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Changes the username of the user to a given one
	 * Checks if the given username already exists
	 *
	 * @param integer $userid id of the user to change the username
	 * @param string $username username of the user
	 *
	 * @return boolean
	 */
	public function changeUsername( $userid, $username )
	{
		$this->debug->guard( );

		if ( empty( $username ) )
		{
			$this->debug->write( 'Problem changing username of a user: no username was given for the user', 'warning' );
			$this->messages->setMessage( 'Problem changing the username of a user: no username was given for the user', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$sql = $this->database->prepare( "SELECT * FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " WHERE user_username = ?" );
		$sql->bindParam( 1, $username );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem changing username of a user: could not read from users table', 'warning' );
			$this->messages->setMessage( 'Problem changing username of a user: could not read from users table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		if ( $sql->rowCount( ) > 0 )
		{
			$this->debug->write( 'Problem changing username of a user: username already exists', 'warning' );
			$this->messages->setMessage( 'Problem changing username of a user: username already exists', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$sql = $this->database->prepare( "UPDATE " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " SET user_username = ? WHERE user_id = ?" );
		$sql->bindParam( 1, $username );
		$sql->bindParam( 2, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem changing username of a user: could not update database', 'warning' );
			$this->messages->setMessage( 'Problem changing username of a user:could not update database', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Gets user information for a given user
	 * Returns the contents of the usertable if the user is found or false
	 *
	 * @param integer $userid id of the user to get information for
	 *
	 * @return integer
	 */
	public function getInformation( $userid )
	{
		$this->debug->guard( );

		$sql = $this->database->prepare( "SELECT * FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " WHERE user_id = ?" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem getting user information: could not read from users table', 'warning' );
			$this->messages->setMessage( 'Problem getting user information: could not read from users table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		if ( $sql->rowCount( ) > 0 )
		{
			$ret = $sql->fetch( PDO::FETCH_ASSOC );

			$this->debug->unguard( $ret );
			return $ret;
		}
		else
		{
			$this->debug->write( 'Problem getting user information: user not found', 'warning' );
			$this->messages->setMessage( 'Problem getting user information: user not found', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( false );
		return false;
	}


	/**
	 * Gets the confirmation key for a given user
	 * Returns the confirmation key if the user is found or false
	 *
	 * @param integer $userid id of the user to check the confirmation key for
	 *
	 * @return integer
	 */
	public function getConfirmationKey( $userid )
	{
		$this->debug->guard( );

		$sql = $this->database->prepare( "SELECT * FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userconfirmation' ) . " WHERE userconfirmation_user = ?" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem confirming a user: could not read from user confirmation table', 'warning' );
			$this->messages->setMessage( 'Problem confirming a user: could not read from user confirmation table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		if ( $sql->rowCount( ) > 0 )
		{
			$row = $sql->fetch( PDO::FETCH_ASSOC );
			$ret = $row[ 'userconfirmation_key' ];

			$this->debug->unguard( $ret );
			return $ret;
		}
		else
		{
			$this->debug->write( 'Problem confirming a user: key not found for given user', 'warning' );
			$this->messages->setMessage( 'Problem confirming a user: key not found for given user', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( false );
		return false;
	}


	/**
	 * Checks if the confirmation key exists
	 * Returns the user id if the key is found or false if key is invalid
	 *
	 * @param string $confirmationkey key to confirm
	 *
	 * @return integer
	 */
	public function checkConfirmation( $confirmationkey )
	{
		$this->debug->guard( );

		$sql = $this->database->prepare( "SELECT * FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userconfirmation' ) . " WHERE userconfirmation_key = ?" );
		$sql->bindParam( 1, $confirmationkey );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem confirming a user: could not read from user confirmation table', 'warning' );
			$this->messages->setMessage( 'Problem confirming a user: could not read from user confirmation table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		if ( $sql->rowCount( ) > 0 )
		{
			$row = $sql->fetch( PDO::FETCH_ASSOC );
			$ret = $row[ 'userconfirmation_user' ];

			$this->debug->unguard( $ret );
			return $ret;
		}
		else
		{
			$this->debug->write( 'Problem confirming a user: given key not found', 'warning' );
			$this->messages->setMessage( 'Problem confirming a user: given key not found', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( false );
		return false;
	}


	/**
	 * Activates a user
	 *
	 * @param integer $userid id of the user to activate
	 *
	 * @return boolean
	 */
	public function activateUser( $userid )
	{
		$this->debug->guard( );

		// activate the user if it's not already active
		$sql = $this->database->prepare( "UPDATE " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " SET user_active = '1' WHERE user_id = ? AND user_active = '0'" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem activating user: user not found or is already active', 'warning' );
			$this->messages->setMessage( 'Problem activating user: user not found or is already active', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// delete the according user confirmation data as it's no longer
		// needed for an active user
		$sql = $this->database->prepare( "DELETE FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userconfirmation' ) . " WHERE userconfirmation_user = ?" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem activating user: could not delete user confirmation data', 'warning' );
			$this->messages->setMessage( 'Problem activating user: could not delete user confirmation data', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Deactivates a user
	 *
	 * @param integer $userid id of the user to deactivate
	 *
	 * @return boolean
	 */
	public function deactivateUser( $userid )
	{
		$this->debug->guard( );

		// deactivate the user if it's active
		$sql = $this->database->prepare( "UPDATE " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " SET user_active = '0' WHERE user_id = ? AND user_active = '1'" );
		$sql->bindParam( 1, $userid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem deactivating user: user not found or is already inactive', 'warning' );
			$this->messages->setMessage( 'Problem deactivating user: user not found or is already inactive', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// create the according user confirmation data as it's needed for the
		// user to activate again through an opt in
		$sql = $this->database->prepare( "INSERT INTO " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userconfirmation' ) . "(userconfirmation_user, userconfirmation_key) VALUES(?, ?)" );
		$sql->bindParam( 1, $userid );
		$sql->bindParam( 2, md5( uniqid( mt_rand( ), true ) ) );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem deactivating user: could not create user confirmation data', 'warning' );
			$this->messages->setMessage( 'Problem deactivating user: could not create user confirmation data', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( true );
		return true;
	}
}

?>
