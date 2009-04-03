<?php

defined('LINERACER_ACTIVE') or die();

class lrAchievements
{
	protected $debug;
	protected $messages;
	protected $objects;
	protected $database;
	protected $configuration;
	protected $user;
	protected $achievementfunctions;
	protected $playermoves;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjects::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->achievementfunctions = new lrAchievementfunctions();
		
		$this->database = new zgDatabase();
		$this->database->connect();
	}

	
	/**
	 * Checks if a given user has the given achievement
	 *
	 * @param integer $achievement id of the achievement
	 *
	 * @return array
	 */
	// TODO Add transactions?
	public function assessAchievements()
	{
		$this->debug->guard();
		
		// get all non assessed races from the database
		// normally, this should only be one as a race is assessed after it's finished
		$sqlRaces = "SELECT * FROM race_to_users WHERE raceuser_user='" . $this->user->getUserID() . "' AND raceuser_assessed='0'";
		$resRaces = $this->database->query($sqlRaces);
		if ((!$resRaces) || ($this->database->numRows($resRaces) < 1))
		{
			$this->debug->write('Could not assess achievements: could not find user races to assess in the database', 'warning');
			$this->messages->setMessage('Could not assess achievements: could not find user races to assess in the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$userachievements = $this->achievementfunctions->getPlayerAchievements();
		
		// iterate through all not assessed races
		while($rowRaces = $this->database->fetchArray($resRaces))
		{
			// get player data for the race
			$sqlRacedata = "SELECT * FROM race_actions WHERE raceaction_race='" . $rowRaces['raceuser_race'] . "' AND raceaction_player='" . $rowRaces['raceuser_order'] . "'";
			$resRacedata = $this->database->query($sqlRacedata);
			if (!$resRacedata)
			{
				$this->debug->write('Could not assess achievements: could not get race data for user from database', 'warning');
				$this->messages->setMessage('Could not assess achievements: could not get race data for user from database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
			
			// store the race data into the class variable
			// the data is used by the achievement functions
			while($rowRacedata = $this->database->fetchArray($resRacedata))
			{
				$this->playermoves[] = $rowRacedata;
			}

			// TODO Kann man das nicht aus der Loop rausholen?
			// get a list of all achievements
			$achievementlist = $this->achievementfunctions->getAllAchievements();

			// iterate through all achievements and check if the player has earned it
			foreach($achievementlist as $achievementid => $achievement)
			{
				if (empty($userachievements[$achievementid]))
				{
					// user does not have the achievement yet
					if (method_exists(&$this, $achievement['achievement_function']))
					{
						// check if achievement was earned
						$achievementearned = call_user_func(array(&$this, $achievement['achievement_function']));
						if ($achievementearned)
						{
							// add achievement to list
							$this->achievementfunctions->addAchievement($achievement['achievement_id'], $rowRaces['raceuser_race']);
						}
					}
					else	
					{
						// could not find achievement functions
						$this->debug->write('Could not assess achievement ' . $achievement['achievement_id'] . ': achievement function does not exist', 'warning');
						$this->messages->setMessage('Could not assess achievement ' . $achievement['achievement_id'] . ': achievement function does not exist', 'warning');
					}
				}
				else
				{
					$this->debug->write('Could not assess achievement ' . $achievement['achievement_id'] . ': user already has the achievement', 'warning');
					$this->messages->setMessage('Could not assess achievement ' . $achievement['achievement_id'] . ': user already has the achievement', 'warning');
				}
			}
			
			// done, now set the race as assessed
			$sqlUpdate = "UPDATE race_to_users SET raceuser_assessed = '1' WHERE raceuser_race='" . $rowRaces['raceuser_race'] . "'";
			$resUpdate = $this->database->query($sqlUpdate);
			if (!$resUpdate)
			{
				$this->debug->write('Could not assess achievements: could not update game assesment information', 'warning');
				$this->messages->setMessage('Could not assess achievements: could not update game assesment information', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function fastround1()
	{
		$this->debug->guard();

		$numberOfMoves = 0;
		$moveaction = $this->configuration->getConfiguration('gamedefinitions', 'actions', 'move');
		foreach ($this->playermoves as $move)
		{
			if ($move['raceaction_action'] == $moveaction)
			{
				$numberOfMoves++;
			}
		}

		if ($numberOfMoves < 50)
		{
			$this->debug->unguard(true);
			return true;
		}

		$this->debug->unguard(false);
		return false;
	}
	
	
	public function fastround2()
	{
		$this->debug->guard();

		$numberOfMoves = 0;
		$moveaction = $this->configuration->getConfiguration('gamedefinitions', 'actions', 'move');
		foreach ($this->playermoves as $move)
		{
			if ($move['raceaction_action'] == $moveaction)
			{
				$numberOfMoves++;
			}
		}

		if ($numberOfMoves < 35)
		{
			$this->debug->unguard(true);
			return true;
		}

		$this->debug->unguard(false);
		return false;
	}


	public function fastround3()
	{
		$this->debug->guard();

		$numberOfMoves = 0;
		$moveaction = $this->configuration->getConfiguration('gamedefinitions', 'actions', 'move');
		foreach ($this->playermoves as $move)
		{
			if ($move['raceaction_action'] == $moveaction)
			{
				$numberOfMoves++;
			}
		}

		if ($numberOfMoves < 5)
		{
			$this->debug->unguard(true);
			return true;
		}

		$this->debug->unguard(false);
		return false;
	}
}

?>
