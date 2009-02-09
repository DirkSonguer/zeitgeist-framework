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
		$this->objects = zgObjects::init();
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

		// check if players are ready
		$lobbyfunctions = new lrLobbyfunctions();
		if(!$lobbyfunctions->checkGameConfirmation($lobbyid))
		{
			$this->debug->write('Could not start game: players are not ready yet', 'warning');
			$this->messages->setMessage('Could not start game: players are not ready yet', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
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
		$rowLobby = $this->database->fetchArray($res);

		// store new race data
		$sql = "INSERT INTO races(race_circuit, race_activeplayer, race_currentround, race_gamecardsallowed) ";
		$sql .= "VALUES('" . $rowLobby['lobby_circuit'] ."', '1', '1', '" . $rowLobby['lobby_gamecardsallowed'] . "')";
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

		// get associated circuit data
		$sql = "SELECT * FROM circuits WHERE circuit_id = '" . $rowLobby['lobby_circuit'] . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not start game: could not find the circuit data', 'warning');
			$this->messages->setMessage('Could not start game: could not find the circuit data', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		$rowCircuit = $this->database->fetchArray($res);

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
		while ($rowLobby = $this->database->fetchArray($res))
		{
			$gameusers[] = $rowLobby['lobbyuser_user'];
		}
		
		$i = 0;
		$sql = 'INSERT INTO race_to_users(raceuser_race, raceuser_user) VALUES';
		foreach ($gameusers as $player)
		{
			$currentPosition = explode(',',$rowCircuit['circuit_startposition']);
			$currentVector = explode(',',$rowCircuit['circuit_startvector']);
			$currentPosition[0] += $currentVector[0] * $i;
			$currentPosition[1] += $currentVector[1] * $i;
			$currentPosition = implode(',',$currentPosition);
			
			$this->database->query("INSERT INTO race_actions(raceaction_race, raceaction_user, raceaction_action, raceaction_parameter) VALUES('" . $raceid . "', '" . $player . "', '1', '" . $currentPosition . "')");
			$sql .= "('" . $raceid . "','" . $player . "'),";
			$i++;
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

		// copy race actions
		$sql = "INSERT INTO race_actions_archive (SELECT * FROM race_actions WHERE raceaction_race='" . $raceid . "')";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not end game: could not archive action data', 'warning');
			$this->messages->setMessage('Could not end game: could not archive action data', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// copy race users
		$sql = "INSERT INTO race_to_users_archive (SELECT * FROM race_to_users WHERE raceuser_race='" . $raceid . "')";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not end game: race data could not be archived', 'warning');
			$this->messages->setMessage('Could not end game: race data could not be archived', 'warning');
			$this->debug->unguard(false);
			return false;
		}		

		// delete race data
		$sql = "DELETE FROM races WHERE race_id='" . $raceid . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not end game: race data could not be erased', 'warning');
			$this->messages->setMessage('Could not end game: race data could not be erased', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// delete race actions
		$sql = "DELETE FROM race_actions WHERE raceaction_race='" . $raceid . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not end game: race actions could not be erased', 'warning');
			$this->messages->setMessage('Could not end game: race actions could not be erased', 'warning');
			$this->debug->unguard(false);
			return false;
		}		

		// delete race users
		$sql = "DELETE FROM race_to_users WHERE raceuser_race='" . $raceid . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not end game: race users could not be erased', 'warning');
			$this->messages->setMessage('Could not end game: race users could not be erased', 'warning');
			$this->debug->unguard(false);
			return false;
		}	
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	// TODO: Punkteverteilung
	// TODO: Achievements		
	// TODO: Finish!
	public function assessRace($raceid)
	{
		$this->debug->guard();
		
		$raceGamestates = array();

		// get race data from database
		$sql = "SELECT * FROM races_archive r LEFT JOIN circuits c ON r.race_circuit = c.circuit_id WHERE r.race_id='" . $raceid . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);
		if(!$row)
		{
			$this->debug->write('Could not assess race: no race information found', 'warning');
			$this->messages->setMessage('Could not assess race: no race information found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// get player data from database
		$sqlPlayers = "SELECT * FROM race_to_users_archive WHERE raceuser_race='" . $raceid . "'";
		$resPlayers = $this->database->query($sqlPlayers);
		while ($rowPlayers = $this->database->fetchArray($resPlayers))
		{
			$raceGamestates['players'][] = $rowPlayers['raceuser_user'];
		}
		$raceGamestates['numPlayers'] = count($raceGamestates['players']);

		// fill structure
		$raceGamestates['currentRace'] = $raceid;
		$raceGamestates['currentCircuit'] = $row['race_circuit'];
		$raceGamestates['currentRound'] = $row['race_currentround'];
		$raceGamestates['currentPlayer'] = $row['race_activeplayer'];
		$raceGamestates['currentRadius'] = $this->configuration->getConfiguration('gamedefinitions', 'gamelogic', 'movementradius');

		// get raceaction from database
		$sql = "SELECT * FROM race_actions WHERE raceaction_race='" . $raceid . "' ORDER BY raceaction_id";
		$res = $this->database->query($sql);

		// get player data
		$raceGamestates['playerdata'] = array();
		while ($row = $this->database->fetchArray($res))
		{
			// get all moves
			if ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'move'))
			{
				$position = explode(',',$row['raceaction_parameter']);
				$raceGamestates['playerdata'][$row['raceaction_user']]['moves'][] = array($row['raceaction_action'], $row['raceaction_parameter']);
			}

			// get checkpoints
			if ( ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'checkpoint1'))
			|| ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'checkpoint2'))
			|| ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'checkpoint3')) )
			{
				$raceGamestates['playerdata'][$row['raceaction_user']]['checkpoints'][$row['raceaction_parameter']] = true;
			}

			// see if player is finished
			if ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'finish'))
			{
				$raceGamestates['playerdata'][$row['raceaction_user']]['finished'] = true;
			}
		}
		
		$this->debug->unguard(true);
		return true;
	}


	public function getRaceID()
	{
		$this->debug->guard();
		
		$sql = "SELECT raceuser_race FROM race_to_users WHERE raceuser_user='" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);
		if (empty($row['raceuser_race']))
		{
			$this->debug->write('Could not get race id: no race found for current player', 'warning');
			$this->messages->setMessage('Could not get race id: no race found for current player', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($row['raceuser_race']);
		return $row['raceuser_race'];
	}


}
?>
