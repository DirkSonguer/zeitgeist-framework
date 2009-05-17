<?php

defined('POKENDESIGN_ACTIVE') or die();

class cards
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
		$tpl->load($this->configuration->getConfiguration('cards', 'templates', 'cards_index'));

		// cards		
		$carddata = $this->cards->getAuthorCards($this->user->getUserID());
		if (count($carddata) == 0)
		{
			$tpl->insertBlock('nocardsfound');
		}
		
		foreach ($carddata as $card)
		{
			$tpl->assign('cardfile', $card['card_filename']);
			$tpl->assign('cardid', $card['card_id']);
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
	public function deletecard($parameters=array())
	{
		$this->debug->guard();
		
		if (!empty($parameters['card']))
		{
			if ($this->cards->deleteCard($parameters['card']))
			{
				$this->messages->setMessage('Die Visitenkarte wurde gelöscht', 'usermessage');
			}
			else
			{
				$this->messages->setMessage('Die Visitenkarte konnte nicht glöscht werden!', 'userwarning');
			}			
		}

		$tpl = new pdTemplate();
		$tpl->redirect($tpl->createLink('cards', 'index'));

		$this->debug->unguard(true);
		return true;
	}
	

	// ok
	public function addcard($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('cards', 'templates', 'cards_addcard'));

		$addcardForm = new zgStaticform();
		$addcardForm->load('forms/addcard.form.ini');
		$formvalid = $addcardForm->process($parameters);

		if (!empty($parameters['submit']))
		{
			if ($formvalid)
			{
				$carddata = $parameters['addcard'];

				if ($this->cards->addCard($carddata))
				{
					$this->messages->setMessage('Die neue Visitenkarte wurden gespeichert.', 'usermessage');
					$tpl = new pdTemplate();
					$tpl->redirect($tpl->createLink('cards', 'index'));
					return true;
				}
				else
				{
					$this->messages->setMessage('Die Informationen konnten nicht gespeichert werden. Bitte verständigen Sie einen Administrator.', 'usererror');
				}
			}
			else
			{
				$this->messages->setMessage('Fehler bei der Eingabe. Bitte überprüfen Sie Ihre Angaben sorgfältig.', 'userwarning');
			}
		}

		$formcreated = $addcardForm->create($tpl);
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


}
?>
