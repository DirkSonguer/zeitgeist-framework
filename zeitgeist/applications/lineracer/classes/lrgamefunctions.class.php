<?php

defined('LINERACER_ACTIVE') or die();

class lrGamefunctions
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


	public function playGamecard($gamecard)
	{
		$this->debug->guard();
		
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not play gamecard: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not play gamecard: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$this->validateTurn())
		{
			$this->debug->write('Could not play gamecard: it is another players turn', 'warning');
			$this->messages->setMessage('Could not play gamecard: it is another players turn', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$gamecardfunctions = new lrGamecardfunctions();
		if (!$gamecardfunctions->checkRights($gamecard, $currentGamestates['activePlayer']))
		{
			$this->debug->write('Could not play gamecard: no rights to play gamecard', 'warning');
			$this->messages->setMessage('Could not play gamecard: no rights to play gamecard', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$gamecardData = $gamecardfunctions->getGamecardData($gamecard))
		{
			$this->debug->write('Could not play gamecard: gamecard not found', 'warning');
			$this->messages->setMessage('Could not play gamecard: gamecard not found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// TODO: 3 = gamecard. use constant
		$this->_saveGamestates('3', $gamecard);
		
		// TODO: player, round
		$gameeventhandler = new lrGameeventhandler();
		$gameeventhandler->saveRaceevent('1', $gamecard, $currentGamestates['activePlayer']+$gamecardData['gamecard_roundoffset']);
		$gamecardfunctions->redeemGamecard($gamecard);

		$this->debug->unguard(true);
		return true;
	}


	public function loadGamestates($raceid)
	{
		$this->debug->guard();
		
		$this->currentRace = $raceid;
		$currentGamedata = array();

		// get race data from database
		$sql = "SELECT * FROM races r LEFT JOIN circuits c ON r.race_circuit = c.circuit_id WHERE r.race_id='" . $raceid . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);
		
		// fill structure
		$this->currentCircuit = $row['race_circuit'];
		$this->currentRound = $row['race_currentround'];
		$currentGamedata['activePlayer'] = $row['race_activeplayer'];
		$currentGamedata['numPlayers'] = 0;
		if ($row['race_player4'] != '') $currentGamedata['numPlayers'] = 4;
		elseif ($row['race_player3'] != '') $currentGamedata['numPlayers'] = 3;
		elseif ($row['race_player2'] != '') $currentGamedata['numPlayers'] = 2;
		else $currentGamedata['numPlayers'] = 1;

		// get moves from database
		$sql = "SELECT * FROM race_moves WHERE move_race='" . $raceid . "' ORDER BY move_id";
		$res = $this->database->query($sql);

		$currentGamedata['playerdata'] = array();
		while($row = $this->database->fetchArray($res))
		{
			$position = explode(',',$row['move_parameter']);
			$currentGamedata['playerdata'][$row['move_user']]['moves'][] = array($row['move_action'], $row['move_parameter']);
		}

		// temp storing gamedata
		$this->objects->storeObject('currentGamedata', $currentGamedata, true);
		
		// get vectors
		for ($i=1; $i<=$currentGamedata['numPlayers']; $i++)
		{
			if (count($this->getMovement($currentGamedata['activePlayer'])) > 1)
			{
				$lastMove = $this->getMovement($i,-1);
				$moveBefore = $this->getMovement($i,-2);
				$currentGamedata['playerdata'][$i]['vector'][0] = $lastMove[0] - $moveBefore[0];
				$currentGamedata['playerdata'][$i]['vector'][1] = $lastMove[1] - $moveBefore[1];
			}
			else
			{
				$currentGamedata['playerdata'][$i]['vector'][0] = 0;
				$currentGamedata['playerdata'][$i]['vector'][1] = 0;
			}
		}
		
		// done loading
		$this->objects->storeObject('currentGamedata', $currentGamedata, true);
		
		// handle current game events and update the gamestates
		$gameeventhandler = new lrGameeventhandler();
		$gameeventhandler->handleRaceevents();

		$this->debug->unguard(true);
		return true;
	}
	
	
	public function getGamestates()
	{
		$this->debug->guard();
		
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not get gamestates: gamestates not loaded yet', 'warning');
			$this->messages->setMessage('Could not get gamestates: gamestates not loaded yet', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($currentGamestates);
		return $currentGamestates;
	}
	

	protected function _saveGamestates($action, $parameter)
	{
		$this->debug->guard();

		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('" . $raceid . "', '" . $player . "', '" . $action . "', '" . $parameter . "')";
		$res = $this->database->query($sql);

		if ($action == 1)
		{
			$sql = "DELETE FROM race_eventhandler WHERE raceevent_race='" . $this->currentRace . "' AND raceevent_player='" . $currentGamestates['activePlayer'] . "' AND raceevent_round='" . $this->currentRound . "'";
			$res = $this->database->query($sql);

			$player++;
			if ($player > $currentGamestates['numPlayers'])
			{
				$player = 1;
				$this->currentRound += 1;
			}

			$currentround = ", race_currentround='" . $this->currentRound . "'";			
			$sql = "UPDATE races SET race_activeplayer='" . $player . "'" . $currentround . "  WHERE race_id='" . $raceid . "'";
			$res = $this->database->query($sql);
		}
		
		$this->debug->unguard(true);
		return true;
	}
	

}

?>
