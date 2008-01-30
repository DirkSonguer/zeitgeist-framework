<?php

defined('LINERACER_ACTIVE') or die();

class lrPregamefunctions
{
	protected $debug;
	protected $messages;
	protected $objects;
	protected $database;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjectcache::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function playerWaitingForGame()
	{
		$this->debug->guard();

		if ($this->user->isLoggedIn())
		{
			$currentUserId = $this->user->getUserID();
			$sql = "SELECT * FROM lobbyusers WHERE lobbyuser_user='" . $currentUserId . "'";
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


	public function getPlayerGameroomData()
	{
		$this->debug->guard();

		if ($this->user->isLoggedIn())
		{
			$currentUserId = $this->user->getUserID();
			$sql = "SELECT * FROM lobbyusers lu LEFT JOIN lobby l ON lu.lobbyuser_lobby = l.lobby_id WHERE lobbyuser_user='" . $currentUserId . "'";
			$res = $this->database->query($sql);
			if ($row = $this->database->fetchArray($res))
			{
				$this->debug->unguard($row);
				return $row;
			}
		}

		$this->debug->unguard(false);
		return false;
	}


	public function leaveGameroom()
	{
		$this->debug->guard();

		$currentUserId = $this->user->getUserID();
		$sql = "DELETE FROM lobbyusers WHERE lobbyuser_user='" . $currentUserId . "'";
		$res = $this->database->query($sql);

		if (!$res)
		{
			$this->debug->write('Problem leaving gameroom: Player seems not to be in a gameroom', 'warning');
			$this->messages->setMessage('Problem leaving gameroom: Player seems not to be in a gameroom', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(false);
		return false;
	}

}

?>
