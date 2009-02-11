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
		$this->objects = zgObjects::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function draw()
	{
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not draw game: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not draw game: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		$offset = $currentGamestates['move']['currentRadius'];

		$circuit = imagecreatefrompng(APPLICATION_ROOTDIRECTORY . '/data/circuits/circuit1.png');
//		$circuit = imagecreatefrompng(APPLICATION_ROOTDIRECTORY . '/data/circuits/circuit1_negative.png');
		if (!$circuit) die('1');

		$colorYellow = imagecolorallocate($circuit, 255, 150, 0);
		$colorBlue = imagecolorallocate($circuit, 0, 0, 255);
		$colorGreen = imagecolorallocate($circuit, 0, 255, 0);
		$colorRed = imagecolorallocate($circuit, 255, 0, 0);
		$colorGray = imagecolorallocate($circuit, 230, 220, 220);
		
		$racefunctions = new lrGamefunctions();
		$raceid = $racefunctions->getRaceID();

		$sqlPlayers = "SELECT * FROM race_to_users WHERE raceuser_race='" . $raceid . "'";
		$resPlayers = $this->database->query($sqlPlayers);
		$i = 1;
		while ($rowPlayers = $this->database->fetchArray($resPlayers))
		{
			$currentPlayer[$i] = $rowPlayers['raceuser_user'];
			$i++;
		}

		for ($j=1; $j<=$currentGamestates['meta']['numPlayers']; $j++)
		{
			if ($j == 1) $currentColor = $colorGreen;
			if ($j == 2) $currentColor = $colorRed;
			if ($j == 3) $currentColor = $colorBlue;
			if ($j == 4) $currentColor = $colorYellow;

			$currentMoves = $currentGamestates['playerdata'][$currentPlayer[$j]]['moves'];
			
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

			if ($currentGamestates['move']['currentPlayer'] == $j)
			{
				$vect[0] = $currentPosition[0] + $currentGamestates['playerdata'][$currentPlayer[$j]]['vector'][0];
				$vect[1] = $currentPosition[1] + $currentGamestates['playerdata'][$currentPlayer[$j]]['vector'][1];
				imageRectangle($circuit, $vect[0]-$offset, $vect[1]-$offset, $vect[0]+$offset, $vect[1]+$offset, $currentColor);
				imageRectangle($circuit, $vect[0]-$offset+1, $vect[1]-$offset+1, $vect[0]+$offset+1, $vect[1]+$offset+1, $currentColor);
			}
		}

		$ret = imagepng($circuit, APPLICATION_ROOTDIRECTORY . '/data/circuits/circuit1_game.png');
		imagedestroy($circuit);
	}

}

?>
