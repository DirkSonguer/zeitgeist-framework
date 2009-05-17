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
					$this->messages->setMessage('Der Benutzername und/oder das Passwort wurde nicht korrekt angegeben. Bitte geben Sie Ihren Benutzernamen und Ihr Passwort sorgfältig ein.', 'userwarning');
				}
			}
			else
			{
				$this->messages->setMessage('Bitte geben Sie einen gültigen Benutzernamen und das dazugehörige Passwort ein.', 'userwarning');
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
				$newUser = $parameters['register'];
				$registerProblems = false;

				if ($newUser['user_password1'] != $newUser['user_password2'])
				{
					$registerForm->validateElement('user_password1', false);
					$registerForm->validateElement('user_password2', false);
					$this->messages->setMessage('Die beiden Passworteingaben stimmen nicht überein.', 'userwarning');
					$registerProblems = true;
				}

				$userdata = array();
				if (!empty($newUser['userdata_username'])) $userdata['userdata_username'] = $newUser['userdata_username'];
				if (!empty($newUser['userdata_url'])) $userdata['userdata_url'] = $newUser['userdata_url'];

				if (!$registerProblems)
				{
					$userfunctions = new pdUserfunctions();
					if ($userfunctions->createUser($newUser['user_username'], $newUser['user_password1'], $userdata))
					{
						$this->messages->setMessage('Dein Benutzer wurde angelegt. Du erhälst gleich eine Email, mit der du den Benutzer aktivieren kannst.', 'usermessage');
						$tpl->redirect($tpl->createLink('main', 'index'));
					}
					else
					{
						$messages = $this->messages->getAllMessages('userhandler.class.php');
						
						foreach ($messages as $message)
						if ($message->message == 'A user with this name already exists in the database. Please choose another username.')
						{
							$this->messages->setMessage('Die EMail ist bereits registriert. Bitte wähle eine andere.', 'userwarning');
						}
							
						$this->messages->setMessage('Der Benutzer wurde nicht angelegt', 'userwarning');
					}
				}
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
