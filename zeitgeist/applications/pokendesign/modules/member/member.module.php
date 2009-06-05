<?php

defined('POKENDESIGN_ACTIVE') or die();

class member
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


	// show overview of the own cards
	public function index($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('member', 'templates', 'member_index'));

		// cards		
		$carddata = $this->cards->getAuthorCards($this->user->getUserID());
		if (count($carddata) == 0)
		{
			$tpl->insertBlock('nocardsfound');
		}
		
		$i = 0;
		foreach ($carddata as $card)
		{
			$i++;
			if ( ($i%2) == 0) $tpl->insertBlock('nextline');

			$tpl->assign('cardfile', $card['card_filename']);
			$tpl->assign('cardid', $card['card_id']);
			$tpl->assign('carddate', $card['card_date']);
			$tpl->assign('cardtitle', $card['card_title']);
			$tpl->assign('cardviews', $card['card_viewed']);
			$tpl->assign('cardclicks', $card['card_clicked']);
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

			$tpl->insertBlock('cardlist');
		}
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	// deletes a card of the user
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
		$tpl->redirect($tpl->createLink('member', 'index'));

		$this->debug->unguard(true);
		return true;
	}
	

	// adds a card
	public function addcard($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('member', 'templates', 'member_addcard'));

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
					$this->messages->setMessage('Die neue Visitenkarte wurde gespeichert.', 'usermessage');
					$tpl = new pdTemplate();
					$tpl->redirect($tpl->createLink('member', 'index'));
					return true;
				}
				else
				{
					$this->messages->setMessage('Die Visitenkarte wurde nicht gespeichert.', 'usererror');
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


	// edits an existing card
	public function editcard($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('member', 'templates', 'member_editcard'));

		$editcardForm = new zgStaticform();
		$editcardForm->load('forms/editcard.form.ini');
		$formvalid = $editcardForm->process($parameters);

		if (!empty($parameters['submit']))
		{
			if ($formvalid)
			{
				$carddata = $parameters['editcard'];

				if ($this->cards->editCard($carddata))
				{
					$this->messages->setMessage('Die neuen Daten wurden gespeichert.', 'usermessage');
					$tpl = new pdTemplate();
					$tpl->redirect($tpl->createLink('member', 'index'));
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
		else
		{
			if (empty($parameters['card']))
			{
				$tpl = new pdTemplate();
				$tpl->redirect($tpl->createLink('member', 'index'));
			}

			$processData = array();
			$cardData = $this->cards->getCardInformation($parameters['card'], true);
			$cardtags = $this->cards->getTags($parameters['card']);
			$cardData['card_tags'] = implode(', ',$cardtags);
			
			$processData['editcard'] = $cardData;
			$formvalid = $editcardForm->process($processData);
		}

		$formcreated = $editcardForm->create($tpl);
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}


	// edits the users profile
	public function editprofile($parameters=array())
	{
		$this->debug->guard();

		$tpl = new pdTemplate();
		$tpl->load($this->configuration->getConfiguration('member', 'templates', 'member_editprofile'));

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
