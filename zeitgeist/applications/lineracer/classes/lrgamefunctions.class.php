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
	protected $lruser;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->session = zgSession::init();
		$this->configuration = zgConfiguration::init();
		$this->objects = zgObjects::init();
		$this->user = zgUserhandler::init();
		
		$this->lruser = new lrUserfunctions();

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
		$currentGamestates = $gamestates->loadGamestates();
		
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
		if (!$this->lruser->validateTurn())
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
		$this->endTurn();

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
		$currentGamestates = $gamestates->loadGamestates();
	
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
		if (!$this->lruser->validateTurn())
		{
			$this->debug->write('Could not move player: not the players turn', 'warning');
			$this->messages->setMessage('Could not move player: not the players turn', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// validate if the player has the right for the gamecard
		$gamecardfunctions = new lrGamecardfunctions();
		if (!$gamecardfunctions->checkRights($gamecard))
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
		$gameeventhandler->saveRaceevent($currentGamestates['round']['currentPlayer'], $this->configuration->getConfiguration('gamedefinitions', 'events', 'playgamecard'), $gamecard, ($currentGamestates['round']['currentRound']+$gamecardData['gamecard_roundoffset']));
		$gamecardfunctions->removeGamecard($gamecard, $currentGamestates['race']['players'][$currentGamestates['round']['currentPlayer']]);
		
		$this->debug->unguard(true);
		return true;
	}


	public function forfeit()
	{
		$this->debug->guard();
		
		// load classes
		$gameeventhandler = new lrGameeventhandler();
		$gamestates = new lrGamestates();
		
		// save race action and handle post turn events
		$gameeventhandler->saveRaceaction($this->configuration->getConfiguration('gamedefinitions', 'actions', 'forfeit'), '1');
		$this->endTurn();
		
		$this->lruser->insertTransaction($this->configuration->getConfiguration('gamedefinitions', 'transaction_types', 'race_forfeit'), 1);

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Ends the turn of the current player and switches to next one
	 *
	 * @return boolean
	 */
	public function endTurn()
	{
		$this->debug->guard();

		// load current gamestates
		$gamestates = new lrGamestates();
		$currentGamestates = $gamestates->loadGamestates();

		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not switch to next player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not switch to next player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// delete all race events for the finished turn
		$sql = "DELETE FROM race_events WHERE raceevent_race='" . $currentGamestates['race']['currentRace'] . "' AND raceevent_player='" . $currentGamestates['round']['currentPlayer'] . "' AND raceevent_round='" . $currentGamestates['round']['currentRound'] . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not end turn: could not delete race events', 'warning');
			$this->messages->setMessage('Could not end turn: could not delete race events', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// find next player that is not finished
		$playerFound = false;
		if (!$gamestates->raceFinished())
		{
			while (!$playerFound)
			{
				$this->debug->write('current player: '.$currentGamestates['round']['currentPlayer'], 'warning');

				// next user
				$currentGamestates['round']['currentPlayer'] += 1;

				// switch to next round if needed
				if ($currentGamestates['round']['currentPlayer'] > $currentGamestates['race']['numPlayers'])
				{
					$currentGamestates['round']['currentPlayer'] = 1;
					$currentGamestates['round']['currentRound'] += 1;
				}

				// found a player
				if (!$gamestates->playerFinished($currentGamestates['round']['currentPlayer']))
				{
					$playerFound = true;
				}
			}
		}

		// insert new data into into database
		$sql = "UPDATE races SET race_activeplayer='" . $currentGamestates['round']['currentPlayer'] . "', race_currentround='" . $currentGamestates['round']['currentRound'] . "' WHERE race_id='" . $currentGamestates['race']['currentRace'] . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not end turn: could not insert new race data', 'warning');
			$this->messages->setMessage('Could not end turn: could not insert new race data', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function startRace()
	{
		$this->debug->guard();

		// get lobby of the user
		$lobbyid = $this->lruser->getUserLobby();
		if (!$lobbyid)
		{
			$this->debug->write('Could not start race: no active lobby for player found', 'warning');
			$this->messages->setMessage('Could not start race: no active lobby for player found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

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

		// get race id as we need it
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
		$sql = 'INSERT INTO race_to_users(raceuser_race, raceuser_user, raceuser_order) VALUES';
		foreach ($gameusers as $player)
		{
			// insert player into race
			$sql .= "('" . $raceid . "','" . $player . "', '" . ($i+1) . "'),";

			// fill initial position
			$currentPosition = explode(',',$rowCircuit['circuit_startposition']);
			$currentVector = explode(',',$rowCircuit['circuit_startvector']);
			$currentPosition[0] += $currentVector[0] * $i;
			$currentPosition[1] += $currentVector[1] * $i;
			$currentPosition = implode(',',$currentPosition);
			$this->database->query("INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES('" . $raceid . "', '" . ($i+1) . "', '1', '" . $currentPosition . "')");

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

		$this->lruser->insertTransaction($this->configuration->getConfiguration('gamedefinitions', 'transaction_types', 'race_start'), 1);
		
		$this->debug->unguard($raceid);
		return $raceid;
	}


	// TODO: function
	public function startDemoRace()
	{
		$this->debug->guard();

		if(!$this->user->isLoggedIn())
		{
			$this->debug->write('Could not start demo game: player is not logged in', 'warning');
			$this->messages->setMessage('Could not start demo game: player is not logged in', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// store new race data
		$sql = "INSERT INTO races(race_circuit, race_activeplayer, race_currentround, race_gamecardsallowed, race_demo) ";
		$sql .= "VALUES('1', '1', '1', '1', '1')";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not start demo game: could not write race data to database', 'warning');
			$this->messages->setMessage('Could not start demo game: could not write race data to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// get race id as we need it
		$raceid = $this->database->insertId();
		if(!$raceid)
		{
			$this->debug->write('Could not start demo game: could not get race id from database', 'warning');
			$this->messages->setMessage('Could not start demo game: could not get race id from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// get associated circuit data
		$sql = "SELECT * FROM circuits WHERE circuit_id = '1'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not start game: could not find the circuit data', 'warning');
			$this->messages->setMessage('Could not start game: could not find the circuit data', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		$rowCircuit = $this->database->fetchArray($res);

		$sql = 'INSERT INTO race_to_users(raceuser_race, raceuser_user, raceuser_order) VALUES';
		$sql .= "('" . $raceid . "','" . $this->user->getUserID() . "', '1')";

		// fill initial position
		$currentPosition = explode(',',$rowCircuit['circuit_startposition']);
		$currentVector = explode(',',$rowCircuit['circuit_startvector']);
		$currentPosition[0] += $currentVector[0];
		$currentPosition[1] += $currentVector[1];
		$currentPosition = implode(',',$currentPosition);
		$this->database->query("INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES('" . $raceid . "', '1', '1', '" . $currentPosition . "')");

		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could not start demo game: could not insert player data', 'warning');
			$this->messages->setMessage('Could not start demo game: could not insert player data', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->lruser->insertTransaction($this->configuration->getConfiguration('gamedefinitions', 'transaction_types', 'demo_start'), 1);
		
		$this->debug->unguard($raceid);
		return $raceid;
	}


	public function archiveRace()
	{
		$this->debug->guard();

		// get race of the user
		$raceid = $this->lruser->getUserRace();
		if (!$raceid)
		{
			$this->debug->write('Could not end race: no active race for player found', 'warning');
			$this->messages->setMessage('Could not end race: no active race for player found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

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
	// TODO: Finish!
	// TODO: Korrekt erstellen
	public function assessRace()
	{
		$this->debug->guard();

		// get race of the user
		$raceid = $this->lruser->getUserRace();
		if (!$raceid)
		{
			$this->debug->write('Could not end race: no active race for player found', 'warning');
			$this->messages->setMessage('Could not end race: no active race for player found', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$achievements = new lrAchievements();
		$ret = $achievements->assessAchievements();
		if(!$ret)
		{
			$this->debug->write('Could not assess race: could not assess achievements', 'warning');
			$this->messages->setMessage('Could not assess race: could not assess achievements', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// done, now set the race as assessed
		$sqlUpdate = "UPDATE race_to_users SET raceuser_assessed = '1' WHERE raceuser_race='" . $raceid . "'";
		$resUpdate = $this->database->query($sqlUpdate);
		if (!$resUpdate)
		{
			$this->debug->write('Could not assess achievements: could not update game assesment information', 'warning');
			$this->messages->setMessage('Could not assess achievements: could not update game assesment information', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function endRace()
	{
		$this->debug->guard();

		// get race of the user
		$raceid = $this->lruser->getUserRace();
		if (!$raceid)
		{
			$this->debug->write('Could not end race: no active race for player found', 'warning');
			$this->messages->setMessage('Could not end race: no active race for player found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->lruser->insertTransaction($this->configuration->getConfiguration('gamedefinitions', 'transaction_types', 'race_finished'), 1);

		$this->debug->unguard(true);
		return true;
	}

}
?>
