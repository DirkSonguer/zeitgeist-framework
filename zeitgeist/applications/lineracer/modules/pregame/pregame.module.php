<?php

defined('LINERACER_ACTIVE') or die();

class pregame
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $miscfunctions;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		$this->miscfunctions = lrMiscfunctions::init();

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

		if ($this->miscfunctions->playerWaitingForGame())
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

		$sql = "SELECT l.*, c.* FROM lobby l LEFT JOIN circuits c ON l.lobby_circuit = c.circuit_id WHERE l.lobby_id='" . intval($parameters['lobbyid']) . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);
		if (!$row)
		{
			$ret = $this->showlobby($parameters);
			$this->debug->unguard(true);
			return $ret;
		}

		$maxPlayers = $row['lobby_maxplayers'];
		$slotFree = false;

		for ($i=2; $i<=$maxPlayers; $i++)
		{
			if ($row['lobby_player' . $i] == '')
			{
				$slotFree = true;
			}
		}

		if ($slotFree)
		{
			$tpl = new lrTemplate();
			$this->debug->unguard(true);
			$linkparameters = array();
			$tpl->redirect($tpl->createLink('pregame', 'showgameroom'));
			return true;
		}
		else
		{
			$this->messages->setMessage('Spiel belegt', 'userwarning');
			$ret = $this->showlobby($parameters);
			$this->debug->unguard($ret);
			return $ret;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function showgameroom($parameters=array())
	{
		$this->debug->guard();

		$tpl = new lrTemplate();
		$tpl->load($this->configuration->getConfiguration('pregame', 'templates', 'pregame_showgameroom'));

		if (!$this->miscfunctions->playerWaitingForGame())
		{
			$ret = $this->showlobby($parameters);
			$this->debug->unguard(true);
			return $ret;
		}

		$tpl->assign('lobbyid', $row['lobby_id']);

		$tpl->show();

		$this->debug->unguard(true);
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
