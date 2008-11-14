<?php

defined('LINERACER_ACTIVE') or die();

class lrGamefunctions
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
		$this->objects = zgObjectcache::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * moves a player to the given position
	 *
	 * @param integer $position_x x position to move to
	 * @param integer $position_y y position to move to
	 *
	 * @return boolean
	 */
	public function move($position_x, $position_y)
	{
		$this->debug->guard();

		// load current gamestates
		$gamestates = new lrGamestates();
		$currentGamestates = $gamestates->loadGamestates(1);
		
		// check status of current gamestates
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// load pre turn events
		$gameeventhandler = new lrGameeventhandler();
		$gameeventhandler->handleRaceeevents();

		// validate if it's the players turn
		$userfunctions = new lrUserfunctions();
		if (!$userfunctions->validateTurn())
		{
			$this->debug->write('Could not move player: not the players turn', 'warning');
			$this->messages->setMessage('Could not move player: not the players turn', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// validate the movement
		$movementfunctions = new lrMovementfunctions();
		if (!$movementfunctions->validateMove($position_x, $position_y))
		{
			$this->debug->write('Could not move player: player moved outside its allowed area', 'warning');
			$this->messages->setMessage('Could not move player: player moved outside its allowed area', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// check terrain and correct movement value
		$correctedMove = array();
		if (!$correctedMove = $movementfunctions->validateTerrain($position_x, $position_y))
		{
			$this->debug->write('Could not move player: validating line failed', 'warning');
			$this->messages->setMessage('Could not move player: validating line failed', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// save race action and handle post turn events
		$gameeventhandler->saveRaceaction($this->configuration->getConfiguration('gamedefinitions', 'actions', 'move'), $correctedMove[0].','.$correctedMove[1]);
		$gamestates->endTurn();

		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * plays a given gamecard
	 *
	 * @param integer $gamecard gamecard to play
	 *
	 * @return boolean
	 */
	public function playgamecard($gamecard)
	{
		$this->debug->guard();

		// load current gamestates
		$gamestates = new lrGamestates();
		$currentGamestates = $gamestates->loadGamestates(1);
	
		// check status of current gamestates		
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not play gamecard: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not play gamecard: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// load pre turn events
		$gameeventhandler = new lrGameeventhandler();
		$gameeventhandler->handleRaceeevents();

		// validate if it's the players turn
		$userfunctions = new lrUserfunctions();
		if (!$userfunctions->validateTurn())
		{
			$this->debug->write('Could not move player: not the players turn', 'warning');
			$this->messages->setMessage('Could not move player: not the players turn', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// validate if the player has the right for the gamecard
		$gamecardfunctions = new lrGamecardfunctions();
		if (!$gamecardfunctions->checkRights($gamecard, $currentGamestates['currentPlayer']))
		{
			$this->debug->write('Could not play gamecard: no rights to play gamecard', 'warning');
			$this->messages->setMessage('Could not play gamecard: no rights to play gamecard', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// get gamecard data
		if (!$gamecardData = $gamecardfunctions->getGamecardData($gamecard))
		{
			$this->debug->write('Could not play gamecard: gamecard not found', 'warning');
			$this->messages->setMessage('Could not play gamecard: gamecard not found', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// save race action and handle post turn events
		$gameeventhandler->saveRaceaction($this->configuration->getConfiguration('gamedefinitions', 'actions', 'playgamecard'), $gamecard);
		$gameeventhandler->saveRaceevent($currentGamestates['currentPlayer'], $this->configuration->getConfiguration('gamedefinitions', 'events', 'playgamecard'), $gamecard, ($currentGamestates['currentRound']+$gamecardData['gamecard_roundoffset']));
		$gamecardfunctions->removeGamecard($gamecard, $currentGamestates['currentPlayer']);
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function startGame($lobbyid)
	{
		$this->debug->guard();

		// get lobby data from database
		$sql = "SELECT * FROM lobby WHERE lobby_id='" . $lobbyid . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not start game: no lobby information found', 'warning');
			$this->messages->setMessage('Could not start game: no lobby information found', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		$row = $this->database->fetchArray($res);

		// store new race data
		$sql = "INSERT INTO races(race_circuit, race_activeplayer, race_currentround, race_gamecardsallowed) ";
		$sql .= "VALUES('" . $row['lobby_circuit'] ."', '1', '1', '" . $row['lobby_gamecardsallowed'] . "')";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not start game: could not write race data to database', 'warning');
			$this->messages->setMessage('Could not start game: could not write race data to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$raceid = $this->database->insertId();
		if(!$raceid)
		{
			$this->debug->write('Could not start game: could not get race id from database', 'warning');
			$this->messages->setMessage('Could not start game: could not get race id from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// get lobby users from database
		$sql = "SELECT * FROM lobby_to_users WHERE lobbyuser_lobby='" . $lobbyid . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not start game: no lobby information found', 'warning');
			$this->messages->setMessage('Could not start game: no lobby information found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$gameusers = array();
		while ($row = $this->database->fetchArray($res))
		{
			$gameusers[] = $row['lobbyuser_user'];
		}
		
		$sql = 'INSERT INTO race_to_users(raceuser_race, raceuser_user) VALUES';
		foreach ($gameusers as $player)
		{
			$sql .= "('" . $raceid . "','" . $player . "'),";
		}
		$sql = substr($sql, 0, -1);
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not start game: could not insert player data', 'warning');
			$this->messages->setMessage('Could not start game: could not insert player data', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM lobby WHERE lobby_id='" . $lobbyid . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not start game: lobby data could not be erased', 'warning');
			$this->messages->setMessage('Could not start game: lobby data could not be erased', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$sql = "DELETE FROM lobby_to_users WHERE lobbyuser_lobby='" . $lobbyid . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not start game: player data could not be erased', 'warning');
			$this->messages->setMessage('Could not start game: player data could not be erased', 'warning');
			$this->debug->unguard(false);
			return false;
		}		
		
		$this->debug->unguard($raceid);
		return $raceid;
	}


	public function endGame($raceid)
	{
		$this->debug->guard();

		// get race data from database
		$sql = "SELECT * FROM races WHERE race_id='" . $raceid . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not end game: no race information found', 'warning');
			$this->messages->setMessage('Could not end game: no race information found', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		$row = $this->database->fetchArray($res);

		// store new race data
		$sql = "INSERT INTO races_archive(race_id, race_circuit, race_gamecardsallowed, race_started) ";
		$sql .= "VALUES('" . $row['race_id'] ."', '" . $row['race_circuit'] ."', '" . $row['race_gamecardsallowed'] ."', '" . $row['race_created'] ."')";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not end game: could not write race archive data to database', 'warning');
			$this->messages->setMessage('Could not end game: could not write race archive data to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// get race actions from database
		$sql = "SELECT * FROM race_actions WHERE raceaction_race='" . $raceid . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not end game: no race actions found', 'warning');
			$this->messages->setMessage('Could not end game: no race actions found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$gameactions = array();
		while ($row = $this->database->fetchArray($res))
		{
			$gameactions[] = $row;
		}
		
		$sql = 'INSERT INTO race_actions_archive(raceaction_id, raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES';
		foreach ($gameactions as $action)
		{
			$sql .= "('" . $action['raceaction_id'] . "','" . $action['raceaction_race'] . "','" . $action['raceaction_user'] . "','" . $action['raceaction_action'] . "','" . $action['raceaction_parameter'] . "'),";
		}
		$sql = substr($sql, 0, -1);
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not end game: could not archive action data', 'warning');
			$this->messages->setMessage('Could not end game: could not archive action data', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM races WHERE race_id='" . $raceid . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not end game: race data could not be erased', 'warning');
			$this->messages->setMessage('Could not end game: race data could not be erased', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$sql = "DELETE FROM race_actions WHERE raceaction_race='" . $raceid . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not end game: race actions could not be erased', 'warning');
			$this->messages->setMessage('Could not end game: race actions could not be erased', 'warning');
			$this->debug->unguard(false);
			return false;
		}		

// TODO: Punkteverteilung
// TODO: Achievments		
		$this->debug->unguard(true);
		return true;
	}


	public function createLobby($circuit, $maxplayers, $gamecardsallowed)
	{
		$this->debug->guard();

		// create new lobby
		$sql = "INSERT INTO lobby(lobby_circuit, lobby_maxplayers, lobby_gamecardsallowed) ";
		$sql .= "VALUES('" . $circuit . "', '" . $maxplayers . "', '" . $gamecardsallowed . "')";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not create lobby: could not enter lobby data into database', 'warning');
			$this->messages->setMessage('Could not create lobby: could not enter lobby data into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$lobbyid = $this->database->insertId();
		if(!$lobbyid)
		{
			$this->debug->write('Could not create lobby: could get lobby id from database', 'warning');
			$this->messages->setMessage('Could not create lobby: could not get lobby id from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// add current user to lobby
		$sql = "INSERT INTO lobby_to_users(lobbyuser_lobby, lobbyuser_user) ";
		$sql .= "VALUES('" . $lobbyid ."', '" . $this->user->getUserId() ."')";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not create lobby: could not write lobby creator to database', 'warning');
			$this->messages->setMessage('Could not create lobby: could not write lobby creator to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->debug->unguard(true);
		return true;
	}

	public function joinLobby($lobbyid)
	{
		$this->debug->guard();

		$userfunctions = new lrUserfunctions();
		if ($userfunctions->waitingForGame())
		{
			$this->debug->write('Could not join lobby: user already waiting for a game', 'warning');
			$this->messages->setMessage('Could not join lobby: user already waiting for a game', 'warning');
			$this->debug->unguard(false);
			return false;
		}		

		$sql = "SELECT * FROM lobby_to_users l2u LEFT JOIN lobby l ON l2u.lobbyuser_lobby = l.lobby_id WHERE l2u.lobbyuser_lobby='" . $lobbyid . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not join lobby: could get lobby data from database', 'warning');
			$this->messages->setMessage('Could not join lobby: could not get lobby data from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$numPlayers = $this->database->numRows($res);
		$row = $this->database->fetchArray($res);
		if($numPlayers == $row['lobby_maxplayers'])
		{
			$this->debug->write('Could not join lobby: lobby already full', 'warning');
			$this->messages->setMessage('Could not join lobby: lobby already full', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// add current user to lobby
		$sql = "INSERT INTO lobby_to_users(lobbyuser_lobby, lobbyuser_user) ";
		$sql .= "VALUES('" . $lobbyid ."', '" . $this->user->getUserId() ."')";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not join lobby: could not write lobby player to database', 'warning');
			$this->messages->setMessage('Could not join lobby: could not write lobby player to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->debug->unguard(true);
		return true;
	}


}
?>
