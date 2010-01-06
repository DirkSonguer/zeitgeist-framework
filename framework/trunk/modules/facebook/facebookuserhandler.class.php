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
	 * connects to Facebook
	 *
	 * @param array $api_key public key for your application
	 * @param array $secret_key secret key for your application
	 * 
	 * @return boolean
	 */
	public function connectToFacebook($api_key, $secret_key)
	{
		$this->debug->guard();	

		// connect to facebook API
		$this->facebook->connectToFacebook($api_key, $secret_key);
		
		$this->debug->unguard(true);
		return true;
	}


	/**
	 * returns the facebook ID of the user
	 * 
	 * @return boolean
	 */
	public function getFacebookID()
	{
		$this->debug->guard();	

		// connect to facebook API
		$fb_uid = $this->facebook->getUserID();
		
		$this->debug->unguard($fb_uid);
		return $fb_uid;
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
			$this->debug->write('Could not establish user session: could not find a session id', 'warning');
			$this->messages->setMessage('Could not establish user session: could not find a session id', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$this->facebook->getUserID())
		{
			$this->debug->write('Could not establish user session: User not logged into facebook', 'warning');
			$this->messages->setMessage('Could not establish user session: User not logged into facebook', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if (!$this->session->getSessionVariable('user_userid'))
		{
			$this->debug->write('Could not establish user session: User session not initialized', 'warning');
			$this->messages->setMessage('Could not establish user session: User session not initialized', 'warning');
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

		$this->debug->unguard($this->loggedIn);
		return $this->loggedIn;
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

		if (!$fbid = $this->facebook->getUserID())
		{
			$this->debug->write('User not logged into facebook', 'warning');
			$this->messages->setMessage('User not logged into facebook', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if ($this->loggedIn)
		{
			$this->debug->write('Error logging in a user: user is already logged in. Cannot login user twice', 'error');
			$this->messages->setMessage('Error logging in a user: user is already logged in. Cannot login user twice', 'error');
			$this->debug->unguard(false);
			return false;
		}
			
		$sql = "SELECT u.user_id, u.user_key, u.user_username FROM " . $this->configuration->getConfiguration('facebook','tables','table_facebookusers') . " fb ";
		$sql .= "LEFT JOIN " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . " u ON fb.facebookuser_user = u.user_id ";
		$sql .= "WHERE fb.facebookuser_fbid = '" . $fbid . "' AND u.user_active='1'";

		if (!$res = $this->database->query($sql))
		{
			$this->debug->write('Error searching a user: could not read the user table', 'error');
			$this->messages->setMessage('Error searching a user: could not read the user table', 'error');
			$this->debug->unguard(false);
			return false;
		}

		if ($this->database->numRows($res) != 1)
		{
			$this->debug->write('Problem logging in the user: no linked user exists for this facebook id', 'warning');
			$this->messages->setMessage('Problem logging in the user: no linked user exists for this facebook id', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);
		$this->session->setSessionVariable('user_userid', $row['user_id']);
		$this->session->setSessionVariable('user_key', $row['user_key']);
		$this->session->setSessionVariable('user_username', $row['user_username']);

		$this->loggedIn = true;

		$this->debug->unguard($this->loggedIn);
		return $this->loggedIn;
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
			$this->facebook->logout($this->configuration->getConfiguration('facebook', 'userhandler', 'logouturl'));
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
	 * Create a user from its facebook profile
	 *
	 * @return boolean
	 */
	public function createUser()
	{
		$this->debug->guard();
		
		if (!$fbid = $this->facebook->getUserID())
		{
			$this->debug->write('User not logged into facebook', 'warning');
			$this->messages->setMessage('User not logged into facebook', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "SELECT * FROM " . $this->configuration->getConfiguration('facebook','tables','table_facebookusers') . " WHERE facebookuser_fbid = '" . $fbid . "'";
		$res = $this->database->query($sql);
		if ($this->database->numRows($res) > 0)
		{
			$this->debug->write('A user with this facebook id already exists in the database', 'warning');
			$this->messages->setMessage('A user with this facebook id already exists in the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		// user logged into facebook and is not known yet
		// inserting user into database
		$active = 1;
		$key = md5(uniqid());
		
		$fbuserdata = $this->facebook->getUserInfo();
		if (!$fbuserdata)
		{
			$this->debug->write('Could not create facebook user: could not get user data for user', 'warning');
			$this->messages->setMessage('Could not create facebook user: could not get user data for user', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$sql = "INSERT INTO " . $this->configuration->getConfiguration('zeitgeist','tables','table_users') . "(user_username, user_key, user_password, user_active) VALUES('" . $fbuserdata[0]['name'] . "', '" . $key . "', '" . '' . "', '" . $active . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem creating the user: could not insert the user into the database', 'error');
			$this->messages->setMessage('Problem creating the user: could not insert the user into the database', 'error');
			$this->debug->unguard(false);
			return false;
		}

		$currentId = $this->database->insertId();
		
		$this->_linkFacebookUser($currentId, $fbid);
		
		$this->debug->unguard($currentId);
		return $currentId;
	}


	/**
	 * Links existing account to Facebook login
	 *
	 * @param integer $userid user id of the (existing) user
	 * @param integer $fbid facebook id of the user
	 *
	 * @return boolean
	 */
	private function _linkFacebookUser($userid, $fbid)
	{
		$this->debug->guard();
		
		$sql = "INSERT INTO " . $this->configuration->getConfiguration('facebook','tables','table_facebookusers') . "(facebookuser_fbid, facebookuser_user) ";
		$sql .= "VALUES('".$fbid."', '" . $userid . "')";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem creating the user: could not connect the user data to the facebook data', 'error');
			$this->messages->setMessage('Problem creating the user: could not connect the user data to the facebook data', 'error');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}
	

	/**
	 * Imports Facebook data and stores it into the user data
	 * Imports according to the mapping in the zgFacebook config file
	 *
	 * @return boolean
	 */
	private function _importFacebookUserdata()
	{
		$this->debug->guard();
		
		if (!$fbid = $this->facebook->getUserID())
		{
			$this->debug->write('User not logged into facebook', 'warning');
			$this->messages->setMessage('User not logged into facebook', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$usermapping = $this->configuration->getConfiguration('facebook','userdata');
		
		foreach ($usermapping as $facebookmapping)
		{
			$fbarray[] = $facebookmapping;
		}

		$fbUserdata = $this->facebook->getUserdata($fbarray);

		$insertarray = array();
		foreach ($usermapping as $userdatakey => $facebookkey)
		{
			$ret = parent::setUserdata($userdatakey, $fbUserdata[0][$facebookkey], false, true);
		}
		
		parent::_saveUserdata();

		$this->debug->unguard(true);
		return true;
	}

}
?>
