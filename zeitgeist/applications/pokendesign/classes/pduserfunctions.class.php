<?php

defined('POKENDESIGN_ACTIVE') or die();

class pdUserfunctions
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
	 * Creates a new user with a given name and password
	 *
	 * @param string $name name of the user
	 * @param string $password password of the user
	 * @param array $userdata array containing the userdata
	 *
	 * @return boolean
	 */
	public function createUser($name, $password, $userdata=array())
	{
		$this->debug->guard();

		if (!$newUserId = $this->user->createUser($name, $password))
		{
			$this->debug->unguard(false);
			return false;
		}
		
		$userdatafunctions = new zgUserdata();
		if (!$userdatafunctions->saveUserdata($newUserId, $userdata))
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}

}
?>
