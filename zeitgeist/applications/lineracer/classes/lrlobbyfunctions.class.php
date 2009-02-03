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

		$sql = "INSERT INTO lobby_to_users(lobbyuser_lobby, lobbyuser_user, lobbyuser_ready) VALUES('" . $lobbyid . "', '" . $this->user->getUserID() . "', '0')";
		$res = $this->database->query($sql);

		$this->debug->unguard(true);
		return true;
	}


	public function leaveGameroom()
	{
		$this->debug->guard();

		$sql = "SELECT * FROM lobby_to_users WHERE lobbyuser_user='" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not leave gameroom: user not found in a lobby', 'warning');
			$this->messages->setMessage('Could not leave gameroom: user not found in a lobby', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		$numPlayers = $this->database->numRows($res);
		$row = $this->database->fetchArray($res);

		$sql = "DELETE FROM lobby_to_users WHERE lobbyuser_user='" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not leave gameroom: user could not be deleted from lobby', 'warning');
			$this->messages->setMessage('Could not leave gameroom: user could not be deleted from lobby', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if ($numPlayers < 2)
		{
			$sql = "DELETE FROM lobby WHERE lobby_id='" . $row['lobbyuser_lobby'] . "'";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Could not leave gameroom: could not delete empty gameroom', 'warning');
				$this->messages->setMessage('Could not leave gameroom: could not delete empty gameroom', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function createGameroom($circuit, $maxplayers=1, $gamecardsallowed=1)
	{
		$this->debug->guard();

		$userfunctions = new lrUserfunctions();
		if ($userfunctions->waitingForGame())
		{
			$this->debug->write('Could not create gameroom: player already waiting for game', 'warning');
			$this->messages->setMessage('Could not create gameroom: player already waiting for game', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "INSERT INTO lobby(lobby_circuit, lobby_maxplayers, lobby_gamecardsallowed) VALUES('" . $circuit . "', '" . $maxplayers . "', '" . $gamecardsallowed . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not create gameroom: could not add lobby to database', 'warning');
			$this->messages->setMessage('Could not create gameroom: could not add lobby to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$lobby = $this->database->insertId();
		if (!$lobby)
		{
			$this->debug->write('Could not create gameroom: could not get lobby if from database', 'warning');
			$this->messages->setMessage('Could not create gameroom: could not get lobby if from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->joinGameroom($lobby);
		if (!$ret)
		{
			$this->debug->write('Could not create gameroom: could not join created gameroom', 'warning');
			$this->messages->setMessage('Could not create gameroom: could not join created gameroom', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}
	
	
	public function checkReadyness($lobbyid)
	{
		$this->debug->guard();
		
		$sql = "SELECT COUNT(*) as playersNotReady FROM lobby_to_users WHERE lobbyuser_lobby='" . $lobbyid . "' and lobbyuser_ready='0'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);
		if ($row['playersNotReady'] > 0)
		{
			$this->debug->write('Game is not ready yet: players are still not ready', 'warning');
			$this->messages->setMessage('Game is not ready yet: players are still not ready', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}
	
}

?>
