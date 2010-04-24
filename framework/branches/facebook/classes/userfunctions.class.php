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

defined( 'ZEITGEIST_ACTIVE' ) or die();

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
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();
		
		$this->userrights = array ();
		$this->userrightsLoaded = false;
		
		$this->userdata = array ();
		$this->userdataLoaded = false;
		
		$this->userroles = array ();
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
	public function createUser($name, $password)
	{
		$this->debug->guard();
		
		if( (empty( $name )) or (empty( $password )) )
		{
			$this->debug->write( 'Problem creating a new user: no name or password was given for the user', 'warning' );
			$this->messages->setMessage( 'Problem creating a new user: no name or password was given for the user', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		$sql = "SELECT * FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " WHERE user_username = '" . $name . "'";
		$res = $this->database->query( $sql );
		if( $this->database->numRows( $res ) > 0 )
		{
			$this->debug->write( 'Problem creating a new user: a user with this name already exists in the database. Please choose another username.', 'warning' );
			$this->messages->setMessage( 'Problem creating a new user: a user with this name already exists in the database. Please choose another username.', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		$active = 1;
		$key = md5( uniqid() );
		if( $this->configuration->getConfiguration( 'zeitgeist', 'userhandler', 'use_doubleoptin' ) == '1' )
		{
			$active = 0;
		}
		
		$sql = "INSERT INTO " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . "(user_username, user_key, user_password, user_active) VALUES('" . $name . "', '" . $key . "', '" . md5( $password ) . "', '" . $active . "')";
		$res = $this->database->query( $sql );
		if( ! $res )
		{
			$this->debug->write( 'Problem creating the user: could not insert the user into the database.', 'error' );
			$this->messages->setMessage( 'Problem creating the user: could not insert the user into the database.', 'error' );
			$this->debug->unguard( false );
			return false;
		}
		
		$currentId = $this->database->insertId();
		
		// insert confirmation key
		$confirmationkey = md5( uniqid() );
		$sqlUser = "INSERT INTO " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userconfirmation' ) . "(userconfirmation_user, userconfirmation_key) VALUES('" . $currentId . "', '" . $confirmationkey . "')";
		$resUser = $this->database->query( $sqlUser );
		
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
	public function deleteUser($userid)
	{
		$this->debug->guard();
		
		// user
		$sql = "DELETE FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " WHERE user_id='" . $userid . "'";
		$res = $this->database->query( $sql );
		
		// userdata
		$sql = "DELETE FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userdata' ) . " WHERE userdata_user='" . $userid . "'";
		$res = $this->database->query( $sql );
		
		// userrights
		$sql = "DELETE FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userrights' ) . " WHERE userright_user='" . $userid . "'";
		$res = $this->database->query( $sql );
		
		// userroles
		$sql = "DELETE FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userroles_to_users' ) . " WHERE userroleuser_user='" . $userid . "'";
		$res = $this->database->query( $sql );
		
		// userconfirmation
		$sql = "DELETE FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userconfirmation' ) . " WHERE userconfirmation_user='" . $userid . "'";
		$res = $this->database->query( $sql );
		
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
	public function login($username, $password)
	{
		$this->debug->guard();
		
		$sql = "SELECT user_id, user_key, user_username FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " WHERE user_username = '" . $username . "' AND user_password = '" . md5( $password ) . "' AND user_active='1'";
		
		$res = $this->database->query( $sql );
		if( $res )
		{
			if( $this->database->numRows( $res ) )
			{
				$row = $this->database->fetchArray( $res );
				$ret = $row ['user_id'];
				
				$this->debug->unguard( $ret );
				return $ret;
			}
			else
			{
				$this->debug->write( 'Problem validating a user: user not found/is inactive or password is wrong', 'warning' );
				$this->messages->setMessage( 'Problem validating a user: user not found/is inactive or password is wrong', 'warning' );
				$this->debug->unguard( false );
				return false;
			}
		}
		else
		{
			$this->debug->write( 'Error searching a user: could not read the user table', 'error' );
			$this->messages->setMessage( 'Error searching a user: could not read the user table', 'error' );
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
	public function changePassword($userid, $password)
	{
		$this->debug->guard();
		
		if( empty( $password ) )
		{
			$this->debug->write( 'Problem changing the password of a user: no password was given for the user', 'warning' );
			$this->messages->setMessage( 'Problem changing the password of a user: no password was given for the user', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		$sql = "UPDATE " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " SET user_password = '" . md5( $password ) . "' WHERE user_id='" . $userid . "'";
		$res = $this->database->query( $sql );
		if( ! $res )
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
	public function changeUsername($userid, $username)
	{
		$this->debug->guard();
		
		if( empty( $username ) )
		{
			$this->debug->write( 'Problem changing the username of a user: no username was given for the user', 'warning' );
			$this->messages->setMessage( 'Problem changing the username of a user: no username was given for the user', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		$sql = "SELECT * FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " WHERE user_username='" . $username . "'";
		$res = $this->database->query( $sql );
		if( $this->database->numRows( $res ) > 0 )
		{
			$this->debug->write( 'Problem changing username of a user: username already exists', 'warning' );
			$this->messages->setMessage( 'Problem changing username of a user: username already exists', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		$sql = "UPDATE " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " SET user_username = '" . $username . "' WHERE user_id='" . $userid . "'";
		$res = $this->database->query( $sql );
		if( ! $res )
		{
			$this->debug->write( 'Problem changing the username of a user: could not update database', 'warning' );
			$this->messages->setMessage( 'Problem changing the username of a user: could not update database', 'warning' );
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
	public function getInformation($userid)
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " WHERE user_id = '" . $userid . "'";
		$res = $this->database->query( $sql );
		if( $res )
		{
			if( $this->database->numRows( $res ) )
			{
				$ret = array ();
				$row = $this->database->fetchArray( $res );
				$ret = $row;
				
				$this->debug->unguard( $ret );
				return $ret;
			}
			else
			{
				$this->debug->write( 'Problem getting user information: id not found for given user', 'warning' );
				$this->messages->setMessage( 'Problem getting user information: id not found for given user', 'warning' );
				$this->debug->unguard( false );
				return false;
			}
		}
		else
		{
			$this->debug->write( 'Error searching a user: could not read the user table', 'error' );
			$this->messages->setMessage( 'Error searching a user: could not read the user table', 'error' );
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
	public function getConfirmationKey($userid)
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userconfirmation' ) . " WHERE userconfirmation_user = '" . $userid . "'";
		$res = $this->database->query( $sql );
		if( $res )
		{
			if( $this->database->numRows( $res ) )
			{
				$row = $this->database->fetchArray( $res );
				$this->debug->unguard( $row ['userconfirmation_key'] );
				return $row ['userconfirmation_key'];
			}
			else
			{
				$this->debug->write( 'Problem confirming a user: key not found for given user', 'warning' );
				$this->messages->setMessage( 'Problem confirming a user: key not found for given user', 'warning' );
				$this->debug->unguard( false );
				return false;
			}
		}
		else
		{
			$this->debug->write( 'Error searching a user: could not read the user table', 'error' );
			$this->messages->setMessage( 'Error searching a user: could not read the user table', 'error' );
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
	public function checkConfirmation($confirmationkey)
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userconfirmation' ) . " WHERE userconfirmation_key = '" . $confirmationkey . "'";
		$res = $this->database->query( $sql );
		if( $res )
		{
			if( $this->database->numRows( $res ) )
			{
				$row = $this->database->fetchArray( $res );
				$this->debug->unguard( $row ['userconfirmation_user'] );
				return $row ['userconfirmation_user'];
			}
			else
			{
				$this->debug->write( 'Problem confirming a user: key not found for given user', 'warning' );
				$this->messages->setMessage( 'Problem confirming a user: key not found for given user', 'warning' );
				$this->debug->unguard( false );
				return false;
			}
		}
		else
		{
			$this->debug->write( 'Error searching a user: could not read the user table', 'error' );
			$this->messages->setMessage( 'Error searching a user: could not read the user table', 'error' );
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
	public function activateUser($userid)
	{
		$this->debug->guard();
		
		// activate the user if it's not already active
		$sql = "UPDATE " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " SET user_active='1' WHERE user_id='" . $userid . "' AND user_active='0'";
		if( ! $res = $this->database->query( $sql ) )
		{
			$this->debug->write( 'Problem activating user: user not found or is already active', 'warning' );
			$this->messages->setMessage( 'Problem activating user: user not found or is already active', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		// delete the according user confirmation data as it's no longer 
		// needed for an active user
		$sql = "DELETE FROM " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userconfirmation' ) . " WHERE userconfirmation_user='" . $userid . "'";
		if( ! $res = $this->database->query( $sql ) )
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
	public function deactivateUser($userid)
	{
		$this->debug->guard();
		
		// deactivate the user if it's active
		$sql = "UPDATE " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " SET user_active='0' WHERE user_id='" . $userid . "' AND user_active='1'";
		if( ! $res = $this->database->query( $sql ) )
		{
			$this->debug->write( 'Problem activating user: user not found or is already active', 'warning' );
			$this->messages->setMessage( 'Problem activating user: user not found or is already active', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		// create the according user confirmation data as it's needed for the
		// user to activate again through an opt in
		$confirmationkey = md5( uniqid() );
		$sql = "INSERT INTO " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_userconfirmation' ) . "(userconfirmation_user, userconfirmation_key) VALUES('" . $userid . "', '" . $confirmationkey . "')";
		if( ! $res = $this->database->query( $sql ) )
		{
			$this->debug->write( 'Problem activating user: could not delete user confirmation data', 'warning' );
			$this->messages->setMessage( 'Problem activating user: could not delete user confirmation data', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		$this->debug->unguard( true );
		return true;
	}

}
?>
