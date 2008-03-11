<?php

defined('TASKKUN_ACTIVE') or die();

class tasktypes
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


	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasktypes', 'templates', 'tasktypes_index'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function edittasktype($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('tasktypes', 'templates', 'tasktypes_edittasktype'));

		if (!empty($parameters['id']))
		{
			$tasktypeid = $parameters['id'];
		}
		elseif (!empty($parameters['tasktypeid']))
		{
			$tasktypeid = $parameters['tasktypeid'];
		}

		$taskfunctions = new tkTaskfunctions();
		$tasktypeInformation = $taskfunctions->getTasktypeInformation($tasktypeid);

		$tpl->assignDataset($tasktypeInformation);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
