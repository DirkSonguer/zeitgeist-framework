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

	
	/**
	 * Gets all achievements of a given user
	 *
	 * @return array
	 */
	public function getPlayerAchievements()
	{
		$this->debug->guard();

		$userid = $this->user->getUserID();
		
		$sql = "SELECT * FROM achievements_to_users WHERE userachievement_user='" . $userid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get player achievements: player not found', 'warning');
			$this->messages->setMessage('Could not get player achievements: player not found', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$achievements = array();
		while($row = $this->database->fetchArray($res))
		{
			$achievements[] = $row;
		}

		$this->debug->unguard($achievements);
		return $achievements;
	}


	/**
	 * Add a given achievement to a given user
	 *
	 * @param integer $achievement id of the achievement
	 *
	 * @return array
	 */
	public function addAchievement($achievement)
	{
		$this->debug->guard();
		
		if ($this->hasAchievement($achievement))
		{
			$this->debug->write('Could not add achievements: player not found', 'warning');
			$this->messages->setMessage('Could not get player achievements: player not found', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		$userid = $this->user->getUserID();
		
		
		
		$this->database->query("INSERT INTO achievements(achievement_id, achievement_name, achievement_description, achievement_image, achievement_level, achievement_reward) VALUES('" . $achievement1 . "', 'test1', 'achievement1', '', '1', '2')");

		$this->debug->unguard(true);
		return true;
	}
	

	/**
	 * Checks if a given user has the given achievement
	 *
	 * @param integer $achievement id of the achievement
	 *
	 * @return array
	 */
	public function hasAchievement($achievement)
	{
		$this->debug->guard();

		$userid = $this->user->getUserID();
		
		$sql = "SELECT * FROM achievements_to_users WHERE userachievement_user='" . $userid . "' AND userachievement_achievement='" . $achievement . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get player achievements: could not get player achievements from database', 'warning');
			$this->messages->setMessage('Could not get player achievements: could not get player achievements from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->database->numRows($res);
		if ($ret != 1)
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}	
}

?>
