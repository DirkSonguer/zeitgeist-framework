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
	public function saveGameevent($game=0, $action, $parameter, $player, $time=0)
	{
		$this->debug->guard();

		$sql  = "INSERT INTO game_events(event_game, event_action, event_parameter, event_player, event_time) ";
		$sql .= "VALUES('" . $game . "', '" . $action . "', '" . $parameter . "', '" . $player . "', '" . $time . "')";
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
	 * Removes a given race event from the event log
	 *
	 * @param integer $event id of the event to remove
	 *
	 * @return boolean
	 */
	public function removeGameevent($event)
	{
		$this->debug->guard();

		$sql  = "DELETE FROM game_events WHERE event_id='" . $event . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could remove game event: could not remove data from database', 'warning');
			$this->messages->setMessage('Could remove game event: could not remove data from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * This stores a given game event to the eventlog.
	 * The event will be taken from the event table (and deleted there).
	 *
	 * @param integer $event id of the event in the event table
	 *
	 * @return boolean
	 */
	public function logGameevent($event)
	{
		$this->debug->guard();

		$sql = "INSERT INTO game_eventlog(eventlog_game, eventlog_action, eventlog_parameter, eventlog_player, eventlog_time) ";
		$sql .= "SELECT event_game, event_action, event_parameter, event_player, event_time FROM game_events WHERE event_id='" . $event . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not store game event to log: could not insert event data into database', 'warning');
			$this->messages->setMessage('Could not store game event to log: could not insert event data into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM game_events WHERE event_id='" . $event . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not store game event to log: could not delete event data from event table', 'warning');
			$this->messages->setMessage('Could not store game event to log: could not delete event data from event table', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->removeGameevent($event);

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
	public function handleGameevents($game, $time, $player=0)
	{
		$this->debug->guard();

		// get all events for the active player and the current round
		$sql = "SELECT ge.event_id, ga.action_class, ge.event_parameter FROM game_events ge ";
		$sql .= "LEFT JOIN game_actions ga ON ge.event_action = ga.action_id ";
		$sql .= "WHERE ge.event_game = '" . $game . "' AND ge.event_time <= '" . $time . "'";
		if ($player > 0)
		{
			$sql .= " AND ge.event_player='" . $player . "'";
		}
		$sql .= " ORDER BY ge.event_time ASC";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not handle game events: could not load events from the database', 'warning');
			$this->messages->setMessage('Could not handle game events: could not load events from the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// execute event data
		while ($event = $this->database->fetchArray($res))
		{
			//check if the event exists and what function to call				
			if ( (empty($event['action_class'])) || (!class_exists($event['action_class'], true)) )
			{
				$this->debug->write('Could not handle game event: event class was not found: '.$event['action_class'], 'warning');
				$this->messages->setMessage('Could not handle game event: event class was not found: '.$event['action_class'], 'warning');
				$this->debug->unguard(false);
				return false;
			}

			// load the module class through the autoloader
			$eventClass = new $event['action_class'];

			call_user_func(array(&$eventClass, 'execute'), $event['event_parameter'], $time);
			$this->logGameevent($event['event_id']);
		}

		$this->debug->unguard(true);
		return true;		
	}

}

?>