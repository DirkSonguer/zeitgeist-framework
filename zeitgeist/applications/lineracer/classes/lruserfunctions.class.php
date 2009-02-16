<?php

defined('LINERACER_ACTIVE') or die();

class lrUserfunctions
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $session;
	protected $objects;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->session = zgSession::init();
		$this->configuration = zgConfiguration::init();
		$this->objects = zgObjects::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * changes the userdata for the current user
	 *
	 * @param array $userdata array that contains all userdata fields as array fields
	 *
	 * @return boolean
	 */
	public function changeUserdata($userdata)
	{
		$this->debug->guard();

		if (!empty($userdata['userdata_lastname'])) $this->user->setUserdata('userdata_lastname', $userdata['userdata_lastname'], false);
		if (!empty($userdata['userdata_firstname'])) $this->user->setUserdata('userdata_firstname', $userdata['userdata_firstname'], false);
		if (!empty($userdata['userdata_address1'])) $this->user->setUserdata('userdata_address1', $userdata['userdata_address1'], false);
		if (!empty($userdata['userdata_address2'])) $this->user->setUserdata('userdata_address2', $userdata['userdata_address2'], false);
		if (!empty($userdata['userdata_zip'])) $this->user->setUserdata('userdata_zip', $userdata['userdata_zip'], false);
		if (!empty($userdata['userdata_city'])) $this->user->setUserdata('userdata_city', $userdata['userdata_city'], false);
		if (!empty($userdata['userdata_url'])) $this->user->setUserdata('userdata_url', $userdata['userdata_url'], false);

		$this->user->saveUserdata();

		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * validates that it is the users turn
	 *
	 * @return boolean
	 */
	public function validateTurn()
	{
		$this->debug->guard();
		
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not validate player turn: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not validate player turn: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if (empty($currentGamestates['meta']['players'][$currentGamestates['move']['currentPlayer']]))
		{
			$this->debug->write('Could not validate player turn: could not find player data in the game data', 'warning');
			$this->messages->setMessage('Could not validate player turn: could not find player data in the game data', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if ($currentGamestates['meta']['players'][$currentGamestates['move']['currentPlayer']] != $this->user->getUserID())
		{
			$this->debug->write('Could not validate player turn: player not found active', 'warning');
			$this->messages->setMessage('Could not validate player turn: player not found active', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;		
	}


	/**
	 * checks if a user is in a lobby, waiting for a game
	 *
	 * @return boolean
	 */
	public function waitingForGame()
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM lobby_to_users WHERE lobbyuser_user='" . $this->user->getUserId() . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could validate user lobby status: could get lobby data from database', 'warning');
			$this->messages->setMessage('Could validate user lobby status: could get lobby data from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$playerFound = $this->database->numRows($res);
		if ($playerFound == 0)
		{
			$this->debug->unguard(false);
			return false;		
		}
		
		$this->debug->unguard(true);
		return true;		
	}


	/**
	 * checks if a user is part of a running game
	 *
	 * @return boolean
	 */
	public function currentlyPlayingGame()
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM race_to_users WHERE raceuser_user='" . $this->user->getUserId() . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could validate user play status: could get race data from database', 'warning');
			$this->messages->setMessage('Could validate play lobby status: could get race data from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$playerFound = $this->database->numRows($res);
		if ($playerFound == 0)
		{
			$this->debug->unguard(false);
			return false;		
		}

		$this->debug->unguard(true);
		return true;		
	}


	/**
	 * Gets the race id of a player if he is currently playing
	 *
	 * @return boolean
	 */
	public function getUserRace()
	{
		$this->debug->guard();
		
		$sql = "SELECT raceuser_race FROM race_to_users WHERE raceuser_user='" . $this->user->getUserID() . "'";		
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get race id: no race found for current player', 'warning');
			$this->messages->setMessage('Could not get race id: no race found for current player', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);

		if (empty($row['raceuser_race']))
		{
			$this->debug->write('Could not get race id: race data seems corrupt', 'warning');
			$this->messages->setMessage('Could not get race id: race data seems corrupt', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($row['raceuser_race']);
		return $row['raceuser_race'];
	}


	/**
	 * Gets the lobby id of a player if he is currently in one
	 *
	 * @return boolean
	 */
	public function getUserLobby()
	{
		$this->debug->guard();
		
		$sql = "SELECT lobbyuser_lobby FROM lobby_to_users WHERE lobbyuser_user='" . $this->user->getUserID() . "'";		
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get lobby id: no lobby found for current player', 'warning');
			$this->messages->setMessage('Could not get lobby id: no lobby found for current player', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);

		if (empty($row['lobbyuser_lobby']))
		{
			$this->debug->write('Could not get lobby id: lobby data seems corrupt', 'warning');
			$this->messages->setMessage('Could not get lobby id: lobby data seems corrupt', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($row['lobbyuser_lobby']);
		return $row['lobbyuser_lobby'];
	}

	
	/**
	 * gets the circuits available for the current player
	 *
	 * @return array
	 */
	public function getAvailableCircuits()
	{
		$this->debug->guard();

		$ret = array();

		$sql = "SELECT * FROM circuits WHERE circuit_public='1' AND circuit_active='1'";
		$resCircuits = $this->database->query($sql);
		if(!$resCircuits)
		{
			$this->debug->write('Could not get available circuits for user: could not read circuit table', 'warning');
			$this->messages->setMessage('Could not get available circuits for user: could not read circuit table', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		while ($rowCircuits = $this->database->fetchArray($resCircuits))
		{
			$ret[] = $rowCircuits;
		}

		$sql = "SELECT * FROM circuits_to_users c2u LEFT JOIN circuits c ON c2u.usercircuit_circuit = c.circuit_id WHERE c2u.usercircuit_user = '" . $this->user->getUserId() . "' AND c.circuit_active='1'";
		$resCircuits = $this->database->query($sql);
		if(!$resCircuits)
		{
			$this->debug->write('Could not get available circuits for user: could not read circuit to user table', 'warning');
			$this->messages->setMessage('Could not get available circuits for user: could not read circuit to user table', 'warning');
			$this->debug->unguard(false);
			return false;
		}		

		while ($rowCircuits = $this->database->fetchArray($resCircuits))
		{
			$ret[] = $rowCircuits;
		}

		$this->debug->unguard($ret);
		return $ret;		
	}
}
?>
