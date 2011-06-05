<?php

defined( 'TICTACTUTORIAL_ACTIVE' ) or die( );

class user
{

	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;


	public function __construct( )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );
		$this->configuration = zgConfiguration::init( );
		$this->user = zgUserhandler::init( );

		$this->database = new zgDatabase( );
		$this->database->connect( );
	}


	public function index( $parameters = array( ) )
	{
		$this->debug->guard( );

		$this->debug->unguard( true );
		return true;
	}


	public function login( $parameters = array( ) )
	{
		$this->debug->guard( );

		$tpl = new zgTemplate( );

		// check if the user is already logged in
		if ( $this->user->isLoggedIn( ) )
		{
			// the user is already logged in
			// no need to do it again
			// redirect the user to the main page
			$tpl->redirect( $this->configuration->getConfiguration( 'application', 'application', 'basepath' ) . $tpl->createLink( 'main', 'index' ) );
			$this->debug->unguard( true );
			return true;
		}

		// load the user login template
		// this just shows a login form
		$tpl->load( $this->configuration->getConfiguration( 'application', 'application', 'templatepath' ) . '/user_login.tpl.html' );

		// check if the login parameters are present
		// if so, the login form has been sent
		if ( ( !empty( $parameters[ 'username' ] ) ) && ( !empty( $parameters[ 'password' ] ) ) )
		{
			// try to log in the user with the given credentials
			// note that we are using the filtered input parameters
			// hence there is no need to escape them
			$login = $this->user->login( $parameters[ 'username' ], $parameters[ 'password' ] );
			if ( $login )
			{
				// if the login is successful, redirect to the main page
				$tpl->redirect( $this->configuration->getConfiguration( 'application', 'application', 'basepath' ) . $tpl->createLink( 'main', 'index' ) );
				$this->debug->unguard( true );
				return true;
			}

			// otherwise show an error message
			$tpl->insertBlock( "loginError" );
		}

		$tpl->show( );

		$this->debug->unguard( true );
		return true;
	}


	public function logout( $parameters = array( ) )
	{
		$this->debug->guard( );

		// although this action does not use an actual template
		// the class is needed for the redirects
		$tpl = new zgTemplate( );

		// check if the user is logged in
		if ( $this->user->isLoggedIn( ) )
		{
			// if the user is logged in, call the logout method
			// this will invalidate the user session and log him out
			$this->user->logout( );
		}

		// redirect the user to the main page
		$tpl->redirect( $this->configuration->getConfiguration( 'application', 'application', 'basepath' ) . $tpl->createLink( 'main', 'index' ) );
		$this->debug->unguard( true );
		return true;
	}


	public function create( $parameters = array( ) )
	{
		$this->debug->guard( );

		$tpl = new zgTemplate( );

		// check if the user is already logged in
		if ( $this->user->isLoggedIn( ) )
		{
			// the user is already logged in
			// no need to create another user
			// redirect the user to the main page
			$tpl->redirect( $this->configuration->getConfiguration( 'application', 'application', 'basepath' ) . $tpl->createLink( 'main', 'index' ) );
			$this->debug->unguard( true );
			return true;
		}

		$tpl->load( $this->configuration->getConfiguration( 'application', 'application', 'templatepath' ) . '/user_create.tpl.html' );

		// check if the create parameters are present
		// if so, the login form has been sent
		if ( ( !empty( $parameters[ 'username' ] ) ) && ( !empty( $parameters[ 'password' ] ) ) )
		{
			// normally you would do all kinds of sanity checks here
			// like having a password confirmation field or
			// using the user email for a double opt in.
			// for this tutorial we'll keep it simple with just username
			// and password

			// flag used to check if a problem occured while creting the account
			$creationerror = false;

			// all relevant user methods are available in
			// the zgUserfunctions class
			$userfunctions = new zgUserfunctions( );

			// try to create the user with the given credentials
			// note that we are using the filtered input parameters
			// hence there is no need to escape them
			$newUserId = $userfunctions->createUser( $parameters[ 'username' ], $parameters[ 'password' ] );
			if ( !$newUserId )
			{
				$creationerror = true;
			}

			// initially a newly created user account is not active
			// normally the account would be activated by a double
			// opt in process but we don't care about this here.
			// just activate the user account by calling the method
			// directly
			if ( !$userfunctions->activateUser( $newUserId ) )
			{
				$creationerror = true;
			}

			// check if problems occured during the account creation
			// if not redirect to the login page
			if ( !$creationerror )
			{
				$tpl->redirect( $this->configuration->getConfiguration( 'application', 'application', 'basepath' ) . $tpl->createLink( 'user', 'login' ) );
				$this->debug->unguard( true );
				return true;
			}

			// otherwise show an error message
			// normally you would actually check for the error message
			// the userfunctions are using
			$creationMessages = $this->messages->getAllMessages('userfunctions.class.php');
			var_dump($creationMessages);

			$tpl->insertBlock( "creationError" );
		}

		$tpl->show( );

		$this->debug->unguard( true );
		return true;
	}
}

?>
