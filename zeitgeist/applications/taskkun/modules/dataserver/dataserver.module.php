<?php


defined('TASKKUN_ACTIVE') or die();

class dataserver
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $dataserver;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		$this->dataserver = new zgDataserver();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function getusertasks($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT SUM(tl.tasklog_hoursworked) as task_hoursworked, t.*, tu.* FROM tasks_to_users tu ";
		$sql .= "LEFT JOIN tasks t ON tu.taskusers_task = t.task_id LEFT JOIN tasklogs tl ON t.task_id = tl.tasklog_task ";
		$sql .= "WHERE taskusers_user='" . $this->user->getUserID() . "' ";
		$sql .= "GROUP BY t.task_id";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function getgrouptasks($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT SUM(tl.tasklog_hoursworked) as task_hoursworked, t.*, u.user_username FROM tasks t ";
		$sql .= "LEFT JOIN tasks_to_users tu ON t.task_id = tu.taskusers_id ";
		$sql .= "LEFT JOIN users u ON tu.taskusers_user = u.user_id ";
		$sql .= "LEFT JOIN tasklogs tl ON t.task_id = tl.tasklog_task ";
		$sql .= "WHERE taskusers_id is null ";
		$sql .= "GROUP BY t.task_id";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}


	public function gettasklogs($parameters=array())
	{
		$this->debug->guard();

		$sql = "SELECT t.*, u.user_username as tasklog_username FROM tasklogs t ";
		$sql .= "LEFT JOIN users u ON t.tasklog_creator = u.user_id ";
		$sql .= "WHERE t.tasklog_task='" . $parameters['id'] . "' ORDER BY t.tasklog_timestamp";

		$xmlData = $this->dataserver->createXMLDatasetFromSQL($sql);
		$this->dataserver->streamXMLDataset($xmlData);
		die();

		$this->debug->unguard(true);
		return true;
	}
}
?>
