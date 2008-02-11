<?php

defined('LINERACER_ACTIVE') or die();

class game
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $objects;
	protected $dataserver;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->objects = zgObjectcache::init();
		$this->user = zgUserhandler::init();
		$this->dataserver = new zgDataserver();

		$this->database = new zgDatabase();
		$this->database->connect();
	}

	public function index($parameters=array())
	{
		$this->debug->guard();

		$gamefunctions = new lrGamefunctions;

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('game', 'templates', 'game_index'));
/*
		$userid = $this->user->getUserId();
		$tpl->assign('playerid', $userid);

		$userkey = $this->user->getUserKey();
		$tpl->assign('playerkey', $userkey);
*/

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

	private function _oldWay($parameters)
	{
		$circuit = imagecreatefromjpeg('testdata/circuit1.jpg');
		if (!$circuit) die('1');

		$colorBlack = imagecolorallocate($circuit, 0, 0, 0);
		$colorBlue = imagecolorallocate($circuit, 0, 0, 255);
		$colorGreen = imagecolorallocate($circuit, 0, 255, 0);
		$colorRed = imagecolorallocate($circuit, 255, 0, 0);
		$colorGray = imagecolorallocate($circuit, 230, 220, 220);

		$currentGamedata = array();
		$gamefunctions = new lrGamefunctions;
		$currentGamedata = $gamefunctions->getGamestates();

		$sql = "SELECT * FROM racedata rd LEFT JOIN races r ON rd.racedata_race = r.race_id LEFT JOIN circuits c ON r.race_circuit = c.circuit_id WHERE rd.racedata_id='1'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		for ($j=1; $j<=$currentGamedata['numPlayers']; $j++)
		{
			if ($j == 1) $currentColor = $colorGreen;
			if ($j == 2) $currentColor = $colorRed;
			if ($j == 3) $currentColor = $colorBlue;
			if ($j == 4) $currentColor = $colorBlack;

			$positionString = $row['racedata_position'.$j];
			$positionsArray = explode(';', $positionString);

			$startPosition = explode(',', $row['circuit_startposition']);
			$moves[0] = $startPosition[0]+($j*20);
			$moves[1] = $startPosition[1];
			$currentMoves = array($moves);

			if ($positionsArray[0] != '')
			{
				foreach($positionsArray as $move)
				{
					$movePosition = explode(',', $move);
					$currentMoves[] = array($movePosition[0], $movePosition[1]);
				}
			}

			for($i=0; $i<count($currentMoves); $i++)// as $index => $move)
			{
				imagefilledellipse($circuit, $currentMoves[$i][0], $currentMoves[$i][1], 6, 6, $currentColor);
				if ($i > 0) imageline($circuit, $currentMoves[$i-1][0], $currentMoves[$i-1][1], $currentMoves[$i][0], $currentMoves[$i][1], $currentColor);
			}
		}

		$ret = imagejpeg($circuit, 'testdata/circuit1_game.jpg');
		imagedestroy($circuit);
	}

	public function reset($parameters=array())
	{
		$this->debug->guard();

		$sql = "UPDATE racedata SET racedata_position1='', racedata_vector1='0,0', racedata_position2='', racedata_vector2='0,0', racedata_position3='', racedata_vector3='0,0', racedata_position4='', racedata_vector4='0,0', racedata_active='1' WHERE racedata_id='1'";
		$res = $this->database->query($sql);

		$tpl = new lrTemplate();
//		$tpl->redirect($tpl->createLink('game', 'index'));

		$this->debug->unguard(true);
		return true;
	}


	public function update($parameters=array())
	{
		$this->debug->guard();

		$gamefunctions = new lrGamefunctions;
		$gamestates[] = $gamefunctions->getGamestates();

		$this->_oldWay($parameters);

		$xmlData = $this->dataserver->createXMLDatasetFromArray($gamestates);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function playbooster($parameters=array())
	{
		$this->debug->guard();

		$this->debug->unguard(true);
		return true;
	}


	public function move($parameters=array())
	{
		$this->debug->guard();

		$gamedata = array();

/*
	   $filename = "debug.txt";
	   $handle = fopen($filename, "a");
	   $newstring = "\r\nja: ".date("d.m.Y H:i:s");

		foreach($_GET as $k => $v)
		{
			$newstring .= " - k: $k, v: $v - ";
		}
	   $numbytes = fwrite($handle, $newstring);
	   fclose($handle);
//*/
		if ( (empty($parameters['position_x'])) || (empty($parameters['position_y'])) )
		{
			$this->debug->unguard(false);
			return false;
		}

		$gamefunctions = new lrGamefunctions;

		$currentMove[0] = $parameters['position_x'];
		$currentMove[1] = $parameters['position_y'];

		if (!$gamefunctions->validateMove($currentMove))
		{
			$ret[] = array('valid' => 'false');
			$xmlData = $this->dataserver->createXMLDatasetFromArray($ret);
			$this->dataserver->streamXMLDataset($xmlData);
			die();
		}

		if (!$gamefunctions->updateGamestates($currentMove))
		{
			$ret[] = array('valid' => 'false');
			$xmlData = $this->dataserver->createXMLDatasetFromArray($ret);
			$this->dataserver->streamXMLDataset($xmlData);
			die();
		}

		$ret[] = array('valid' => 'true');
		$xmlData = $this->dataserver->createXMLDatasetFromArray($ret);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function abortgame($parameters=array())
	{
		$this->debug->guard();

		$this->debug->unguard(true);
		return true;
	}


	public function done($parameters=array())
	{
		$this->debug->guard();

		$this->debug->unguard(true);
		return true;
	}
}
?>
