<?php

defined('TASKKUN_ACTIVE') or die();

class search
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
		$tpl->load($this->configuration->getConfiguration('search', 'templates', 'search_index'));

		if (!empty($parameters['searchterms']))
		{
			$terms = explode(' ', $parameters['searchterms']);
			$items = array();
			$itemscore = array();

			foreach ($terms as $term)
			{
				// search in database
				$sql = "SELECT * FROM tasks t LEFT JOIN tags_to_tasks t2t ON t.task_id = t2t.tagtasks_task LEFT JOIN tags ta ON t2t.tagtasks_tag = ta.tag_id ";
				$sql .= "WHERE t.task_name LIKE '%" . $term . "%' OR t.task_description LIKE '%" . $term . "%' OR ta.tag_text LIKE '%" . $term . "%'";
				$res = $this->database->query($sql);
				if (!$res)
				{
					$this->debug->write('Problem getting search results from database', 'warning');
					$this->messages->setMessage('Problem getting search results from database', 'warning');
					$this->debug->unguard(false);
					return false;
				}

				// store result and add score
				while($row = $this->database->fetchArray($res))
				{
					$score = 0;

					if (strpos(strtoupper($row['task_name']), strtoupper($term)) !== false)
					{
						if (empty($items[$row['task_id']])) $score += 3;
					}

					if (strpos(strtoupper($row['task_description']), strtoupper($term)) !== false)
					{
						if (empty($items[$row['task_id']])) $score += 1;
					}

					if (strpos(strtoupper($row['tag_text']), strtoupper($term)) !== false)
					{
						$score += 6;
					}

					if ($score > 0)
					{
						if (empty($items[$row['task_id']]))
						{
							$items[$row['task_id']] = $row;
							$itemscore[$row['task_id']] = $score;
						}
						else
						{
							$itemscore[$row['task_id']] += $score;
						}
					}
				}
			}

			// sort by score
			arsort($itemscore);
		}

		$tpl->assign('searchterms', $parameters['searchterms']);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
