<?php

defined('POKENDESIGN_ACTIVE') or die();

class pdUserfunctions
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $session;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->session = zgSession::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * Creates a new user with a given name and password
	 *
	 * @param string $name name of the user
	 * @param string $password password of the user
	 * @param array $userdata array containing the userdata
	 *
	 * @return boolean
	 */
	public function createUser($name, $password, $userdata=array())
	{
		$this->debug->guard();

		if (!$newUserId = $this->user->createUser($name, $password))
		{
			$this->debug->unguard(false);
			return false;
		}
		
		$userdatafunctions = new zgUserdata();
		if (!$userdatafunctions->saveUserdata($newUserId, $userdata))
		{
			$this->debug->unguard(false);
			return false;
		}
		
		
		$userrolearray = array();
		$userrolearray[1] = true;
		$userrolefunctions = new zgUserroles();
		if (!$userrolefunctions->saveUserroles($newUserId, $userrolearray))
		{
			$this->debug->unguard(false);
			return false;
		}		
		
		$userconfirmation = $this->user->getConfirmationKey($newUserId);
		
		$this->sendRegistrationMail($name, $userdata['userdata_username'], $userconfirmation);

		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * Sends out a registration mail
	 *
	 * @param string $mail mail to send the mail to
	 * @param string $username username used for personalisation
	 * @param string $confirmation confirmation string for the user
	 *
	 * @return boolean
	 */
	public function sendRegistrationMail($mail, $username, $confirmation)
	{
		$this->debug->guard();

		//define the subject of the email
		$subject = 'Willkommen bei Pokendesign';
		//define the message to be sent. Each line should be separated with \n
		$message = "Hallo " . $username . "\n\nDu hast vor dich auf Pokendesign.com zu registrieren. Deine Registrierung ist fast abgeschlossen.\n\n";
		$message .= "Bitte bestaetige deine Email, indem du auf folgenden Link klickst bzw. den Link in die Adresszeile deines Browsers kopierst:\n\n";
		$message .= 'http://www.pokendesign.de/index.php?action=confirmregistration&id=' . $confirmation . "\n\n";
		$message .= "Vielen Dank,\nDas Pokendesign-Team";
		//define the headers we want passed. Note that they are separated with \r\n
		$headers = "From: noreply@pokendesign.de\r\nReply-To: noreply@pokendesign.com";
		//send the email
		$mail_sent = mail( $mail, $subject, $message, $headers );
		$mail_sent = mail( 'dirk@songuer.de', $subject, $message, $headers );
		//if the message is sent successfully print "Mail sent". Otherwise print "Mail failed" 

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Gets all relevant user data for the current user
	 *
	 * @return array
	 */
	public function getAllUserdata()
	{
		$this->debug->guard();
		
		$userdata = array();
		$userdata = $this->user->getUserdata();
		$userdata['user_username'] = $this->user->getUsername();

		$this->debug->unguard($userdata);
		return $userdata;
	}

}
?>
