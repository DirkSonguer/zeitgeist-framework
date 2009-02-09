<?php

defined('LINERACER_ACTIVE') or die();

class lobby
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $lruser;
	protected $lobbyfunctions;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		$this->lruser = new lrUserfunctions();
		$this->lobbyfunctions = new lrLobbyfunctions();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function index($parameters=array())
	{
		$this->debug->guard();

		if ($this->lruser->waitingForGame())
		{
			$this->debug->write('User is already waiting. Internal redirect to gameroom', 'warning');
			$this->messages->setMessage('User is already waiting. Internal redirect to gameroom', 'warning');
			$ret = $this->showgameroom($parameters);
			$this->debug->unguard($ret);
			return $ret;
		}

		$tpl = new lrTemplate();

		if ($this->lruser->currentlyPlayingGame())
		{
			$this->debug->write('Game has started. Internal redirect to game', 'warning');
			$this->messages->setMessage('Game has started. Redirect to game', 'warning');
			$this->debug->unguard(true);
			$tpl->redirect($tpl->createLink('game', 'index'));
			return true;
		}

		$tpl->load($this->configuration->getConfiguration('lobby', 'templates', 'lobby_showlobby'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function joingameroom($parameters=array())
	{
		$this->debug->guard();

		if ($this->lruser->waitingForGame())
		{
			$this->debug->write('User is already waiting. Internal redirect to gameroom', 'warning');
			$this->messages->setMessage('User is already waiting. Internal redirect to gameroom', 'warning');
			$ret = $this->showgameroom($parameters);
			$this->debug->unguard($ret);
			return $ret;
		}

		$tpl = new lrTemplate();

		if ($this->lruser->currentlyPlayingGame())
		{
			$this->debug->write('Game has started. Internal redirect to game', 'warning');
			$this->messages->setMessage('Game has started. Redirect to game', 'warning');
			$this->debug->unguard(true);
			$tpl->redirect($tpl->createLink('game', 'index'));
			return true;
		}

		$this->lobbyfunctions->joinGameroom(intval($parameters['lobbyid']));

		$tpl = new lrTemplate();
		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('lobby', 'showgameroom'));
		return true;
	}


	public function showgameroom($parameters=array())
	{
		$this->debug->guard();

		if (!$this->lruser->waitingForGame())
		{
			$ret = $this->index($parameters);
			$this->debug->unguard($ret);
			return $ret;
		}

		$tpl = new lrTemplate();

		if ($this->lruser->currentlyPlayingGame())
		{
			$this->debug->write('Game has started. Internal redirect to game', 'warning');
			$this->messages->setMessage('Game has started. Redirect to game', 'warning');
			$this->debug->unguard(true);
			$tpl->redirect($tpl->createLink('game', 'index'));
			return true;
		}

		$tpl->load($this->configuration->getConfiguration('lobby', 'templates', 'lobby_showgameroom'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}
	

	public function leavegameroom($parameters=array())
	{
		$this->debug->guard();

		if (!$this->lruser->waitingForGame())
		{
			$ret = $this->index($parameters);
			$this->debug->unguard($ret);
			return $ret;
		}

		$this->lobbyfunctions->leaveGameroom();

		$tpl = new lrTemplate();
		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('lobby', 'index'));
		return true;
	}


	public function confirmgame($parameters=array())
	{
		$this->debug->guard();

		if (!$this->lruser->waitingForGame())
		{
			$ret = $this->index($parameters);
			$this->debug->unguard($ret);
			return $ret;
		}

		if ($this->lobbyfunctions->setConfirmation())
		{
			$currentLobby = $this->lobbyfunctions->getLobbyID();
			if ( ($currentLobby > 0) && ($this->lobbyfunctions->checkGameConfirmation($currentLobby)) )
			{
				$gamefunctions = new lrGamefunctions();
				$gamefunctions->startGame($currentLobby);
				
				$tpl = new lrTemplate();
				$this->debug->unguard(true);
				$tpl->redirect($tpl->createLink('game', 'index'));
				return true;
			}
		}

		$tpl = new lrTemplate();
		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('lobby', 'showgameroom'));
		return true;
	}


// TODO: alt
	public function creategameroom($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('lobby', 'templates', 'lobby_creategame'));


		$creategameroomForm = new zgStaticform();
		$creategameroomForm->load('forms/creategameroom.form.ini');
		$formvalid = $creategameroomForm->process($parameters);

		if (!empty($parameters['submit']))
		{
			if ($formvalid)
			{
				$this->lobbyfunctions->createGameroom($parameters['creategameroom']['race_circuit'], $parameters['creategameroom']['race_maxplayers'], $parameters['creategameroom']['race_gamecardsallowed']);
				$this->debug->unguard(true);
				$tpl->redirect($tpl->createLink('lobby', 'showgameroom'));
				return true;
			}
			else
			{
				$this->messages->setMessage('Bitte füllen Sie alle Formularfelder korrekt aus.', 'userwarning');
			}
		}

		$formcreated = $creategameroomForm->create($tpl);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
