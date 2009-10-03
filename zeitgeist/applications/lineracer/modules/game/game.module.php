<?php

defined('LINERACER_ACTIVE') or die();

class game
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $lruser;
	protected $objects;
	protected $dataserver;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->objects = zgObjects::init();
		$this->user = zgUserhandler::init();
		$this->dataserver = new zgDataserver();
		
		$this->lruser = new lrUserfunctions();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('game', 'templates', 'game_index'));
		
		$userfunctions = new lrUserfunctions();
		if (!$userfunctions->currentlyPlayingGame())
		{
			$this->debug->write('Could not join game: user not part of a game', 'warning');
			$this->messages->setMessage('Could not game lobby: user not part of a game', 'warning');
			$this->debug->unguard(false);
			$tpl->redirect($tpl->createLink('main', 'index'));
			return false;
		}		
		
		// initialize classes
		$gamestates = new lrGamestates();
		$gamecardfunctions = new lrGamecardfunctions();		
		$renderer = new prototypeRenderer();
		$gameeventhandler = new lrGameeventhandler();
		
		// load gamestates
		$gamestates->loadGamestates();

		$currentGamestates = $this->objects->getObject('currentGamestates');
		
		$gameeventhandler->handleRaceeevents();
		$currentGamestates = $this->objects->getObject('currentGamestates');

		if ($gamestates->raceFinished())
		{
			$gamefunctions = new lrGamefunctions();
			$gamefunctions->assessRace();
			$gamefunctions->endRace();
			$tpl->redirect($tpl->createLink('game', 'finished'));			
		}

		// draw current situation based on the gamestates
		$renderer->draw();

		// fill template
		if ($currentGamestates['round']['currentPlayer'] == 1) $tpl->assign('bgcolor', '#00ff00');
		elseif ($currentGamestates['round']['currentPlayer'] == 2) $tpl->assign('bgcolor', '#ff0000');
		elseif ($currentGamestates['round']['currentPlayer'] == 3) $tpl->assign('bgcolor', '#0000ff');
		else $tpl->assign('bgcolor', '#000000');
		
		$tpl->assign('round', $currentGamestates['round']['currentRound']);

		$userdeck = $gamecardfunctions->getPlayerDeck();

		foreach ($userdeck as $gamecard)
		{
			$tpl->assign('gamecard_id', $gamecard['gamecard_id']);
			$tpl->assign('gamecard_name', $gamecard['gamecard_name']);
			$tpl->assign('gamecard_description', $gamecard['gamecard_description']);
			$tpl->insertBlock('gamecard');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function init($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();

		$userfunctions = new lrUserfunctions();
		if (!$userfunctions->currentlyPlayingGame())
		{
			$this->debug->write('Could not init game: user not part of a game', 'warning');
			$this->messages->setMessage('Could not init game: user not part of a game', 'warning');

			$this->debug->unguard(false);
			$tpl->redirect($tpl->createLink('main', 'index'));
			return false;
		}

		// initialize classes
		$gamestates = new lrGamestates();
		$dataserver = new zgDataserver();

		// load gamestates
		$gamestates->loadGamestates();
		$currentGamestates = $this->objects->getObject('currentGamestates');
		
		$xmlData = $dataserver->createXMLDatasetFromArray($currentGamestates);
		$dataserver->streamXMLDataset($xmlData);

		$this->debug->unguard(true);
		return true;
	}


	public function update($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();

		$userfunctions = new lrUserfunctions();
		if (!$userfunctions->currentlyPlayingGame())
		{
			$this->debug->write('Could not init game: user not part of a game', 'warning');
			$this->messages->setMessage('Could not init game: user not part of a game', 'warning');

			$this->debug->unguard(false);
			$tpl->redirect($tpl->createLink('main', 'index'));
			return false;
		}

		// initialize classes
		$gamestates = new lrGamestates();
		$dataserver = new zgDataserver();

		// load gamestates
		$gamestates->loadGamestates();
		$currentGamestates = $this->objects->getObject('currentGamestates');
		
		$xmlData = $dataserver->createXMLDatasetFromArray($currentGamestates);
		$dataserver->streamXMLDataset($xmlData);

		$this->debug->unguard(true);
		return true;
	}


	public function move($parameters=array())
	{
		$this->debug->guard();

		if ( (empty($parameters['position_x'])) || (empty($parameters['position_y'])) )
		{
			$this->debug->write('Could not move player: no movement detected', 'warning');
			$this->messages->setMessage('Could not move player: no movement detected', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$gamefunctions = new lrGamefunctions();
		$gamefunctions->move($parameters['position_x'], $parameters['position_y']);
		
		$currentGamestates = $this->objects->getObject('currentGamestates');

		// redirect to game overview
//		$tpl = new lrTemplate();
//		$tpl->redirect($tpl->createLink('game', 'index'));

		$this->debug->unguard(true);
		return true;
	}


	public function playgamecard($parameters=array())
	{
		$this->debug->guard();

		if (empty($parameters['gamecard']))
		{
			$this->debug->write('Could not play gamecard: no gamecard detected', 'warning');
			$this->messages->setMessage('Could not play gamecard: no gamecard detected', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		// TODO check if gamecards are allowed in the game
		
		$gamefunctions = new lrGamefunctions();
		$gamefunctions->playgamecard($parameters['gamecard']);
		
		// redirect to game overview
		$tpl = new lrTemplate();
		$tpl->redirect($tpl->createLink('game', 'index'));

		$this->debug->unguard(true);
		return true;
	}
	
	
	public function finished($parameters=array())
	{
		$this->debug->guard();

		// redirect to index
		$tpl = new lrTemplate();
		$tpl->redirect($tpl->createLink('main', 'index'));

		// Show Game results
		echo "Game results<br />";

		$this->debug->unguard(true);
		return true;
	}
	
	
	public function forfeit($parameters=array())
	{
		$this->debug->guard();

		$gamestates = new lrGamestates();
		$gamestates->loadGamestates();
		$currentGamestates = $this->objects->getObject('currentGamestates');
		
		$gamefunctions = new lrGamefunctions();

		if (!$gamefunctions->forfeit())
		{
			// redirect to lobby overview
			$tpl = new lrTemplate();
			$tpl->redirect($tpl->createLink('lobby', 'index'));
		}

		$tpl = new lrTemplate();
		$tpl->redirect($tpl->createLink('game', 'index'));

		$this->debug->unguard(true);
		return true;
	}
		

	public function reset($parameters=array())
	{
		$this->debug->guard();

		$this->database->query('TRUNCATE TABLE races');
		$this->database->query('TRUNCATE TABLE race_actions');
		$this->database->query('TRUNCATE TABLE race_to_users');
		$this->database->query('TRUNCATE TABLE race_events');
		$this->database->query('TRUNCATE TABLE race_actions');
		$this->database->query('TRUNCATE TABLE gamecards_to_users');

		$sql = "INSERT INTO gamecards_to_users(usergamecard_user, usergamecard_gamecard, usergamecard_count) VALUES('1', '1', '1'), ('1', '2', '1'), ('1', '3', '1')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO gamecards_to_users(usergamecard_user, usergamecard_gamecard, usergamecard_count) VALUES('2', '1', '1'), ('2', '2', '1'), ('2', '3', '1')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO gamecards_to_users(usergamecard_user, usergamecard_gamecard, usergamecard_count) VALUES('3', '1', '1'), ('3', '2', '1'), ('3', '3', '1')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES('1', '1', '1', '150,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES('1', '2', '1', '170,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES('1', '3', '1', '190,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO races(race_id, race_circuit, race_activeplayer, race_currentround, race_gamecardsallowed)";
		$sql .= "VALUES(1, 1, 1, 1, 1)";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_to_users(raceuser_race, raceuser_user, raceuser_order) VALUES('1', '1', '1')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_to_users(raceuser_race, raceuser_user, raceuser_order) VALUES('1', '2', '2')";
		$res = $this->database->query($sql);

		$tpl = new lrTemplate();
		$tpl->redirect($tpl->createLink('game', 'index'));

		$this->debug->unguard(true);
		return true;
	}
}
?>
