<?php

defined('LINERACER_ACTIVE') or die();

class lrUserfunctions
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $session;
	protected $objects;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->session = zgSession::init();
		$this->configuration = zgConfiguration::init();
		$this->objects = zgObjects::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * changes the userdata for the current user
	 *
	 * @param array $userdata array that contains all userdata fields as array fields
	 *
	 * @return boolean
	 */
	public function changeUserdata($userdata)
	{
		$this->debug->guard();

		if (!empty($userdata['userdata_lastname'])) $this->user->setUserdata('userdata_lastname', $userdata['userdata_lastname'], false);
		if (!empty($userdata['userdata_firstname'])) $this->user->setUserdata('userdata_firstname', $userdata['userdata_firstname'], false);
		if (!empty($userdata['userdata_address1'])) $this->user->setUserdata('userdata_address1', $userdata['userdata_address1'], false);
		if (!empty($userdata['userdata_address2'])) $this->user->setUserdata('userdata_address2', $userdata['userdata_address2'], false);
		if (!empty($userdata['userdata_zip'])) $this->user->setUserdata('userdata_zip', $userdata['userdata_zip'], false);
		if (!empty($userdata['userdata_city'])) $this->user->setUserdata('userdata_city', $userdata['userdata_city'], false);
		if (!empty($userdata['userdata_url'])) $this->user->setUserdata('userdata_url', $userdata['userdata_url'], false);

		$this->user->saveUserdata();

		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * validates that it is the users turn
	 *
	 * @return boolean
	 */
	public function validateTurn()
	{
		$this->debug->guard();
		
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not validate player turn: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not validate player turn: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if (empty($currentGamestates['players'][($currentGamestates['currentPlayer']-1)]))
		{
			$this->debug->write('Could not validate player turn: could not find player data in the game data', 'warning');
			$this->messages->setMessage('Could not validate player turn: could not find player data in the game data', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if ($currentGamestates['players'][($currentGamestates['currentPlayer']-1)] != $this->user->getUserID())
		{
			$this->debug->write('Could not validate player turn: player not found active', 'warning');
			$this->messages->setMessage('Could not validate player turn: player not found active', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;		
	}


	/**
	 * checks if a user is in a lobby, waiting for a game
	 *
	 * @return boolean
	 */
	public function waitingForGame()
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM lobby_to_users WHERE lobbyuser_user='" . $this->user->getUserId() . "'";
		$res = $this->database->query($sql);
		if(!$res)
		{
			$this->debug->write('Could validate user lobby status: could get lobby data from database', 'warning');
			$this->messages->setMessage('Could validate user lobby status: could get lobby data from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$playerFound = $this->database->numRows($res);
		if ($playerFound == 0)
		{
			$this->debug->unguard(false);
			return false;		
		}

		$this->debug->unguard(true);
		return true;		
	}


}
?>
