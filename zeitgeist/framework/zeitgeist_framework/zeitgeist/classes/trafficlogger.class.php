<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Trafficlogger class
 *
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST TRAFFICLOGGER
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgErrorhandler::init();
 */
class zgTrafficlogger
{
	private static $instance = false;

	protected $debug;
	protected $messages;
	protected $configuration;
	protected $database;

	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * Initialize the singleton
	 *
	 * @return object
	 */
	public static function init()
	{
		if (self::$instance === false)
		{
			self::$instance = new zgTrafficlogger();
		}

		return self::$instance;
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

		$sql = "INSERT INTO trafficlog(trafficlog_module, trafficlog_action, trafficlog_user, trafficlog_ip) VALUES('" . $module . "', '" . $action . "', '" . $user . "', '" . getenv('REMOTE_ADDR') . "')";
		if (!$res = $this->database->query($sql))
		{
			$this->debug->write('Problem logging the pageview: could not write to log table', 'warning');
			$this->messages->setMessage('Problem logging the pageview: could not write to log table', 'warning');

			$this->debug->unguard(false);
			return false;
		}

		$logId = $this->database->insertId();

		foreach($parameters as $key => $value)
		{
			$sql = "INSERT INTO trafficlog_parameters(trafficparameters_trafficid, trafficparameters_key, trafficparameters_value) VALUES('" . $logId . "', '" . $key . "', '" . $value . "')";
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
