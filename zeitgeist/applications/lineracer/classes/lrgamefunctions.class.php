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


	public function _validateMove()
	{
		$this->debug->guard();

		$currentGamedata = $this->objects->getObject('currentGamedata');

		$lastMove = $currentGamedata['moves'][count($currentGamedata['moves'])-1];
		$currentVector = $currentGamedata['currentVector'];

		$minX = $lastMove[0]+$currentVector[0]-20;
		$maxX = $lastMove[0]+$currentVector[0]+20;
		$minY = $lastMove[1]+$currentVector[1]-20;
		$maxY = $lastMove[1]+$currentVector[1]+20;

		if (!empty($currentGamedata['click']))
		{
			if ( ($currentGamedata['click'][0] < $minX) || ($currentGamedata['click'][0] > $maxX) ||
				($currentGamedata['click'][1] < $minY) || ($currentGamedata['click'][1] > $maxY) )
			{
				$this->debug->unguard(false);
				return false;
			}
		}

		$currentGamedata['valid'] = true;
		$this->objects->storeObject('currentGamedata', $currentGamedata, true);

		$this->debug->unguard(true);
		return true;
	}


	public function getGamestates()
	{
		$this->debug->guard();

		$sql = "SELECT * FROM racedata rd LEFT JOIN races r ON rd.racedata_race = r.race_id LEFT JOIN circuits c ON r.race_circuit = c.circuit_id WHERE rd.racedata_id='1'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$currentGamedata = array();

		$currentGamedata['num'] = rand(1,20);

		if ($row['racedata_active'] != '')
		{
			$currentGamedata['activePlayer'] = $row['racedata_active'];
		}
		else
		{
			$currentGamedata['activePlayer'] = 1;
		}

		if ($row['race_player4'] != '') $currentGamedata['numPlayers'] = 4;
			elseif ($row['race_player3'] != '') $currentGamedata['numPlayers'] = 3;
			elseif ($row['race_player2'] != '') $currentGamedata['numPlayers'] = 2;
			else $currentGamedata['numPlayers'] = 1;

		for ($i=1; $i<=$currentGamedata['numPlayers']; $i++)
		{
			$positionString = $row['racedata_position'.$i];
			$positionsArray = explode(';', $positionString);

			$startPosition = explode(',', $row['circuit_startposition']);
			$moves[0] = $startPosition[0]+($currentGamedata['activePlayer']*20);
			$moves[1] = $startPosition[1];
			$currentGamedata['players'][$i]['moves'] = array($moves);

			if ($positionsArray[0] != '')
			{
				foreach($positionsArray as $move)
				{
					$movePosition = explode(',', $move);
					$currentGamedata['players'][$i]['moves'][] = array($movePosition[0], $movePosition[1]);
				}
			}

			$currentGamedata['players'][$i]['movecount'] = count($currentGamedata['players'][$i]['moves']);

			$currentVector = $row['racedata_vector'.$i];
			$currentVector = explode(',', $currentVector);
			if (is_array($currentVector))
			{
				$currentGamedata['players'][$i]['vector'][0] = $currentVector[0];
				$currentGamedata['players'][$i]['vector'][1] = $currentVector[1];
			}
			else
			{
				$currentGamedata['players'][$i]['vector'][0] = 0;
				$currentGamedata['players'][$i]['vector'][1] = 1;
			}
		}

		if ( (!empty($parameters['click_x'])) && (!empty($parameters['click_y'])) )
		{
			$currentGamedata['click'] = array($parameters['click_x'], $parameters['click_y']);
		}

		$currentGamedata['valid'] = false;

//		$this->objects->storeObject('currentGamedata', $currentGamedata, true);

		$this->debug->unguard($currentGamedata);
		return $currentGamedata;
	}


	public function _setupGamestates($parameters)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM racedata rd LEFT JOIN races r ON rd.racedata_race = r.race_id LEFT JOIN circuits c ON r.race_circuit = c.circuit_id WHERE rd.racedata_id='1'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$currentGamedata = array();

		if ($row['racedata_active'] != '') $currentGamedata['activePlayer'] = $row['racedata_active'];
			else $currentGamedata['activePlayer'] = 1;

		if ($row['race_player4'] != '') $currentGamedata['numPlayers'] = 4;
			elseif ($row['race_player3'] != '') $currentGamedata['numPlayers'] = 3;
			elseif ($row['race_player2'] != '') $currentGamedata['numPlayers'] = 2;
			else $currentGamedata['numPlayers'] = 1;

		$positionString = $row['racedata_position'.$currentGamedata['activePlayer']];
		$positionsArray = explode(';', $positionString);

		$startPosition = explode(',', $row['circuit_startposition']);
		$moves[0] = $startPosition[0]+($currentGamedata['activePlayer']*20);
		$moves[1] = $startPosition[1];
		$currentGamedata['moves'] = array($moves);

		if ($positionsArray[0] != '')
		{
			foreach($positionsArray as $move)
			{
				$movePosition = explode(',', $move);
				$currentGamedata['moves'][] = array($movePosition[0], $movePosition[1]);
			}
		}

		if ( (!empty($parameters['click_x'])) && (!empty($parameters['click_y'])) )
		{
			$currentGamedata['click'] = array($parameters['click_x'], $parameters['click_y']);
		}

		$currentVector = $row['racedata_vector'.$currentGamedata['activePlayer']];
		$currentVector = explode(',', $currentVector);
		if (is_array($currentVector))
		{
			$currentGamedata['currentVector'][0] = $currentVector[0];
			$currentGamedata['currentVector'][1] = $currentVector[1];
		}
		else
		{
			$currentGamedata['currentVector'][0] = 0;
			$currentGamedata['currentVector'][1] = 1;
		}

		$currentGamedata['valid'] = false;
		$currentGamedata['movecount'] = count($currentGamedata['moves']);

		$this->objects->storeObject('currentGamedata', $currentGamedata, true);

		$this->debug->unguard(true);
		return true;
	}


	public function _updateGamestates()
	{
		$this->debug->guard();

		$currentGamedata = $this->objects->getObject('currentGamedata');

		if (!empty($currentGamedata['click']))
		{
			$currentGamedata['moves'][] = array($currentGamedata['click'][0], $currentGamedata['click'][1]);
		}

		if (count($currentGamedata['moves']) > 1)
		{
			$newVector = array();
			$newVector[0] = $currentGamedata['moves'][count($currentGamedata['moves'])-1][0]-$currentGamedata['moves'][count($currentGamedata['moves'])-2][0];
			$newVector[1] = $currentGamedata['moves'][count($currentGamedata['moves'])-1][1]-$currentGamedata['moves'][count($currentGamedata['moves'])-2][1];
			$currentGamedata['newVector'][0] = $newVector[0];
			$currentGamedata['newVector'][1] = $newVector[1];
		}
		else
		{
			$currentGamedata['newVector'][0] = 0;
			$currentGamedata['newVector'][1] = 0;
		}

		$circuitnegative = imagecreatefromjpeg('testdata/circuit1_negative.jpg');
		if (!$circuitnegative) die('1');
		$pixelcolor = imagecolorat($circuitnegative, $currentGamedata['moves'][count($currentGamedata['moves'])-1][0], $currentGamedata['moves'][count($currentGamedata['moves'])-1][1]);
		$colorarray = imagecolorsforindex($circuitnegative, $pixelcolor);
		if ( ($colorarray['red'] == 0) && ($colorarray['green'] == 0) && ($colorarray['blue'] == 0) )
		{
			$currentGamedata['newVector'][0] = 0;
			$currentGamedata['newVector'][1] = 0;
		}

		$currentGamedata['movecount'] = count($currentGamedata['moves']);

		$this->objects->storeObject('currentGamedata', $currentGamedata, true);

		$this->debug->unguard(true);
		return true;
	}


	public function _saveGamestates()
	{
		$this->debug->guard();

		$currentGamedata = $this->objects->getObject('currentGamedata');

		$nextPlayer = $currentGamedata['activePlayer'] + 1;
		if ($nextPlayer > $currentGamedata['numPlayers'])
		{
			$nextPlayer = 1;
		}

		$movestring = '';
		foreach($currentGamedata['moves'] as $index => $move)
		{
			if ($index > 0)
			{
				$movestring .= implode(',', $move).';';
			}
		}

		$movestring = substr($movestring, 0, -1);

		$sql = "UPDATE racedata SET racedata_vector".$currentGamedata['activePlayer']."='" . $currentGamedata['newVector'][0] . "," . $currentGamedata['newVector'][1] . "', racedata_position".$currentGamedata['activePlayer']."='".$movestring."', racedata_active='".$nextPlayer."' WHERE racedata_id='1'";
		$res = $this->database ->query($sql);

		$this->debug->unguard(true);
		return true;
	}

}

?>
