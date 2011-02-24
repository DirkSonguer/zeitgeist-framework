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

defined( 'ZEITGEIST_ACTIVE' ) or die( );

require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/facebook/facebook-platform/facebook.php' );

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
	protected function __construct( )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );
		$this->configuration = zgConfiguration::init( );

		$this->session = zgSession::init( );
		$this->session->startSession( );

		$this->facebook = NULL;

		$this->database = new zgDatabasePDO( "mysql:host=" . ZG_DB_DBSERVER . ";dbname=" . ZG_DB_DATABASE, ZG_DB_USERNAME, ZG_DB_USERPASS );
		$this->database->query( "SET NAMES 'utf8'" );
		$this->database->query( "SET CHARACTER SET utf8" );

		parent::__construct( );

		if ( file_exists( ZEITGEIST_ROOTDIRECTORY . 'configuration/zgfacebook.ini' ) )
		{
			$this->configuration->loadConfiguration( 'facebook', ZEITGEIST_ROOTDIRECTORY . 'configuration/zgfacebook.ini' );
		}

		if ( file_exists( APPLICATION_ROOTDIRECTORY . 'configuration/zgfacebook.ini' ) )
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
	public static function init( )
	{
		if ( self::$instance === false )
		{
			self::$instance = new zgFacebookUserhandler( );
		}

		return self::$instance;
	}


	/**
	 * Tries to establish a login for a user from the session data
	 * Only works if the user was correctly logged in while the current session is active
	 *
	 * @return boolean
	 */
	public function establishUserSession( )
	{
		$this->debug->guard( );

		// check if the session handling works
		if ( !$this->session->getSessionId( ) )
		{
			$this->debug->write( 'Could not establish user session: could not find a session id', 'warning' );
			$this->messages->setMessage( 'Could not establish user session: could not find a session id', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// check if the user is logged into facebook
		$fbid = $this->facebook->get_loggedin_user( );
		if ( empty( $fbid ) )
		{
			$this->debug->write( 'Could not establish user session: facebook session not initialized', 'warning' );
			$this->messages->setMessage( 'Could not establish user session: facebook session not initialized', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// check if the users facebook id can be linked to a user account
		if ( !$this->validateFacebookUser( $fbid ) )
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
	public function validateFacebookUser( $fbid )
	{
		$this->debug->guard( );

		// check if the user has its facebook data already in the session
		$fbSessionId = $this->session->getSessionVariable( 'user_facebookid' );
		if ( !empty( $fbSessionId ) )
		{
			$this->debug->unguard( true );
			return true;
		}

		// check if the facebook user is already known in the system
		// if so, fill the session vars and create the user
		$userInformation = $this->_getUserInformationFromFacebookId( $fbid );
		if ( is_array( $userInformation ) )
		{
			$this->session->setSessionVariable( 'user_facebookid', $fbid );
			$this->session->setSessionVariable( 'user_id', $userInformation [ 'user_id' ] );
			$this->session->setSessionVariable( 'user_key', $userInformation [ 'user_key' ] );
			$this->session->setSessionVariable( 'user_username', $userInformation [ 'user_username' ] );

			$this->debug->unguard( true );
			return true;
		}

		// seems like the user is not known to the system
		// first create a new user based on the facebook data
		$userid = $this->createUser( $fbid );
		if ( !$userid )
		{
			$this->debug->write( 'Problem validating the user: could not create the user for the facebook account', 'warning' );
			$this->messages->setMessage( 'Problem validating the user: could not create the user for the facebook account', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// now the user should exist in the system
		// if so, fill the session vars and create the user
		$userInformation = $this->_getUserInformationFromFacebookId( $fbid );
		if ( is_array( $userInformation ) )
		{
			$this->session->setSessionVariable( 'user_facebookid', $fbid );
			$this->session->setSessionVariable( 'user_id', $userInformation [ 'user_id' ] );
			$this->session->setSessionVariable( 'user_key', $userInformation [ 'user_key' ] );
			$this->session->setSessionVariable( 'user_username', $userInformation [ 'user_username' ] );

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
	public function login( )
	{
		$this->debug->guard( );

		// check if the user is already known and logged in
		if ( $this->loggedIn )
		{
			$this->debug->write( 'Problem logging in a user: user is already logged in. Cannot login user twice', 'warning' );
			$this->messages->setMessage( 'Problem logging in a user: user is already logged in. Cannot login user twice', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// call the facebook login and auth method
		$fbid = $this->facebook->require_login( );
		if ( empty( $fbid ) )
		{
			$this->debug->write( 'Problem logging in a user: facebook session not initialized', 'warning' );
			$this->messages->setMessage( 'Problem logging in a user: facebook session not initialized', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// link the facebook user session to a local system session
		$this->debug->write( 'linking facebook user', 'message' );
		$linkstatus = $this->validateFacebookUser( $fbid );
		$this->loggedIn = $linkstatus;

		$this->debug->unguard( $linkstatus );
		return $linkstatus;
	}


	/**
	 * Log out the user if he is currently logged in
	 *
	 * @return boolean
	 */
	public function logout( )
	{
		$this->debug->guard( );

		if ( $this->loggedIn )
		{
			$this->facebook->expire_session( );
			$this->session->unsetAllSessionVariables( );
		}
		else
		{
			$this->debug->write( 'Problem logging out user: user is not logged in', 'warning' );
			$this->messages->setMessage( 'Problem logging out user: user is not logged in', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$this->session->stopSession( );
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
	public function createUser( $fbid )
	{
		$this->debug->guard( );

		// begin transaction as we have multiple inserts depending on each other
		if ( !$this->database->beginTransaction( ) )
		{
			$this->debug->write( 'Problem creating the user: could no begin database transaction', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: could no begin database transaction', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// see if user already exists in database
		$sql = $this->database->prepare( "SELECT * FROM " . $this->configuration->getConfiguration( 'facebook', 'tables', 'table_facebookusers' ) . " WHERE facebookuser_fbid = ?" );
		$sql->bindParam( 1, $fbid );

		if ( !$sql->execute( ) )
		{
			$this->database->rollBack( );
			$this->debug->write( 'Problem creating the user: could not access the user table', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: could not access the user table', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		if ( $sql->rowCount( ) > 0 )
		{
			$this->database->rollBack( );
			$this->debug->write( 'Problem creating the user: a user with this facebook id already exists in the database', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: a user with this facebook id already exists in the database', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// call the facebook login and auth method
		$fbid = $this->facebook->require_login( );
		if ( empty( $fbid ) )
		{
			$this->database->rollBack( );
			$this->debug->write( 'Problem creating the user: facebook session not initialized', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: facebook session not initialized', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// get userdata from facebook
		$fbuserdata = $this->facebook->api_client->users_getInfo( $fbid, 'first_name, last_name' );
		if ( !is_array( $fbuserdata ) )
		{
			$this->database->rollBack( );
			$this->debug->write( 'Problem creating the user: could not get user data for user', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: could not get user data for user', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// insert a new user into database
		$active = 1;
		$key = md5( uniqid( md5( mt_rand( ) ), true ) );
		$fbusername = $fbuserdata [ 0 ] [ 'first_name' ] . ' ' . $fbuserdata [ 0 ] [ 'last_name' ];

		$sql = $this->database->prepare( "INSERT INTO " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . "(user_username, user_key, user_password, user_active) VALUES(?, ?, ?, ?)" );
		$sql->bindParam( 1, $fbusername );
		$sql->bindParam( 2, $key );
		$sql->bindParam( 3, $key );
		$sql->bindParam( 4, $active );

		if ( !$sql->execute( ) )
		{
			$this->database->rollBack( );
			$this->debug->write( 'Problem creating the user: could not insert the user into the database', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: could not insert the user into the database', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// this is the id for the user that just has been created
		$currentId = $this->database->lastInsertId( );

		// insert facebook user to link table
		$sql = $this->database->prepare( "INSERT INTO " . $this->configuration->getConfiguration( 'facebook', 'tables', 'table_facebookusers' ) . "(facebookuser_fbid, facebookuser_user) VALUES(?, ?)" );
		$sql->bindParam( 1, $fbid );
		$sql->bindParam( 2, $currentId );

		if ( !$sql->execute( ) )
		{
			$this->database->rollBack( );
			$this->debug->write( 'Problem creating the user: could not connect the user data to the facebook data', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: could not connect the user data to the facebook data', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// commit inserts into database
		$this->database->commit( );
		
		$this->debug->unguard( $currentId );
		return $currentId;
	}


	/**
	 * Gets a user id from a facebook id
	 *
	 * @param integer $fbid facebook id of the user
	 *
	 * @return integer
	 */
	private function _getUserInformationFromFacebookId( $fbid )
	{
		$this->debug->guard( );

		// get userinformation from database
		$sqlquery = "SELECT u.user_id, u.user_key, u.user_username FROM " . $this->configuration->getConfiguration( 'facebook', 'tables', 'table_facebookusers' ) . " fb ";
		$sqlquery .= "LEFT JOIN " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " u ON fb.facebookuser_user = u.user_id ";
		$sqlquery .= "WHERE fb.facebookuser_fbid = ? AND u.user_active='1'";
		$sql = $this->database->prepare( $sqlquery );
		$sql->bindParam( 1, $fbid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem getting facebook user information: could not read the user table', 'warning' );
			$this->messages->setMessage( 'Problem getting facebook user information: could not read the user table', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		if ( $sql->rowCount( )  != 1 )
		{
			$this->debug->write( 'Problem getting facebook user information: no linked user exists for this facebook id', 'warning' );
			$this->messages->setMessage( 'Problem getting facebook user information: no linked user exists for this facebook id', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$ret = $sql->fetch( PDO::FETCH_ASSOC );

		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Gets the current facebook id for a user
	 *
	 * @return integer
	 */
	public function getFacebookUserID( )
	{
		$this->debug->guard( );

		$fbid = $this->facebook->get_loggedin_user( );
		if ( empty( $fbid ) )
		{
			$this->debug->write( 'Could not get facebook user id: facebook session not initialized', 'warning' );
			$this->messages->setMessage( 'Could not get facebook user id: facebook session not initialized', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( $fbid );
		return $fbid;
	}
}

?>
