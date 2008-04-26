<?php

defined('FEEDKUN_ACTIVE') or die();

class fkFeed
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

		$sql = "SELECT * FROM articles WHERE article_feed='$feedid'";
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


	public function updateFeed($feedid)
	{
		$this->debug->guard();

		// initialize simplepie
		$feed = new SimplePie();
		$feed->enable_cache(false);

		// check time and get url for the feed
		$sql = "SELECT feed_url, UNIX_TIMESTAMP(feed_lastupdate) as feed_lastupdate FROM feeds WHERE feed_id='$feedid'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting feed from database', 'warning');
			$this->messages->setMessage('Problem getting feed from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);

		if (time() > $row['feed_lastupdate']+3600)
		{
			$feed->set_feed_url($row['feed_url']);
			$feed->init();

			$feed->handle_content_type();

			if ($feed->data)
			{
				$lastitem = $feed->get_item();
				if ($lastitem->get_date('U') <= $row['feed_lastupdate'])
				{
					// feed is up to date
					$this->debug->unguard(true);
					return true;
				}
				else
				{
					$allitems = $feed->get_items();
					foreach($allitems as $item)
					{
						echo "found: ".$item->get_date('U')."<br />";
						if ($item->get_date('U') > $row['feed_lastupdate'])
						{
							$sql = 'INSERT INTO articles(article_feed, article_title, article_content, article_description,';
							$sql .= 'article_link, article_author, article_tags, article_categories) VALUES';
							$sql .= "('" . $feedid . "', '" . $this->database->escape($item->get_title()) . "', '" . $this->database->escape($item->get_content()) . "', '" . $this->database->escape($item->get_description()) . "', ";
							$sql .= "'" . $this->database->escape($item->get_permalink()) . "', '', '', '')";

							$res = $this->database->query($sql);
							if (!$res)
							{
								$this->debug->write('Problem inserting article to database', 'warning');
								$this->messages->setMessage('Problem inserting article to database', 'warning');
								$this->debug->unguard(false);
								return false;
							}
						}
					}

					$sql = "UPDATE feeds SET feed_lastupdate=NOW() WHERE feed_id='$feedid'";
					$res = $this->database->query($sql);
					if (!$res)
					{
						$this->debug->write('Problem updating the feed entry', 'warning');
						$this->messages->setMessage('Problem updating the feed entry', 'warning');
						$this->debug->unguard(false);
						return false;
					}

					$this->debug->unguard(true);
					return true;
				}
			}
			else
			{
				$this->debug->write('Could not pull data from feed: '.$feedurl, 'warning');
				$this->messages->setMessage('Could not pull data from feed: '.$feedurl, 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}

		$this->debug->unguard(true);
		return true;
	}
}

?>
