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


	public function move($moveX, $moveY)
	{
		$this->debug->guard();
		
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
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
		
		// TODO: 1 = movement. use constant
		$this->_saveGamestates('1', $correctedMove[0].','.$correctedMove[1]);

		$this->debug->unguard(true);
		return true;
	}
	

	public function validateMove($moveX, $moveY)
	{
		$this->debug->guard();

		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$lastMove = $this->getMovement(-1);
		$currentVector = $currentGamestates['playerdata'][$currentGamestates['activePlayer']]['vector'];

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

		$lastMove = $this->getMovement(-1);
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
				
				$gameeventhandler = new lrGameeventhandler();
				$gameeventhandler->saveRaceevent('2', '1', +1);
				
//				echo "move: ".$moveX.",".$moveY." hit at ".$terrain[$key][1].",".$terrain[$key][2]." corrected to: ".$correctedMove[0].",".$correctedMove[1]."<br />";

				// TODO: Vector auf 0,0
				break;
			}
		}
		
		$this->debug->unguard($correctedMove);
		return $correctedMove;
	}

	
	public function getMovement($history=0)
	{
		$this->debug->guard();
		
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$player = $currentGamestates['activePlayer'];

		$movement = array();
		foreach ($currentGamestates['playerdata'][$player]['moves'] as $move)
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
		if (!$res)
		{
			$this->debug->write('Could not get circuit data: circuit not found', 'warning');
			$this->messages->setMessage('Could not get circuit data: circuit not found', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$row = $this->database->fetchArray($res);

		$this->debug->unguard($row);
		return $row;
	}

}

?>
