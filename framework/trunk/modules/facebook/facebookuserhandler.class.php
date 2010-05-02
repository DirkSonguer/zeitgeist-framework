<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Facebook Userhandler class
 * 
 * WORK IN PROGRESS!!
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST FACEBOOK
 */

defined( 'ZEITGEIST_ACTIVE' ) or die();

require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/facebook/facebook-platform/facebook.php');

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgFacebookUserhandler::init();
 * Extends the core zgUserhandler
 */
class zgFacebookUserhandler extends zgUserhandler
{
	private static $instance = false;
	
	protected $debug;
	protected $messages;
	protected $session;
	protected $database;
	protected $configuration;

	public $facebook;
	
	protected $loggedIn;


	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	protected function __construct()
	{
		$this->facebook = NULL;
		
		parent::__construct();
		
		if( file_exists( ZEITGEIST_ROOTDIRECTORY . 'configuration/zgfacebook.ini' ) )
		{
			$this->configuration->loadConfiguration( 'facebook', ZEITGEIST_ROOTDIRECTORY . 'configuration/zgfacebook.ini' );
		}
		
		if( file_exists( APPLICATION_ROOTDIRECTORY . 'configuration/zgfacebook.ini' ) )
		{
			$this->configuration->loadConfiguration( 'facebook', APPLICATION_ROOTDIRECTORY . 'configuration/zgfacebook.ini', true );
		}
		
		$this->facebook = new Facebook( $this->configuration->getConfiguration( 'facebook', 'api', 'api_key' ), $this->configuration->getConfiguration( 'facebook', 'api', 'secret_key' ) );
	}


	/**
	 * Initialize the singleton
	 *
	 * @return zgFacebookUserhandler
	 */
	public static function init()
	{
		if( self::$instance === false )
		{
			self::$instance = new zgFacebookUserhandler();
		}
		
		return self::$instance;
	}


