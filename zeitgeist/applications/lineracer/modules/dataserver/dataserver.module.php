<?php


defined('LINERACER_ACTIVE') or die();

class dataserver
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $lruser;
	protected $dataserver;
	protected $objects;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->objects = zgObjects::init();
		$this->user = zgUserhandler::init();
		$this->lruser = new lrUserfunctions();
		$this->dataserver = new zgDataserver();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


// TODO: alt
	public function getlobbydata($parameters=array())
	{
		$this->debug->guard();

		$sql = 'SELECT l.lobby_id, l.lobby_maxplayers, c.circuit_name, c.circuit_description, COUNT(lu.lobbyuser_user) as lobby_currentplayers ';
		$sql .= 'FROM lobby l LEFT JOIN circuits c ON l.lobby_circuit = c.circuit_id LEFT JOIN lobby_to_users lu ON l.lobby_id = lu.lobbyuser_lobby ';
		$sql .= 'GROUP BY l.lobby_id';

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(false);
		return false;
	}


	public function getgameroomdata($parameters=array())
	{
		$this->debug->guard();

		if (!$this->lruser->waitingForGame())
		{
			$this->debug->write('User is not waiting for a game', 'warning');
			$this->messages->setMessage('User is not waiting for a game', 'warning');
			$this->debug->unguard($ret);
			return false;
		}

		$sql = 'SELECT l.*, lu.*, u.user_username, c.circuit_name, c.circuit_description FROM lobby l ';
		$sql .= 'LEFT JOIN lobby_to_users lu ON l.lobby_id = lu.lobbyuser_lobby ';
		$sql .= 'LEFT JOIN users u ON lu.lobbyuser_user = u.user_id ';
		$sql .= 'LEFT JOIN circuits c ON l.lobby_circuit = c.circuit_id ';
		$sql .= "WHERE l.lobby_id = (SELECT lobbyuser_lobby FROM lobby_to_users WHERE lobbyuser_user = '" . $this->user->getUserID() . "')";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();
	}


	public function getgamestates($parameters=array())
	{
		$this->debug->guard();

		// load gamestates
		$gamestates = new lrGamestates();
		$gamestates->loadGamestates();

		// check if gamestates are loaded
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not move player: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not move player: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$xmlData = $this->dataserver->createXMLDatasetFromArray($currentGamestates);
		$this->dataserver->streamXMLDataset($xmlData);
		die();
	}
	
}
?>
