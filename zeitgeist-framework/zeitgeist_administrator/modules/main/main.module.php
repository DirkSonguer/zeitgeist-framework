<?php


defined('ZGADMIN_ACTIVE') or die();

class main
{
	public $debug;
	public $messages;
	public $database;
	public $configuration;
	public $user;

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
		$tpl = new adminTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));
		
		$tpl->assign('roottest', 'test');
		
		$tpl->show();
		
		return true;
	}
	
	
	public function login($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new adminTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_login'));
		
		if ($this->user->isLoggedIn())
		{
			$tpl->redirect($tpl->createLink('main', 'index'));
		}
				
		if (!empty($parameters['login']))
		{
			if ( (!empty($parameters['username'])) && (!empty($parameters['password'])) )
			{
				if ($this->user->loginUser($parameters['username'], $parameters['password']))
				{
					$tpl->redirect($tpl->createLink('main', 'index'));
				}
				else
				{
					$this->messages->setMessage('Username and/or password was not correct. Please enter your name and password carefully.', 'userwarning');
				}
			}
			else
			{
				$this->messages->setMessage('Please enter a valid username and password.', 'userwarning');
			}
		}
		
		$tpl->assign('pagetitle', 'Login');
		
		$tpl->show();
				
		$this->debug->unguard(true);
		return true;
	}



}
?>
