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

		$userid = $this->user->getUserId();
		$tpl->assign('playerid', $userid);

		$userkey = $this->user->getUserKey();
		$tpl->assign('playerkey', $userkey);

		$circuit = imagecreatefromjpeg('testdata/circuit1.jpg');
		if (!$circuit) die('1');
/*
		$colorBlack = imagecolorallocate($circuit, 0, 0, 0);
		$colorBlue = imagecolorallocate($circuit, 0, 0, 255);
		$colorGreen = imagecolorallocate($circuit, 0, 255, 0);
		$colorRed = imagecolorallocate($circuit, 255, 0, 0);
		$colorGray = imagecolorallocate($circuit, 230, 220, 220);

		$gamedata = array();

		if(!empty($parameters['submit']))
		{
			$gamedata['click_x'] = $parameters['position_x'];
			$gamedata['click_y'] = $parameters['position_y'];
		}

		$gamefunctions->_setupGamestates($gamedata);

		if ($gamefunctions->_validateMove())
		{
			$gamefunctions->_updateGamestates();
		}

		$currentGamedata = $this->objects->getObject('currentGamedata');

		if ( (!empty($parameters['submit'])) && ($currentGamedata['valid'] == true) )
		{
			$gamefunctions->_saveGamestates();
		}

		$gamefunctions->_setupGamestates('');
		$currentGamedata = $this->objects->getObject('currentGamedata');

		$sql = "SELECT * FROM racedata rd LEFT JOIN races r ON rd.racedata_race = r.race_id LEFT JOIN circuits c ON r.race_circuit = c.circuit_id WHERE rd.racedata_id='1'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		for ($j=1; $j<=$currentGamedata['numPlayers']; $j++)
		{
			if ($j == 1) $currentColor = $colorGreen;
			if ($j == 2) $currentColor = $colorRed;
			if ($j == 3) $currentColor = $colorBlue;
			if ($j == 4) $currentColor = $colorBlack;

			if ($j != $currentGamedata['activePlayer'])
			{
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
			else
			{
				for($i=0; $i<count($currentGamedata['moves']); $i++)// as $index => $move)
				{
					imagefilledellipse($circuit, $currentGamedata['moves'][$i][0], $currentGamedata['moves'][$i][1], 8, 8, $currentColor);
					imagesetthickness($circuit, 5);
					if ($i > 0) imageline($circuit, $currentGamedata['moves'][$i-1][0], $currentGamedata['moves'][$i-1][1], $currentGamedata['moves'][$i][0], $currentGamedata['moves'][$i][1], $currentColor);
					imagesetthickness($circuit, 1);
				}

				if ($currentGamedata['valid'] == true)
				{
					$vectorCenter = array();
					$vectorCenter[0] = $currentGamedata['moves'][count($currentGamedata['moves'])-1][0] + $currentGamedata['newVector'][0];
					$vectorCenter[1] = $currentGamedata['moves'][count($currentGamedata['moves'])-1][1] + $currentGamedata['newVector'][1];
					imagerectangle ($circuit, $vectorCenter[0]-20, $vectorCenter[1]-20, $vectorCenter[0]+20, $vectorCenter[1]+20, $currentColor);
				}
				else
				{
					$vectorCenter = array();
					$vectorCenter[0] = $currentGamedata['moves'][count($currentGamedata['moves'])-1][0] + $currentGamedata['currentVector'][0];
					$vectorCenter[1] = $currentGamedata['moves'][count($currentGamedata['moves'])-1][1] + $currentGamedata['currentVector'][1];
					imagerectangle ($circuit, $vectorCenter[0]-20, $vectorCenter[1]-20, $vectorCenter[0]+20, $vectorCenter[1]+20, $currentColor);
				}
			}
		}
*/
		$ret = imagejpeg($circuit, 'testdata/circuit1_game.jpg');
		imagedestroy($circuit);

//		$tpl->assign('movecount', $currentGamedata['movecount']-1);
//		$tpl->assign('currentplayer', $currentGamedata['activePlayer']);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function reset($parameters=array())
	{
		$this->debug->guard();

		$sql = "UPDATE racedata SET racedata_position1='', racedata_vector1='0,0', racedata_position2='', racedata_vector2='0,0', racedata_position3='', racedata_vector3='0,0', racedata_position4='', racedata_vector4='0,0', racedata_active='1' WHERE racedata_id='1'";
		$res = $this->database->query($sql);

		$tpl = new lrTemplate();
		$tpl->redirect($tpl->createLink('game', 'index'));

		$this->debug->unguard(true);
		return true;
	}


	public function update($parameters=array())
	{
		$this->debug->guard();

		$gamefunctions = new lrGamefunctions;
		$gamestates[] = $gamefunctions->getGamestates();

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
