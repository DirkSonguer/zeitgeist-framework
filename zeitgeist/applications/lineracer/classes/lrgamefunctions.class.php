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


	public function move($moveX, $moveY)
	{
		$this->debug->guard();
		
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$userfunctions = new lrUserfunctions();
		if (!$userfunctions->validateTurn())
		{
			$this->debug->write('Could not move player: not the players turn', 'warning');
			$this->messages->setMessage('Could not move player: not the players turn', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$movementfunctions = new lrMovementfunctions();
		if (!$movementfunctions->validateMove($moveX, $moveY))
		{
			$this->debug->write('Could not move player: player moved outside its allowed area', 'warning');
			$this->messages->setMessage('Could not move player: player moved outside its allowed area', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$correctedMove = array();
		if (!$correctedMove = $movementfunctions->validateTerrain($moveX, $moveY))
		{
			$this->debug->write('Could not move player: validating line failed', 'warning');
			$this->messages->setMessage('Could not move player: validating line failed', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$gamestates = new lrGamestates();
		$gamestates->saveGameaction($this->configuration->getConfiguration('gamedefinitions', 'actions', 'move'), $correctedMove[0].','.$correctedMove[1]);

		$this->debug->unguard(true);
		return true;
	}
	

	public function playGamecard($gamecard)
	{
		$this->debug->guard();
		
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not play gamecard: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not play gamecard: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
/*
		if (!$this->validateTurn())
		{
			$this->debug->write('Could not play gamecard: it is another players turn', 'warning');
			$this->messages->setMessage('Could not play gamecard: it is another players turn', 'warning');
			$this->debug->unguard(false);
			return false;
		}
*/
		$gamecardfunctions = new lrGamecardfunctions();
		if (!$gamecardfunctions->checkRights($gamecard, $currentGamestates['currentPlayer']))
		{
			$this->debug->write('Could not play gamecard: no rights to play gamecard', 'warning');
			$this->messages->setMessage('Could not play gamecard: no rights to play gamecard', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$gamecardData = $gamecardfunctions->getGamecardData($gamecard))
		{
			$this->debug->write('Could not play gamecard: gamecard not found', 'warning');
			$this->messages->setMessage('Could not play gamecard: gamecard not found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$gamestates = new lrGamestates();
		$gamestates->saveGameaction($this->configuration->getConfiguration('gamedefinitions', 'actions', 'playgamecard'), $gamecard);
		
		// TODO: player, round
		$gameeventhandler = new lrGameeventhandler();
		
		$gameeventhandler->saveRaceevent($currentGamestates['currentPlayer'], $this->configuration->getConfiguration('gamedefinitions', 'events', 'playgamecard'), $gamecard, ($currentGamestates['currentRound']+$gamecardData['gamecard_roundoffset']));
		$gamecardfunctions->removeGamecard($gamecard, $currentGamestates['currentPlayer']);

		$this->debug->unguard(true);
		return true;
	}

}

?>
