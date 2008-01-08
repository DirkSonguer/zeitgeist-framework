<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Userrights class
 *
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERDATA
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class handles the userdata
 * Although it is a stand alone class, it is most likely called directly from the userhandler class
 */
class zgUserdata
{
	protected $debug;
	protected $messages;
	protected $session;
	protected $configuration;
	protected $database;
	protected $userdataLoaded;

	public $userdata;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->session = zgSession::init();

		$this->userdataLoaded = false;

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * Load all userdata for a given user
	 *
	 * @param integer $userid id of the user
	 *
	 * @return boolean
	 */
	public function loadUserdata($userid)
	{
		$this->debug->guard();

		$userdataTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userdata');
		$sql = "SELECT * FROM " . $userdataTablename . " WHERE userdata_user = '" . $userid . "'";

		if ($res = $this->database->query($sql))
		{
			$row = $this->database->fetchArray($res);

			if (is_array($row))
			{
				$this->userdata = $row;
			}
			else
			{
				$this->debug->write('Possible problem getting userdata for a user: the user seems to habe no assigned data', 'warning');
				$this->messages->setMessage('Possible problem getting userdata for a user: the user seems to habe no assigned data', 'warning');

				$this->debug->unguard(false);
				return false;
			}

			$this->userdataLoaded = true;
			$this->debug->unguard(true);
			return true;
		}
		else
		{
			$this->debug->write('Error getting userdata for a user: could not find the userdata', 'error');
			$this->messages->setMessage('Error getting userdata for a user: could not find the userdata', 'error');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(false);
		return false;
	}


	/**
	 * Save all userrdata to the session for later use
	 * Also updates the according userdata table with the current data
	 *
	 * @return boolean
	 */
	public function saveUserdata()
	{
		$this->debug->guard();

		if ($this->userdataLoaded)
		{
			$userdataTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userdata');
			$userid = $this->session->getSessionVariable('user_userid');

			$sql = 'UPDATE ' . $userdataTablename . ' SET ';
			$sqlupdate = '';

			foreach ($this->userdata as $key => $value)
			{
				if ($sqlupdate != '') $sqlupdate .= ', ';
				$sqlupdate .= $key . "='" . $value . "'";
			}

			$sql .= $sqlupdate . " WHERE userdata_user='" . $userid . "'";
			$res = $this->database->query($sql);
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
			$this->loadUserdata($this->session->getSessionVariable('user_userid'));
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
	 *
	 * @return boolean
	 */
	public function setUserdata($userdata, $value)
	{
		$this->debug->guard();

		if (!$this->userdataLoaded)
		{
			$this->loadUserdata($this->session->getSessionVariable('user_userid'));
		}

		if (isset($this->userdata[$userdata]))
		{
			$this->userdata[$userdata] = $value;
			$this->saveUserdata();

			$this->debug->unguard(true);
			return true;
		}

		$this->debug->write('Error setting userdata: Userdata (' . $userdata . ') does not exist and could not be set.', 'error');
		$this->messages->setMessage('Error setting userdata: Userdata (' . $userdata . ') does not exist and could not be set.', 'error');

		$this->debug->unguard(false);
		return false;
	}

}
?>
