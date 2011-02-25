<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Twitter Userhandler class
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

require_once ( ZEITGEIST_ROOTDIRECTORY . 'modules/twitter/twitteroauth/twitteroauth.php' );

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgTwitterUserhandler::init();
 * Extends the core zgUserhandler
 */
class zgTwitterUserhandler extends zgUserhandler
{
	private static $instance = false;
	protected $debug;
	protected $messages;
	protected $session;
	protected $database;
	protected $configuration;
	protected $loggedIn;

	public $twitteroauth;


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

		$this->twitteroauth = NULL;

		$this->database = new zgDatabasePDO( "mysql:host=" . ZG_DB_DBSERVER . ";dbname=" . ZG_DB_DATABASE, ZG_DB_USERNAME, ZG_DB_USERPASS );
		$this->database->query( "SET NAMES 'utf8'" );
		$this->database->query( "SET CHARACTER SET utf8" );

		parent::__construct( );

		if ( file_exists( ZEITGEIST_ROOTDIRECTORY . 'configuration/zgtwitter.ini' ) )
		{
			$this->configuration->loadConfiguration( 'twitter', ZEITGEIST_ROOTDIRECTORY . 'configuration/zgtwitter.ini' );
		}

		if ( file_exists( APPLICATION_ROOTDIRECTORY . 'configuration/zgtwitter.ini' ) )
		{
			$this->configuration->loadConfiguration( 'twitter', APPLICATION_ROOTDIRECTORY . 'configuration/zgtwitter.ini', true );
		}
	}


	/**
	 * Initialize the singleton
	 *
	 * @return zgTwitterUserhandler
	 */
	public static function init( )
	{
		if ( self::$instance === false )
		{
			self::$instance = new zgTwitterUserhandler( );
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

		// check if the user has a valid twitter token
		$twitterAccessToken = $this->session->getSessionVariable( 'user_access_token' );
		if ( ( empty( $twitterAccessToken ) ) || ( empty( $twitterAccessToken[ 'oauth_token' ] ) ) || ( empty( $twitterAccessToken[ 'oauth_token_secret' ] ) ) )
		{
			$this->debug->write( 'Could not establish user session: twitter token not initialized', 'warning' );
			$this->messages->setMessage( 'Could not establish user session: twitter token not initialized', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// bind twitter class to current twitter app and user oauth token
		$this->twitteroauth = new TwitterOAuth( $this->configuration->getConfiguration( 'twitter', 'api', 'consumer_key' ), $this->configuration->getConfiguration( 'twitter', 'api', 'consumer_secret' ), $twitterAccessToken[ 'oauth_token' ], $twitterAccessToken[ 'oauth_token_secret' ] );
		if ( !empty( $this->twitteroauth->id ) )
		{
			$this->debug->write( 'Could not establish user session: twitter class does not accept token or API key', 'warning' );
			$this->messages->setMessage( 'Could not establish user session: twitter class does not accept token or API key', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// check if the users twitter id of the user can be linked to an existing user account
		// this will only work if the twitter login is validated in a previous request
		if ( !$this->validateTwitterUser( ) )
		{
			$this->debug->write( 'Problem validating a user login: twitter user can not be linked to a local user', 'warning' );
			$this->messages->setMessage( 'Problem validating a user login: twitter user can not be linked to a local user', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->loggedIn = true;

		$this->debug->unguard( $this->loggedIn );
		return $this->loggedIn;
	}


	/**
	 * Validates a user
	 * This has to be called as the callback from twitter
	 * This also checks if the user is already known in the system
	 * If not it calls the necessary methods to create a new user and bind
	 * the twitter user to it
	 *
	 * @return boolean
	 */
	public function validateLogin( )
	{
		$this->debug->guard( );

		// check if the user is already known and logged in
		if ( $this->loggedIn )
		{
			$this->debug->write( 'Problem validating a user login: user is already logged in', 'warning' );
			$this->messages->setMessage( 'Problem validating a user login: user is already logged in', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// check if the login process has already started
		if ( !$this->session->getSessionVariable( 'twitter_oauth_initiated' ) )
		{
			$this->debug->write( 'Problem validating a user login: no login process in progress', 'warning' );
			$this->messages->setMessage( 'Problem validating a user login: no login process in progress', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// get tokens from the session
		$oauth_token = $this->session->getSessionVariable( 'oauth_token' );
		$oauth_token_secret = $this->session->getSessionVariable( 'oauth_token_secret' );

		// check for the answer by twitter
		if ( empty( $_REQUEST[ 'oauth_verifier' ] ) )
		{
			$this->session->unsetSessionVariable( 'twitter_oauth_initiated' );
			$this->debug->write( 'Problem validating a user login: oauth verifier not found in request', 'warning' );
			$this->messages->setMessage( 'Problem validating a user login: oauth verifier not found in request', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// requests access tokens from twitter and checks them against the stored ones
		$this->twitteroauth = new TwitterOAuth( $this->configuration->getConfiguration( 'twitter', 'api', 'consumer_key' ), $this->configuration->getConfiguration( 'twitter', 'api', 'consumer_secret' ), $oauth_token, $oauth_token_secret );
		$access_token = $this->twitteroauth->getAccessToken( $_REQUEST[ 'oauth_verifier' ] );
		if ( !$access_token )
		{
			$this->session->unsetSessionVariable( 'twitter_oauth_initiated' );
			$this->debug->write( 'Problem validating a user login: could not get twitter token', 'warning' );
			$this->messages->setMessage( 'Problem validating a user login: could not get twitter token', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// save the access tokens in the session
		$this->session->setSessionVariable( 'user_access_token', $access_token );

		// the auth tokens are not needed anymore
		$this->session->unsetSessionVariable( 'oauth_token' );
		$this->session->unsetSessionVariable( 'oauth_token_secret' );

		// check if the oauth call was successful and twitter accepted the connection
		if ( 200 == $this->twitteroauth->http_code )
		{
			// user has been verified and the access tokens are stored in the session
			$this->session->unsetSessionVariable( 'twitter_oauth_initiated' );

			$this->debug->write( 'Oauth connection call ok and verified', 'warning' );
			$this->messages->setMessage( 'Oauth connection call ok and verified', 'warning' );
		}
		else
		{
			// user could not be verified
			$this->session->unsetSessionVariable( 'twitter_oauth_initiated' );
			$this->debug->write( 'Problem validating a user login: user could not be verified', 'warning' );
			$this->messages->setMessage( 'Problem validating a user login: user could not be verified', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Checks if a loged in twitter user is already known as a local user
	 * If not, the user will be created and the account linked to the twitter id
	 *
	 * @return boolean
	 */
	public function validateTwitterUser( )
	{
		$this->debug->guard( );

		// check if the user has its twitter data already in the session
		if ( $this->session->getSessionVariable( 'user_twitterid' ) )
		{
			// the user had his session activated at least once
			// and this user must be known to the system
			$this->debug->unguard( true );
			return true;
		}

		// check if the twitteroauth class has been initialized correctly
		if ( empty( $this->twitteroauth ) )
		{
			$this->debug->write( 'Problem validating the twitter user: twitter oauth object not initialized', 'warning' );
			$this->messages->setMessage( 'Problem validating the twitter user: twitter oauth object not initialized', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// check if the connection is still active or if twitter has closed the session
		$twitteruserdata = $this->twitteroauth->get( 'account/verify_credentials' );
		if ( empty( $twitteruserdata->id ) )
		{
			$this->debug->write( 'Problem validating the twitter user: twitter id could not be found in object', 'warning' );
			$this->messages->setMessage( 'Problem validating the twitter user: twitter id could not be found in object', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// check if user already exists in the database
		$userInformation = $this->_getUserInformationFromTwitterId( $twitteruserdata->id );
		if ( is_array( $userInformation ) )
		{
			$this->session->setSessionVariable( 'user_facebookid', $twitteruserdata->id );
			$this->session->setSessionVariable( 'user_id', $userInformation [ 'user_id' ] );
			$this->session->setSessionVariable( 'user_key', $userInformation [ 'user_key' ] );
			$this->session->setSessionVariable( 'user_username', $userInformation [ 'user_username' ] );

			$this->debug->unguard( true );
			return true;
		}

		// seems like the user is not known to the system
		// first create a new user based on the twitter data
		$userid = $this->createUser( $twitteruserdata );
		if ( !$userInformation )
		{
			$this->debug->write( 'Problem validating the twitter user: could not create the user for the facebook account', 'warning' );
			$this->messages->setMessage( 'Problem validating the twitter user: could not create the user for the facebook account', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// now the user should exist in the system
		// if so, fill the session vars and create the user
		$userInformation = $this->_getUserInformationFromTwitterId( $twitteruserdata->id );
		if ( is_array( $userInformation ) )
		{
			$this->session->setSessionVariable( 'user_twitterid', $twitteruserdata->id );
			$this->session->setSessionVariable( 'user_id', $userInformation[ 'user_id' ] );
			$this->session->setSessionVariable( 'user_key', $userInformation[ 'user_key' ] );
			$this->session->setSessionVariable( 'user_username', $userInformation[ 'user_username' ] );

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
			$this->debug->write( 'Problem logging in a user: user is already logged in . Cannot login user twice', 'warning' );
			$this->messages->setMessage( 'Problem logging in a user: user is already logged in . Cannot login user twice', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// check if the user already has started a login process
		if ( $this->session->getSessionVariable( 'twitter_oauth_initiated' ) )
		{
			$this->debug->write( 'Problem logging in a user: user has already started a login process', 'warning' );
			$this->messages->setMessage( 'Problem logging in a user: user has already started a login process', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// bind twitter class to current twitter app and user oauth token
		// this uses the application key and secret
		$connection = new TwitterOAuth( $this->configuration->getConfiguration( 'twitter', 'api', 'consumer_key' ), $this->configuration->getConfiguration( 'twitter', 'api', 'consumer_secret' ) );

		// get temporary credentials from twitter
		// will be redirected to the callback url afterwards
		$request_token = $connection->getRequestToken( $this->configuration->getConfiguration( 'twitter', 'api', 'oauth_callback' ) );

		// store temporary credentials to the session
		$this->session->setSessionVariable( 'oauth_token', $request_token[ 'oauth_token' ] );
		$this->session->setSessionVariable( 'oauth_token_secret', $request_token[ 'oauth_token_secret' ] );

		// check what happened to the auth request preparation call
		switch ( $connection->http_code )
		{
			case 200:
				// store into session that we attempt to authorize a user
				$this->session->setSessionVariable( 'twitter_oauth_initiated', true );

				// get the authorize url with the request token we negotiated earlier
				$url = $connection->getAuthorizeURL( $request_token[ 'oauth_token' ] );
				$tpl = new zgTemplate( );
				$tpl->redirect( $url );
				break;
			default:
				$this->debug->write( 'Problem logging in a user: could not connect to twitter', 'warning' );
				$this->messages->setMessage( 'Problem logging in a user: could not connect to twitter', 'warning' );

				$this->debug->unguard( false );
				return false;
		}

		$this->debug->unguard( false );
		return false;
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
	 * Create a user from its twitter profile
	 *
	 * @param object $twitteruserdata twitter user object
	 *
	 * @return boolean
	 */
	public function createUser( $twitteruserdata )
	{
		$this->debug->guard( );

		// check if twitter data is of the right type
		if ( empty( $twitteruserdata->id ) )
		{
			$this->debug->write( 'Problem creating the user: given object is not a twitter userdata object', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: given object is not a twitter userdata object', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// begin transaction as we have multiple inserts depending on each other
		if ( !$this->database->beginTransaction( ) )
		{
			$this->debug->write( 'Problem creating the user: could no begin database transaction', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: could no begin database transaction', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// see if user already exists in database
		$sql = $this->database->prepare( "SELECT * FROM " . $this->configuration->getConfiguration( 'twitter', 'tables', 'table_twitterusers' ) . " WHERE twitteruser_twitterid = ?" );
		$sql->bindParam( 1, $twitteruserdata->id );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem creating the user: could not access the user table', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: could not access the user table', 'warning' );

			$this->database->rollBack( );
			$this->debug->unguard( false );
			return false;
		}

		if ( $sql->rowCount( ) > 0 )
		{
			$this->debug->write( 'Problem creating the user: user with this twitter id already exists in the database', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: user with this twitter id already exists in the database', 'warning' );

			$this->database->rollBack( );
			$this->debug->unguard( false );
			return false;
		}

		// insert a new user into database
		$active = 1;
		$key = md5( uniqid( md5( mt_rand( ) ), true ) );

		$sql = $this->database->prepare( "INSERT INTO " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . "(user_username, user_key, user_password, user_active) VALUES(?, ?, ?, ?)" );
		$sql->bindParam( 1, $twitteruserdata->screen_name );
		$sql->bindParam( 2, $key );
		$sql->bindParam( 3, $key );
		$sql->bindParam( 4, $active );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem creating the user: could not insert the user into the database', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: could not insert the user into the database', 'warning' );

			$this->database->rollBack( );
			$this->debug->unguard( false );
			return false;
		}

		// this is the id for the user that just has been created
		$currentId = $this->database->lastInsertId( );

		$sql = $this->database->prepare( "INSERT INTO " . $this->configuration->getConfiguration( 'twitter', 'tables', 'table_twitterusers' ) . "(twitteruser_twitterid, twitteruser_user) VALUES(?, ?)" );
		$sql->bindParam( 1, $twitteruserdata->id );
		$sql->bindParam( 2, $currentId );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem creating the user: could not insert the twitter to user mapping into the database', 'warning' );
			$this->messages->setMessage( 'Problem creating the user: could not insert the twitter to user mapping into the database', 'warning' );

			$this->database->rollBack( );
			$this->debug->unguard( false );
			return false;
		}

		// commit inserts into database
		$this->database->commit( );

		$this->debug->unguard( $currentId );
		return $currentId;
	}


	/**
	 * Gets a users id and information from a twitter id
	 * Returns an array with the information
	 *
	 * @param integer $twitterid twitter id of the user
	 *
	 * @return array
	 */
	private function _getUserInformationFromTwitterId( $twitterid )
	{
		$this->debug->guard( );

		// get userinformation from database
		$sqlquery = "SELECT u . user_id, u . user_key, u . user_username FROM " . $this->configuration->getConfiguration( 'twitter', 'tables', 'table_twitterusers' ) . " tw ";
		$sqlquery .= "LEFT JOIN " . $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_users' ) . " u ON tw . twitteruser_user = u . user_id ";
		$sqlquery .= "WHERE tw . twitteruser_twitterid = ? AND u . user_active = '1'";
		$sql = $this->database->prepare( $sqlquery );
		$sql->bindParam( 1, $twitterid );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem getting twitter user information: could not read the user table', 'warning' );
			$this->messages->setMessage( 'Problem getting twitter user information: could not read the user table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		if ( $sql->rowCount( ) != 1 )
		{
			$this->debug->write( 'Problem getting twitter user information: no linked user exists for this twitter id{', 'warning' );
			$this->messages->setMessage( 'Problem getting twitter user information: no linked user exists for this twitter id{', 'warning' );

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
	public function getTwitterUserID( )
	{
		$this->debug->guard( );

		// check if the user is already known and logged in
		if ( !$this->loggedIn )
		{
			$this->debug->write( 'Problem getting the twitter id: user is not logged in', 'warning' );
			$this->messages->setMessage( 'Problem getting the twitter id: user is not logged in', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$twitterid = $this->session->getSessionVariable( 'user_twitterid' );

		$this->debug->unguard( $twitterid );
		return $twitterid;
	}
}

?>
