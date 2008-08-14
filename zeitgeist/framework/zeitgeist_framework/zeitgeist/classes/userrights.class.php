<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Userrights class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is extended by userroles.class.php.
 */
class zgUserrights
{
	protected $debug;
	protected $messages;
	protected $session;
	protected $database;
	protected $configuration;

	protected $userrightsLoaded;
	public $userrights;

	/**
	 * Class constructor
	 */
	public function __construct()
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
	 * Load all userrights for a given user
	 *
	 * @param integer $userid id of the user
	 *
	 * @return boolean
	 */
	public function loadUserrights()
	{
		$this->debug->guard();

		$userrightsTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userrights');
		$sql = "SELECT * FROM " . $userrightsTablename . " WHERE userright_user = '" . $this->getUserID() . "'";

		if ($res = $this->database->query($sql))
		{
			while ($row = $this->database->fetchArray($res))
			{
				$this->userrights[$row['userright_action']] = true;
			}

			$this->_getUserrightsForRoles();

			if (count($this->userrights) == 0)
			{
				$this->debug->write('Possible problem getting userrights for a user: the user seems to habe no assigned rights', 'warning');
				$this->messages->setMessage('Possible problem getting userrights for a user: the user seems to habe no assigned rights', 'warning');

				$this->debug->unguard(false);
				return false;
			}

			$this->userrightsLoaded = true;
			$this->debug->unguard(true);
			return true;
		}
		else
		{
			$this->debug->write('Error getting userrights for a user: could not find the userrights', 'error');
			$this->messages->setMessage('Error getting userrights for a user: could not find the userrights', 'error');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(false);
		return false;
	}


	/**
	 * Save all userrights to the session for later use
	 * Also updates the according userright table with the current data
	 *
	 * @return boolean
	 */
	public function saveUserrights()
	{
		$this->debug->guard();

		if ($this->userrightsLoaded)
		{
			$userrightsTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userrights');
			$userid = $this->session->getSessionVariable('user_userid');

			$sql = 'DELETE FROM ' . $userrightsTablename . " WHERE userright_user='" . $userid . "'";
			$res = $this->database->query($sql);

			foreach ($this->userrights as $key => $value)
			{
				$sql = 'INSERT INTO ' . $userrightsTablename . "(userright_action, userright_user) VALUES('" . $key . "', '" . $userid . "')";
				$res = $this->database->query($sql);
			}
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
			$this->loadUserrights();
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
	 * @param integer $userright id of the action to add rights to
	 *
	 * @return boolean
	 */
	public function addUserright($userright)
	{
		$this->debug->guard();

		if (!$this->userrightsLoaded)
		{
			$this->loadUserrights();
		}

		$this->userrights[$userright] = true;
		$this->saveUserrights();

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Deletes a userright for an action
	 *
	 * @param integer $userright id of the action to delete rights for
	 *
	 * @return boolean
	 */
	public function deleteUserright($userright)
	{
		$this->debug->guard();

		if (isset($this->userrights[$userright]))
		{
			unset($this->userrights[$userright]);
			$this->saveUserrights();
		}

		$this->debug->unguard(true);
		return true;
	}

}
?>
