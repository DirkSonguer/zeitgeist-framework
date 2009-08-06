<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Facebook Userhandler class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST FACEBOOK USERHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgObjects::init();
 */
class zgFacebookUserhandler extends zgUserhandler
{
	private static $instance = false;

	protected $debug;
	protected $messages;
	protected $session;
	protected $database;
	protected $configuration;
	protected $facebook;

	protected $loggedIn;

	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	protected function __construct()
	{
		$this->facebook = new zgFacebook();
		
		parent::__construct();
	}


	/**
	 * Initialize the singleton
	 *
	 * @return object
	 */
	public static function init()
	{
		if (self::$instance === false)
		{
			self::$instance = new zgFacebookUserhandler();
		}

		return self::$instance;
	}


	/**
	 * Login a user with username and password
	 * If successfull it will gather the user specific data and tie it to the session
	 *
	 * @param string $username name of the user
	 * @param string $password supposed password of the user
	 *
	 * @return boolean
	 */
	public function login()
	{
		$this->debug->guard();

		if (!$this->facebook->getUserID())
		{
			$this->debug->write('User not logged into facebook', 'warning');
			$this->messages->setMessage('User not logged into facebook', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$this->loggedIn)
		{
			$sql = "SELECT * FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " WHERE user_username = '" . $username . "' AND user_password = '". md5($password) . "' AND user_active='1'";

			if ($res = $this->database->query($sql))
			{
				if ($this->database->numRows($res))
				{
					$row = $this->database->fetchArray($res);
					$this->session->setSessionVariable('user_userid', $row['user_id']);
					$this->session->setSessionVariable('user_key', $row['user_key']);
					$this->session->setSessionVariable('user_username', $row['user_username']);

					$this->loggedIn = true;

					$this->debug->unguard(true);
					return true;
				}
				else
				{
					$this->debug->write('Problem validating a user: user not found/is inactive or password is wrong', 'warning');
					$this->messages->setMessage('Problem validating a user: user not found/is inactive or password is wrong', 'warning');
					$this->debug->unguard(false);
					return false;
				}
			}
			else
			{
				$this->debug->write('Error searching a user: could not read the user table', 'error');
				$this->messages->setMessage('Error searching a user: could not read the user table', 'error');
				$this->debug->unguard(false);
				return false;
			}
		}
		else
		{
			$this->debug->write('Error logging in a user: user is already logged in. Cannot login user twice', 'error');
			$this->messages->setMessage('Error logging in a user: user is already logged in. Cannot login user twice', 'error');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(false);
		return false;
	}


	/**
	 * Log out the user if he is currently logged in
	 *
	 * @return boolean
	 */
	public function logout()
	{
		$this->debug->guard();

		if ($this->loggedIn)
		{
			$this->session->unsetAllSessionVariables();
		}
		else
		{
			$this->debug->write('Problem logging out user: user is not logged in', 'warning');
			$this->messages->setMessage('Problem logging out user: user is not logged in', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->session->stopSession();
		$this->loggedIn = false;

		$this->debug->unguard(true);
		return true;
	}
	

	/**
	 * Creates a new user with a given name and password
	 *
	 * @param string $name name of the user
	 * @param string $password password of the user
	 *
	 * @return boolean
	 */
	public function createUser($name, $password)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " WHERE user_username = '" . $name . "'";
		$res = $this->database->query($sql);
		if ($this->database->numRows($res) > 0)
		{
			$this->debug->write('A user with this name already exists in the database. Please choose another username.', 'warning');
			$this->messages->setMessage('A user with this name already exists in the database. Please choose another username.', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$active = 1;
		$key = md5(uniqid());
		if ($this->configuration->getConfiguration('zeitgeist', 'userhandler', 'use_doubleoptin') == '1')
		{
			$active = 0;
		}

		$sql = "INSERT INTO " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . "(user_username, user_key, user_password, user_active) VALUES('" . $name . "', '" . $key . "', '" . md5($password) . "', '" . $active . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem creating the user: could not insert the user into the database.', 'error');
			$this->messages->setMessage('Problem creating the user: could not insert the user into the database.', 'error');
			$this->debug->unguard(false);
			return false;
		}

		$currentId = $this->database->insertId();

		// insert confirmation key
		$confirmationkey = md5(uniqid());
		$sqlUser = "INSERT INTO " . $this->configuration->getConfiguration('zeitgeist','tables','table_userconfirmation') . "(userconfirmation_user, userconfirmation_key) VALUES('" . $currentId . "', '" . $confirmationkey . "')";
		$resUser = $this->database->query($sqlUser);
		
		$this->debug->unguard($currentId);
		return $currentId;
	}

}
?>
