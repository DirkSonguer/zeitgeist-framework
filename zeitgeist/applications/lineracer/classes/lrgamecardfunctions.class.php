<?php

defined('LINERACER_ACTIVE') or die();

class lrGamecardfunctions
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

	
	// TODO: Richitg aufsetzen
	public function getGamecardData($gamecard)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM gamecards WHERE gamecard_id='" . $gamecard . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get gamecard data: gamecard not found', 'warning');
			$this->messages->setMessage('Could not get gamecard data: gamecard not found', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$row = $this->database->fetchArray($res);

		$this->debug->unguard($row);
		return $row;
	}
	
	public function checkRights($gamecard, $user)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM users_to_gamecards WHERE usergamecard_gamecard='" . $gamecard . "' AND usergamecard_user='" . $user . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Right denied: could not search gamecard database', 'warning');
			$this->messages->setMessage('Right denied: could not search gamecard database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$cards = $this->database->numRows($res);
		if ($cards == 0)
		{
			$this->debug->write('Right denied: could not find gamecard in user deck', 'warning');
			$this->messages->setMessage('Right denied: could not find gamecard in user deck', 'warning');
			$this->debug->unguard(false);
			return false;			
		}

		$this->debug->unguard(true);
		return true;
	}

	// TODO: user Abfrage
	public function redeemGamecard($gamecard, $user=0)
	{
		$this->debug->guard();

		$sql = "DELETE FROM users_to_gamecards WHERE usergamecard_gamecard='" . $gamecard . "' AND usergamecard_user='" . $user . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Right denied: could not find gamecard in user deck', 'warning');
			$this->messages->setMessage('Right denied: could not find gamecard in user deck', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);

		$this->debug->unguard(true);
		return true;
	}
	
}

?>
