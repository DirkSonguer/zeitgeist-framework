<?php

defined('LINERACER_ACTIVE') or die();

class lrStatisticfunctions
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $session;
	protected $objects;
	protected $configuration;
	protected $user;
	protected $lruser;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->session = zgSession::init();
		$this->configuration = zgConfiguration::init();
		$this->objects = zgObjects::init();
		$this->user = zgUserhandler::init();
		
		$this->lruser = new lrUserfunctions();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * gets the user statistics for the current player
	 * includes: racepointsm wins, ties and losses
	 *
	 * @return array
	 */
	public function getUserStatistics()
	{
		$this->debug->guard();

		$sql = "SELECT userdata_racepoints, userdata_raceswon, userdata_raceslost FROM userdata WHERE userdata_user = '" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get number of racepoints: could not query userdata', 'warning');
			$this->messages->setMessage('Could not get number of racepoints: could not query userdata', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$row = $this->database->fetchArray($res);
		
		$ret = array();
		$ret['userdata_racepoints'] = $row['userdata_racepoints'];
		$ret['userdata_raceswon'] = $row['userdata_raceswon'];
		$ret['userdata_raceslost'] = $row['userdata_raceslost'];

		$this->debug->unguard($ret);
		return $ret;
	}
	

	/**
	 * adds a number of racepoints to the player
	 *
	 * @param integer $racepoints number of racepoints to add
	 *
	 * @return boolean
	 */
	public function addRacepoints($racepoints)
	{
		$this->debug->guard();

		$sql = "UPDATE userdata SET userdata_racepoints = userdata_racepoints + " . $racepoints . " WHERE userdata_user = '" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not add racepoints: could not update userdata', 'warning');
			$this->messages->setMessage('Could not add racepoints: could not update userdata', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->lruser->insertTransaction($this->configuration->getConfiguration('gamedefinitions', 'transaction_types', 'racepoits_add'), $racepoints);

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * substracts a number of racepoints to the player
	 *
	 * @param integer $racepoints number of racepoints to substract
	 *
	 * @return boolean
	 */
	public function substractRacepoints($racepoints)
	{
		$this->debug->guard();

		$sql = "UPDATE userdata SET userdata_racepoints = userdata_racepoints - " . $racepoints . " WHERE userdata_user = '" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not substract racepoints: could not update userdata', 'warning');
			$this->messages->setMessage('Could not substract racepoints: could not update userdata', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->lruser->insertTransaction($this->configuration->getConfiguration('gamedefinitions', 'transaction_types', 'racepoints_substract'), $racepoints);

		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * adds a win to the current player and rewards him the associated racepoints
	 *
	 * @return boolean
	 */
	public function addWin()
	{
		$this->debug->guard();

		$sql = "UPDATE userdata SET userdata_raceswon = userdata_raceswon + 1 WHERE userdata_user = '" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not add win: could not update userdata', 'warning');
			$this->messages->setMessage('Could not add win: could not update userdata', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->lruser->insertTransaction($this->configuration->getConfiguration('gamedefinitions', 'transaction_types', 'race_won'), $racepoints);

		$points = $this->configuration->getConfiguration('gamedefinitions', 'points', 'won');
		$this->addRacepoints($points);

		$this->debug->unguard(true);
		return true;
	}
	

	/**
	 * adds a loose to the current player and rewards him the associated racepoints
	 *
	 * @return boolean
	 */
	public function addLoss()
	{
		$this->debug->guard();

		$sql = "UPDATE userdata SET userdata_raceslost = userdata_raceslost + 1 WHERE userdata_user = '" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not add win: could not update userdata', 'warning');
			$this->messages->setMessage('Could not add win: could not update userdata', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->lruser->insertTransaction($this->configuration->getConfiguration('gamedefinitions', 'transaction_types', 'race_lost'), $racepoints);

		$points = $this->configuration->getConfiguration('gamedefinitions', 'points', 'lost');
		$this->addRacepoints($points);

		$this->debug->unguard(true);
		return true;
	}
}
?>
