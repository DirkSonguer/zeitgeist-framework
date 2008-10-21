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


	public function draw($currentGamestates)
	{
		$offset = 20;
		
		$circuit = imagecreatefrompng('../data/circuits/circuit1.png');
//		$circuit = imagecreatefrompng('../data/circuits/circuit1_negative.png');
		if (!$circuit) die('1');

		$colorYellow = imagecolorallocate($circuit, 255, 150, 0);
		$colorBlue = imagecolorallocate($circuit, 0, 0, 255);
		$colorGreen = imagecolorallocate($circuit, 0, 255, 0);
		$colorRed = imagecolorallocate($circuit, 255, 0, 0);
		$colorGray = imagecolorallocate($circuit, 230, 220, 220);

		for ($j=1; $j<=$currentGamestates['numPlayers']; $j++)
		{
			if ($j == 1) $currentColor = $colorGreen;
			if ($j == 2) $currentColor = $colorRed;
			if ($j == 3) $currentColor = $colorBlue;
			if ($j == 4) $currentColor = $colorYellow;

			$currentMoves = $currentGamestates['playerdata'][$j]['moves'];
			
			$lastPosition = array(0,0);
			foreach ($currentMoves as $move)
			{
				if ($move[0] == '1')
				{
					$currentPosition = explode(',', $move[1]);
					imagefilledellipse($circuit, $currentPosition[0], $currentPosition[1], 6, 6, $currentColor);
					if ($lastPosition[0] > 0) imageline($circuit, $lastPosition[0], $lastPosition[1], $currentPosition[0], $currentPosition[1], $currentColor);
					$lastPosition = $currentPosition;
				}
			}

			if ($currentGamestates['activePlayer'] == $j)
			{
				$vect[0] = $currentPosition[0] + $currentGamestates['playerdata'][$j]['vector'][0];
				$vect[1] = $currentPosition[1] + $currentGamestates['playerdata'][$j]['vector'][1];
				imageRectangle($circuit, $vect[0]-$offset, $vect[1]-$offset, $vect[0]+$offset, $vect[1]+$offset, $currentColor);
				imageRectangle($circuit, $vect[0]-$offset+1, $vect[1]-$offset+1, $vect[0]+$offset+1, $vect[1]+$offset+1, $currentColor);
			}
		}

		$ret = imagepng($circuit, '../data/circuits/circuit1_game.png');
		imagedestroy($circuit);
	}

}

?>
