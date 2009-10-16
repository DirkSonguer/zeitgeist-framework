<?php

defined('TASKKUN_ACTIVE') or die();

class tkInstancefunctions
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $session;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->session = zgSession::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * Creates a new instance
	 *
	 * instance-safe!
	 *
	 * @param string $name name of the instance
	 * @param integer $type type of the instance
	 *
	 * @return integer
	 */
	public function createInstance($name, $type=0)
	{
		$this->debug->guard();

		$sql = "INSERT INTO instances(instance_name, instance_type) VALUES('" . $name . "', '" . $type . "')";
		$res = $this->database->query($sql);

		$lastinsert = $this->database->insertId();

		$this->debug->unguard($lastinsert);
		return $lastinsert;
	}

}
?>
