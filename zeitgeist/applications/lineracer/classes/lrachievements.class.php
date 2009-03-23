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
		
		$sql = "SELECT * FROM achievements";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not assess achievements: could not get achievement list from database', 'warning');
			$this->messages->setMessage('Could not assess achievements: could not get achievement list from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		while($row = $this->database->fetchArray($res))
		{
			$achievementearned = call_user_func(array(&$this, $row['achievement_function']));
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
