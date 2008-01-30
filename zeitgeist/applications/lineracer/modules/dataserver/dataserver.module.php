<?php


defined('LINERACER_ACTIVE') or die();

include_once('includes/open-flash-chart/open-flash-chart.php');

class dataserver
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $dataserver;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		$this->dataserver = new zgDataserver();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function getlobbydata($parameters=array())
	{
		$this->debug->guard();

		$sql = 'SELECT l.lobby_id, l.lobby_maxplayers, c.circuit_name, c.circuit_description, COUNT(lu.lobbyuser_user) as lobby_currentplayers ';
		$sql .= 'FROM lobby l LEFT JOIN circuits c ON l.lobby_circuit = c.circuit_id LEFT JOIN lobbyusers lu ON l.lobby_id = lu.lobbyuser_lobby ';
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

		$pregamefunctions = new lrPregamefunctions();
		$gameroomData = $pregamefunctions->getPlayerGameroomData();
		if ($gameroomData !== false)
		{
			$sql = 'SELECT l.*, lu.*, u.user_username, c.circuit_name, c.circuit_description FROM lobbyusers lu ';
			$sql .= 'LEFT JOIN users u ON lu.lobbyuser_user = u.user_id ';
			$sql .= 'LEFT JOIN lobby l ON lu.lobbyuser_lobby = l.lobby_id ';
			$sql .= 'LEFT JOIN circuits c ON l.lobby_circuit = c.circuit_id ';
			$sql .= "WHERE l.lobby_id='" . $gameroomData['lobby_id'] . "'";

			$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
			$this->dataserver->streamXMLDataset($xmlData);
			die();
		}

		$this->debug->unguard(false);
		return false;
	}

}
?>
