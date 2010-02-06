<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Userhandler class
 * 
 * Manages a specific user session
 * Based on the userfunctions.class.php
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgUserhandler::init();
 */
class zgUserhandler
{
	private static $instance = false;

	protected $debug;
	protected $messages;
	protected $session;
	protected $database;
	protected $configuration;

	protected $userroles;
	protected $userrolesLoaded;

	protected $userrights;
	protected $userrightsLoaded;

	protected $userdata;
	protected $userdataLoaded;

	protected $loggedIn;
	protected $userid;

	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	protected function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();

		$this->session = zgSession::init();
		$this->session->startSession();

		$this->userroles = array();
		$this->userrolesLoaded = false;

		$this->userrights = array();
		$this->userrightsLoaded = false;

		$this->userdata = array();
		$this->userdataLoaded = false;

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

		if (!$this->session->getSessionVariable('user_id'))
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
			$userfunctions = new zgUserfunctions();
			if ($userid = $userfunctions->login($username, $password))
			{
				$userinformation = $userfunctions->getInformation($userid);
				$this->session->setSessionVariable('user_id', $userinformation['user_id']);
				$this->session->setSessionVariable('user_key', $userinformation['user_key']);
				$this->session->setSessionVariable('user_username', $userinformation['user_username']);

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
		$this->_saveUserroles();
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
			$userid = $this->session->getSessionVariable('user_id');

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

		if ( (!is_array($this->userdata)) || (count($this->userdata) < 1) )
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
			$this->debug->write('Userdata is not loaded for user: no update needed.', 'message');
			$this->messages->setMessage('Userdata is not loaded for user: no update needed.', 'message');
			$this->debug->unguard(true);
			return true;
		}

		$userdata = new zgUserdata();
		$ret = $userdata->saveUserdata($this->getUserID(), $this->userdata);
		if (!$ret)
		{
			$this->debug->write('Problem saving the user data: could not save userdata for user', 'warning');
			$this->messages->setMessage('Problem saving the user data: could not save userdata for user', 'warning');
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
	 * @param boolean $forceupdate forces the creation of a new userdata field
	 *
	 * @return boolean
	 */
	public function setUserdata($userdata, $value, $saveuserdata=true, $forceupdate=false)
	{
		$this->debug->guard();

		if (!$this->userdataLoaded)
		{
			$this->_loadUserdata();
		}

		if (array_key_exists($userdata, $this->userdata))
		{
			$this->userdata[$userdata] = $value;
			if ($saveuserdata) $this->_saveUserdata();

			$this->debug->unguard(true);
			return true;
		}

		if ($forceupdate)
		{
			$this->userdata[$userdata] = $value;
			if ($saveuserdata) $this->_saveUserdata();

			$this->debug->unguard(true);
			return true;
		}

		$this->debug->write('Problem setting userdata: Userdata (' . $userdata . ') does not exist and could not be set.', 'warning');
		$this->messages->setMessage('Problem setting userdata: Userdata (' . $userdata . ') does not exist and could not be set.', 'warning');

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

		if ( (!is_array($this->userrights)) || (count($this->userrights) < 1) )
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
			$this->debug->write('User rights are not loaded for user: no update needed.', 'message');
			$this->messages->setMessage('User rights are not loaded for user: no update needed.', 'message');
			$this->debug->unguard(true);
			return true;
		}

		$userrights = new zgUserrights();
		$ret = $userrights->saveUserrights($this->getUserID(), $this->userrights);
		if (!$ret)
		{
			$this->debug->write('Problem saving the user rights: could not save userrights for user', 'warning');
			$this->messages->setMessage('Problem saving the user rights: could not save userrights for user', 'warning');
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

		$this->debug->write('User does not have the requested right for action (' . $actionid . ')', 'message');
		$this->messages->setMessage('User does not have the requested right for action (' . $actionid . ')', 'message');

		$this->debug->unguard(false);
		return false;
	}


	/**
	 * Grants rights for a given action to the current user
	 *
	 * @param integer $actionid id of the action to add rights to
	 * @param boolean $saveuserrights flag if user rights should be saved to database
	 *
	 * @return boolean
	 */
	public function grantUserright($actionid, $saveuserrights=true)
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
	 * Revokes the right for a given action for the current user
	 *
	 * @param integer $actionid id of the action to delete rights for
	 * @param boolean $saveuserrights flag if user rights should be saved to database
	 *
	 * @return boolean
	 */
	public function revokeUserright($actionid, $saveuserrights=true)
	{
		$this->debug->guard();

		if (!$this->userrightsLoaded)
		{
			$this->_loadUserrights();
		}

		if (isset($this->userrights[$actionid]))
		{
			unset($this->userrights[$actionid]);
			if ($saveuserrights) $this->_saveUserrights();
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Load all userroles for the current user
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
		
		if (!is_array($this->userroles))
		{
			$this->debug->write('Problem getting userroles for a user: could not load userroles', 'warning');
			$this->messages->setMessage('Problem getting userroles for a user: could not load userroles', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->userrolesLoaded = true;
		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Save all userroles to the database
	 *
	 * @return boolean
	 */
	protected function _saveUserroles()
	{
		$this->debug->guard();

		if (!$this->userrolesLoaded)
		{
			$this->debug->write('User roles are not loaded for user: no update needed.', 'message');
			$this->messages->setMessage('User roles are not loaded for user: no update needed.', 'message');
			$this->debug->unguard(true);
			return true;
		}

		$userroles = new zgUserroles();
		$ret = $userroles->saveUserroles($this->getUserID(), $this->userroles);
		if (!$ret)
		{
			$this->debug->write('Problem saving the user roles: could not save userroles for user', 'warning');
			$this->messages->setMessage('Problem saving the user roles: could not save userroles for user', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Check if the user has a given userrole
	 *
	 * @param string $rolename name of the userrole
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
			$this->debug->write('Problem checking userroles: you should not ask for generic roles', 'warning');
			$this->messages->setMessage('Problem checking userroles: you should not ask for generic roles', 'warning');
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


	/**
	 * Grants a role to the current user
	 *
	 * @param string $rolename name of the userrole to add
	 * @param boolean $saveuserroles flag if user roles should be saved to database
	 *
	 * @return boolean
	 */
	public function grantUserrole($rolename, $saveuserroles=true)
	{
		$this->debug->guard();

		if (!$this->userrolesLoaded)
		{
			$this->_loadUserroles();
		}

		$userroles = new zgUserroles();
		$roleid  = $userroles->identifyRole($rolename);

		$this->userroles[$roleid] = $rolename;
		if ($saveuserroles) $this->_saveUserroles();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Revokes a role for the current user
	 *
	 * @param string $rolename name of the userrole to add
	 * @param boolean $saveuserroles flag if user roles should be saved to database
	 *
	 * @return boolean
	 */
	public function revokeUserrole($rolename, $saveuserroles=true)
	{
		$this->debug->guard();

		if (!$this->userrolesLoaded)
		{
			$this->_loadUserroles();
		}

		if ($roleid = array_search($rolename, $this->userroles))
		{
			unset($this->userroles[$roleid]);
			if ($saveuserroles) $this->_saveUserroles();
		}

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
			$this->session->unsetSessionVariable('user_id');
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
