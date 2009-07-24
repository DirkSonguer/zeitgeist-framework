<?php

defined('LINERACER_ACTIVE') or die();

class lrGameeventhandler
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
	 * This stores a given game action to the database
	 * Only stores actions for the current player
	 *
	 * @param integer $action action to store
	 * @param integer $parameter parameter of the action
	 *
	 * @return boolean
	 */
	public function saveRaceaction($action, $parameter)
	{
		$this->debug->guard();

		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could save race event: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could save race event: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES('" . $currentGamestates['race']['currentRace'] . "', '" . $currentGamestates['round']['currentPlayer'] . "', '" . $action . "', '" . $parameter . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could save race action: could not insert action into database', 'warning');
			$this->messages->setMessage('Could save race action: could not insert action into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->debug->unguard(true);
		return true;
	}
	

	/**
	 * Saves a raceevent to the stack
	 * If not round is given, the current round will be used
	 * If target player is < current player, the next round will be used
	 *
	 * @param integer $player id of the player that the event concerns
	 * @param integer $action id of the action that should be executed
	 * @param integer $gparameter the parameter/s of the action
	 * @param integer $round this is the offset, in how many rounds the event should be handled
	 *
	 * @return boolean
	 */
	public function saveRaceevent($player, $action, $parameter, $round=0)
	{
		$this->debug->guard();

		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could save race event: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could save race event: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if ($round == 0) $round = $currentGamestates['round']['currentRound'];
		if ($player < $currentGamestates['round']['currentPlayer']) $round += 1;

		$sql  = "INSERT INTO race_events(raceevent_race, raceevent_round, raceevent_action, raceevent_parameter, raceevent_player) VALUES('" . $currentGamestates['race']['currentRace'] . "', '" . $round . "', '" . $action . "', '" . $parameter . "', '" . $player . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could save race event: could not write data into database', 'warning');
			$this->messages->setMessage('Could save race event: could not write data into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Handles all race events
	 *
	 * @return boolean
	 */
	public function handleRaceeevents()
	{
		$this->debug->guard();
		
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not handle race events: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not handle race events: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// get all events for the active player and the current round
		$sql = "SELECT raceevent_action, raceevent_parameter FROM race_events WHERE raceevent_race='" . $currentGamestates['race']['currentRace'] . "' AND raceevent_round='" . $currentGamestates['round']['currentRound'] . "' AND raceevent_player='" . $currentGamestates['round']['currentPlayer'] . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not handle race events: could not load events from the database', 'warning');
			$this->messages->setMessage('Could not handle race events: could not load events from the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// extract event data
		$activeevents = array();
		while ($row = $this->database->fetchArray($res))
		{
			$activeevents[$row['raceevent_action']] = $row['raceevent_parameter'];
		}

		foreach ($activeevents as $event => $parameter)
		{
			// TODO: Was ist, wenn ein Spieler eine Karte zweimal ausspielt?
			// gamecard was played
			if ($event == $this->configuration->getConfiguration('gamedefinitions', 'events', 'playgamecard'))
			{
				//check if the gamecard exists
				$gamecardfunctions = new lrGamecardfunctions();
				if (!$gamecardData = $gamecardfunctions->getGamecardData($parameter))
				{
					$this->debug->write('Could not handle race events: gamecard data was not found', 'warning');
					$this->messages->setMessage('Could not handle race events: gamecard data was not found', 'warning');
					$this->debug->unguard(false);
					return false;
				}
				
				$gamecardClassname = $gamecardData['gamecard_classname'];
				if (!class_exists($gamecardClassname, true))
				{
					$this->debug->write('Could not handle race events: gamecard class was not found: '.$gamecardClassname, 'warning');
					$this->messages->setMessage('Could not handle race events: gamecard class was not found: '.$gamecardClassname, 'warning');
					$this->debug->unguard(false);
					return false;
				}

				// load the module class through the autoloader
				$gamecardClass = new $gamecardClassname;
				$ret = call_user_func(array(&$gamecardClass, 'execute'));
			}

			// player crashed
			if ($event == $this->configuration->getConfiguration('gamedefinitions', 'events', 'crash'))
			{
				$currentGamestates['playerdata'][$currentGamestates['round']['currentPlayer']]['vector'][0] = 0;
				$currentGamestates['playerdata'][$currentGamestates['round']['currentPlayer']]['vector'][1] = 0;

				$this->objects->storeObject('currentGamestates', $currentGamestates, true);
			}
		}

		$this->debug->unguard(true);
		return true;		
	}

}

?>
