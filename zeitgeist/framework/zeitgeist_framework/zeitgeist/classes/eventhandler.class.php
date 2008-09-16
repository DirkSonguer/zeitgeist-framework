<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Eventhandler class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST EVENTHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgEventhandler
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $messagecache;
	protected $configuration;
	protected $user;
	protected $traffic;


	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->messagecache = zgMessagecache::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		$this->traffic = new zgTrafficlogger();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * Loads all relevant data for a module from the database
	 *
	 * @param string $module name of the module
	 *
	 * @return boolean
	 */
	protected function _getModuleData($module)
	{
		$this->debug->guard();

		$modulesTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_modules');
		$sql = "SELECT * FROM " . $modulesTablename . " WHERE module_name = '" . $module . "'";

		if ($res = $this->database->query($sql))
		{
			$row = $this->database->fetchArray($res);

			$this->debug->unguard($row);
			return $row;
		}
		else
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
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
	protected function _getActionData($module, $action)
	{
		$this->debug->guard();

		$actionsTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_actions');
		$sql = "SELECT * FROM " . $actionsTablename . " WHERE action_module = '" . $module['module_id'] . "' AND action_name = '" . $action . "'";

		if ($res = $this->database->query($sql))
		{
			$row = $this->database->fetchArray($res);

			$this->debug->unguard($row);
			return $row;
		}
		else
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
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
	protected function _checkRightsForAction($moduleData, $actionData)
	{
		$this->debug->guard();

		if ($this->user->isLoggedIn())
		{
			$ret = $this->user->hasUserright($actionData['action_id']);

			$this->debug->unguard($ret);
			return $ret;
		}

		$this->debug->unguard(false);
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
	public function callEvent($module, $action)
	{
		$this->debug->guard();

		// load message data for user
		if ($this->configuration->getConfiguration('zeitgeist', 'messages', 'use_persistent_messages'))
		{
			$this->messagecache->loadMessagesFromDatabase();
		}

		// check if module is installed and get module data
		if (!$moduleData = $this->_getModuleData($module))
		{
			die('Error loading the module: Module is not found/ installed. Zeitgeist halted!');
		}

		// check from data if module is active
		if ($moduleData['module_active'] != '1')
		{
			die('Error loading the module: Module is not active. Zeitgeist halted!');
		}

		// check if the classname is already used
		if (class_exists($module, false))
		{
			die('Error loading the module class: Class name already used. Zeitgeist halted!');
		}

		//check if zeitgeist can load the module
		if (!class_exists($module, true))
		{
			die('Error loading the module class: Could not find matching class (' . $module . '). Zeitgeist halted!');
		}

		// load the module class through the autoloader
		$moduleClass = new $module;

		// check if action is installed and get action data
		if (!$actionData = $this->_getActionData($moduleData, $action))
		{
			die('Error loading the action (' . $action . ') in module (' . $module . '): Action is not installed for module. Zeitgeist halted!');
		}

		// check if action method exists in module
		if (!method_exists($moduleClass, $action))
		{
			die('Error loading the action (' . $action . ') in module (' . $module . '): Could not find method. Zeitgeist halted!');
		}

		// check if user has rights for given action
		if ($actionData['action_requiresuserright'] == '1')
		{
			if (!$this->_checkRightsForAction($moduleData, $actionData))
			{
				$this->debug->write('User (' . $this->user->getUserID() . ') has no rights for action (' . $action . ') in module (' . $module . ')', 'warning');
				$this->messages->setMessage('User (' . $this->user->getUserID() . ') has no rights for action (' . $action . ') in module (' . $module . ')', 'warning');

				$this->debug->unguard($this->configuration->getConfiguration('zeitgeist', 'eventhandler', 'no_userrights_for_action'));
				return $this->configuration->getConfiguration('zeitgeist', 'eventhandler', 'no_userrights_for_action');
			}
		}


		// load configuration
		if (!$this->configuration->loadConfiguration($module, APPLICATION_ROOTDIRECTORY . 'modules/' . $module . '/' . $module . '.ini'))
		{
			$this->debug->write('Could not get configuration for module ' . $module, 'warning');
			$this->messages->setMessage('Could not get configuration for module ' . $module, 'warning');
		}

		// filter parameters and get safe ones
		$parameterhandler = new zgParameterhandler();
		$parameters = $parameterhandler->getSafeParameters($module, $action);

		// log the pageview if logging is active
		if ($this->configuration->getConfiguration('zeitgeist','trafficlogger','trafficlogger_active') == '1')
		{
			$this->traffic->logPageview($moduleData['module_id'], $actionData['action_id'], $this->user->getUserID(), $parameters);
		}

		// execute action in module
		$ret = call_user_func(array(&$moduleClass, $action), $parameters);
		if ($ret !== true)
		{
			$this->debug->unguard($ret);
			return $ret;
		}

		// save message data for user
		if ($this->configuration->getConfiguration('zeitgeist', 'messages', 'use_persistent_messages'))
		{
			$this->messagecache->saveMessagesToDatabase();
		}

		$this->debug->unguard(true);
		return true;
	}

}
?>
