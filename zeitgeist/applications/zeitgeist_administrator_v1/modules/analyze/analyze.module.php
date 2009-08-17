<?php


defined('ZGADMIN_ACTIVE') or die();

include_once('includes/open-flash-chart/open_flash_chart_object.php');

class analyze
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
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('analyze', 'templates', 'analyze_index'));
				
		$dataLink = $tpl->createLink('dataserver', 'getmodulechartdata');
		$ret = open_flash_chart_object_str( 900, 200, $dataLink, false, $this->configuration->getConfiguration('administrator', 'application', 'basepath') . '/includes/open-flash-chart/');
		$tpl->assign('modulechart', $ret);

		$dataLink = $tpl->createLink('dataserver', 'getactionchartdata');
		$ret = open_flash_chart_object_str( 900, 200, $dataLink, false, $this->configuration->getConfiguration('administrator', 'application', 'basepath') . '/includes/open-flash-chart/');
		$tpl->assign('actionchart', $ret);
		
		$dataLink = $tpl->createLink('dataserver', 'getuserchartdata');
		$ret = open_flash_chart_object_str( 900, 200, $dataLink, false, $this->configuration->getConfiguration('administrator', 'application', 'basepath') . '/includes/open-flash-chart/');
		$tpl->assign('userchart', $ret);
		
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}

	
	public function showmodules($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('analyze', 'templates', 'analyze_modules'));
				
		
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}	

	
	public function showactions($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('analyze', 'templates', 'analyze_actions'));
				
		
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}	
	
	
	public function showusers($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('analyze', 'templates', 'analyze_users'));
				
		
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}	
}
?>
