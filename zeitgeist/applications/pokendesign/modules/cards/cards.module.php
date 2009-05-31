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
		
		foreach ($carddata as $card)
		{
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

			$favs = $this->cards->getFavs($card['card_id']);
			$tpl->assign('favs', $favs);
	
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

		if (empty($parameters['q'])) $tpl->redirect($tpl->createLink('main', 'index'));

		// cards		
		$carddata = $this->cards->searchCards($parameters['q']);
		if (count($carddata) == 0)
		{
			$tpl->insertBlock('noresultsfound');
		}
		
		foreach ($carddata as $card)
		{
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

		$tpl->assign('search', $parameters['q']);
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
		$tpl->assign('cardtitle', $card['card_title']);
		$tpl->assign('carddescription', $card['card_description']);

		if ($card['card_user'] != $this->user->getUserID() )
		{
			$this->cards->addCardClick($card['card_id']);
		}
		
		$favs = $this->cards->getFavs($parameters['id']);
		$tpl->assign('favs', $favs);
		
		if (!$this->cards->hasFaved($parameters['id']))
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

}
?>
