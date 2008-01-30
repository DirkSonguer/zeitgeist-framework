<?php

defined('LINERACER_ACTIVE') or die();

class pregame
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function showlobby($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('pregame', 'templates', 'pregame_showlobby'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function joingame($parameters=array())
	{
		$this->debug->guard();

		$pregamefunctions = new lrPregamefunctions();
		if ($pregamefunctions->playerWaitingForGame())
		{
			$this->messages->setMessage('Wartest bereits', 'userwarning');
			$ret = $this->showlobby($parameters);
			$this->debug->unguard(true);
			return $ret;
		}

		if (empty($parameters['lobbyid']))
		{
			$ret = $this->showlobby($parameters);
			$this->debug->unguard(true);
			return true;
		}

		$sql = 'SELECT l.lobby_id, l.lobby_maxplayers, COUNT(lu.lobbyuser_user) as lobby_currentplayers ';
		$sql .= 'FROM lobby l LEFT JOIN circuits c ON l.lobby_circuit = c.circuit_id LEFT JOIN lobbyusers lu ON l.lobby_id = lu.Lobbyuser_lobby ';
		$sql .= "WHERE l.lobby_id='" . intval($parameters['lobbyid']) . "' GROUP BY l.lobby_id";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);
		if (!$row)
		{
			$ret = $this->showlobby($parameters);
			$this->debug->unguard(true);
			return $ret;
		}

		if ($row['lobby_currentplayers'] >= $row['lobby_maxplayers'])
		{
			$this->messages->setMessage('Spiel belegt', 'userwarning');
			$ret = $this->showlobby($parameters);
			$this->debug->unguard($ret);
			return $ret;
		}

		$currentUserId = $this->user->getUserID();
		$sql = "INSERT INTO lobbyusers(lobbyuser_lobby, lobbyuser_user) VALUES('" . $row['lobby_id'] . "', '" . $currentUserId . "')";
		$res = $this->database->query($sql);

		$tpl = new lrTemplate();
		$this->debug->unguard(true);
		$linkparameters = array();
		$tpl->redirect($tpl->createLink('pregame', 'showgameroom'));
		return true;
	}


	public function showgameroom($parameters=array())
	{
		$this->debug->guard();

		$pregamefunctions = new lrPregamefunctions();
		if (!$pregamefunctions->playerWaitingForGame())
		{
			$ret = $this->showlobby($parameters);
			$this->debug->unguard($ret);
			return $ret;
		}

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('pregame', 'templates', 'pregame_showgameroom'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function leavegameroom($parameters=array())
	{
		$this->debug->guard();

		$pregamefunctions = new lrPregamefunctions();
		if (!$pregamefunctions->playerWaitingForGame())
		{
			$tpl = new lrTemplate();
			$this->debug->unguard(true);
			$linkparameters = array();
			$tpl->redirect($tpl->createLink('pregame', 'showlobby'));
			return true;
		}

		$pregamefunctions->leaveGameroom();

		$tpl = new lrTemplate();
		$this->debug->unguard(true);
		$linkparameters = array();
		$tpl->redirect($tpl->createLink('pregame', 'showlobby'));
		return true;
	}


	public function creategame($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('pregame', 'templates', 'pregame_creategame'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


}
?>
