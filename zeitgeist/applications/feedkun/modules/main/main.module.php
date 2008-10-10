<?php

defined('FEEDKUN_ACTIVE') or die();

class main
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

		$tpl = new fkTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));

/*
		echo "starting import<br />";
		$feeds = new fkFeeds();
		$articles = $feeds->getFeedContent(1);

		foreach($articles as $article)
		{
			if (!empty($article['article_read']))
			{
				echo "<b>".$article['article_timestamp'] . ", ".$article['article_title']."</b><br />";
			}
			else
			{
				echo $article['article_timestamp'] . ", ".$article['article_title']."<br />";
			}
		}

		echo "done<br />";
*/
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function login($parameters=array())
	{
		$this->debug->guard();

		$tpl = new fkTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_login'));

		if ($this->user->isLoggedIn())
		{
			$tpl->redirect($tpl->createLink('main', 'index'));
		}

		if (!empty($parameters['login']))
		{
			if ( (!empty($parameters['username'])) && (!empty($parameters['password'])) )
			{
				if ($this->user->login($parameters['username'], $parameters['password']))
				{
					$tpl->redirect($tpl->createLink('main', 'index'));
				}
				else
				{
					$this->messages->setMessage('Username and/or password was not correct. Please enter your username and password carefully.', 'userwarning');
				}
			}
			else
			{
				$this->messages->setMessage('Please enter a valid username and password.', 'userwarning');
			}
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	public function logout($parameters=array())
	{
		$this->debug->guard();

		$this->user->logout();

		$tpl = new fkTemplate();
		$tpl->redirect($tpl->createLink('main', 'index'));

		$this->debug->unguard(true);
		return true;
	}


}
?>
