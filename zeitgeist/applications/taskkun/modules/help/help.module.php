<?php

defined('TASKKUN_ACTIVE') or die();

class help
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

		$helpTemplate = 'help_index';
		if (!empty($parameters['topic']))
		{
			if ($this->configuration->getConfiguration('help', 'templates', 'help_' . $parameters['topic']))
			{
				$helpTemplate = 'help_' . $parameters['topic'];
			}
		}

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('help', 'templates', $helpTemplate));
		$tpl->assign('documenttitle', 'Hilfe');

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
