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
		$this->objects = zgObjectcache::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


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
		
		if ($round == 0) $round = $currentGamestates['currentRound'];

		$sql  = "INSERT INTO race_eventhandler(raceevent_race, raceevent_round, raceevent_action, raceevent_parameter, raceevent_player) VALUES('" . $currentGamestates['currentRace'] . "', '" . $round . "', '" . $action . "', '" . $parameter . "', '" . $player . "')";
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


	public function handleRaceevents()
	{
		$this->debug->guard();

		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not handle race events: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not handle race events: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "SELECT * FROM race_eventhandler WHERE raceevent_race='" . $currentGamestates['currentRace'] . "' AND raceevent_round='" . $currentGamestates['currentRound'] . "' AND raceevent_player='" . $currentGamestates['currentPlayer'] . "'";
		$res = $this->database->query($sql);
		
		$activeevents = array();
		while ($row = $this->database->fetchArray($res))
		{
			$activeevents[$row['raceevent_action']] = $row['raceevent_parameter'];
		}

		foreach ($activeevents as $action => $parameter)
		{
			
			if ($action == $this->configuration->getConfiguration('gamedefinitions', 'events', 'playgamegard'))
			{
				//check if the gamecard exists
				$gamecardClassname = $this->configuration->getConfiguration('gamedefinitions', 'gamecards', $parameter);
				if (!class_exists($gamecardClassname, true))
				{
					$this->debug->write('Could not handle race events: gamecard class was not found: '.$gamecardClassname, 'warning');
					$this->messages->setMessage('Could not handle race events: gamecard class was not found: '.$gamecardClassname, 'warning');
					$this->debug->unguard(false);
					return false;
				}

				// load the module class through the autoloader
				$gamecardClass = new $gamecardClassname;
				$ret = call_user_func(array(&$gamecardClass, execute));
			}

			if ($action == $this->configuration->getConfiguration('gamedefinitions', 'events', 'crash'))
			{
				$this->currentGamestates['playerdata'][$this->currentGamestates['currentPlayer']]['vector'][0] = 0;
				$this->currentGamestates['playerdata'][$this->currentGamestates['currentPlayer']]['vector'][1] = 0;
			}
		}

		$this->debug->unguard(true);
		return true;		
	}

}

?>
