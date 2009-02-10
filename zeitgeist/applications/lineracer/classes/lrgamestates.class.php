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
	 * Loads all the information associated with a running game
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
		$sql = "SELECT * FROM races r LEFT JOIN circuits c ON r.race_circuit = c.circuit_id WHERE r.race_id='" . $raceid . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);
		if(!$row)
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
			$currentGamestates['players'][$rowPlayers['raceuser_order']] = $rowPlayers['raceuser_user'];
		}
		$currentGamestates['numPlayers'] = count($currentGamestates['players']);

		// fill structure
		$currentGamestates['currentRace'] = $raceid;
		$currentGamestates['currentCircuit'] = $row['race_circuit'];
		$currentGamestates['currentRound'] = $row['race_currentround'];
		$currentGamestates['currentPlayer'] = $row['race_activeplayer'];
		$currentGamestates['currentRadius'] = $this->configuration->getConfiguration('gamedefinitions', 'gamelogic', 'movementradius');

		// get raceaction from database
		$sql = "SELECT * FROM race_actions WHERE raceaction_race='" . $raceid . "' ORDER BY raceaction_id";
		$res = $this->database->query($sql);

		// get player data
		$currentGamestates['playerdata'] = array();
		while ($row = $this->database->fetchArray($res))
		{
			// get all moves
			if ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'move'))
			{
				$position = explode(',',$row['raceaction_parameter']);
				$currentGamestates['playerdata'][$row['raceaction_user']]['moves'][] = array($row['raceaction_action'], $row['raceaction_parameter']);
			}

			// get checkpoints
			if ( ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'checkpoint1'))
			|| ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'checkpoint2'))
			|| ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'checkpoint3')) )
			{
				$currentGamestates['playerdata'][$row['raceaction_user']]['checkpoints'][$row['raceaction_parameter']] = true;
			}

			// see if player is finished
			if ($row['raceaction_action'] == $this->configuration->getConfiguration('gamedefinitions', 'actions', 'finish'))
			{
				$currentGamestates['playerdata'][$row['raceaction_user']]['finished'] = true;
			}
		}
		
		// temp storing gamedata
		$this->objects->storeObject('currentGamestates', $currentGamestates, true);
		
		// get vectors for each player
		foreach($currentGamestates['players'] as $player)
		{
			if (count($movementfunctions->getMovement($player)) > 1)
			{
				$lastMove = $movementfunctions->getMovement($player, -1);
				$moveBefore = $movementfunctions->getMovement($player, -2);
				$currentGamestates['playerdata'][$player]['vector'][0] = $lastMove[0] - $moveBefore[0];
				$currentGamestates['playerdata'][$player]['vector'][1] = $lastMove[1] - $moveBefore[1];
			}
			else
			{
				$currentGamestates['playerdata'][$player]['vector'][0] = 0;
				$currentGamestates['playerdata'][$player]['vector'][1] = 0;
			}
		}
		
		// store complete gamestates in object
		$this->objects->storeObject('currentGamestates', $currentGamestates, true);

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Switches to next player
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

		$sql = "DELETE FROM race_events WHERE raceevent_race='" . $currentGamestates['currentRace'] . "' AND raceevent_player='" . $currentGamestates['currentPlayer'] . "' AND raceevent_round='" . $currentGamestates['currentRound'] . "'";
		$res = $this->database->query($sql);

		$playerFound = false;
		if (!$this->raceFinished())
		{
			while (!$playerFound)
			{
				$this->debug->write('current player: '.$currentGamestates['currentPlayer'], 'warning');

				$currentGamestates['currentPlayer'] += 1;
				if ($currentGamestates['currentPlayer'] > $currentGamestates['numPlayers'])
				{
					$currentGamestates['currentPlayer'] = 1;
					$currentGamestates['currentRound'] += 1;
				}

				if (!$this->playerFinished($currentGamestates['currentPlayer']))
				{
					$playerFound = true;
				}
			}
		}

		$currentround = ", race_currentround='" . $currentGamestates['currentRound'] . "'";
		$sql = "UPDATE races SET race_activeplayer='" . $currentGamestates['currentPlayer'] . "'" . $currentround . "  WHERE race_id='" . $currentGamestates['currentRace'] . "'";
		$res = $this->database->query($sql);
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function endRace()
	{
		$this->debug->guard();
		
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
		
		$finished = true;
		for ($i=1; $i<=$currentGamestates['numPlayers']; $i++)
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
			$player = $currentGamestates['currentPlayer'];
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
