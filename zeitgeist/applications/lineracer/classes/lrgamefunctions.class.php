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

		if ($row['race_player4'] != '') $currentGamedata['numPlayers'] = 4;
			elseif ($row['race_player3'] != '') $currentGamedata['numPlayers'] = 3;
			elseif ($row['race_player2'] != '') $currentGamedata['numPlayers'] = 2;
			else $currentGamedata['numPlayers'] = 1;

		$currentGamedata['moves'][$row['race_player1']] = array();
		$currentGamedata['moves'][$row['race_player2']] = array();
		$currentGamedata['moves'][$row['race_player3']] = array();
		$currentGamedata['moves'][$row['race_player4']] = array();
			
		$sql = "SELECT * FROM race_moves WHERE move_race='" . $raceid . "'";
		$res = $this->database->query($sql);

		while($row = $this->database->fetchArray($res))
		{
			if ($row['move_action'] == '1')
			{
				$vectors = explode(',',$row['move_parameter']);
				$currentGamedata['moves'][$row['move_user']][] = array($vectors[0], $vectors[1]);
			}
		}

			
/*
		for ($i=1; $i<=$currentGamedata['numPlayers']; $i++)
		{
			$positionString = $row['racedata_position'.$i];
			$positionsArray = explode(';', $positionString);

			$startPosition = explode(',', $row['circuit_startposition']);
			$moves[0] = $startPosition[0]+($i*20);
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
*/
		$this->debug->unguard($currentGamedata);
		return $currentGamedata;
	}
	
/*
	public function validateMove($currentMove = array())
	{
		$this->debug->guard();

		$currentGamedata = $this->getGamestates();

		$moves = array();
		$moves = $currentGamedata['players'][$currentGamedata['activePlayer']]['moves'];
		$lastMove = $moves[count($moves)-1];
		$currentVector = $currentGamedata['players'][$currentGamedata['activePlayer']]['vector'];

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





	public function updateGamestates($currentMove = array())
	{
		$this->debug->guard();

		$currentGamedata = $this->getGamestates();
		$currentGamedata['players'][$currentGamedata['activePlayer']]['moves'][] = array($currentMove[0], $currentMove[1]);

		if (count($currentGamedata['players'][$currentGamedata['activePlayer']]['moves']) > 1)
		{
			$moves = $currentGamedata['players'][$currentGamedata['activePlayer']]['moves'];
			$newVector = array();
			$newVector[0] = $moves[count($moves)-1][0] - $moves[count($moves)-2][0];
			$newVector[1] = $moves[count($moves)-1][1] - $moves[count($moves)-2][1];
			$currentGamedata['players'][$currentGamedata['activePlayer']]['vector'][0] = $newVector[0];
			$currentGamedata['players'][$currentGamedata['activePlayer']]['vector'][1] = $newVector[1];
		}
		else
		{
			$currentGamedata['players'][$currentGamedata['activePlayer']]['vector'][0] = 0;
			$currentGamedata['players'][$currentGamedata['activePlayer']]['vector'][1] = 0;
		}

		$circuitnegative = imagecreatefromjpeg('testdata/circuit1_negative.jpg');
		if (!$circuitnegative) die('1');
		$pixelcolor = imagecolorat($circuitnegative, $moves[count($moves)-1][0], $moves[count($moves)-1][1]);
		$colorarray = imagecolorsforindex($circuitnegative, $pixelcolor);
		if ( ($colorarray['red'] == 0) && ($colorarray['green'] == 0) && ($colorarray['blue'] == 0) )
		{
			$currentGamedata['players'][$currentGamedata['activePlayer']]['vector'][0] = 0;
			$currentGamedata['players'][$currentGamedata['activePlayer']]['vector'][1] = 0;
		}

		$currentGamedata['players'][$currentGamedata['activePlayer']]['movecount'] += 1;

		if (!$this->_saveGamestates($currentGamedata))
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	private function _saveGamestates($currentGamedata = array())
	{
		$this->debug->guard();

		$nextPlayer = $currentGamedata['activePlayer'] + 1;
		if ($nextPlayer > $currentGamedata['numPlayers'])
		{
			$nextPlayer = 1;
		}

		$movestring = '';
		foreach ($currentGamedata['players'][$currentGamedata['activePlayer']]['moves'] as $index => $move)
		{
			if ($index > 0)
			{
				$movestring .= implode(',', $move).';';
			}
		}

		$movestring = substr($movestring, 0, -1);

		$vector = $currentGamedata['players'][$currentGamedata['activePlayer']]['vector'];
		$sql = "UPDATE racedata SET racedata_vector".$currentGamedata['activePlayer']."='" . $vector[0] . "," .$vector[1] . "', racedata_position".$currentGamedata['activePlayer']."='".$movestring."', racedata_active='".$nextPlayer."' WHERE racedata_id='1'";

		$res = $this->database ->query($sql);
		if (!$res)
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}
*/
}

?>
