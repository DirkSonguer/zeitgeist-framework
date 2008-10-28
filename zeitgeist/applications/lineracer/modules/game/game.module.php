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

// TODO: alt
	public function index($parameters=array())
	{
		$this->debug->guard();

		$gamefunctions = new lrGamefunctions;

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('game', 'templates', 'game_index'));

		$gamestates = new lrGamestates();
		$gamestates->loadGamestates(1);
		$currentGamestates = $this->objects->getObject('currentGamestates');
	
		$gamecardfunctions = new lrGamecardfunctions();
		
		$renderer = new prototypeRenderer();
		$renderer->draw();

		if ($currentGamestates['currentPlayer'] == 1) $tpl->assign('bgcolor', '#00ff00');
		elseif ($currentGamestates['currentPlayer'] == 2) $tpl->assign('bgcolor', '#ff0000');
		elseif ($currentGamestates['currentPlayer'] == 3) $tpl->assign('bgcolor', '#0000ff');
		else $tpl->assign('bgcolor', '#000000');

		$tpl->assign('bgcolor_p1', '#00ff00');
		$tpl->assign('bgcolor_p2', '#ff0000');
		$tpl->assign('bgcolor_p3', '#0000ff');
		$tpl->assign('bgcolor_p4', '#ff6000');

		if ($gamecardfunctions->checkRights('1', $currentGamestates['currentPlayer'])) $tpl->assign('bgcolor_card1', '#ffff00');
			else $tpl->assign('bgcolor_card1', '#cccc00');
		if ($gamecardfunctions->checkRights('2', $currentGamestates['currentPlayer'])) $tpl->assign('bgcolor_card2', '#ffff00');
			else $tpl->assign('bgcolor_card2', '#cccc00');
		if ($gamecardfunctions->checkRights('3', $currentGamestates['currentPlayer'])) $tpl->assign('bgcolor_card3', '#ffff00');
			else $tpl->assign('bgcolor_card3', '#cccc00');

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function move($parameters=array())
	{
		$this->debug->guard();

		$gamefunctions = new lrGamefunctions();
	
		$gamestates = new lrGamestates();
		$currentGamestates = $gamestates->loadGamestates(1);
		
		$ret = $gamefunctions->move($parameters['position_x'], $parameters['position_y']);
	
		$tpl = new lrTemplate();
		$tpl->redirect($tpl->createLink('game', 'index'));

		$this->debug->unguard(true);
		return true;
	}


	public function playgamecard($parameters=array())
	{
		$this->debug->guard();

		$gamefunctions = new lrGamefunctions();
	
		$gamestates = new lrGamestates();
		$currentGamestates = $gamestates->loadGamestates(1);
		
		$ret = $gamefunctions->playGamecard($parameters['gamecard']);
	
		$tpl = new lrTemplate();
		$tpl->redirect($tpl->createLink('game', 'index'));

		$this->debug->unguard(true);
		return true;
	}
	

	public function reset($parameters=array())
	{
		$this->debug->guard();

		$sql = "TRUNCATE TABLE race_moves";
		$res = $this->database->query($sql);
		$sql = "TRUNCATE TABLE race_eventhandler";
		$res = $this->database->query($sql);
		$sql = "TRUNCATE TABLE users_to_gamecards";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO users_to_gamecards(usergamecard_user, usergamecard_gamecard, usergamecard_count) VALUES('1', '1', '1'), ('1', '2', '1'), ('1', '3', '1')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO users_to_gamecards(usergamecard_user, usergamecard_gamecard, usergamecard_count) VALUES('2', '1', '1'), ('2', '2', '1'), ('2', '3', '1')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO users_to_gamecards(usergamecard_user, usergamecard_gamecard, usergamecard_count) VALUES('3', '1', '1'), ('3', '2', '1'), ('3', '3', '1')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('1', '1', '1', '150,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('1', '2', '1', '170,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('1', '3', '1', '190,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_moves(move_race, move_user, move_action, move_parameter) VALUES('1', '4', '1', '210,370')";
		$res = $this->database->query($sql);
		$sql = "UPDATE races SET race_currentround='1', race_activeplayer='1' WHERE race_id='1'";
		$res = $this->database->query($sql);
	
		$tpl = new lrTemplate();
		$tpl->redirect($tpl->createLink('game', 'index'));

		$this->debug->unguard(true);
		return true;
	}
}
?>
