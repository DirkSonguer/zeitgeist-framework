<?php

defined('FEEDKUN_ACTIVE') or die();

class fkFeeds
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->user = zgUserhandler::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function getFeedContent($feedid)
	{
		$this->debug->guard();

		$sql = "SELECT a.*, ua.unreadarticle_article as article_read FROM articles a ";
		$sql .= "LEFT JOIN unreadarticles ua ON a.article_id = ua.unreadarticle_article ";
		$sql .= "WHERE a.article_feed='" . $feedid . "' AND (ua.unreadarticle_user = '" . $this->user->getUserID() . "' OR ua.unreadarticle_user is null) ";
		$sql .= "ORDER BY a.article_timestamp DESC";
		$res = $this->database->query($sql);

		if (!$res)
		{
			$this->debug->write('Problem getting articles from database', 'warning');
			$this->messages->setMessage('Problem articles feed from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$articles = array();
		while ($row = $this->database->fetchArray($res))
		{
			$articles[] = $row;
		}

		$this->debug->unguard($articles);
		return $articles;
	}

}

?>
