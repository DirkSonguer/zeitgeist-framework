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
	 * If no user is given, the current user is used
	 *
	 * @return array
	 */
	public function getPlayerAchievements($userid)
	{
		$this->debug->guard();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Add a given achievement to a given user
	 * If no user is given, the current user is used
	 *
	 * @param integer $achievement id of the achievement
	 * @param integer $userid id of the user
	 *
	 * @return array
	 */
	public function addAchievement($achievement, $userid)
	{
		$this->debug->guard();

		$this->debug->unguard(true);
		return true;
	}
	

	/**
	 * Checks if a given user has the given achievement
	 * If no user is given, the current user is used
	 *
	 * @param integer $achievement id of the achievement
	 * @param integer $userid id of the user
	 *
	 * @return array
	 */
	public function hasAchievement($achievement, $userid)
	{
		$this->debug->guard();

		$this->debug->unguard(true);
		return true;
	}	
}

?>