	/**
	 * Tries to establish a login for a user from the session data
	 * Only works if the user was correctly logged in while the current session is active
	 *
	 * @return boolean
	 */
	public function establishUserSession()
	{
		$this->debug->guard();
		
		// check if the session handling works
		if( ! $this->session->getSessionId() )
		{
			$this->debug->write( 'Could not establish user session: could not find a session id', 'warning' );
			$this->messages->setMessage( 'Could not establish user session: could not find a session id', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		// check if the user is logged into facebook
		$fbid = $this->facebook->get_loggedin_user();
		if( empty( $fbid ) )
		{
			$this->debug->write( 'Could not establish user session: facebook session not initialized', 'warning' );
			$this->messages->setMessage( 'Could not establish user session: facebook session not initialized', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		// check if the users facebook id can be linked to a user account
		if( ! $this->linkFacebookUser( $fbid ) )
		{
			$this->debug->write( 'Could not establish user session: facebook user can not be linked to a local user', 'warning' );
			$this->messages->setMessage( 'Could not establish user session: facebook user can not be linked to a local user', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		$this->loggedIn = true;
		
		$this->debug->unguard( $this->loggedIn );
		return $this->loggedIn;
	}


	/**
	 * Checks if a facebook user is already known as a local user
	 * If not, the user will be created and the account linked to the facebook id
	 *
	 * @param integer $fbid facebook id of the user
	 *
	 * @return boolean
	 */
	public function linkFacebookUser($fbid)
	{
		$this->debug->guard();
		
		// check if the user has its facebook data already in the session
		$fbSessionId = $this->session->getSessionVariable( 'user_facebookid' );
		if( ! empty( $fbSessionId ) )
		{
			$this->debug->unguard( true );
			return true;
		}
		
		// check if the facebook user is already known in the system
		// if so, fill the session vars and create the user
		$userInformation = $this->_getUserInformationFromFacebookId( $fbid );
		if( is_array( $userInformation ) )
		{
			$this->session->setSessionVariable( 'user_facebookid', $fbid );
			$this->session->setSessionVariable( 'user_id', $userInformation ['user_id'] );
			$this->session->setSessionVariable( 'user_key', $userInformation ['user_key'] );
			$this->session->setSessionVariable( 'user_username', $userInformation ['user_username'] );
			
			$this->debug->unguard( true );
			return true;
		}
		
		// seems like the user is not known to the system
		// first create a new user based on the facebook data
		$userid = $this->createUser( $fbid );
		if( ! $userid )
		{
			$this->debug->write( 'Problem linking the user: could not create the user for the facebook account', 'error' );
			$this->messages->setMessage( 'Problem creating the user: could not create the user for the facebook account', 'error' );
			$this->debug->unguard( false );
			return false;
		}
		
		// now the user should exist in the system
		// if so, fill the session vars and create the user
		$userInformation = $this->_getUserInformationFromFacebookId( $fbid );
		if( is_array( $userInformation ) )
		{
			$this->session->setSessionVariable( 'user_facebookid', $fbid );
			$this->session->setSessionVariable( 'user_id', $userInformation ['user_id'] );
			$this->session->setSessionVariable( 'user_key', $userInformation ['user_key'] );
			$this->session->setSessionVariable( 'user_username', $userInformation ['user_username'] );
			
			$this->debug->unguard( true );
			return true;
		}
		
		$this->debug->unguard( false );
		return false;
	}


	/**
	 * Login a user with username and password
	 * If successfull it will gather the user specific data and tie it to the session
	 *
	 * @return boolean
	 */
	public function login()
	{
		$this->debug->guard();
		
		// check if the user is already nown and logged in
		if( $this->loggedIn )
		{
			$this->debug->write( 'Error logging in a user: user is already logged in. Cannot login user twice', 'error' );
			$this->messages->setMessage( 'Error logging in a user: user is already logged in. Cannot login user twice', 'error' );
			$this->debug->unguard( false );
			return false;
		}
		
		// call the facebook login and auth method
		$fbid = $this->facebook->require_login();
		if( empty( $fbid ) )
		{
			$this->debug->write( 'Error logging in a user: facebook session not initialized', 'warning' );
			$this->messages->setMessage( 'Error logging in a user: facebook session not initialized', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		// link the facebook user session to a local system session
		$this->debug->write( 'linking facebook user', 'message' );
		$linkstatus = $this->linkFacebookUser( $fbid );
		$this->loggedIn = $linkstatus;
		
		$this->debug->unguard( $linkstatus );
		return $linkstatus;
	}


	/**
	 * Log out the user if he is currently logged in
	 *
	 * @return boolean
	 */
	public function logout()
	{
		$this->debug->guard();
		
		if( $this->loggedIn )
		{
			$this->facebook->expire_session();
			$this->session->unsetAllSessionVariables();
		}
		else
		{
			$this->debug->write( 'Problem logging out user: user is not logged in', 'warning' );
			$this->messages->setMessage( 'Problem logging out user: user is not logged in', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		$this->session->stopSession();
		$this->loggedIn = false;
		
		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Create a user from its facebook profile
	 *
	 * @param integer $fbid facebook id to create user from
	 *
	 * @return boolean
	 */
	public function createUser($fbid)
	{
		$this->debug->guard();
		
		// see if user already exists in database
		$sql = "SELECT * FROM " . $this->configuration->getConfiguration( 'facebook', 'tables', 'table_facebookusers' ) . " WHERE facebookuser_fbid = '" . $fbid . "'";
		$res = $this->database->query( $sql );
		if( $this->database->numRows( $res ) > 0 )
		{
			$this->debug->write( 'A user with this facebook id already exists in the database', 'warning' );
			$this->messages->setMessage( 'A user with this facebook id already exists in the database', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		// call the facebook login and auth method
		$fbid = $this->facebook->require_login();
		if( empty( $fbid ) )
		{
			$this->debug->write( 'Could not create facebook user: facebook session not initialized', 'warning' );
			$this->messages->setMessage( 'Could not create facebook user: facebook session not initialized', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		// get userdata from facebook
		$fbuserdata = $this->facebook->api_client->users_getInfo( $fbid, 'first_name, last_name' );
		if( ! is_array( $fbuserdata ) )
		{
			$this->debug->write( 'Could not create facebook user: could not get user data for user', 'warning' );
			$this->messages->setMessage( 'Could not create facebook user: could not get user data for user', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		// insert a new user into database
		$active = 1;
		$key = md5( uniqid() );
		$fbusername = $fbuserdata [0] ['first_name'] . ' ' . $fbuserdata [0] ['last_name'];
		
		$sql = "INSERT INTO " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . "(user_username, user_key, user_password, user_active) VALUES('" . $fbusername . "', '" . $key . "', '" . '' . "', '" . $active . "')";
		$res = $this->database->query( $sql );
		if( ! $res )
		{
			$this->debug->write( 'Problem creating the user: could not insert the user into the database', 'error' );
			$this->messages->setMessage( 'Problem creating the user: could not insert the user into the database', 'error' );
			$this->debug->unguard( false );
			return false;
		}
		
		$userid = $this->database->insertId();
		
		// insert facebook user to link table
		$sql = "INSERT INTO " . $this->configuration->getConfiguration( 'facebook', 'tables', 'table_facebookusers' ) . "(facebookuser_fbid, facebookuser_user) ";
		$sql .= "VALUES('" . $fbid . "', '" . $userid . "')";
		$res = $this->database->query( $sql );
		if( ! $res )
		{
			$this->debug->write( 'Problem creating the user: could not connect the user data to the facebook data', 'error' );
			$this->messages->setMessage( 'Problem creating the user: could not connect the user data to the facebook data', 'error' );
			$this->debug->unguard( false );
			return false;
		}
		
		$this->debug->unguard( $userid );
		return $userid;
	}


	/**
	 * Gets a user id from a facebook id
	 *
	 * @param integer $fbid facebook id of the user
	 *
	 * @return integer
	 */
	private function _getUserInformationFromFacebookId($fbid)
	{
		$this->debug->guard();
		
		$sql = "SELECT u.user_id, u.user_key, u.user_username FROM " . $this->configuration->getConfiguration( 'facebook', 'tables', 'table_facebookusers' ) . " fb ";
		$sql .= "LEFT JOIN " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " u ON fb.facebookuser_user = u.user_id ";
		$sql .= "WHERE fb.facebookuser_fbid = '" . $fbid . "' AND u.user_active='1'";
		if( ! $res = $this->database->query( $sql ) )
		{
			$this->debug->write( 'Error searching a user: could not read the user table', 'error' );
			$this->messages->setMessage( 'Error searching a user: could not read the user table', 'error' );
			$this->debug->unguard( false );
			return false;
		}
		
		if( $this->database->numRows( $res ) != 1 )
		{
			$this->debug->write( 'Problem logging in the user: no linked user exists for this facebook id', 'warning' );
			$this->messages->setMessage( 'Problem logging in the user: no linked user exists for this facebook id', 'warning' );
			$this->debug->unguard( false );
			return false;
		}
		
		$row = $this->database->fetchArray( $res );
		
		$this->debug->unguard( $row );
		return $row;
	}

}
?>
