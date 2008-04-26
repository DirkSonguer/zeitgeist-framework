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

		echo "starting import<br />";
		$feed = new fkFeed();
		$feed->updateFeed(1);
		$articles = $feed->getFeedContent(1);

		foreach($articles as $article)
		{
			echo $article['article_title']."<br />";
		}

		echo "done<br />";

		$this->debug->unguard(true);
		return true;
	}

}
?>
