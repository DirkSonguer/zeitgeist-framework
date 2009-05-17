<?php

defined('POKENDESIGN_ACTIVE') or die();

class main
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;
	protected $cards;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->cards = new pdCards();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	// ok
	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));
		
		$carddata = $this->cards->getRandomCard();
		
		$randomcard = $carddata['card_filename'];
		$tpl->assign('randomcard', $randomcard);
		$tpl->assign('cardname', $carddata['card_title']);
		$tpl->assign('cardauthorname', $carddata['userdata_username']);
		$tpl->assign('cardauthorid', $carddata['card_user']);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}



	// ok
	public function overview($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_overview'));
		
		if (empty($parameters['page'])) $parameters['page'] = 1;
		
		// cards		
		$carddata = $this->cards->getAllCards($parameters['page']);
		if (count($carddata) == 0)
		{
			$tpl->insertBlock('nocardsfound');
		}
		
		foreach ($carddata as $card)
		{
			$tpl->assign('cardfile', $card['card_filename']);
			$tpl->assign('carddate', $card['card_date']);
			$tpl->assign('cardtitle', $card['card_title']);
			$tpl->assign('cardauthorname', $card['userdata_username']);
			$tpl->assign('cardauthorid', $card['card_user']);
			$tpl->assign('carddescription', $card['card_description']);
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
	

	// ok
	public function showauthor($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_showauthor'));
		
		if (empty($parameters['id'])) $tpl->redirect($tpl->createLink('main', 'index'));
		
		// author
		$authordata = $this->cards->getAuthorData($parameters['id']);
		if (!$authordata) $tpl->redirect($tpl->createLink('main', 'index'));
		
		$tpl->assign('authorname', $authordata['userdata_username']);
		$tpl->assign('gravatar', md5($this->user->getUsername()));
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
			$tpl->assign('cardfile', $card['card_filename']);
			$tpl->assign('carddate', $card['card_date']);
			$tpl->assign('cardtitle', $card['card_title']);
			$tpl->assign('carddescription', $card['card_description']);
			$tpl->insertBlock('cardlist');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}
	

	// ok
	public function search($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_search'));

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
			$tpl->assign('carddate', $card['card_date']);
			$tpl->assign('cardtitle', $card['card_title']);
			$tpl->assign('carddescription', $card['card_description']);
			$tpl->insertBlock('cardlist');
		}

		$tpl->assign('search', $parameters['q']);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}
	
		
	// ok
	public function login($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();

		if ($this->user->isLoggedIn())
		{
			$tpl->redirect($tpl->createLink('main', 'index'));
		}

		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_login'));
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
					$this->messages->setMessage('Der Benutzername und/oder das Passwort wurde nicht korrekt angegeben. Bitte geben Sie Ihren Benutzernamen und Ihr Passwort sorgf�ltig ein.', 'userwarning');
				}
			}
			else
			{
				$this->messages->setMessage('Bitte geben Sie einen g�ltigen Benutzernamen und das dazugeh�rige Passwort ein.', 'userwarning');
			}
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	// ok
	public function logout($parameters=array())
	{
		$this->debug->guard();

		$this->user->logout();

		$tpl = new pdTemplate();
		$tpl->redirect($tpl->createLink('main', 'index'));

		$this->debug->unguard(true);
		return true;
	}


	// ok
	public function register($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_register'));
		
		$registerForm = new zgStaticform();
		$registerForm->load('forms/register.form.ini');
		$formvalid = $registerForm->process($parameters);

		if (!empty($parameters['submit']))
		{
			if ($formvalid)
			{
				$carddata = $parameters['addcard'];
/*
				if ($this->cards->addCard($carddata))
				{
					$this->messages->setMessage('Die neue Visitenkarte wurden gespeichert.', 'usermessage');
					$tpl = new pdTemplate();
					$tpl->redirect($tpl->createLink('main', 'index'));
					return true;
				}
				else
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verständigen Sie einen Administrator.', 'usererror');
				}
*/				
			}
			else
			{
				$this->messages->setMessage('Fehler bei der Eingabe. Bitte überprüfen Sie Ihre Angaben sorgfältig.', 'userwarning');
			}
		}

		$formcreated = $registerForm->create($tpl);
		$tpl->show();
				
		$this->debug->unguard(true);
		return true;
	}

}
?>
