<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Gamehandler class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST GAMEHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgGamehandler
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;


	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * This stores a given game action to the database
	 * An action is always something a player DID
	 * To store events that a player will do in the future
	 * (timed events), store a game event
	 *
	 * @param integer $action action to store
	 * @param integer $parameter parameter of the action
	 * @param integer $player player to store the action for
	 *
	 * @return boolean
	 */
	public function saveGameaction($action, $parameter, $player)
	{
		$this->debug->guard();

		$sql = "INSERT INTO game_actions(action_id, action_parameter, action_player) ";
		$sql .= "VALUES('" . $action . "', '" . $parameter . "', '" . $player . "')";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could save game action: could not insert action into database', 'warning');
			$this->messages->setMessage('Could game race action: could not insert action into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}
	

	/**
	 * Saves a game event to the event handler
	 * The time offset defines the time when the event will be triggered
	 * Note that the timescale has to be defined by the game applicationm
	 * This can be seconds, ticks, rounds, depending on your game
	 *
	 * @param integer $action id of the action that should be executed
	 * @param integer $parameter the parameter/s of the action
	 * @param integer $player id of the player that the event concerns
	 * @param integer $time this is the time when the event should be handled
	 *
	 * @return boolean
	 */
	public function saveRaceevent($action, $parameter, $player, $time=0)
	{
		$this->debug->guard();

		$sql  = "INSERT INTO game_events(event_action, event_parameter, event_player, event_time) ";
		$sql .= "VALUES('" . $action . "', '" . $parameter . "', '" . $player . "', '" . $time . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could save game event: could not write data into database', 'warning');
			$this->messages->setMessage('Could save game event: could not write data into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Handles all race events
	 * The time define the upper limit for the events to be executed
	 * If a player id is given, only the events for the respective player
	 * will be executed
	 *
	 * @param integer $time the current time
	 * @param integer $player id of the player to execute events for
	 * 
	 * @return boolean
	 */
	public function handleRaceeevents($time, $player=0)
	{
		$this->debug->guard();
		
		// get all events for the active player and the current round
		$sql = "SELECT event_action, event_parameter FROM game_events WHERE event_time<='" . $time . "'";
		if ($player > 0)
		{
			$sql .= " AND event_player='" . $player . "'";
		}
		$sql .= " ORDER BY event_time ASC";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not handle game events: could not load events from the database', 'warning');
			$this->messages->setMessage('Could not handle game events: could not load events from the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// extract event data
		$activeevents = array();
		while ($row = $this->database->fetchArray($res))
		{
			$event = array();
			$event['action'] = $row['event_action'];
			$event['parameter'] = $row['event_action'];
			$event['time'] = $row['event_action'];
			$activeevents[] = $event;
		}

		foreach ($activeevents as $event)
		{
			//check if the event exists and what function to call				
			$gamecardClassname = $event['even_action'];
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

		$this->debug->unguard(true);
		return true;		
	}

}

?>