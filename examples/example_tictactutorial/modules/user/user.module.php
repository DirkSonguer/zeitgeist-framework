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
}

?>
