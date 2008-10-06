<?php

defined('LINERACER_ACTIVE') or die();

class prototypeRenderer
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


	public function draw($race_id)
	{

		$gamefunctions = new lrGamefunctions();
		$gamestates = $gamefunctions->getGamestates(1);
		
		var_dump($gamestates);

/*
		$circuit = imagecreatefromjpeg('data/circuits/circuit1.jpg');
		if (!$circuit) die('1');

		$colorBlack = imagecolorallocate($circuit, 0, 0, 0);
		$colorBlue = imagecolorallocate($circuit, 0, 0, 255);
		$colorGreen = imagecolorallocate($circuit, 0, 255, 0);
		$colorRed = imagecolorallocate($circuit, 255, 0, 0);
		$colorGray = imagecolorallocate($circuit, 230, 220, 220);

//		$currentGamedata = array();
//		$gamefunctions = new lrGamefunctions;
//		$currentGamedata = $gamefunctions->getGamestates();

		$sql = "SELECT * FROM racedata rd LEFT JOIN races r ON rd.racedata_race = r.race_id LEFT JOIN circuits c ON r.race_circuit = c.circuit_id WHERE rd.racedata_id='1'";
		$res = $database->query($sql);
		$row = $database->fetchArray($res);

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
*/
	}

}

?>
