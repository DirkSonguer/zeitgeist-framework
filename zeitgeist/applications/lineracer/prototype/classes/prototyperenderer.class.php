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
		$offset = 20;
		
		$gamefunctions = new lrGamefunctions();
		$currentGamedata = $gamefunctions->getGamestates($race_id);
		
		$circuit = imagecreatefromjpeg('../data/circuits/circuit1.jpg');
		if (!$circuit) die('1');

		$colorBlack = imagecolorallocate($circuit, 0, 0, 0);
		$colorBlue = imagecolorallocate($circuit, 0, 0, 255);
		$colorGreen = imagecolorallocate($circuit, 0, 255, 0);
		$colorRed = imagecolorallocate($circuit, 255, 0, 0);
		$colorGray = imagecolorallocate($circuit, 230, 220, 220);

		for ($j=1; $j<=$currentGamedata['numPlayers']; $j++)
		{
			if ($j == 1) $currentColor = $colorGreen;
			if ($j == 2) $currentColor = $colorRed;
			if ($j == 3) $currentColor = $colorBlue;
			if ($j == 4) $currentColor = $colorBlack;

			$currentMoves = $currentGamedata['playerdata'][$j]['moves'];
			
			for ($i=0; $i<count($currentMoves); $i++)// as $index => $move)
			{
				imagefilledellipse($circuit, $currentMoves[$i][0], $currentMoves[$i][1], 6, 6, $currentColor);
				if ($i > 0) imageline($circuit, $currentMoves[$i-1][0], $currentMoves[$i-1][1], $currentMoves[$i][0], $currentMoves[$i][1], $currentColor);
			}

			if ($currentGamedata['activePlayer'] == $j)
			{
				$vect[0] = $currentMoves[count($currentMoves)-1][0] + $currentGamedata['playerdata'][$j]['vector'][0];
				$vect[1] = $currentMoves[count($currentMoves)-1][1] + $currentGamedata['playerdata'][$j]['vector'][1];
				imageRectangle($circuit, $vect[0]-$offset, $vect[1]-$offset, $vect[0]+$offset, $vect[1]+$offset, $currentColor);
			}
		}

		$ret = imagejpeg($circuit, '../data/circuits/circuit1_game.jpg');
		imagedestroy($circuit);
	}

}

?>
