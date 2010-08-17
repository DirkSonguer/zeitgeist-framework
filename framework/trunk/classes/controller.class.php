<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Controller class
 *
 * This class acts as a simple front controller to call modules and
 * actions
 * It also handles the usual tasks like checking user and security related
 * issues
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST CONTROLLER
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

class zgController
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $actionlog;


	/**
	 * Class constructor
	 */
	public function __construct( )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );
		$this->configuration = zgConfiguration::init( );
		$this->actionlog = new zgActionlog( );

		$this->database = new zgDatabasePDO( "mysql:host=" . ZG_DB_DBSERVER . ";dbname=" . ZG_DB_DATABASE, ZG_DB_USERNAME, ZG_DB_USERPASS );
	}


	/**
	 * Loads all relevant data for a module from the database
	 *
	 * @param string $module name of the module
	 *
	 * @return array
	 */
	protected function _getModuleData( $module )
	{
		$this->debug->guard( );

		$modulesTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_modules' );

		$sql = $this->database->prepare( "SELECT * FROM " . $modulesTablename . " WHERE module_name = ?" );
		$sql->bindParam( 1, $module );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem getting the module data: could not read from module table', 'warning' );
			$this->messages->setMessage( 'Problem getting the module data: could not read from module table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$row = $sql->fetch( PDO::FETCH_ASSOC );

		$this->debug->unguard( $row );
		return $row;
	}


	/**
	 * Loads all relevant data for an action from the database
	 *
	 * @param array $moduledata moduledata as defined by _getModuleData()
	 * @param string $action name of the action
	 *
	 * @return boolean
	 */
	protected function _getActionData( $moduledata, $action )
	{
		$this->debug->guard( );

		$actionsTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_actions' );

		$sql = $this->database->prepare( "SELECT * FROM " . $actionsTablename . " WHERE action_module = ? AND action_name = ?" );
		$sql->bindParam( 1, $moduledata['module_id'] );
		$sql->bindParam( 2, $action );

		if ( !$sql->execute( ) )
		{
			$this->debug->write( 'Problem getting the action data: could not read from action table', 'warning' );
			$this->messages->setMessage( 'Problem getting the action data: could not read from action table', 'warning' );

			$this->debug->unguard( false );
			return false;
		}

		$row = $sql->fetch( PDO::FETCH_ASSOC );

		$this->debug->unguard( $row );
		return $row;
	}


	/**
	 * Executes an action inside a module
	 * Also handles all security related aspects
	 *
	 * @param string $module name of the module to load
	 * @param string $action name of the action to load
	 *
	 * @return boolean
	 */
	public function callEvent( $module, $action )
	{
		$this->debug->guard( );

		// load message data for the current session
		if ( $this->configuration->getConfiguration( 'zeitgeist', 'messages', 'use_persistent_messages' ) )
		{
			$this->messages->loadMessagesFromSession( );
		}

		// check if module is installed and get module data
		if ( !$moduleData = $this->_getModuleData( $module ) )
		{
			$this->debug->write( 'Problem loading the module: Module is not found / installed: ' . $module, 'warning' );
			$this->messages->setMessage( 'Problem loading the module: Module is not found / installed: ' . $module, 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// check from data if module is active
		if ( $moduleData['module_active'] != '1' )
		{
			$this->debug->write( 'Problem loading the module: Module is not active: ' . $module, 'warning' );
			$this->messages->setMessage( 'Problem loading the module: Module is not active: ' . $module, 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// check if the classname is already used
		if ( class_exists( $module, false ) )
		{
			$this->debug->write( 'Problem loading the module (' . $module . '): Class name already used', 'warning' );
			$this->messages->setMessage( 'Problem loading the module: Class name already used', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		//check if zeitgeist can load the module
		if ( !class_exists( $module, true ) )
		{
			$this->debug->write( 'Problem loading the module (' . $module . '): Could not find matching class', 'warning' );
			$this->messages->setMessage( 'Problem loading the module: Could not find matching class', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// load the module class through the autoloader
		$moduleClass = new $module( );

		// check if action is installed and get action data
		if ( !$actionData = $this->_getActionData( $moduleData, $action ) )
		{
			$this->debug->write( 'Problem loading the action (' . $action . ') in module (' . $module . '): Action is not installed for module', 'warning' );
			$this->messages->setMessage( 'Problem loading the action (' . $action . ') in module (' . $module . '): Action is not installed for module', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// check from data if action is active
		if ( $actionData['action_active'] != '1' )
		{
			$this->debug->write( 'Problem loading the action (' . $action . ') in module (' . $module . '): Action is not active', 'warning' );
			$this->messages->setMessage( 'Problem loading the action (' . $action . ') in module (' . $module . '): Action is not active', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// check if action method exists in module
		if ( !method_exists( $moduleClass, $action ) )
		{
			$this->debug->write( 'Problem loading the action (' . $action . ') in module (' . $module . '): Could not find method', 'warning' );
			$this->messages->setMessage( 'Problem loading the action (' . $action . ') in module (' . $module . '): Could not find method', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		// load configuration
		if ( !$this->configuration->loadConfiguration( $module, APPLICATION_ROOTDIRECTORY . 'modules/' . $module . '/' . $module . '.ini' ) )
		{
			$this->debug->write( 'Could not get configuration for module ' . $module, 'warning' );
			$this->messages->setMessage( 'Could not get configuration for module ' . $module, 'warning' );
		}

		// filter parameters and get safe ones
		$parameters = new zgParameters( );
		$safeparameters = $parameters->getSafeParameters( $module, $action );

		// log the pageview if logging is active
		if ( $this->configuration->getConfiguration( 'zeitgeist', 'actionlog', 'actionlog_active' ) == '1' )
		{
			$this->actionlog->logAction( $moduleData['module_id'], $actionData['action_id'], $safeparameters );
		}

		// execute action in module
		$ret = call_user_func( array(&$moduleClass, $action), $safeparameters );
		if ( $ret !== true )
		{
			$this->debug->unguard( $ret );
			return $ret;
		}

		// save message data for session
		if ( $this->configuration->getConfiguration( 'zeitgeist', 'messages', 'use_persistent_messages' ) )
		{
			$this->messages->saveMessagesToSession( );
		}

		$this->debug->unguard( true );
		return true;
	}
}

?>
