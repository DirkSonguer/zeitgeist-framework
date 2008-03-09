<?php

defined('TASKKUN_ACTIVE') or die();

class tkUserfunctions
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function getUserInstance()
	{
		$this->debug->guard();

		$sql = "SELECT * FROM users_to_instances WHERE userinstance_user='" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		$row = $this->database->fetchArray($res);

		$ret = $row['userinstance_instance'];

		$this->debug->unguard($ret);
		return $ret;
	}


	public function checkRightsForTask($taskid)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM tasks WHERE task_id='" . $taskid . "' AND task_instance='" . $this->getUserInstance() . "'";
		$res = $this->database->query($sql);
		$numTasks = $this->database->numRows($res);

		if ($numTasks == 0)
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}

}
?>
