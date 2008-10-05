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
	
}
?>
