<?php

defined('LINERACER_ACTIVE') or die();

class lrGamestates
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
	 * Loads all the information associated with a given game
	 * Stores all the information in the objectcache in 'currentGamestates'
	 *
	 * @param integer $raceid id of the race
	 *
	 * @return boolean
	 */
	public function loadGamestates($raceid)
	{
		$this->debug->guard();

		$movementfunctions = new lrMovementfunctions();
		$currentGamestates = array();

		// get race data from database
		$sqlRacedata = "SELECT * FROM races r LEFT JOIN circuits c ON r.race_circuit = c.circuit_id WHERE r.race_id='" . $raceid . "'";
		$resRacedata = $this->database->query($sqlRacedata);
		$rowRacedata = $this->database->fetchArray($resRacedata);
		if(!$rowRacedata)
		{
			$this->debug->write('Could not load gamestates: no race information found', 'warning');
			$this->messages->setMessage('Could not load gamestates: no race information found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// get player data from database
		$sqlPlayers = "SELECT * FROM race_to_users WHERE raceuser_race='" . $raceid . "' ORDER BY raceuser_order";
		$resPlayers = $this->database->query($sqlPlayers);
		while ($rowPlayers = $this->database->fetchArray($resPlayers))
		{
			$currentGamestates['meta']['players'][$rowPlayers['raceuser_order']] = $rowPlayers['raceuser_user'];
		}
		$currentGamestates['meta']['numPlayers'] = count($currentGamestates['meta']['players']);

		// fill general info
		$currentGamestates['meta']['currentRace'] = $raceid;
		$currentGamestates['meta']['currentCircuit'] = $rowRacedata['race_circuit'];
		$currentGamestates['move']['currentRound'] = $rowRacedata['race_currentround'];
		$currentGamestates['move']['currentPlayer'] = $rowRacedata['race_activeplayer'];
		$currentGamestates['move']['currentRadius'] = $this->configuration->getConfiguration('gamedefinitions', 'gamelogic', 'movementradius');

		// get raceaction from database
		$sqlActions = "SELECT ra.*, r2u.raceuser_order FROM race_actions ra LEFT JOIN race_to_users r2u on ra.raceaction_user = r2u.raceuser_user WHERE ra.raceaction_race='" . $raceid . "' ORDER BY ra.raceaction_timestamp";
		$resActions = $this->database->query($sqlActions);

		// get player data
		while ($rowActions = $this->database->fetchArray($resActions))
		{
			// action is a move
			if ($rowActions['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'move'))
			{
				$currentGamestates['playerdata'][$rowActions['raceuser_order']]['moves'][] = $rowActions['raceaction_parameter'];
			}

			// action is a checkpoint
			if ( ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'checkpoint1'))
			|| ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'checkpoint2'))
			|| ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'checkpoint3')) )
			{
				$currentGamestates['playerdata'][$rowActions['raceuser_order']]['checkpoints'][$rowActions['raceaction_parameter']] = true;
			}

			// see if player is finished
			if ($rowActions['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'finish'))
			{
				$currentGamestates['playerdata'][$rowActions['raceuser_order']]['finished'] = true;
			}
		}
		
		// temp storing gamedata
		$this->objects->storeObject('currentGamestates', $currentGamestates, true);
		
		// get vectors for each player
		for($i=1; $i<=$currentGamestates['meta']['numPlayers']; $i++)
		{
			if (count($movementfunctions->getMovement($i)) > 1)
			{
				$lastMove = $movementfunctions->getMovement($i, -1);
				$moveBefore = $movementfunctions->getMovement($i, -2);
				$currentGamestates['playerdata'][$i]['vector'][0] = $lastMove[0] - $moveBefore[0];
				$currentGamestates['playerdata'][$i]['vector'][1] = $lastMove[1] - $moveBefore[1];
			}
			else
			{
				$currentGamestates['playerdata'][$i]['vector'][0] = 0;
				$currentGamestates['playerdata'][$i]['vector'][1] = 0;
			}
		}
		
		// store complete gamestates in object
		$this->objects->storeObject('currentGamestates', $currentGamestates, true);

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

		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not switch to next player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not switch to next player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// delete all race events for the finished turn
		$sql = "DELETE FROM race_events WHERE raceevent_race='" . $currentGamestates['meta']['currentRace'] . "' AND raceevent_player='" . $currentGamestates['meta']['players'][$currentGamestates['move']['currentPlayer']] . "' AND raceevent_round='" . $currentGamestates['move']['currentRound'] . "'";
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
		if (!$this->raceFinished())
		{
			while (!$playerFound)
			{
				$this->debug->write('current player: '.$currentGamestates['move']['currentPlayer'], 'warning');

				// next user
				// switch to next round if needed
				$currentGamestates['move']['currentPlayer'] += 1;
				if ($currentGamestates['move']['currentPlayer'] > $currentGamestates['meta']['numPlayers'])
				{
					$currentGamestates['move']['currentPlayer'] = 1;
					$currentGamestates['move']['currentRound'] += 1;
				}

				// found a player
				if (!$this->playerFinished($currentGamestates['move']['currentPlayer']))
				{
					$playerFound = true;
				}
			}
		}

		// insert new data into into database
		$sql = "UPDATE races SET race_activeplayer='" . $currentGamestates['move']['currentPlayer'] . "', race_currentround='" . $currentGamestates['move']['currentRound'] . "' WHERE race_id='" . $currentGamestates['meta']['currentRace'] . "'";
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
	

	/**
	 * Checks if race is finished by all players
	 *
	 * @return boolean
	 */
	public function raceFinished()
	{
		$this->debug->guard();

		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not save gameaction: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not save gameaction: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// iterate through all players and see if they're finished		
		$finished = true;
		for ($i=1; $i<=$currentGamestates['meta']['numPlayers']; $i++)
		{
			if (empty($currentGamestates['playerdata'][$i]['finished']))
			{
				$finished = false;
			}
		}

		$this->debug->unguard($finished);
		return $finished;
	}


	/**
	 * Checks if race is finished for the given player
	 * If no player is given, the current player is used
	 *
	 * @return boolean
	 */
	public function playerFinished($player=0)
	{
		$this->debug->guard();

		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not save gameaction: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not save gameaction: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if (!$player)
		{
			$player = $currentGamestates['meta']['currentPlayer'];
		}
		
		$finished = true;
		if (empty($currentGamestates['playerdata'][$player]['finished']))
		{
			$finished = false;
		}

		$this->debug->unguard($finished);
		return $finished;
	}

}

?>
