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

			// get a list of all achievements
			$sql = "SELECT * FROM achievements";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Could not assess achievements: could not get achievement list from database', 'warning');
				$this->messages->setMessage('Could not assess achievements: could not get achievement list from database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
			
			// iterate through all achievements and check if the player has earned it
			while($row = $this->database->fetchArray($res))
			{
				if (method_exists(&$this, $row['achievement_function']))
				{
					$achievementearned = call_user_func(array(&$this, $row['achievement_function']));
				}
				else	
				{
					$this->debug->write('Could not assess achievements: achievement function does not exist', 'warning');
					$this->messages->setMessage('Could not assess achievements: achievement function does not exist', 'warning');
				}
			}
			
			// done, now set the race as assessed
/*
			$sqlRaces = "SELECT * FROM race_to_users WHERE raceuser_user='" . $this->user->getUserID() . "' AND raceuser_assessed='0'";
			$resRaces = $this->database->query($sqlRaces);
			if ((!$resRaces) || ($this->database->numRows($resRaces) < 1))
			{
				$this->debug->write('Could not assess achievements: could not find user races to assess in the database', 'warning');
				$this->messages->setMessage('Could not assess achievements: could not find user races to assess in the database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
*/
		}
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function fastround1()
	{
		$this->debug->guard();

		echo "1<br />";

		$this->debug->unguard(true);
		return true;
	}
	
	
	public function fastround2()
	{
		$this->debug->guard();
		
		echo "2<br />";
		
		$this->debug->unguard(true);
		return true;
	}


	public function fastround3()
	{
		$this->debug->guard();

		echo "3<br />";
		
		$this->debug->unguard(true);
		return true;
	}
}

?>
