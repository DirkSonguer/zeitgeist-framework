<?php

defined('POKENDESIGN_ACTIVE') or die();

class main
{
	protected $debug;
	protected $messages;
	protected $messagecache;
	protected $database;
	protected $configuration;
	protected $user;
	protected $pduserfunctions;
	protected $cards;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->messagecache = zgMessagecache::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->pduserfunctions = new pdUserfunctions();
		$this->cards = new pdCards();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	// show start page
	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));
		
		$card = $this->cards->getRandomCard();
		
		$randomcard = $card['card_filename'];
		$tpl->assign('randomcard', $randomcard);
		$tpl->assign('cardid', $card['card_id']);
		$tpl->assign('cardname', $card['card_title']);
		$tpl->assign('cardauthorname', $card['userdata_username']);
		$tpl->assign('cardauthorid', $card['card_user']);
		
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
		
		$tagcloud = $this->cards->getTagcloud();
		
		// TODO: Implement Tagcloud

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

		
	// login screen
	public function login($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();

		if ($this->user->isLoggedIn())
		{
			$tpl->redirect($tpl->createLink('member', 'index'));
		}

		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_login'));
		if (!empty($parameters['login']))
		{
			if ( (!empty($parameters['username'])) && (!empty($parameters['password'])) )
			{
				if ($this->user->login($parameters['username'], $parameters['password']))
				{
					$tpl->redirect($tpl->createLink('member', 'index'));
				}
				else
				{
					$this->messages->setMessage('Der Benutzername und/oder das Passwort wurde nicht korrekt angegeben. Bitte geben Sie Ihren Benutzernamen und Ihr Passwort sorgfältig ein.', 'userwarning');
				}
			}
			else
			{
				$this->messages->setMessage('Bitte gebe einen gültigen Benutzernamen und das dazugehörige Passwort ein.', 'userwarning');
			}
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	// logout functionality
	public function logout($parameters=array())
	{
		$this->debug->guard();

		$this->user->logout();

		$tpl = new pdTemplate();
		$tpl->redirect($tpl->createLink('main', 'index'));

		$this->debug->unguard(true);
		return true;
	}


	// register a new user
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
					if ($this->pduserfunctions->createUser($newUser['user_username'], $newUser['user_password1'], $userdata))
					{
						$this->messages->setMessage('Dein Benutzer wurde angelegt. Du erhälst gleich eine Email, mit der du den Benutzer aktivieren kannst.', 'usermessage');
					}
					else
					{
						$messages = $this->messages->getAllMessages();
						
						foreach ($messages as $message)
						{
							if ($message->message == 'A user with this name already exists in the database. Please choose another username.')
							{
								$this->messages->setMessage('Die Email ist bereits registriert. Bitte wähle eine andere.', 'userwarning');
							}
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


	// confirms the registration of a new user and activate it
	public function confirmregistration($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));

		if (empty($parameters['id'])) $tpl->redirect($tpl->createLink('main', 'index'));
		
		if ($userid = $this->user->checkConfirmation($parameters['id']))
		{
			$this->user->activateUser($userid);
			$this->messages->setMessage('Dein Benutzer wurde aktiviert und du kannst dich nun einloggen. Viel Vergnügen!', 'usermessage');
		}
		else
		{
			$this->messages->setMessage('Es wurde kein dazugehöriger Benutzer gefunden. Bitte überprüfe den Aktivierungscode', 'userwarning');
		}
		
		$carddata = $this->cards->getRandomCard();
		
		$randomcard = $carddata['card_filename'];
		$tpl->assign('randomcard', $randomcard);
		$tpl->assign('cardid', $carddata['card_id']);
		$tpl->assign('cardname', $carddata['card_title']);
		$tpl->assign('cardauthorname', $carddata['userdata_username']);
		$tpl->assign('cardauthorid', $carddata['card_user']);

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}

}
?>
