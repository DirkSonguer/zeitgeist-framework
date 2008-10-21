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


	public function validateMove($currentMove = array())
	{
		$this->debug->guard();

		$currentGamestates = $this->getGamestates();

		$moves = array();
		$moves = $currentGamestates['players'][$currentGamestates['activePlayer']]['moves'];
		$lastMove = $moves[count($moves)-1];
		$currentVector = $currentGamestates['players'][$currentGamestates['activePlayer']]['vector'];

		$minX = $lastMove[0]+$currentVector[0]-20;
		$maxX = $lastMove[0]+$currentVector[0]+20;
		$minY = $lastMove[1]+$currentVector[1]-20;
		$maxY = $lastMove[1]+$currentVector[1]+20;

		if ( ($currentMove[0] < $minX) || ($currentMove[0] > $maxX) ||
			($currentMove[1] < $minY) || ($currentMove[1] > $maxY) )
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function getGamestates()
	{
		$this->debug->guard();

		$sql = "SELECT * FROM racedata rd LEFT JOIN races r ON rd.racedata_race = r.race_id LEFT JOIN circuits c ON r.race_circuit = c.circuit_id WHERE rd.racedata_id='1'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$currentGamestates = array();

		if ($row['racedata_active'] != '')
		{
			$currentGamestates['activePlayer'] = $row['racedata_active'];
		}
		else
		{
			$currentGamestates['activePlayer'] = 1;
		}

		if ($row['race_player4'] != '') $currentGamestates['numPlayers'] = 4;
			elseif ($row['race_player3'] != '') $currentGamestates['numPlayers'] = 3;
			elseif ($row['race_player2'] != '') $currentGamestates['numPlayers'] = 2;
			else $currentGamestates['numPlayers'] = 1;

		for ($i=1; $i<=$currentGamestates['numPlayers']; $i++)
		{
			$positionString = $row['racedata_position'.$i];
			$positionsArray = explode(';', $positionString);

			$startPosition = explode(',', $row['circuit_startposition']);
			$moves[0] = $startPosition[0]+($i*20);
			$moves[1] = $startPosition[1];
			$currentGamestates['players'][$i]['moves'] = array($moves);

			if ($positionsArray[0] != '')
			{
				foreach($positionsArray as $move)
				{
					$movePosition = explode(',', $move);
					$currentGamestates['players'][$i]['moves'][] = array($movePosition[0], $movePosition[1]);
				}
			}

			$currentGamestates['players'][$i]['movecount'] = count($currentGamestates['players'][$i]['moves']);

			$currentVector = $row['racedata_vector'.$i];
			$currentVector = explode(',', $currentVector);
			if (is_array($currentVector))
			{
				$currentGamestates['players'][$i]['vector'][0] = $currentVector[0];
				$currentGamestates['players'][$i]['vector'][1] = $currentVector[1];
			}
			else
			{
				$currentGamestates['players'][$i]['vector'][0] = 0;
				$currentGamestates['players'][$i]['vector'][1] = 1;
			}
		}

		$this->debug->unguard($currentGamestates);
		return $currentGamestates;
	}


	public function updateGamestates($currentMove = array())
	{
		$this->debug->guard();

		$currentGamestates = $this->getGamestates();
		$currentGamestates['players'][$currentGamestates['activePlayer']]['moves'][] = array($currentMove[0], $currentMove[1]);

		if (count($currentGamestates['players'][$currentGamestates['activePlayer']]['moves']) > 1)
		{
			$moves = $currentGamestates['players'][$currentGamestates['activePlayer']]['moves'];
			$newVector = array();
			$newVector[0] = $moves[count($moves)-1][0] - $moves[count($moves)-2][0];
			$newVector[1] = $moves[count($moves)-1][1] - $moves[count($moves)-2][1];
			$currentGamestates['players'][$currentGamestates['activePlayer']]['vector'][0] = $newVector[0];
			$currentGamestates['players'][$currentGamestates['activePlayer']]['vector'][1] = $newVector[1];
		}
		else
		{
			$currentGamestates['players'][$currentGamestates['activePlayer']]['vector'][0] = 0;
			$currentGamestates['players'][$currentGamestates['activePlayer']]['vector'][1] = 0;
		}

		$circuitnegative = imagecreatefromjpeg('testdata/circuit1_negative.jpg');
		if (!$circuitnegative) die('1');
		$pixelcolor = imagecolorat($circuitnegative, $moves[count($moves)-1][0], $moves[count($moves)-1][1]);
		$colorarray = imagecolorsforindex($circuitnegative, $pixelcolor);
		if ( ($colorarray['red'] == 0) && ($colorarray['green'] == 0) && ($colorarray['blue'] == 0) )
		{
			$currentGamestates['players'][$currentGamestates['activePlayer']]['vector'][0] = 0;
			$currentGamestates['players'][$currentGamestates['activePlayer']]['vector'][1] = 0;
		}

		$currentGamestates['players'][$currentGamestates['activePlayer']]['movecount'] += 1;

		if (!$this->_saveGamestates($currentGamestates))
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	private function _saveGamestates($currentGamestates = array())
	{
		$this->debug->guard();

		$nextPlayer = $currentGamestates['activePlayer'] + 1;
		if ($nextPlayer > $currentGamestates['numPlayers'])
		{
			$nextPlayer = 1;
		}

		$movestring = '';
		foreach ($currentGamestates['players'][$currentGamestates['activePlayer']]['moves'] as $index => $move)
		{
			if ($index > 0)
			{
				$movestring .= implode(',', $move).';';
			}
		}

		$movestring = substr($movestring, 0, -1);

		$vector = $currentGamestates['players'][$currentGamestates['activePlayer']]['vector'];
		$sql = "UPDATE racedata SET racedata_vector".$currentGamestates['activePlayer']."='" . $vector[0] . "," .$vector[1] . "', racedata_position".$currentGamestates['activePlayer']."='".$movestring."', racedata_active='".$nextPlayer."' WHERE racedata_id='1'";

		$res = $this->database ->query($sql);
		if (!$res)
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}

}

?>
