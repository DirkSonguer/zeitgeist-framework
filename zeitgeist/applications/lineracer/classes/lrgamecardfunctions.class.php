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
		$this->objects = zgObjects::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();
	}

	
	/**
	 * Gets all data for a given gamecard
	 *
	 * @param integer $gamecard id of the gamecard to get
	 *
	 * @return array
	 */
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


	/**
	 * Gets all gamecards and their data for a given user
	 *
	 * @param integer $userid user id of the player
	 *
	 * @return array
	 */
	public function getPlayerDeck($userid=0)
	{
		$this->debug->guard();

		if ($userid == 0) $userid = $this->user->getUserID();
		$sql = "SELECT * FROM gamecards_to_users u2g LEFT JOIN gamecards g ON u2g.usergamecard_gamecard = g.gamecard_id WHERE u2g.usergamecard_user='" . $userid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get gamecard data: no gamecards found', 'warning');
			$this->messages->setMessage('Could not get gamecard data: no gamecards found', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$gamecards = array();
		while ($row = $this->database->fetchArray($res))
		{
			$gamecards[] = $row;
		}


		$this->debug->unguard($gamecards);
		return $gamecards;
	}
	

	/**
	 * Checks if a given user has the gamecard in his stack
	 * If no userid is given, the current user will be used
	 *
	 * @param integer $gamecard id of the gamecard
	 * @param integer $userid id of the user
	 *
	 * @return boolean
	 */
	public function checkRights($gamecard, $userid=0)
	{
		$this->debug->guard();

		if ($userid == 0) $userid = $this->user->getUserID();

		$sql = "SELECT * FROM gamecards_to_users WHERE usergamecard_gamecard='" . $gamecard . "' AND usergamecard_user='" . $userid . "'";
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


	/**
	 * This removes one instance of the given gamecard from the stack of the given user
	 * If the user only one instance of the given card, it is removed entirely from his stack
	 * If no userid is given, the current user will be used
	 *
	 * @param integer $gamecard id of the gamecard to remove
	 * @param integer $userid user to remove the gamecard from
	 *
	 * @return boolean
	 */
	public function removeGamecard($gamecard, $userid=0)
	{
		$this->debug->guard();

		if ($userid == 0) $userid = $this->user->getUserID();

		$sql = "SELECT * FROM gamecards_to_users WHERE usergamecard_gamecard='" . $gamecard . "' AND usergamecard_user='" . $userid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not remove gamecard: could not get gamecard information from the database', 'warning');
			$this->messages->setMessage('Could not remove gamecard: could not get gamecard information from the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$count = $this->database->numRows($res);
		if ($count == 0)
		{
			$this->debug->write('Gamecard to remove was not found. Possible problem with interface', 'warning');
			$this->messages->setMessage('Gamecard to remove was not found. Possible problem with interface', 'warning');
			$this->debug->unguard(true);
			return true;
		}
			
		$row = $this->database->fetchArray($res);
		if ($row['usergamecard_count'] == '1')
		{
			$sql = "DELETE FROM gamecards_to_users WHERE usergamecard_gamecard='" . $gamecard . "' AND usergamecard_user='" . $userid . "'";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Could not remove gamecard: could not delete gamecard values from the database', 'warning');
				$this->messages->setMessage('Could not remove gamecard: could not delete gamecard values from the database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		else
		{
			$sql = "UPDATE gamecards_to_users SET usergamecard_count='" . ($row['usergamecard_count']-1) . "' WHERE usergamecard_gamecard='" . $gamecard . "' AND usergamecard_user='" . $userid . "'";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Could not remove gamecard: could not update gamecard values in the database', 'warning');
				$this->messages->setMessage('Could not remove gamecard: could not update gamecard values in the database', 'warning');
				$this->debug->unguard(false);
				return false;
			}		
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Adds the given gamecard to the stack of the given user
	 * If no userid is given, the current user will be used
	 *
	 * @param integer $gamecard id of the gamecard to add
	 * @param integer $userid user to add the gamecard to
	 *
	 * @return boolean
	 */
	public function addGamecard($gamecard, $userid=0)
	{
		$this->debug->guard();

		if ($userid == 0) $userid = $this->user->getUserID();

		$sql = "SELECT * FROM gamecards_to_users WHERE usergamecard_gamecard='" . $gamecard . "' AND usergamecard_user='" . $userid . "'";
		$res = $this->database->query($sql);
		$count = $this->database->numRows($res);
		
		if ($count == 0)
		{
			$sql = "INSERT INTO gamecards_to_users(usergamecard_gamecard, usergamecard_user, usergamecard_count) VALUES('" . $gamecard . "', '" . $userid . "', '1')";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Could not add gamecard: could not insert gamecard values into the database', 'warning');
				$this->messages->setMessage('Could not add gamecard: could not insert gamecard values into the database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		else
		{
			$sql = "UPDATE gamecards_to_users SET usergamecard_count='" . ($count+1) . "' WHERE usergamecard_gamecard='" . $gamecard . "' AND usergamecard_user='" . $userid . "'";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Could not add gamecard: could not update gamecard values in the database', 'warning');
				$this->messages->setMessage('Could not add gamecard: could not update gamecard values in the database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}

		$this->debug->unguard(true);
		return true;
	}
	
}

?>
