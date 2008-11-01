<?php

defined('LINERACER_ACTIVE') or die();

class lrMovementfunctions
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


	/**
	 * Validates the move of a player based on a given move and the last move
	 * Returns false if move is not valid
	 *
	 * @param integer $moveX x coordinate to move to
	 * @param integer $moveY y coordinate to move to
	 *
	 * @return boolean
	 */
	public function validateMove($moveX, $moveY)
	{
		$this->debug->guard();

		// check if gamestates are loaded
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// gets last move and vector for current player
		$lastMove = $this->getMovement($currentGamestates['currentPlayer'], -1);
		$currentVector = $currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'];

		// on the basis of the movement radius, calculate a valid region
		$movementradius = $this->configuration->getConfiguration('gamedefinitions', 'gamelogic', 'movementradius');
		$minX = $lastMove[0]+$currentVector[0]-$movementradius;
		$maxX = $lastMove[0]+$currentVector[0]+$movementradius;
		$minY = $lastMove[1]+$currentVector[1]-$movementradius;
		$maxY = $lastMove[1]+$currentVector[1]+$movementradius;

		// check if the player is outside the allowed region
		if ( ($moveX < $minX) || ($moveX > $maxX) || ($moveY < $minY) || ($moveY > $maxY) )
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Validates a move against the terrain
	 *
	 * @param integer $moveX x coordinate to move to
	 * @param integer $moveY y coordinate to move to
	 *
	 * @return boolean
	 */
	public function validateTerrain($moveX, $moveY)
	{
		$this->debug->guard();
		
		// check if gamestates are loaded
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// get last move
		$lastMove = $this->getMovement($currentGamestates['currentPlayer'], -1);
		$fromX = $lastMove[0];
		$fromY = $lastMove[1];

		// get an array of terrain points between the last position and the given one
		$terrain = array();
		$terrain = $this->_checkTerrainType($fromX, $fromY, $moveX, $moveY);

/*
		echo "from: ".$fromX.",".$fromY." to: ".$moveX.",".$moveY."<br />";
		echo "terrain: ".$terrain."<br />";
		foreach($terrain as $step)
		{
			echo $step[0];
		}
		echo "<br />";
//*/

		$correctedMove = array();
		$correctedMove[0] = $moveX;
		$correctedMove[1] = $moveY;
		$correctedMove[2] = 0;

		// cycle through all the points on the movement line and check their terrain type
		foreach($terrain as $key => $step)
		{
//			echo "key: ".$key." - terrain: ".$step[0]." position: ".$step[1].",".$step[2]."<br />";

			// check for blocking terrain
			if ($step[0] == $this->configuration->getConfiguration('gamedefinitions', 'game_surfaces', 'unpassable'))
			{
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

				// save event to clear vector
				$gameeventhandler = new lrGameeventhandler();
				$gameeventhandler->saveRaceevent($currentGamestates['currentPlayer'], '2', '1', $currentGamestates['currentRound']+1, '1');

				// save crash to game moves
				$gameeventhandler->saveRaceaction($this->configuration->getConfiguration('gamedefinitions', 'actions', 'playgamecard'), $moveX.",".$moveY);
				
				$this->debug->write("Crash happened with move: ".$moveX.",".$moveY." hit at ".$terrain[$key][1].",".$terrain[$key][2]." corrected to: ".$correctedMove[0].",".$correctedMove[1], 'message');
				break;
			}

			// check for checkpoint1
			if ($step[0] == $this->configuration->getConfiguration('gamedefinitions', 'game_surfaces', 'checkpoint1')) 
			{
				if (empty($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['checkpoints']['1']))
				{
					// save action
					$gameeventhandler->saveRaceaction($this->configuration->getConfiguration('gamedefinitions', 'actions', 'checkpoint1'), '1');
					$currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['checkpoints']['1'] = true;
					$this->objects->storeObject('currentGamestates', $currentGamestates, true);
					$this->debug->write("Checkpoint 1 hit with move: ".$moveX.",".$moveY." hit at ".$terrain[$key][1].",".$terrain[$key][2], 'message');
				}
			}

			// check for checkpoint2
			if ($step[0] == $this->configuration->getConfiguration('gamedefinitions', 'game_surfaces', 'checkpoint2')) 
			{
				if ( (empty($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['checkpoints']['2']))
				&& (!empty($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['checkpoints']['1'])) )
				{
					// save action
					$gameeventhandler->saveRaceaction($this->configuration->getConfiguration('gamedefinitions', 'actions', 'checkpoint2'), '2');
					$currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['checkpoints']['2'] = true;
					$this->objects->storeObject('currentGamestates', $currentGamestates, true);
					$this->debug->write("Checkpoint 2 hit with move: ".$moveX.",".$moveY." hit at ".$terrain[$key][1].",".$terrain[$key][2], 'message');
				}
			}
			
			// check for checkpoint3
			if ($step[0] == $this->configuration->getConfiguration('gamedefinitions', 'game_surfaces', 'checkpoint3')) 
			{
				if ( (empty($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['checkpoints']['3']))
				&& (!empty($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['checkpoints']['2'])) )
				{
					// save action
					$gameeventhandler->saveRaceaction($this->configuration->getConfiguration('gamedefinitions', 'actions', 'checkpoint2'), '3');
					$currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['checkpoints']['3'] = true;
					$this->objects->storeObject('currentGamestates', $currentGamestates, true);
					$this->debug->write("Checkpoint 3 hit with move: ".$moveX.",".$moveY." hit at ".$terrain[$key][1].",".$terrain[$key][2], 'message');
				}
			}

			// check for finish line
			if ($step[0] == $this->configuration->getConfiguration('gamedefinitions', 'game_surfaces', 'finish')) 
			{
				if ( (empty($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['finish']))
				&& (!empty($currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['checkpoints']['3'])) )
				{
					// save action
					$gameeventhandler->saveRaceaction($this->configuration->getConfiguration('gamedefinitions', 'actions', 'finish'), '1');
					$currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['finished'] = true;
					$this->objects->storeObject('currentGamestates', $currentGamestates, true);
					$this->debug->write("Player finished with move: ".$moveX.",".$moveY." hit at ".$terrain[$key][1].",".$terrain[$key][2], 'message');
					break;
				}
			}
		}
		
		$this->debug->unguard($correctedMove);
		return $correctedMove;
	}

	
	/**
	 * Gets movements of the currently active player 
	 * If parameter is empty, all moves will be given
	 * Otherwise, only the last moves according to the parameter are given
	 *
	 * @param integer $history number of moves to return
	 *
	 * @return boolean
	 */
	public function getMovement($player, $history=0)
	{
		$this->debug->guard();

		// check if gamestates are loaded
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$movement = array();
		foreach ($currentGamestates['playerdata'][$player]['moves'] as $move)
		{
			if ($move[0] == '1')
			{
				$movement[] = explode(',', $move[1]);
			}
		}
		
		if ($history == 0)
		{
			$return = $movement;
		}
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
	

	/**
	 * Returns all the terrain types of a move
	 * This will check pixel by pixel along he line of the move
	 * Returns array with terrain data
	 *
	 * @access protected
	 *
	 * @param string $fromX x coordinate from where the terrain is checked
	 * @param string $fromY y coordinate from where the terrain is checked
	 * @param string $toX x coordinate to where the terrain is checked
	 * @param string $toY y coordinate to where the terrain is checked
	 *
	 * @return array
	 */
	protected function _checkTerrainType($fromX, $fromY, $toX, $toY)
	{
		$this->debug->guard();

		// check if gamestates are loaded
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// load circuit data and attributes from database
		$circuitData = $this->_getCircuitData($currentGamestates['currentCircuit']);
		$circuitSize = explode(',', $circuitData['circuitdata_size']);
		$circuitData = $circuitData['circuitdata_data'];
		
		// we use a simple system here and move along the largest axis
		$lengthX = $toX - $fromX;
		if ($lengthX > 0) $factorX = 1; else $factorX = -1;
		$lengthY = $toY - $fromY;
		if ($lengthY > 0) $factorY = 1; else $factorY = -1;

		if (abs($lengthX) > abs($lengthY))
		{
			for ($i=1; $i<=abs($lengthX); $i++)
			{
				$checkX = $fromX + $i*$factorX;
				$checkY = $fromY + round(abs($lengthY)/abs($lengthX)*$i*$factorY);
				$terrainData[] = array(substr($circuitData, $checkY*$circuitSize[0]+$checkX, 1), $checkX, $checkY);
			}
		}
		else
		{
			for ($i=1; $i<=abs($lengthY); $i++)
			{
				$checkX = $fromX + round(abs($lengthX)/abs($lengthY)*$i*$factorX);
				$checkY = $fromY + $i*$factorY;
				$terrainData[] = array(substr($circuitData, $checkY*$circuitSize[0]+$checkX, 1), $checkX, $checkY);
			}
		}

		$this->debug->unguard($terrainData);
		return $terrainData;
	}


	/**
	 * Returns all the terrain data of the current circuit
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function _getCircuitData($circuit)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM circuit_data WHERE circuitdata_circuit='" . $circuit . "'";
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
