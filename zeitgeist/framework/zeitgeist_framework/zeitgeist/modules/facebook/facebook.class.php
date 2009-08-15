<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Facebook interface class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST FACEBOOK INTERFACE
 */

defined('ZEITGEIST_ACTIVE') or die();

require_once('facebook-platform/php/facebook.php');

class zgFacebook
{
	protected $debug;
	protected $messages;
	protected $objects;
	protected $configuration;
	protected $database;
	
	protected $facebookObject;

	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjects::init();
		$this->configuration = zgConfiguration::init();
		
		// load facebook specific configuration
		$this->configuration->loadConfiguration('facebook', ZEITGEIST_ROOTDIRECTORY.'configuration/zgfacebook.ini');

		if (file_exists(APPLICATION_ROOTDIRECTORY . 'configuration/zgfacebook.ini'))
		{
			$this->configuration->loadConfiguration('facebook', APPLICATION_ROOTDIRECTORY.'configuration/zgfacebook.ini', true);
		}

		$this->database = new zgDatabase();
		$this->database->connect();

		if ( ($this->configuration->getConfiguration('facebook','api','api_key')) && ($this->configuration->getConfiguration('facebook','api','secret_key')) )
		{
			// get API keys
			$api_key = $this->configuration->getConfiguration('facebook','api','api_key');
			$secret_key = $this->configuration->getConfiguration('facebook','api','secret_key');

			// connect to facebook API
			$this->facebookObject = new Facebook($api_key, $secret_key);
		}

	}
	
	
	/**
	 * Initializes a new facebook api connection
	 *
	 * @param array $keys array with keys
	 * 
	 * @return array
	 */
	public function initFacebookObject($api_key, $secret_key)
	{
		$this->debug->guard();	

		// connect to facebook API
		$this->facebookObject = new Facebook($api_key, $secret_key);
		
		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Logs the user out of facebook
	 *
	 * @param string $redirectURL defines a URL to go after a successful logout
	 *
	 * @return boolean
	 */
	public function logout($redirectURL='')
	{
		$this->debug->guard();

		$fb_answer = $this->facebookObject->logout($redirectURL); 

		$this->debug->unguard($fb_answer);
		return $fb_answer;
	}


	/**
	 * Gets the facebook id of the user
	 * This will only return a value if the user is logged in
	 *
	 * @return integer
	 */
	public function getUserID()
	{
		$this->debug->guard();

		$fb_uid = $this->facebookObject->user; 

		$this->debug->unguard($fb_uid);
		return $fb_uid;
	}
	


	/**
	 * Gets general information for the currently logged in user
	 *
	 * @return array
	 */
	public function getUserInfo()
	{
		$this->debug->guard();
		
		if (!$this->getUserID())
		{
			$this->debug->write('Could not get user data: User not logged in', 'warning');
			$this->messages->setMessage('Could not get user data: User not logged in', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$userdata = $this->facebookObject->api_client->users_getStandardInfo($this->getUserID(), array('first_name', 'last_name', 'name', 'sex', 'locale', 'profile_url'));
		
		$this->debug->unguard($userdata);
		return $userdata;
	}
	
		
	/**
	 * Gets specific information for the currently logged in user
	 * For a list of possible keys, see the facebook API: http://wiki.developers.facebook.com/index.php/Users.getInfo
	 *
	 * @param array $keys array with keys
	 * 
	 * @return array
	 */
	public function getUserdata($keys)
	{
		$this->debug->guard();
		
		$userdata = $this->facebookObject->api_client->users_getInfo($this->getUserID(), $keys);

		$this->debug->unguard(true);
		return $userdata;
	}
		
}
?>