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
	protected $user;
	protected $actionlog;


	/**
	 * Class constructor
	 */
	public function __construct( )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );
		$this->configuration = zgConfiguration::init( );
		$this->user = zgUserhandler::init( );
		$this->actionlog = new zgActionlog( );

		$this->database = new zgDatabase( );
		$this->database->connect( );
	}


	/**
	 * Loads all relevant data for a module from the database
	 *
	 * @param string $module name of the module
	 *
	 * @return boolean
	 */
	protected function _getModuleData( $module )
	{
		$this->debug->guard( );

		$modulesTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_modules' );
		$sql = "SELECT * FROM " . $modulesTablename . " WHERE module_name = '" . mysql_real_escape_string( $module ) . "'";

		$res = $this->database->query( $sql );
		if ( !empty( $res ) )
		{
			$row = $this->database->fetchArray( $res );

			$this->debug->unguard( $row );
			return $row;
		}
		else
		{
			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Loads all relevant data for an action from the database
	 *
	 * @param string $module name of the module
	 * @param string $action name of the action
	 *
	 * @return boolean
	 */
	protected function _getActionData( $module, $action )
	{
		$this->debug->guard( );

		$actionsTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_actions' );
		$sql = "SELECT * FROM " . $actionsTablename . " WHERE action_module = '" . $module ['module_id'] . "' AND action_name = '" . mysql_real_escape_string( $action ) . "'";

		$res = $this->database->query( $sql );
		if ( !empty( $res ) )
		{
			$row = $this->database->fetchArray( $res );

			$this->debug->unguard( $row );
			return $row;
		}
		else
		{
			$this->debug->unguard( false );
			return false;
		}

		$this->debug->unguard( true );
		return true;
	}


	/**
	 * Checks if the user has the right for a given action
	 *
	 * @param array $moduleData data of the module to load
	 * @param array $actionData data of the action to load
	 *
	 * @return boolean
	 */
	protected function _checkRightsForAction( $moduleData, $actionData )
	{
		$this->debug->guard( );

		if ( $this->user->isLoggedIn( ) )
		{
			$ret = $this->user->hasUserright( $actionData ['action_id'] );

			$this->debug->unguard( $ret );
			return $ret;
		}

		$this->debug->unguard( false );
		return false;
	}


	/**
	 * Checks if the given action requires special user rights
	 *
	 * @param string $module name of the module
	 * @param string $action name of the action
	 *
	 * @return boolean
	 */
	public function requiresUserRights( $module, $action )
	{
		$this->debug->guard( );

		$actionsTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_actions' );
		$modulesTablename = $this->configuration->getConfiguration( 'zeitgeist', 'tables', 'table_modules' );
		$sql = "SELECT a.action_requiresuserright FROM " . $actionsTablename . " a ";
		$sql .= "LEFT JOIN " . $modulesTablename . " m ON a.action_module = m.module_id ";
		$sql .= "WHERE m.module_name = '" . mysql_real_escape_string( $module ) . "' AND a.action_name = '" . mysql_real_escape_string( $action ) . "'";

		$res = $this->database->query( $sql );
		if ( !empty( $res ) )
		{
			$row = $this->database->fetchArray( $res );

			$this->debug->unguard( $row ['action_requiresuserright'] );
			return $row ['action_requiresuserright'];
		}

		$this->debug->unguard( false );
		return false;
	}


	/**
	 * Sets a custom userhandler
	 * The userhandler class will be used to check rights and roles of a user
	 *
	 * @param zgUserhandler $userhandler userhandler class
	 *
	 * @return boolean
	 */
	public function setEventhandler( $userhandler )
	{
		$this->debug->guard( );

		if ( $userhandler instanceof zgUserhandler )
		{
			$this->user = $userhandler;
			$this->debug->unguard( true );
			return true;
		}

		$this->debug->unguard( false );
		return false;
	}


	/**
	 * Executes an action inside a module
	 * Also handles all security related aspects
	 *
	 * @param string $module name of the module to load
	 * @param string $action name of the action to load
	 * @param array $parameters array with parameters the action is called
	 * @param integer $user id of the current user
	 *
	 * @return boolean
	 */
	public function callEvent( $module, $action )
	{
		$this->debug->guard( );

		// load message data for user
		if ( $this->configuration->getConfiguration( 'zeitgeist', 'messages', 'use_persistent_messages' ) )
		{
			$this->messages->loadMessagesFromSession( );
		}

		// check if module is installed and get module data
		if ( !$moduleData = $this->_getModuleData( $module ) )
		{
			$this->debug->write( 'Error loading the module: Module is not found/ installed: ' . $module, 'error' );
			$this->messages->setMessage( 'Error loading the module: Module is not found/ installed: ' . $module, 'error' );
			$this->debug->unguard( false );
			return false;
		}

		// check from data if module is active
		if ( $moduleData ['module_active'] != '1' )
		{
			$this->debug->write( 'Error loading the module: Module is not active: ' . $module, 'error' );
			$this->messages->setMessage( 'Error loading the module: Module is not active: ' . $module, 'error' );
			$this->debug->unguard( false );
			return false;
		}

		// check if the classname is already used
		if ( class_exists( $module, false ) )
		{
			$this->debug->write( 'Error loading the module: Class name already used: ' . $module, 'error' );
			$this->messages->setMessage( 'Error loading the module: Class name already used: ' . $module, 'error' );
			$this->debug->unguard( false );
			return false;
		}

		//check if zeitgeist can load the module
		if ( !class_exists( $module, true ) )
		{
			$this->debug->write( 'Error loading the module: Could not find matching class: ' . $module, 'error' );
			$this->messages->setMessage( 'Error loading the module: Could not find matching class: ' . $module, 'error' );
			$this->debug->unguard( false );
			return false;
		}

		// load the module class through the autoloader
		$moduleClass = new $module( );

		// check if action is installed and get action data
		if ( !$actionData = $this->_getActionData( $moduleData, $action ) )
		{
			$this->debug->write( 'Error loading the action (' . $action . ') in module (' . $module . '): Action is not installed for module', 'error' );
			$this->messages->setMessage( 'Error loading the action (' . $action . ') in module (' . $module . '): Action is not installed for module', 'error' );
			$this->debug->unguard( false );
			return false;
		}

		// check if action method exists in module
		if ( !method_exists( $moduleClass, $action ) )
		{
			$this->debug->write( 'Error loading the action (' . $action . ') in module (' . $module . '): Could not find method', 'error' );
			$this->messages->setMessage( 'Error loading the action (' . $action . ') in module (' . $module . '): Could not find method', 'error' );
			$this->debug->unguard( false );
			return false;
		}

		// check if user has rights for given action
		if ( $actionData ['action_requiresuserright'] == '1' )
		{
			if ( !$this->_checkRightsForAction( $moduleData, $actionData ) )
			{
				$this->debug->write( 'User (' . $this->user->getUserID( ) . ') has no rights for action (' . $action . ') in module (' . $module . ')', 'warning' );
				$this->messages->setMessage( 'User (' . $this->user->getUserID( ) . ') has no rights for action (' . $action . ') in module (' . $module . ')', 'warning' );

				$this->debug->unguard( false );
				return false;
			}
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
			$this->actionlog->logAction( $moduleData ['module_id'], $actionData ['action_id'], $safeparameters );
		}

		// execute action in module
		$ret = call_user_func( array(&$moduleClass, $action), $safeparameters );
		if ( $ret !== true )
		{
			$this->debug->unguard( $ret );
			return $ret;
		}

		// save message data for user
		if ( $this->configuration->getConfiguration( 'zeitgeist', 'messages', 'use_persistent_messages' ) )
		{
			$this->messages->saveMessagesToSession( );
		}

		$this->debug->unguard( true );
		return true;
	}
}

?>
