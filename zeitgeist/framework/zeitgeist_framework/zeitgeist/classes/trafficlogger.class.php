<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Trafficlogger class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST TRAFFICLOGGER
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgTrafficlogger
{

	protected $debug;
	protected $messages;
	protected $database;

	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * Function that logs the given pageview information
	 *
	 * @param int $module id of the module
	 * @param int $action id of the action
	 * @param int $user id of the user
	 * @param array $parameters array with parameters of the call
	 */
	public function logPageview($module, $action, $user=0, $parameters=array())
	{
		$this->debug->guard();

		$sql = "INSERT INTO trafficlog(trafficlog_module, trafficlog_action, trafficlog_user, trafficlog_ip) VALUES('" . $module . "', '" . $action . "', '" . $user . "', INET_ATON('" . getenv('REMOTE_ADDR') . "'))";
		if (!$res = $this->database->query($sql))
		{
			$this->debug->write('Problem logging the pageview: could not write to log table', 'warning');
			$this->messages->setMessage('Problem logging the pageview: could not write to log table', 'warning');

			$this->debug->unguard(false);
			return false;
		}

		$logId = $this->database->insertId();

		if (count($parameters) > 0)
		{
			$sql = "INSERT INTO trafficlog_parameters(trafficparameters_trafficid, trafficparameters_key, trafficparameters_value) VALUES";
			$sqlinserts = '';
			foreach ($parameters as $key => $value)
			{
				if ($sqlinserts != '') $sqlinserts .= ',';
				$sqlinserts .= "('" . $logId . "', '" . $key . "', '" . $value . "')";
			}

			$sql .= $sqlinserts;

			if (!$res = $this->database->query($sql))
			{
				$this->debug->write('Problem logging the pageview: could not write parameter to log table', 'warning');
				$this->messages->setMessage('Problem logging the pageview: could not write parameter to log table', 'warning');

				$this->debug->unguard(false);
				return false;
			}
		}

		$this->debug->unguard(true);
		return true;
	}

}
?>
