<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Userhandler class
 *
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgObjectcache::init();
 */
class zgUserhandler
{
	private static $instance = false;

	protected $debug;
	protected $messages;
	protected $session;
	protected $database;
	protected $configuration;

	public $userdata;
	public $userrights;
	public $userroles;

	protected $loggedIn;

	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();

		$this->session = zgSession::init();
		$this->session->startSession();

		$this->userdata = new zgUserdata();
		$this->userrights = new zgUserrights();
		$this->userroles = new zgUserroles();

		$this->loggedIn = false;
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
			self::$instance = new zgUserhandler();
		}

		return self::$instance;
	}


	/**
	 * Tries to establish a login for a user from the session data
	 * Only works if the user was correctly logged in while the current session is active
	 *
	 * @return boolean
	 */
	public function establishUserSession()
	{
		$this->debug->guard();

		if (!$this->session->getSessionId())
		{
			$this->debug->write('Error establishing user session: could not find a session id', 'error');
			$this->messages->setMessage('Error establishing user session: could not find a session id', 'error');
			$this->debug->unguard(false);
			return false;
		}

		if (!$this->session->getSessionVariable('user_userid'))
		{
			$this->debug->write('Could not establish user session: user id not found in session', 'warning');
			$this->messages->setMessage('Could not establish user session: user id not found in session', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$this->_validateUserSession())
		{
			$this->debug->write('Could not validate the user session: session is not safe!', 'warning');
			$this->messages->setMessage('Could not validate the user session: session is not safe!', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$this->_reloginFromSession())
		{
			$this->debug->write('Could not relogin the user from the session', 'warning');
			$this->messages->setMessage('Could not relogin the user from the session', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->loggedIn = true;

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Validates the session, trying to make sure that it is really the right user calling it
	 *
	 * @return boolean
	 */
	protected function _validateUserSession()
	{
		$this->debug->guard();

		if ($this->session->getBoundIP() != getenv('REMOTE_ADDR'))
		{
			$this->debug->write('Problem validating the user session: IP does not match the session', 'warning');
			$this->messages->setMessage('Problem validating the user session: IP does not match the session', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Login a user with username and password
	 * If successfull it will gather the user specific data and tie it to the session
	 *
	 * @param string $name name of the user
	 * @param string $password supposed password of the user
	 *
	 * @return boolean
	 */
	public function loginUser($name, $password)
	{
		$this->debug->guard();

		if (!$this->loggedIn)
		{
			$sql = "SELECT * FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " WHERE user_username = '" . $name . "' AND user_password = '". md5($password) . "' AND user_active='1'";

			if ($res = $this->database->query($sql))
			{
				if ($this->database->numRows($res))
				{
					$row = $this->database->fetchArray($res);
					$this->session->setSessionVariable('user_userid', $row['user_id']);
					$this->session->setSessionVariable('user_key', $row['user_key']);

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
	public function logoutUser()
	{
		$this->debug->guard();

		if ($this->loggedIn)
		{
			$this->session->unsetSessionVariable('user_userid');
			$this->session->unsetSessionVariable('user_key');
		}
		else
		{
			$this->debug->write('Problem logging out user: user is not logged in', 'warning');
			$this->messages->setMessage('Problem logging out user: user is not logged in', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->session->stopSession();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Reload all the user specific data from the session into the structures and classes
	 *
	 * @return boolean
	 */
	protected function _reloginFromSession()
	{
		$this->debug->guard();

		//		$this->userrights->reloadUserrights();
		//		$this->userdata->reloadUserdata();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Calls all functions to save all user specific data into the session
	 *
	 * @return boolean
	 */
	public function saveUserstates()
	{
		$this->debug->guard();

		$this->userrights->saveUserrights();
		$this->userdata->saveUserdata();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Checks if the user is currently logged in
	 *
	 * @return boolean
	 */
	public function isLoggedIn()
	{
		$this->debug->guard();

		if ($this->loggedIn)
		{
			$this->debug->unguard(true);
			return true;
		}

		$this->debug->unguard(false);
		return false;
	}


	/**
	 * Returns the current UserID
	 *
	 * @return integer
	 */
	public function getUserID()
	{
		$this->debug->guard();

		if ($this->loggedIn)
		{
			$userid = $this->session->getSessionVariable('user_userid');

			if ($userid)
			{
				$this->debug->unguard($userid);
				return $userid;
			}
		}

		$this->debug->unguard(false);
		return false;
	}


	/**
	 * Returns the current UserKey
	 *
	 * @return string
	 */
	public function getUserKey()
	{
		$this->debug->guard();

		if ($this->loggedIn)
		{
			$userkey = $this->session->getSessionVariable('user_key');

			if ($userkey)
			{
				$this->debug->unguard($userkey);
				return $userkey;
			}
		}

		$this->debug->unguard(false);
		return false;
	}


	/**
	 * Creates a new user with a given name and password
	 * Optional a usergroup and userdata can be given
	 *
	 * @param string $name name of the user
	 * @param string $password password of the user
	 * @param integer $userrole id of the userrole
	 * @param array $userdata array containing the userdata
	 * 	 *
	 * @return boolean
	 */
	public function createUser($name, $password, $userrole=1, $userdata=array())
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

		// insert user
		srand(microtime()*1000000);
		$key = rand(10000,1000000000);
		$key = md5($key);
		$second = rand(10000,1000000000);
		$key .= md5($second);

		$active = 0;
		if ($this->configuration->getConfiguration('zeitgeist', 'userhandler', 'use_doubleoptin') == '1')
		{
			$active = 1;
		}

		$sqlUser = "INSERT INTO " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . "(user_username, user_key, user_password, user_active) VALUES('" . $name . "', '" . $key . "', '" . md5($password) . "', '" . $active . "')";
		$resUser = $this->database->query($sqlUser);

		$currentId = $this->database->insertId();

		// insert confirmation key
		$confirmationkey = rand(10000,1000000000);
		$confirmationkey = md5($confirmationkey);
		$second = rand(10000,1000000000);
		$confirmationkey .= md5($second);
		$sqlUser = "INSERT INTO " . $this->configuration->getConfiguration('zeitgeist','tables','table_userconfirmation') . "(userconfirmation_user, userconfirmation_key) VALUES('" . $currentId . "', '" . $key . "')";
		$resUser = $this->database->query($sqlUser);

		//userrole
		$sqlUserrole = "INSERT INTO " . $this->configuration->getConfiguration('zeitgeist','tables','table_userroles_to_users') . "(userroleuser_userrole, userroleuser_user) VALUES('" . $userrole . "', '" . $currentId . "')";
		$resUserrole = $this->database->query($sqlUserrole);

		//userdata
		$userdataKeys = array();
		$userdataValues = array();
		foreach ($userdata as $key => $value)
		{
			$userdataKeys[] = $key;
			$userdataValues[] = $value;
		}

		$sqlUserdata = "INSERT INTO " . $this->configuration->getConfiguration('zeitgeist','tables','table_userdata') . "(userdata_user, " . implode(', ', $userdataKeys) . ") VALUES('" . $currentId . "', '" . implode("', '", $userdataValues) . "')";
		$resPassword = $this->database->query($sqlUserdata);

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Deletes a user from the database
	 *
	 * @param integer $userid id of the user
	 *
	 * @return boolean
	 */
	public function deleteUser($userid)
	{
		$this->debug->guard();

		// user account
		$sql = "DELETE FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " WHERE user_id='" . $userid . "'";
		$res = $this->database->query($sql);

		// userdata
		$sql = "DELETE FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_userdata') . " WHERE userdata_user='" . $userid . "'";
		$res = $this->database->query($sql);

		// userrights
		$sql = "DELETE FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_userrights') . " WHERE userright_user='" . $userid . "'";
		$res = $this->database->query($sql);

		// userrole
		$sql = "DELETE FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_userroles_to_users') . " WHERE userroleuser_user='" . $userid . "'";
		$res = $this->database->query($sql);

		// userconfirmation
		$sql = "DELETE FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_userconfirmation') . " WHERE userconfirmation_user='" . $userid . "'";
		$res = $this->database->query($sql);

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Checks if the confirmation key exists
	 * Returns the user id if the key is found or false
	 *
	 * @param string $confirmationkey key to confirm
	 *
	 * @return integer
	 */
	public function checkConfirmation($confirmationkey)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_userconfirmation') . " WHERE userconfirmation_key = '" . $confirmationkey . "'";

		if ($res = $this->database->query($sql))
		{
			if ($this->database->numRows($res))
			{
				$this->debug->unguard($row['userconfirmation_user']);
				return $row['userconfirmation_user'];
			}
			else
			{
				$this->debug->write('Problem confirming a user: key not found', 'warning');
				$this->messages->setMessage('Problem confirming a user: key not found', 'warning');
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

		$this->debug->unguard(false);
		return false;
	}


	/**
	 * Activates a user
	 *
	 * @param integer $userid id of the user to activate
	 *
	 * @return boolean
	 */
	public function activateUser($userid)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " WHERE user_id = '" . $userid . "' AND user_active='0'";

		if ($res = $this->database->query($sql))
		{
			if ($this->database->numRows($res))
			{
				// activate user
				$sql = "UPDATE " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " SET user_active='1' WHERE user_id='" . $userid . "'";
				$res = $this->database->query($sql);

				// userconfirmation
				$sql = "DELETE FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_userconfirmation') . " WHERE userconfirmation_user='" . $userid . "'";
				$res = $this->database->query($sql);

				$this->debug->unguard(true);
				return true;
			}
			else
			{
				$this->debug->write('Problem activating user: user not found or is already active', 'warning');
				$this->messages->setMessage('Problem activating user: user not found or is already active', 'warning');
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

		$this->debug->unguard(false);
		return false;
	}

}
?>
