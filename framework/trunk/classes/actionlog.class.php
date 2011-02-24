<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Actionlog class
 *
 * The actionlog is able to log every call to a Zeitgeist project including
 * all parameters
 * Handle with care as this is pretty verbose and performance heavy
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST ACTIONLOG
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

class zgActionlog
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
		$this->database->query( "SET NAMES 'utf8'" );
		$this->database->query( "SET CHARACTER SET utf8" );
	}


	/**
	 * Function that logs the given action information
	 *
	 * @param int $module id of the module
	 * @param int $action id of the action
	 * @param array $parameters array with parameters of the call
	 */
	public function logAction( $module, $action, $parameters = array( ) )
	{
		$this->debug->guard( );

		$modulesTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_actionlog' );

		// begin transaction as we have multiple inserts depending on each other
		if ( !$this->database->beginTransaction( ) )
		{
			$this->debug->write( 'Problem logging the action: could no begin database transaction', 'warning' );
			$this->messages->setMessage( 'Problem logging the action: could no begin database transaction', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		// insert the main call into the database
		// this will only log the module and action, not the parameters
		$sql = $this->database->prepare( "INSERT INTO " . $modulesTablename . "(actionlog_module, actionlog_action, actionlog_ip) VALUES(?, ?, INET_ATON('" . getenv( 'REMOTE_ADDR' ) . "'))" );
		$sql->bindParam( 1, $module );
		$sql->bindParam( 2, $action );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem logging the action: could not write to log table', 'warning' );
			$this->messages->setMessage( 'Problem logging the action: could not write to log table', 'warning' );

			$this->database->rollBack( );
			$this->debug->unguard( false );
			return false;
		}

		// retreive the insert id of the last insert
		// this id represents the current request call
		// the parameters will be bound to this id and thus to the call
		$logId = $this->database->lastInsertId( );

		// traverse through all parameters and save them to the parameterlog database
		if ( count( $parameters ) > 0 )
		{
			$sql = $this->database->prepare( "INSERT INTO actionlog_parameters(actionparameter_trafficid, actionparameter_key, actionparameter_value) VALUES(?, ?, ?)" );
			foreach ( $parameters as $key => $value )
			{
				$sql->bindParam( 1, $logId );
				$sql->bindParam( 2, $key );
				$sql->bindParam( 3, $value );

				if ( !$sql->execute( ) )
				{
					$this->debug->write( 'Problem logging the action: could not write parameter to log table', 'warning' );
					$this->messages->setMessage( 'Problem logging the action: could not write parameter to log table', 'warning' );

					$this->database->rollBack( );
					$this->debug->unguard( false );
					return false;
				}
			}
		}

		// commit inserts into database
		$this->database->commit( );

		$this->debug->unguard( true );
		return true;
	}
}

?>
