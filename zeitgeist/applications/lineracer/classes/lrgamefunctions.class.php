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

	protected $currentGamestates;
	protected $currentRace;
	protected $currentCircuit;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjectcache::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		
		$this->currentGamestates = false;
		$this->currentRace = false;
		$this->currentCircuit = false;

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function getGamestates()
	{
		$this->debug->guard();
		
		if (!is_array($this->currentGamestates))
		{
			$this->debug->write('Could not get gamestates: gamestates not loaded yet', 'warning');
			$this->messages->setMessage('Could not get gamestates: gamestates not loaded yet', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($this->currentGamestates);
		return $this->currentGamestates;
	}


	public function loadGamestates($raceid)
	{
		$this->debug->guard();
		
		$this->currentRace = $raceid;
		$currentGamedata = array();

		$sql = "SELECT * FROM races r LEFT JOIN circuits c ON r.race_circuit = c.circuit_id WHERE r.race_id='" . $raceid . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);
		
		$this->currentCircuit = $row['race_circuit'];

		$currentGamedata['activePlayer'] = $row['race_activeplayer'];
		$currentGamedata['numPlayers'] = 0;
		if ($row['race_player4'] != '') $currentGamedata['numPlayers'] = 4;
		elseif ($row['race_player3'] != '') $currentGamedata['numPlayers'] = 3;
		elseif ($row['race_player2'] != '') $currentGamedata['numPlayers'] = 2;
		else $currentGamedata['numPlayers'] = 1;

		$sql = "SELECT * FROM race_moves WHERE move_race='" . $raceid . "' ORDER BY move_id";
		$res = $this->database->query($sql);

		$currentGamedata['playerdata'] = array();
		while($row = $this->database->fetchArray($res))
		{
			$position = explode(',',$row['move_parameter']);
			$currentGamedata['playerdata'][$row['move_user']]['moves'][] = array($row['move_action'], $row['move_parameter']);
		}

		$this->currentGamestates = $currentGamedata;
		
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
		
		$this->currentGamestates = $currentGamedata;
		
		$this->_activateGamecards();

		$this->debug->unguard(true);
		return true;
	}
	
	
	public function move($moveX, $moveY)
	{
		$this->debug->guard();
		
		if ( (!$this->currentGamestates) || (!$this->currentRace) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if (!$this->validateTurn())
		{
			$this->debug->write('Could not move player: it is another players turn', 'warning');
			$this->messages->setMessage('Could not move player: it is another players turn', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$this->validateMove($moveX, $moveY))
		{
			$this->debug->write('Could not move player: player moved outside its allowed area', 'warning');
			$this->messages->setMessage('Could not move player: player moved outside its allowed area', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$correctedMove = array();
		if (!$correctedMove = $this->validateTerrain($moveX, $moveY))
		{
			$this->debug->write('Could not move player: validating line failed', 'warning');
			$this->messages->setMessage('Could not move player: validating line failed', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->_saveGamestates($this->currentRace, $this->currentGamestates['activePlayer'], '1', $correctedMove[0].','.$correctedMove[1]);
		if ($correctedMove[2] == 1) $this->_saveGamestates($this->currentRace, $this->currentGamestates['activePlayer'], '2', $correctedMove[0].','.$correctedMove[1]);

		$this->debug->unguard(true);
		return true;
	}
	
	
	public function playGamecard($gamecard)
	{
		$this->debug->guard();
		
		if ( (!$this->currentGamestates) || (!$this->currentRace) )
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
		
		$this->_saveGamestates($this->currentRace, $this->currentGamestates['activePlayer'], '3', $gamecard);
		
		// TODO: player
		$this->_saveGamecard($gamecard, $this->currentGamestates['activePlayer']);

		$this->debug->unguard(true);
		return true;
	}
	


	public function validateTurn()
	{
		$this->debug->guard();

		// $this->currentRace, $this->currentGamestates['activePlayer']
		// validate current user against player num
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function validateMove($moveX, $moveY)
	{
		$this->debug->guard();

		if ( (!$this->currentGamestates) || (!$this->currentRace) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$lastMove = $this->getMovement($this->currentGamestates['activePlayer'],-1);
		$currentVector = $this->currentGamestates['playerdata'][$this->currentGamestates['activePlayer']]['vector'];

		$minX = $lastMove[0]+$currentVector[0]-20;
		$maxX = $lastMove[0]+$currentVector[0]+20;
		$minY = $lastMove[1]+$currentVector[1]-20;
		$maxY = $lastMove[1]+$currentVector[1]+20;

		if ( ($moveX < $minX) || ($moveX > $maxX) || ($moveY < $minY) || ($moveY > $maxY) )
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function validateTerrain($moveX, $moveY)
	{
		$this->debug->guard();

		if ( (!$this->currentGamestates) || (!$this->currentRace) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$lastMove = $this->getMovement($this->currentGamestates['activePlayer'],-1);
		$fromX = $lastMove[0];
		$fromY = $lastMove[1];

		$terrain = array();
		$terrain = $this->_checkTerrainType($fromX, $fromY, $moveX, $moveY);

/*
		echo "from: ".$fromX.",".$fromY." to: ".$moveX.",".$moveY."<br />";
		foreach($terrain as $step)
		{
			echo $step[0];
		}
		echo "<br />";
*/

		$correctedMove = array();
		$correctedMove[0] = $moveX;
		$correctedMove[1] = $moveY;
		$correctedMove[2] = 0;

		foreach($terrain as $key => $step)
		{
//			echo "terrain: ".$step[0]." position: ".$step[1].",".$step[2]."<br />";

			// check for blocking terrain
			if ($step[0] == 0)
			{
				$corrected = array();
				if ($key > 0)
				{
					$correctedMove[0] = $terrain[$key][1] + (($terrain[$key-1][1] - $terrain[$key][1])*5);
					$correctedMove[1] = $terrain[$key][2] + (($terrain[$key-1][2] - $terrain[$key][2])*5);
				}
				else
				{
					$correctedMove[0] = $terrain[$key][1] + (($lastMove[0] - $terrain[$key][1])*5);
					$correctedMove[1] = $terrain[$key][2] + (($lastMove[1] - $terrain[$key][2])*5);
				}
				
				$correctedMove[2] = 1;
				
//				echo "move: ".$moveX.",".$moveY." hit at ".$terrain[$key][1].",".$terrain[$key][2]." corrected to: ".$correctedMove[0].",".$correctedMove[1]."<br />";

				// TODO: Vector auf 0,0
				break;
			}
		}
		
		$this->debug->unguard($correctedMove);
		return $correctedMove;
	}


	protected function _saveGamestates($raceid, $player, $action, $parameter)
	{
		$this->debug->guard();

		if ( (!$this->currentGamestates) || (!$this->currentRace) )
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
			$sql = "DELETE FROM gamecards_stack WHERE gamecardstack_race='" . $this->currentRace . "' AND gamecardstack_player='" . $this->currentGamestates['activePlayer'] . "'";
			$res = $this->database->query($sql);

			$player++;
			if ($player > $this->currentGamestates['numPlayers']) $player = 1;
			
			$sql = "UPDATE races SET race_activeplayer='" . $player . "'  WHERE race_id='" . $raceid . "'";
			$res = $this->database->query($sql);
		}
		
		$this->debug->unguard(true);
		return true;
	}


	protected function _saveGamecard($gamecard, $player)
	{
		$this->debug->guard();

		if (!$this->currentRace)
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "INSERT INTO gamecards_stack(gamecardstack_race, gamecardstack_gamecard, gamecardstack_player) VALUES('" . $this->currentRace . "', '" . $gamecard . "', '" . $player . "')";
		$res = $this->database->query($sql);

		$this->debug->unguard(true);
		return true;
	}


	protected function _checkTerrainType($fromX, $fromY, $toX, $toY)
	{
		$this->debug->guard();

//		echo "from: ".$fromX.",".$fromY." to: ".$toX.",".$toY."<br />";
		$circuitData = $this->_getCircuitData();
		$circuitSize = explode(',', $circuitData['circuitdata_size']);
		$circuitData = $circuitData['circuitdata_data'];

		$lengthX = $toX - $fromX;
		if ($lengthX > 0) $factorX = 1; else $factorX = -1;
		$lengthY = $toY - $fromY;
		if ($lengthY > 0) $factorY = 1; else $factorY = -1;

//		echo "factor: ".$factorX.",".$factorY."<br />";

		if (abs($lengthX) > abs($lengthY))
		{
			for ($i=1; $i<=abs($lengthX); $i++)
			{
				$checkX = $fromX + $i*$factorX;
				$checkY = $fromY + round(abs($lengthY)/abs($lengthX)*$i*$factorY);
		// TODO: Allgemeine Breite
				$terrainData[] = array(substr($circuitData, $checkY*$circuitSize[0]+$checkX, 1), $checkX, $checkY);
//				echo "type 1: pos: <b>".$checkX.",".$checkY."</b> - (".($i*$factorX).",".(round(abs($lengthY)/abs($lengthX)*$i*$factorY)).") - ".$terrainData[count($terrainData)-1]."<br />";
			}
		}
		else
		{
			for ($i=1; $i<=abs($lengthY); $i++)
			{
				$checkX = $fromX + round(abs($lengthX)/abs($lengthY)*$i*$factorX);
				$checkY = $fromY + $i*$factorY;
		// TODO: Allgemeine Breite
				$terrainData[] = array(substr($circuitData, $checkY*$circuitSize[0]+$checkX, 1), $checkX, $checkY);
//				echo "type 2:: pos: <b>".$checkX.",".$checkY."</b> - (".(round(abs($lengthX)/abs($lengthY)*$i*$factorX)).",".($i*$factorY).") - ".$terrainData[count($terrainData)-1]."<br />";
			}
		}

		$this->debug->unguard($terrainData);
		return $terrainData;
	}



	// TODO: Richitg aufsetzen
	protected function _getCircuitData()
	{
		$this->debug->guard();

		$sql = "SELECT * FROM circuit_data WHERE circuitdata_circuit='" . $this->currentCircuit . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$this->debug->unguard($row);
		return $row;
	}
	
	
	public function getMovement($player, $history=0)
	{
		$this->debug->guard();
		
		if (!$this->currentGamestates)
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$movement = array();
		foreach ($this->currentGamestates['playerdata'][$player]['moves'] as $move)
		{
			if ($move[0] == '1')
			{
				$movement[] = explode(',', $move[1]);
			}
		}
		
		if ($history == 0) $return = $movement;
		else
		{
			if (empty($movement[count($movement)+$history]))
			{
				$this->debug->write('Could not get last position: not enough moves', 'warning');
				$this->messages->setMessage('Could not get last position: not enough moves', 'warning');
				$this->debug->unguard(false);
				return false;
			}
			$return = $movement[count($movement)+$history];
		}

		$this->debug->unguard($return);
		return $return;
	}


	protected function _activateGamecards()
	{
		$this->debug->guard();
		
		if ( (!$this->currentGamestates) || (!$this->currentRace) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "SELECT * FROM gamecards_stack WHERE gamecardstack_race='" . $this->currentRace . "' AND gamecardstack_player='" . $this->currentGamestates['activePlayer'] . "'";
		$res = $this->database->query($sql);
		
		$activecards = array();
		while ($row = $this->database->fetchArray($res))
		{
			$activecards[] = $row['gamecardstack_gamecard'];
		}
		
		foreach ($activecards as $card)
		{
			if ($card == '2')
			{
				$this->currentGamestates['playerdata'][$this->currentGamestates['activePlayer']]['vector'][0] *= 2;
				$this->currentGamestates['playerdata'][$this->currentGamestates['activePlayer']]['vector'][1] *= 2;
			}

			if ($card == '3')
			{
				$this->currentGamestates['playerdata'][$this->currentGamestates['activePlayer']]['vector'][0] = 0;
				$this->currentGamestates['playerdata'][$this->currentGamestates['activePlayer']]['vector'][1] = 0;
			}
		}

		$this->debug->unguard(true);
		return true;		
	}

}

?>
