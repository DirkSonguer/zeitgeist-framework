<?php

defined('LINERACER_ACTIVE') or die();

class lrMiscfunctions
{
	private static $instance = false;

	protected $debug;
	protected $messages;
	protected $objects;
	protected $database;
	protected $configuration;
	protected $user;

	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjectcache::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

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
			self::$instance = new lrMiscFunctions();
		}

		return self::$instance;
	}


	public function playerWaitingForGame()
	{
		$this->debug->guard();

		if ($this->user->isLoggedIn())
		{
			$currentUserId = $this->user->getUserID();
			$sql = "SELECT * FROM lobby WHERE";
			$sql .= " lobby_creator='" . $currentUserId . "' OR ";
			$sql .= " lobby_player2='" . $currentUserId . "' OR ";
			$sql .= " lobby_player3='" . $currentUserId . "' OR ";
			$sql .= " lobby_player4='" . $currentUserId . "'";
			$res = $this->database->query($sql);
			if ($this->database->numRows($res) > 0)
			{
				$this->debug->unguard(true);
				return true;
			}
		}

		$this->debug->unguard(false);
		return false;
	}

}

?>
