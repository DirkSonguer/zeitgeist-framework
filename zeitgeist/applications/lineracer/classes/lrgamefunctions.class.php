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


	public function getGamestates($raceid)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM races r LEFT JOIN circuits c ON r.race_circuit = c.circuit_id WHERE r.race_id='" . $raceid . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$currentGamedata = array();

		$currentGamedata['activePlayer'] = $row['race_activeplayer'];

		$player = array();
		$currentGamedata['playerdata'] = array();
		$currentGamedata['numPlayers'] = 0;
		if ($row['race_player4'] != '') $currentGamedata['numPlayers'] = 4;
		elseif ($row['race_player3'] != '') $currentGamedata['numPlayers'] = 3;
		elseif ($row['race_player2'] != '') $currentGamedata['numPlayers'] = 2;
		else $currentGamedata['numPlayers'] = 2;

		$sql = "SELECT * FROM race_moves WHERE move_race='" . $raceid . "' ORDER BY move_id";
		$res = $this->database->query($sql);

		while($row = $this->database->fetchArray($res))
		{
			if ($row['move_action'] == '1')
			{
				$position = explode(',',$row['move_parameter']);
				$currentGamedata['playerdata'][$row['move_user']]['moves'][] = array($position[0], $position[1]);
			}
		}
		
		for ($i=1; $i<=$currentGamedata['numPlayers']; $i++)
		{
			if (count($currentGamedata['playerdata'][$i]['moves']) > 1)
			{
				$currentGamedata['playerdata'][$i]['vector'][0] = $currentGamedata['playerdata'][$i]['moves'][count($currentGamedata['playerdata'][$i]['moves'])-1][0] - $currentGamedata['playerdata'][$i]['moves'][count($currentGamedata['playerdata'][$i]['moves'])-2][0];
				$currentGamedata['playerdata'][$i]['vector'][1] = $currentGamedata['playerdata'][$i]['moves'][count($currentGamedata['playerdata'][$i]['moves'])-1][1] - $currentGamedata['playerdata'][$i]['moves'][count($currentGamedata['playerdata'][$i]['moves'])-2][1];
			}
			else
			{
				$currentGamedata['playerdata'][$i]['vector'][0] = 0;
				$currentGamedata['playerdata'][$i]['vector'][1] = 0;
			}
		}

		$this->debug->unguard($currentGamedata);
		return $currentGamedata;
	}
	
	
	public function move($raceid, $moveX, $moveY)
	{
		$this->debug->guard();
		
		$gamestates = $this->getGamestates($raceid);
		
		if (!$this->validateTurn($raceid, $gamestates['activePlayer']))
		{
			$this->debug->write('Could not move player: it is another players turn', 'warning');
			$this->messages->setMessage('Could not move player: it is another players turn', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$this->validateMove($moveX, $moveY, $gamestates['playerdata'][$gamestates['activePlayer']]))
		{
			$this->debug->write('Could not move player: player moved outside its allowed area', 'warning');
			$this->messages->setMessage('Could not move player: player moved outside its allowed area', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$correctedMove = array();
		if (!$correctedMove = $this->validateTerrain($moveX, $moveY, $gamestates['playerdata'][$gamestates['activePlayer']]))
		{
			$this->debug->write('Could not move player: validating line failed', 'warning');
			$this->messages->setMessage('Could not move player: validating line failed', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->_saveGamestates($raceid, $gamestates['activePlayer'], $correctedMove[0], $correctedMove[1]);

		$this->debug->unguard(true);
		return true;
	}


	public function validateTurn()
	{
		$this->debug->guard();

		// validate current user against player num
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function validateMove($moveX, $moveY, $playerGamestates)
	{
		$this->debug->guard();

		$lastMove = $playerGamestates['moves'][count($playerGamestates['moves'])-1];
		$currentVector = $playerGamestates['vector'];

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


	public function validateTerrain($moveX, $moveY, $playerGamestates)
	{
		$this->debug->guard();

		$correctedMove = array();
		$lastMove = $playerGamestates['moves'][count($playerGamestates['moves'])-1];

		$fromX = $lastMove[0];
		$fromY = $lastMove[1];

		$terrain = array();
		$terrain = $this->_checkTerrainType($fromX, $fromY, $moveX, $moveY);
		
		foreach($terrain as $step)
		{
			echo $step[0];
		}
		echo "<br />";

		$correctedMove[0] = $moveX;
		$correctedMove[1] = $moveY;

		foreach($terrain as $step)
		{
			// check for blocking terrain
			if ($step[0] == 0)
			{
				echo "move: ".$moveX.",".$moveY." corrected to ".$step[1].",".$step[2]."<br />";
				$correctedMove[0] = $step[1];
				$correctedMove[1] = $step[2];
				// TODO: Gamestates global
				// TODO: Vector auf 0,0
				break;
			}
		}
		
		$this->debug->unguard($correctedMove);
		return $correctedMove;
	}


	private function _saveGamestates($raceid, $player, $moveX, $moveY)
	{
		$this->debug->guard();

		$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('" . $raceid . "', '" . $player . "', '1', '" . $moveX . "," . $moveY . "')";
		$res = $this->database->query($sql);

		// TODO: max player
		$player++;
		if ($player > 4) $player = 1;
		
		$sql = "UPDATE races SET race_activeplayer='" . $player . "'  WHERE race_id='" . $raceid . "'";
		$res = $this->database->query($sql);
		
		$this->debug->unguard(true);
		return true;
	}


	private function _checkTerrainType($fromX, $fromY, $toX, $toY)
	{
		$this->debug->guard();

//		echo "from: ".$fromX.",".$fromY."<br />";
//		echo "to: ".$toX.",".$toY."<br />";

		$circuitData = $this->_getCircuitData();

		$lengthX = $toX - $fromX;
		if ($lengthX > 0) $factorX = 1; else $factorX = -1;
		$lengthY = $toY - $fromY;
		if ($lengthY > 0) $factorY = 1; else $factorY = -1;

		if (abs($lengthX) > abs($lengthY))
		{
			for ($i=1; $i<=abs($lengthX); $i++)
			{
				$checkX = $fromX + $i*$factorX;
				$checkY = $fromY + round($lengthY/$lengthX*$i*$factorY);
		// TODO: Allgemeine Breite
				$terrainData[] = array(substr($circuitData, $checkY*800+$checkX, 1), $checkX, $checkY);
//				echo $checkX.",".$checkY." - (".round($lengthX/$i*$factorX).",".abs($i*$factorY).") - ".$terrainData[count($terrainData)-1]."<br />";
			}
		}
		else
		{
			for ($i=1; $i<=abs($lengthY); $i++)
			{
				$checkX = $fromX + round($lengthX/$lengthY*$i*$factorX);
				$checkY = $fromY + $i*$factorY;
		// TODO: Allgemeine Breite
				$terrainData[] = array(substr($circuitData, $checkY*800+$checkX, 1), $checkX, $checkY);
//				echo $checkX.",".$checkY." - (".round($lengthX/$i*$factorX).",".abs($i*$factorY).") - ".$terrainData[count($terrainData)-1]."<br />";
			}
		}

		$this->debug->unguard($terrainData);
		return $terrainData;
	}



	// TODO: Richitg aufsetzen
	private function _getCircuitData()
	{
		$this->debug->guard();

		$sql = "SELECT * FROM circuit_data";
		$res = $this->database->query($sql);

		$row = $this->database->fetchArray($res);
		$circuitData = $row['circuitdata_data'];

// TODO: Longtext checken

/*
echo "<p style='font-size:5px;'>";
for($j=1;$j<620;$j++)
{
		for($i=1;$i<800;$i++)
		{
			echo substr($circuitData, $j*800+$i, 1);
		}
		echo "<br />";
}
echo "</p>";
//*/

		$this->debug->unguard($circuitData);
		return $circuitData;
	}

}

?>
