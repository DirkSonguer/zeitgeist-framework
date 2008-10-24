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
		$this->objects = zgObjectcache::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function loadGamestates($raceid)
	{
		$this->debug->guard();

		$movementfunctions = new lrMovementfunctions();
		
		$this->currentRace = $raceid;
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

		// fill structure
		$currentGamestates['currentRace'] = $raceid;
		$currentGamestates['currentCircuit'] = $row['race_circuit'];
		$currentGamestates['currentRound'] = $row['race_currentround'];
		$currentGamestates['currentPlayer'] = $row['race_activeplayer'];
		$currentGamestates['numPlayers'] = 0;
		if ($row['race_player4'] != '') $currentGamestates['numPlayers'] = 4;
		elseif ($row['race_player3'] != '') $currentGamestates['numPlayers'] = 3;
		elseif ($row['race_player2'] != '') $currentGamestates['numPlayers'] = 2;
		else $currentGamestates['numPlayers'] = 1;

		// get moves from database
		$sql = "SELECT * FROM race_moves WHERE move_race='" . $raceid . "'AND move_action='1' ORDER BY move_id";
		$res = $this->database->query($sql);

		$currentGamestates['playerdata'] = array();
		while($row = $this->database->fetchArray($res))
		{
			$position = explode(',',$row['move_parameter']);
			$currentGamestates['playerdata'][$row['move_user']]['moves'][] = array($row['move_action'], $row['move_parameter']);
		}
		
		// temp storing gamedata
		$this->objects->storeObject('currentGamestates', $currentGamestates, true);
		
		// get vectors
		for ($i=1; $i<=$currentGamestates['numPlayers']; $i++)
		{
			if (count($movementfunctions->getMovement()) > 1)
			{
				$lastMove = $movementfunctions($i,-1);
				$moveBefore = $movementfunctions($i,-2);
				$currentGamestates['playerdata'][$i]['vector'][0] = $lastMove[0] - $moveBefore[0];
				$currentGamestates['playerdata'][$i]['vector'][1] = $lastMove[1] - $moveBefore[1];
			}
			else
			{
				$currentGamestates['playerdata'][$i]['vector'][0] = 0;
				$currentGamestates['playerdata'][$i]['vector'][1] = 0;
			}
		}
		
		// done loading
		$this->objects->storeObject('currentGamestates', $currentGamestates, true);
		
		// handle current game events and update the gamestates
		$gameeventhandler = new lrGameeventhandler();
//		$gameeventhandler->handleRaceevents();

		$this->debug->unguard(true);
		return true;
	}
	
	
	public function saveGameaction($action, $parameter)
	{
		$this->debug->guard();

		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not save gameaction: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not save gameaction: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('" . $currentGamestates['currentRace'] . "', '" . $currentGamestates['currentPlayer'] . "', '" . $action . "', '" . $parameter . "')";
		$res = $this->database->query($sql);

		if ($action == 1)
		{
			$sql = "DELETE FROM race_eventhandler WHERE raceevent_race='" . $currentGamestates['currentRace'] . "' AND raceevent_player='" . $currentGamestates['currentPlayer'] . "' AND raceevent_round='" . $currentGamestates['currentRound'] . "'";
			$res = $this->database->query($sql);

			$currentGamestates['currentPlayer'] += 1;
			if ($currentGamestates['currentPlayer'] > $currentGamestates['numPlayers'])
			{
				$currentGamestates['currentPlayer'] = 1;
				$currentGamestates['$currentRound'] += 1;
			}

			$currentround = ", race_currentround='" . $currentGamestates['$currentRound'] . "'";			
			$sql = "UPDATE races SET race_activeplayer='" . $currentGamestates['currentPlayer'] . "'" . $currentround . "  WHERE race_id='" . $currentGamestates['currentRace'] . "'";
			$res = $this->database->query($sql);
		}
		
		$this->objects->storeObject('currentGamestates', $currentGamestates, true);
		
		$this->debug->unguard(true);
		return true;
	}
	

}

?>
