<?php

defined('TASKKUN_ACTIVE') or die();

class reports
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
		$tpl->load($this->configuration->getConfiguration('reports', 'templates', 'reports_index'));

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function finishedtasks($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('reports', 'templates', 'reports_finishedtasks'));

		$tasktypefunctions = new tkTasktypefunctions();
		$userfunctions = new tkUserfunctions();

		$dataLink = $tpl->createLink('dataserver', 'finishedtaskschartdata');
		$ret = open_flash_chart_object_str( 920, 200, $dataLink, true, $this->configuration->getConfiguration('taskkun', 'application', 'basepath') . '/includes/open-flash-chart/');
		$tpl->assign('finishedtasks_chart', $ret);

		$tasktypes = $tasktypefunctions->getTasktypes();
		foreach ($tasktypes as $tasktype)
		{
			$tpl->assignDataset($tasktype);
			$tpl->insertBlock('tasktype_loop');
		}

		$users = $userfunctions->getUserinformation();
		foreach ($users as $user)
		{
			$tpl->assignDataset($user);
			$tpl->insertBlock('user_loop');
		}

		$data_begin = date("d.m.Y", time()-(86400 * 14));
		$tpl->assign('data_begin', $data_begin);

		$data_end = date("d.m.Y", time());
		$tpl->assign('data_end', $data_end);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function workedhours($parameters=array())
	{
		$this->debug->guard();

		$tpl = new tkTemplate();
		$tpl->load($this->configuration->getConfiguration('reports', 'templates', 'reports_workedhours'));

		$groupfunctions = new tkGroupfunctions();
		$userfunctions = new tkUserfunctions();

		$dataLink = $tpl->createLink('dataserver', 'workedhourschartdata');
		$ret = open_flash_chart_object_str( 920, 200, $dataLink, true, $this->configuration->getConfiguration('taskkun', 'application', 'basepath') . '/includes/open-flash-chart/');
		$tpl->assign('workedhours_chart', $ret);

		$groups = $groupfunctions->getGroupsForUser();
		foreach ($groups as $group)
		{
			$tpl->assignDataset($group);
			$tpl->insertBlock('group_loop');
		}

		$users = $userfunctions->getUserinformation();
		foreach ($users as $user)
		{
			$tpl->assignDataset($user);
			$tpl->insertBlock('user_loop');
		}

		$data_begin = date("d.m.Y", time()-(86400 * 14));
		$tpl->assign('data_begin', $data_begin);

		$data_end = date("d.m.Y", time());
		$tpl->assign('data_end', $data_end);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>