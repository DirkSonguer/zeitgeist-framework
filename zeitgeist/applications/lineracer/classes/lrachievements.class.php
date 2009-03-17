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

		$achievements = array();

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
		
		$userid = $this->user->getUserID();
		

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

		$this->debug->unguard(true);
		return true;
	}	
}

?>
