<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Userhandler class
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
	protected $userrightsLoaded;
	protected $userdataLoaded;
	protected $userrolesLoaded;

	public $userrights;
	public $userdata;
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

		$this->userrights = array();
		$this->userrightsLoaded = false;

		$this->userdata = array();
		$this->userdataLoaded = false;

		$this->userroles = array();
		$this->userrolesLoaded = false;

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
			$this->debug->write('Problem establishing user session: could not find a session id', 'warning');
			$this->messages->setMessage('Problem establishing user session: could not find a session id', 'warning');
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

		$this->loggedIn = true;

		$this->debug->unguard(true);
		return true;
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
	public function login($username, $password)
	{
		$this->debug->guard();

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
	 * Sets the login status of a user
	 * This is public as the unit tests use it as well
	 *
	 * @param boolean $status login status pf the user
	 *
	 * @return boolean
	 */
	public function setLoginStatus($status=false)
	{
		$this->debug->guard();
		
		$this->loggedIn = $status;
		
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

		$this->_saveUserrights();
		// TODO: Add roles!
//		$this->saveUserroles();
		$this->_saveUserdata();

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
	 * Returns the current User ID
	 *
	 * @return string
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
	 * Returns the current Username
	 *
	 * @return string
	 */
	public function getUsername()
	{
		$this->debug->guard();

		if ($this->loggedIn)
		{
			$username = $this->session->getSessionVariable('user_username');

			if ($username)
			{
				$this->debug->unguard($username);
				return $username;
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
	 * Changes the password of the user to a given one password
	 *
	 * @param integer $userid id of the user to chek the userrole for. if 0 the current user is used
	 * @param string $password password of the user
	 *
	 * @return boolean
	 */
	public function changePassword($userid, $password)
	{
		$this->debug->guard();

		$sql = "UPDATE " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " SET user_password = '" . md5($password) . "' WHERE user_id='" . $userid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem changing the password of a user', 'warning');
			$this->messages->setMessage('Problem changing the password of a user', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Changes the username of the user to a given one
	 * Checks if the given username already exists
	 *
	 * @param integer $userid id of the user to chek the userrole for. if 0 the current user is used
	 * @param string $username username of the user
	 *
	 * @return boolean
	 */
	public function changeUsername($userid, $username)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " WHERE user_username='" . $username . "'";
		$res = $this->database->query($sql);
		if ($this->database->numRows($res) > 0)
		{
			$this->debug->write('Problem changing username: username already exists', 'warning');
			$this->messages->setMessage('Problem changing username: username already exists', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "UPDATE " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " SET user_username = '" . $username . "' WHERE user_id='" . $userid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem changing the username of a user', 'warning');
			$this->messages->setMessage('Problem changing the username of a user', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->session->setSessionVariable('user_username', $username);

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Checks if the confirmation key exists
	 * Returns the user id if the key is found or false if key is invalid
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
				$row = $this->database->fetchArray($res);
				$this->debug->unguard($row['userconfirmation_user']);
				return $row['userconfirmation_user'];
			}
			else
			{
				$this->debug->write('Problem confirming a user: key not found for given user', 'warning');
				$this->messages->setMessage('Problem confirming a user: key not found for given user', 'warning');
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


	/**
	 * Deactivates a user
	 *
	 * @param integer $userid id of the user to deactivate
	 *
	 * @return boolean
	 */
	public function deactivateUser($userid)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " WHERE user_id = '" . $userid . "' AND user_active='1'";

		if ($res = $this->database->query($sql))
		{
			if ($this->database->numRows($res))
			{
				// activate user
				$sql = "UPDATE " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " SET user_active='0' WHERE user_id='" . $userid . "'";
				$res = $this->database->query($sql);

				// insert confirmation key
				$confirmationkey = md5(uniqid());
				$sqlUser = "INSERT INTO " . $this->configuration->getConfiguration('zeitgeist','tables','table_userconfirmation') . "(userconfirmation_user, userconfirmation_key) VALUES('" . $userid . "', '" . $confirmationkey . "')";
				$resUser = $this->database->query($sqlUser);

				$this->debug->unguard(true);
				return true;
			}
			else
			{
				$this->debug->write('Problem deactivating user: user not found or is already deactivated', 'warning');
				$this->messages->setMessage('Problem deactivating user: user not found or is already deactivated', 'warning');
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
	 * Load all userdata for the current user
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _loadUserdata()
	{
		$this->debug->guard();

		$userdata = new zgUserdata();
		$this->userdata = $userdata->loadUserdata($this->getUserID());

		if (!is_array($this->userdata) || (count($this->userdata) == 0))
		{
			$this->debug->write('Error getting userdata for a user: could not find the userdata', 'error');
			$this->messages->setMessage('Error getting userdata for a user: could not find the userdata', 'error');
			$this->debug->unguard(false);
			return false;
		}

		$this->userdataLoaded = true;
		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Saves all userdata for the current user
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _saveUserdata()
	{
		$this->debug->guard();

		if (!$this->userdataLoaded)
		{
			$this->debug->write('Problem saving userdata: Userdata is not loaded for user. No update needed.', 'message');
			$this->messages->setMessage('Problem saving userdata: Userdata is not loaded for user. No update needed.', 'message');
			$this->debug->unguard(true);
			return true;
		}

		$userdata = new zgUserdata();
		$ret = $userdata->saveUserdata($this->getUserID(), $this->userdata);

		if (!$ret)
		{
			$this->debug->write('Problem saving the user data', 'warning');
			$this->messages->setMessage('Problem saving the user data', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Gets userdata for the current user
	 * Returns a given key or the whole array
	 *
	 * @param string $datakey key of the userdata to fetch
	 *
	 * @return boolean
	 */
	public function getUserdata($datakey='')
	{
		$this->debug->guard();

		if (!$this->userdataLoaded)
		{
			$this->_loadUserdata();
		}

		if ($datakey != '')
		{
			if (!empty($this->userdata[$datakey]))
			{
				$this->debug->unguard($this->userdata[$datakey]);
				return $this->userdata[$datakey];
			}
			else
			{
				$this->debug->write('Problem getting selected userdata: userdata with given key (' . $datakey . ') not found', 'warning');
				$this->messages->setMessage('Problem getting selected userdata: userdata with given key (' . $datakey . ') not found', 'warning');

				$this->debug->unguard(false);
				return false;
			}
		}
		else
		{
			$this->debug->unguard('No key given, returning all userdata');
			return $this->userdata;
		}

		$this->debug->unguard(false);
		return false;
	}


	/**
	 * Sets new value for a given userdata
	 *
	 * @param string $userdata key of the userdata to write
	 * @param string $value content to write
	 * @param boolean $saveuserdata flag if userdata should be saved to database
	 *
	 * @return boolean
	 */
	public function setUserdata($userdata, $value, $saveuserdata=true)
	{
		$this->debug->guard();

		if (!$this->userdataLoaded)
		{
			$this->_loadUserdata();
		}

		if (isset($this->userdata[$userdata]))
		{
			$this->userdata[$userdata] = $value;
			if ($saveuserdata) $this->_saveUserdata();

			$this->debug->unguard(true);
			return true;
		}

		$this->debug->write('Error setting userdata: Userdata (' . $userdata . ') does not exist and could not be set.', 'error');
		$this->messages->setMessage('Error setting userdata: Userdata (' . $userdata . ') does not exist and could not be set.', 'error');

		$this->debug->unguard(false);
		return false;
	}


	/**
	 * Load all userrights for the current user
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _loadUserrights()
	{
		$this->debug->guard();

		$userrights = new zgUserrights();		
		$this->userrights = $userrights->loadUserrights($this->getUserID());

		if (!is_array($this->userrights) || (count($this->userrights) == 0))
		{
			$this->debug->write('Problem getting userrights for a user: could not find the userrights', 'warning');
			$this->messages->setMessage('Problem getting userrights for a user: could not find the userrights', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->userrightsLoaded = true;
		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Save all userrights to the database
	 *
	 * @return boolean
	 */
	protected function _saveUserrights()
	{
		$this->debug->guard();

		if (!$this->userrightsLoaded)
		{
			$this->debug->write('Problem saving user rights: User rights are not loaded for user. No update needed.', 'message');
			$this->messages->setMessage('Problem saving user rights: User rights are not loaded for user. No update needed.', 'message');
			$this->debug->unguard(true);
			return true;
		}

		$userrights = new zgUserrights();
		$ret = $userrights->saveUserrights($this->getUserID(), $this->userrights);

		if (!$ret)
		{
			$this->debug->write('Problem saving the user rights', 'warning');
			$this->messages->setMessage('Problem saving the user rights', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Check if the user has a given userright
	 *
	 * @param integer $actionid id of the action
	 *
	 * @return boolean
	 */
	public function hasUserright($actionid)
	{
		$this->debug->guard();

		if (!$this->userrightsLoaded)
		{
			$this->_loadUserrights();
		}
		
		if (!empty($this->userrights[$actionid]))
		{
			$this->debug->unguard(true);
			return true;
		}

		$this->debug->write('User does not have the requested right for action (' . $actionid . ')', 'warning');
		$this->messages->setMessage('User does not have the requested right for action (' . $actionid . ')', 'warning');

		$this->debug->unguard(false);
		return false;
	}


	/**
	 * Adds rights for the user for a given action
	 *
	 * @param integer $actionid id of the action to add rights to
	 * @param boolean $saveuserrights flag if user rights should be saved to database
	 *
	 * @return boolean
	 */
	public function addUserright($actionid, $saveuserrights=true)
	{
		$this->debug->guard();

		if (!$this->userrightsLoaded)
		{
			$this->_loadUserrights();
		}

		$this->userrights[$actionid] = true;
		if ($saveuserrights) $this->_saveUserrights();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Deletes a userright for an action
	 *
	 * @param integer $actionid id of the action to delete rights for
	 * @param boolean $saveuserrights flag if user rights should be saved to database
	 *
	 * @return boolean
	 */
	public function deleteUserright($actionid, $saveuserrights=true)
	{
		$this->debug->guard();

		if (isset($this->userrights[$actionid]))
		{
			unset($this->userrights[$actionid]);
			if ($saveuserrights) $this->_saveUserrights();
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Load all userrights for the current user
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _loadUserroles()
	{
		$this->debug->guard();

		$userroles = new zgUserroles();
		$this->userroles = $userroles->loadUserroles($this->getUserID());
		
		if ((is_array($this->userroles)) && (count($this->userroles) > 0))
		{
			foreach ($this->userroles as $roleid => $value)
			{
				$this->debug->write('Problem getting userroles for a user: could not find the userroles', 'warning');
				$this->messages->setMessage('Problem getting userroles for a user: could not find the userroles', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		
		$this->userrolesLoaded = true;
		$this->debug->unguard(true);
		return true;
	}



	/**
	 * Check if the user has a given userrole
	 *
	 * @param string $arolename name of the userrole
	 *
	 * @return boolean
	 */
	public function hasUserrole($rolename)
	{
		$this->debug->guard();

		if (!$this->userrolesLoaded)
		{
			$this->_loadUserroles();
		}
		if ($rolename === true)
		{
			$this->debug->write('You should not ask for generic roles', 'warning');
			$this->messages->setMessage('You should not ask for generic roles', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if (in_array($rolename, $this->userroles))
		{
			$this->debug->unguard(true);
			return true;
		}

		$this->debug->write('User does not have the requested role assigned (' . $rolename . ')', 'warning');
		$this->messages->setMessage('User does not have the requested role assigned (' . $rolename . ')', 'warning');
		$this->debug->unguard(false);
		return false;
	}


	public function addUserrole()
	{
		// TODO: FUNCTION!
	}


	public function deleteUserrole()
	{
		// TODO: FUNCTION!
	}


	public function saveUserroles()
	{
		// TODO: FUNCTION!
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
			$this->session->unsetSessionVariable('user_userid');
			$this->session->unsetSessionVariable('user_key');
			$this->session->stopSession();

			$this->debug->write('Problem validating the user session: IP does not match the session', 'warning');
			$this->messages->setMessage('Problem validating the user session: IP does not match the session', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}

}
?>
