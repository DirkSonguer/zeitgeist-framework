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
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST ACTIONLOG
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgActionlog
{

	protected $debug;
	protected $messages;
	protected $database;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * Function that logs the given action information
	 *
	 * @param int $module id of the module
	 * @param int $action id of the action
	 * @param array $parameters array with parameters of the call
	 */
	public function logAction($module, $action, $parameters=array())
	{
		$this->debug->guard();

		$sql = "INSERT INTO actionlog(actionlog_module, actionlog_action, actionlog_ip) VALUES('" . $module . "', '" . $action . "', INET_ATON('" . getenv('REMOTE_ADDR') . "'))";
		if (!$res = $this->database->query($sql))
		{
			$this->debug->write('Problem logging the action: could not write to log table', 'warning');
			$this->messages->setMessage('Problem logging the action: could not write to log table', 'warning');

			$this->debug->unguard(false);
			return false;
		}

		$logId = $this->database->insertId();

		if (count($parameters) > 0)
		{
			$sql = "INSERT INTO actionlog_parameters(actionparameters_trafficid, actionparameters_key, actionparameters_value) VALUES";
			$sqlinserts = '';
			foreach ($parameters as $key => $value)
			{
				if ($sqlinserts != '') $sqlinserts .= ',';
				$sqlinserts .= "('" . $logId . "', '" . $key . "', '" . $value . "')";
			}

			$sql .= $sqlinserts;

			if (!$res = $this->database->query($sql))
			{
				$this->debug->write('Problem logging the action: could not write parameter to log table', 'warning');
				$this->messages->setMessage('Problem logging the action: could not write parameter to log table', 'warning');

				$this->debug->unguard(false);
				return false;
			}
		}

		$this->debug->unguard(true);
		return true;
	}

}
?>
