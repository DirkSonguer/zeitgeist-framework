<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Lobbyfunctions class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package LINERACER
 * @subpackage LINERACER CLASSES
 */

defined('LINERACER_ACTIVE') or die();

class lrLobbyfunctions
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
		$this->objects = zgObjects::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();
	}
	
	
	public function joinGameroom($lobbyid)
	{
		$this->debug->guard();
		
		$sql = 'SELECT l.lobby_id, l.lobby_maxplayers, COUNT(lu.lobbyuser_user) as lobby_currentplayers ';
		$sql .= 'FROM lobby l LEFT JOIN circuits c ON l.lobby_circuit = c.circuit_id ';
		$sql .= 'LEFT JOIN lobby_to_users lu ON l.lobby_id = lu.lobbyuser_lobby ';
		$sql .= "WHERE l.lobby_id='" . $lobbyid . "' GROUP BY l.lobby_id";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);
		if (!$row)
		{
			$this->debug->write('Could not join gameroom: lobby with given ID not found', 'warning');
			$this->messages->setMessage('Could not join gameroom: lobby with given ID not found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if ($row['lobby_currentplayers'] >= $row['lobby_maxplayers'])
		{
			$this->debug->write('Could not join gameroom: max number of players reached', 'warning');
			$this->messages->setMessage('Could not join gameroom: max number of players reached', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "INSERT INTO lobby_to_users(lobbyuser_lobby, lobbyuser_user) VALUES('" . $lobbyid . "', '" . $this->user->getUserID() . "')";
		$res = $this->database->query($sql);

		$this->debug->unguard(true);
		return true;
	}


	public function leaveGameroom()
	{
		$this->debug->guard();

		$sql = "DELETE FROM lobby_to_users WHERE lobbyuser_user='" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not leave gameroom: user could not be deleted from lobby', 'warning');
			$this->messages->setMessage('Could not leave gameroom: user could not be deleted from lobby', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->debug->unguard(true);
		return true;
	}

}

?>
