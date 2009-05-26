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


	// ok
	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_index'));
		
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
			$tpl->assign('cardid', $card['card_id']);
			$tpl->assign('cardfile', $card['card_filename']);
			$tpl->assign('carddate', $card['card_date']);
			$tpl->assign('cardtitle', $card['card_title']);
			$tpl->assign('cardauthorname', $card['userdata_username']);
			$tpl->assign('cardauthorid', $card['card_user']);
			$tpl->assign('carddescription', $card['card_description']);

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
			$tpl->assign('cardid', $card['card_id']);
			$tpl->assign('cardfile', $card['card_filename']);
			$tpl->assign('carddate', $card['card_date']);
			$tpl->assign('cardtitle', $card['card_title']);
			$tpl->assign('carddescription', $card['card_description']);

			$favs = $this->cards->getFavs($card['card_id']);
			$tpl->assign('favs', $favs);

			$tpl->insertBlock('cardlist');
		}

		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}
	
	
	// ok
	public function showdetail($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_showdetail'));
		
		if (empty($parameters['id'])) $tpl->redirect($tpl->createLink('main', 'index'));
		
		$card = $this->cards->getCardInformation($parameters['id'], false);
		
		$tpl->assign('cardfile', $card['card_filename']);
		$tpl->assign('cardauthorid', $card['card_user']);
		$tpl->assign('cardid', $card['card_id']);
		$tpl->assign('cardauthorname', $card['userdata_username']);
		$tpl->assign('carddate', $card['card_date']);
		$tpl->assign('cardtitle', $card['card_title']);
		$tpl->assign('carddescription', $card['card_description']);
		
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
		

	// ok
	public function addfav($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_showdetail'));
		
		if (empty($parameters['id'])) $tpl->redirect($tpl->createLink('main', 'index'));
		
		$ret = $this->cards->addFav($parameters['id']);
		
		$tpl->redirect($tpl->createLink('main', 'showdetail').'&id='.$parameters['id']);

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
				$this->messages->setMessage('Bitte gebe einen gültigen Benutzernamen und das dazugehörige Passwort ein.', 'userwarning');
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
					}
					else
					{
						$messages = $this->messages->getAllMessages('userhandler.class.php');
						
						foreach ($messages as $message)
						if ($message->message == 'A user with this name already exists in the database. Please choose another username.')
						{
							$this->messages->setMessage('Die Email ist bereits registriert. Bitte wähle eine andere.', 'userwarning');
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


	// ok
	public function editprofile($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('main', 'templates', 'main_editprofile'));

		$editprofileForm = new zgStaticform();
		$editprofileForm->load('forms/editprofile.form.ini');
		$formvalid = $editprofileForm->process($parameters);

		if (!empty($parameters['submit']))
		{
			if ($formvalid)
			{
				// email
				if (!empty($parameters['profile']['user_username']))
				{
					if ($parameters['profile']['user_username'] != $this->user->getUsername())
					{
						if ($this->user->changeUsername($this->user->getUserID(), $parameters['profile']['user_username']))
						{
							$this->messages->setMessage('Deine Email wurde geändert.', 'usermessage');
						}
						else
						{
							$messages = $this->messages->getAllMessages('userhandler.class.php');
							
							foreach ($messages as $message)
							if ($message->message == 'Problem changing username: username already exists')
							{
								$this->messages->setMessage('Die Email ist bereits registriert. Bitte wähle eine andere.', 'userwarning');
							}

							$this->messages->setMessage('Die Email wurde nicht geändert', 'userwarning');
						}
					}
					
					// username
					if ($parameters['profile']['userdata_username'] != $this->user->getUserdata('userdata_username'))
					{
						
						if ($this->user->setUserdata('userdata_username', $parameters['profile']['userdata_username']))
						{
							$this->messages->setMessage('Dein Benutzername wurde geändert.', 'usermessage');
						}
						else
						{
							$this->messages->setMessage('Dein Benutzername konnte nicht geändert werden', 'userwarning');
						}
					}

					// URL
					if ($parameters['profile']['userdata_url'] != $this->user->getUserdata('userdata_url'))
					{
						
						if ($this->user->setUserdata('userdata_url', $parameters['profile']['userdata_url']))
						{
							$this->messages->setMessage('Deine URL wurde geändert.', 'usermessage');
						}
						else
						{
							$this->messages->setMessage('Deine URL konnte nicht geändert werden', 'userwarning');
						}
					}

					// Password
					if ( (!empty($parameters['profile']['user_password1'])) && (!empty($parameters['profile']['user_password2'])) )
					{
						if ($parameters['profile']['user_password1'] == $parameters['profile']['user_password2'])
						{
							if ($this->user->changePassword($this->user->getUserID(), $parameters['profile']['user_password1']))
							{
								$this->messages->setMessage('Das Passwort wurde geändert.', 'usermessage');
							}
							else
							{
								$this->messages->setMessage('Das Passwort konnte nicht geändert werden', 'userwarning');
							}
						}
						else
						{
							$this->messages->setMessage('Die beiden Passworteingaben stimmen nicht überein', 'userwarning');
						}						
					}
					
				}
			}
			else
			{
				$this->messages->setMessage('Fehler bei der Eingabe. Bitte überprüfen Sie Ihre Angaben sorgfältig.', 'userwarning');
			}
		}
		else
		{
			$userdata = $this->pduserfunctions->getAllUserdata();
			$processData['profile'] = $userdata;
			$formvalid = $editprofileForm->process($processData);
		}

		$formcreated = $editprofileForm->create($tpl);

		$tpl->show();
				
		$this->debug->unguard(true);
		return true;
	}

}
?>
