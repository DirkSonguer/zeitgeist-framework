<?php

defined('LINERACER_ACTIVE') or die();

class lrGamefunctions
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $session;
	protected $objects;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->session = zgSession::init();
		$this->configuration = zgConfiguration::init();
		$this->objects = zgObjectcache::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * moves a player to the given position
	 *
	 * @param integer $position_x x position to move to
	 * @param integer $position_y y position to move to
	 *
	 * @return boolean
	 */
	public function move($position_x, $position_y)
	{
		$this->debug->guard();

		// load current gamestates
		$gamestates = new lrGamestates();
		$currentGamestates = $gamestates->loadGamestates(1);
		
		// check status of current gamestates
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// load pre turn events
		$gameeventhandler = new lrGameeventhandler();
		$gameeventhandler->handleRaceeevents();

		// validate if it's the players turn
		$userfunctions = new lrUserfunctions();
		if (!$userfunctions->validateTurn())
		{
			$this->debug->write('Could not move player: not the players turn', 'warning');
			$this->messages->setMessage('Could not move player: not the players turn', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// validate the movement
		$movementfunctions = new lrMovementfunctions();
		if (!$movementfunctions->validateMove($position_x, $position_y))
		{
			$this->debug->write('Could not move player: player moved outside its allowed area', 'warning');
			$this->messages->setMessage('Could not move player: player moved outside its allowed area', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// check terrain and correct movement value
		$correctedMove = array();
		if (!$correctedMove = $movementfunctions->validateTerrain($position_x, $position_y))
		{
			$this->debug->write('Could not move player: validating line failed', 'warning');
			$this->messages->setMessage('Could not move player: validating line failed', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// save race action and handle post turn events
		$gameeventhandler->saveRaceaction($this->configuration->getConfiguration('gamedefinitions', 'actions', 'move'), $correctedMove[0].','.$correctedMove[1]);
		$gamestates->endTurn();

		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * plays a given gamecard
	 *
	 * @param integer $gamecard gamecard to play
	 *
	 * @return boolean
	 */
	public function playgamecard($gamecard)
	{
		$this->debug->guard();

		// load current gamestates
		$gamestates = new lrGamestates();
		$currentGamestates = $gamestates->loadGamestates(1);
	
		// check status of current gamestates		
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not play gamecard: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not play gamecard: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// load pre turn events
		$gameeventhandler = new lrGameeventhandler();
		$gameeventhandler->handleRaceeevents();

		// validate if it's the players turn
		$userfunctions = new lrUserfunctions();
		if (!$userfunctions->validateTurn())
		{
			$this->debug->write('Could not move player: not the players turn', 'warning');
			$this->messages->setMessage('Could not move player: not the players turn', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// validate if the player has the right for the gamecard
		$gamecardfunctions = new lrGamecardfunctions();
		if (!$gamecardfunctions->checkRights($gamecard, $currentGamestates['currentPlayer']))
		{
			$this->debug->write('Could not play gamecard: no rights to play gamecard', 'warning');
			$this->messages->setMessage('Could not play gamecard: no rights to play gamecard', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// get gamecard data
		if (!$gamecardData = $gamecardfunctions->getGamecardData($gamecard))
		{
			$this->debug->write('Could not play gamecard: gamecard not found', 'warning');
			$this->messages->setMessage('Could not play gamecard: gamecard not found', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// save race action and handle post turn events
		$gameeventhandler->saveRaceaction($this->configuration->getConfiguration('gamedefinitions', 'actions', 'playgamecard'), $gamecard);
		$gameeventhandler->saveRaceevent($currentGamestates['currentPlayer'], $this->configuration->getConfiguration('gamedefinitions', 'events', 'playgamecard'), $gamecard, ($currentGamestates['currentRound']+$gamecardData['gamecard_roundoffset']));
		$gamecardfunctions->removeGamecard($gamecard, $currentGamestates['currentPlayer']);
		
		$this->debug->unguard(true);
		return true;
	}	

}
?>
