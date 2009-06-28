<?php

defined('POKENDESIGN_ACTIVE') or die();

class cards
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $pduserfunctions;
	protected $cards;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		
		$this->pduserfunctions = new pdUserfunctions();
		$this->cards = new pdCards();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	// show overview of cards / explore cards
	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('cards', 'templates', 'cards_index'));
		
		if (empty($parameters['page'])) $parameters['page'] = 1;
		
		// cards		
		$carddata = $this->cards->getAllCards($parameters['page']);
		if (count($carddata) == 0)
		{
			$tpl->insertBlock('nocardsfound');
		}
		
		$i = 0;
		foreach ($carddata as $card)
		{
			$i++;
			if ( ($i%2) == 0) $tpl->insertBlock('nextline');
			
			$tpl->assign('cardid', $card['card_id']);
			$tpl->assign('cardfile', $card['card_filename']);
			$tpl->assign('carddate', $card['card_date']);
			$tpl->assign('cardtitle', $card['card_title']);
			$tpl->assign('cardauthorname', $card['userdata_username']);
			$tpl->assign('cardauthorid', $card['card_user']);
			$tpl->assign('carddescription', $card['card_description']);

			if ($card['card_user'] != $this->user->getUserID() )
			{
				$this->cards->addCardView($card['card_id']);
			}

			if (!$this->cards->hasFaved($card['card_id']))
			{
				$tpl->insertBlock('notfaved');
			}
			else
			{
				$favs = $this->cards->getFavs($card['card_id']);
				$tpl->assign('favs', $favs);
				$tpl->insertBlock('faved');
			}

			$taglist = $this->cards->getTags($card['card_id']);
			if (count($taglist) > 0)
			{
				foreach ($taglist as $tag)
				{
					$tpl->assign('tag', $tag);
					$tpl->insertBlock('tag');
				}
				$tpl->insertBlock('taglist');
			}

			$tpl->insertBlock('cardlist');
		}
		
		if ($parameters['page'] > 1)
		{
			$tpl->assign('paginationleft_link', ($parameters['page']-1));
			$tpl->insertBlock('paginationleft');
		}
		
		if ((($parameters['page']) * $this->cards->pagination_items) < $this->cards->getNumberOfCards())
		{
			$tpl->assign('paginationright_link', ($parameters['page']+1));
			$tpl->insertBlock('paginationright');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	// search cards
	public function search($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('cards', 'templates', 'cards_search'));

		$searchterm = '';
		if (!empty($parameters['q'])) $searchterm = $parameters['q'];
		if (!empty($parameters['t'])) $searchterm = $parameters['t'];

		if ($searchterm == '') $tpl->redirect($tpl->createLink('main', 'index'));

		// cards
		$carddata = $this->cards->searchCards($searchterm);
		if (count($carddata) == 0)
		{
			$tpl->insertBlock('noresultsfound');
		}
		
		$i = 0;
		foreach ($carddata as $card)
		{
			$i++;
			if ( ($i%2) == 0) $tpl->insertBlock('nextline');

			$tpl->assign('cardfile', $card['card_filename']);
			$tpl->assign('cardauthorid', $card['card_user']);
			$tpl->assign('cardid', $card['card_id']);
			$tpl->assign('cardauthorname', $card['userdata_username']);
			$tpl->assign('carddate', $card['card_date']);
			$tpl->assign('cardtitle', $card['card_title']);
			$tpl->assign('carddescription', $card['card_description']);

			if ($card['card_user'] != $this->user->getUserID() )
			{
				$this->cards->addCardView($card['card_id']);
			}
			
			$favs = $this->cards->getFavs($card['card_id']);
			$tpl->assign('favs', $favs);

			if (!$this->cards->hasFaved($card['card_id']))
			{
				$tpl->insertBlock('notfaved');
			}
			else
			{
				$tpl->insertBlock('faved');
			}

			$tpl->insertBlock('cardlist');
		}

		$tagcloud = $this->cards->getTagcloud();
		
		foreach ($tagcloud as $tag => $tagcount)
		{
			$maxcount = max($tagcloud);
			$pokenclass = ceil(($tagcount/$maxcount)*4);
			if ($pokenclass == 4) $pokenclass = 3;
			$tpl->assign('tagclass', $pokenclass);
			$tpl->assign('tag', $tag);
			$tpl->insertBlock('tag');
		}
		
		$tpl->assign('search', $searchterm);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}
	

	// show author overview
	public function showauthor($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('cards', 'templates', 'cards_showauthor'));
		
		if (empty($parameters['id'])) $tpl->redirect($tpl->createLink('main', 'index'));
		
		// author
		$authordata = $this->cards->getAuthorData($parameters['id']);
		if (!$authordata) $tpl->redirect($tpl->createLink('main', 'index'));
		
		$tpl->assign('authorname', $authordata['userdata_username']);
		$tpl->assign('gravatar', md5($authordata['user_username']));
		if ($authordata['userdata_url'] != '')
		{
			$tpl->assign('authorlink', $authordata['userdata_url']);
			$tpl->insertBlock('authorwithlink');
		}
		else
		{
			$tpl->insertBlock('authornolink');
		}
		
		// cards		
		$carddata = $this->cards->getAuthorCards($parameters['id']);
		if (count($carddata) == 0)
		{
			$tpl->insertBlock('nocardsfound');
		}
		
		foreach ($carddata as $card)
		{
			$tpl->assign('cardid', $card['card_id']);
			$tpl->assign('cardfile', $card['card_filename']);
			$tpl->assign('carddate', $card['card_date']);
			$tpl->assign('cardtitle', $card['card_title']);
			$tpl->assign('carddescription', $card['card_description']);

			if ($card['card_user'] != $this->user->getUserID() )
			{
				$this->cards->addCardView($card['card_id']);
			}

			$favs = $this->cards->getFavs($card['card_id']);
			$tpl->assign('favs', $favs);

			$tpl->insertBlock('cardlist');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}
	

	// show top ten list
	public function topten($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('cards', 'templates', 'cards_topten'));

		// cards
		$carddata = $this->cards->getTopTen();
		
		$i = 0;
		foreach ($carddata as $card)
		{
			$i++;
			$tpl->assign('cardranking', $i);
			$tpl->assign('cardfile', $card['card_filename']);
			$tpl->assign('cardauthorid', $card['card_user']);
			$tpl->assign('cardid', $card['card_id']);
			$tpl->assign('cardauthorname', $card['userdata_username']);
			$tpl->assign('carddate', $card['card_date']);
			$tpl->assign('cardviews', $card['card_viewed']);
			$tpl->assign('cardclicks', $card['card_clicked']);
			$tpl->assign('carddownloads', $card['card_downloaded']);
			$tpl->assign('cardtitle', $card['card_title']);
			$tpl->assign('carddescription', $card['card_description']);
			$tpl->assign('favs', $card['card_favs']);
			
			$taglist = $this->cards->getTags($card['card_id']);
			if (count($taglist) > 0)
			{
				foreach ($taglist as $tag)
				{
					$tpl->assign('tag', $tag);
					$tpl->insertBlock('tag');
				}
				$tpl->insertBlock('taglist');
			}
	
			if ($card['card_user'] != $this->user->getUserID() )
			{
				$this->cards->addCardView($card['card_id']);
			}
						
			if (!$this->cards->hasFaved($card['card_id']))
			{
				$tpl->insertBlock('notfaved');
			}
			else
			{
				$tpl->insertBlock('faved');
			}

			$tpl->insertBlock('cardlist');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

	
	// shows the detail page of a card
	public function showdetail($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('cards', 'templates', 'cards_showdetail'));
		
		if (empty($parameters['id'])) $tpl->redirect($tpl->createLink('main', 'index'));
		
		$card = $this->cards->getCardInformation($parameters['id'], false);
		
		$tpl->assign('cardfile', $card['card_filename']);
		$tpl->assign('cardauthorid', $card['card_user']);
		$tpl->assign('cardid', $card['card_id']);
		$tpl->assign('cardauthorname', $card['userdata_username']);
		$tpl->assign('carddate', $card['card_date']);
		$tpl->assign('cardviews', $card['card_viewed']);
		$tpl->assign('cardclicks', $card['card_clicked']);
		$tpl->assign('carddownloads', $card['card_downloaded']);
		$tpl->assign('cardtitle', $card['card_title']);
		$tpl->assign('carddescription', $card['card_description']);

		$favs = $this->cards->getFavs($card['card_id']);
		$tpl->assign('favs', $favs);
		
		$taglist = $this->cards->getTags($card['card_id']);
		if (count($taglist) > 0)
		{
			foreach ($taglist as $tag)
			{
				$tpl->assign('tag', $tag);
				$tpl->insertBlock('tag');
			}
			$tpl->insertBlock('taglist');
		}

		if ($card['card_user'] != $this->user->getUserID() )
		{
			$this->cards->addCardClick($card['card_id']);
		}
					
		if (!$this->cards->hasFaved($card['card_id']))
		{
			$tpl->insertBlock('notfaved');
		}
		else
		{
			$tpl->insertBlock('faved');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}
		

	// adds a fav vote to a card
	public function addfav($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		
		if (empty($parameters['id'])) $tpl->redirect($tpl->createLink('main', 'index'));
		
		$ret = $this->cards->addFav($parameters['id']);
		
		$tpl->redirect($tpl->createLink('cards', 'showdetail').'&id='.$parameters['id']);

		$this->debug->unguard(true);
		return true;
	}
		

	// dowbloads a card
	public function download($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		
		if (empty($parameters['id'])) $tpl->redirect($tpl->createLink('main', 'index'));
		
		$this->cards->addCardDownload($parameters['id']);
		$carddata = $this->cards->getCardInformation($parameters['id'], false);

		$filename = 'uploads/'.$carddata["card_filename"];
		if (!file_exists($filename)) $tpl->redirect($tpl->createLink('main', 'index'));			 
		
		// required for IE, otherwise Content-disposition is ignored
		if (ini_get('zlib.output_compression'))
		{
			ini_set('zlib.output_compression', 'Off');
		}
		
		// addition by Jorg Weske
		$file_extension = strtolower(substr(strrchr($filename,"."), 1));
		
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: application/force-download");
		// change, added quotes to allow spaces in filenames, by Rajkumar Singh
		header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($filename));
		readfile("$filename"); 
		
		$tpl->redirect($tpl->createLink('cards', 'showdetail').'&id='.$parameters['id']);

		$this->debug->unguard(true);
		return true;
	}
	
}
?>
