<?php

defined('LINERACER_ACTIVE') or die();

class lrAchievementfunctions
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
			$achievements[$row['userachievement_achievement']] = true;
		}

		$this->debug->unguard($achievements);
		return $achievements;
	}


	/**
	 * Add a given achievement to a given user
	 *
	 * @param integer $achievement id of the achievement
	 * @param integer $race id of the race the achievement was earned
	 *
	 * @return array
	 */
	public function addAchievement($achievement, $race=0)
	{
		$this->debug->guard();
		
		if ($this->hasAchievement($achievement))
		{
			$this->debug->write('Could not add achievements: player already has the achievement', 'warning');
			$this->messages->setMessage('Could not add achievements: player already has the achievement', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$userid = $this->user->getUserID();

		$sql = "INSERT INTO achievements_to_users(userachievement_user, userachievement_achievement, userachievement_race) VALUES('" . $userid . "', '" . $achievement . "', '" . $race . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not add achievement: could not write data to database', 'warning');
			$this->messages->setMessage('Could not add achievement: could not write data to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

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


	/**
	 * Gets a list of all achievements
	 *
	 * @return array
	 */
	public function getAllAchievements()
	{
		$this->debug->guard();

		$sql = "SELECT * FROM achievements";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get list of achievements: could not get achievements from database', 'warning');
			$this->messages->setMessage('Could not get list of achievements: could not get achievements from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$achievements = array();
		while ($row = $this->database->fetchArray($res))
		{
			$achievements[$row['achievement_id']] = $row;
		}

		$this->debug->unguard($achievements);
		return $achievements;
	}

}