<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Userdata class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST USERHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgUserdata
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;

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
	}


	/**
	 * Loads all userdata for a given user
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
			$ret = array();
			if ($this->database->numRows($res) > 0)
			{
				$row = $this->database->fetchArray($res);
				$ret = $row;
			}
			else
			{
				$sql = "EXPLAIN " . $userdataTablename;
				$res = $this->database->query($sql);
				
				while($row = $this->database->fetchArray($res))
				{
					$ret[$row['Field']] = '';
				}
				
				$this->debug->write('The user seems to habe no assigned data. Userdata returned empty.', 'message');
				$this->messages->setMessage('The user seems to habe no assigned data. Userdata returned empty.', 'message');
			}

			$this->debug->unguard($ret);
			return $ret;
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
	 * Save the according userdata to the database
	 *
	 * @param integer $userid id of the user
	 * @param array $userdata array containing all user data
	 *
	 * @return boolean
	 */
	public function saveUserdata($userid, $userdata)
	{
		$this->debug->guard();

		if ((is_array($userdata)) && (count($userdata) < 1))
		{
			$this->debug->write('Problem setting the user data: array not valid', 'warning');
			$this->messages->setMessage('Problem setting the user data: array not valid', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$userdataTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userdata');
		$sql = 'DELETE FROM ' . $userdataTablename . " WHERE userdata_user='" . $userid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem setting the user data: could not clean up the data table', 'warning');
			$this->messages->setMessage('Problem setting the user data: could not clean up the data table', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sqlkeys = '';
		$sqlvalues = '';
		foreach ($userdata as $key => $value)
		{
			if (($key != 'userdata_timestamp') && ($key != 'userdata_user'))
			{
				if ($sqlkeys != '') $sqlkeys .= ', ';
				$sqlkeys .= $key;
				if ($sqlvalues != '') $sqlvalues .= ', ';
				$sqlvalues .= "'" . $value . "'";
			}
		}

		$sql = 'INSERT INTO ' . $userdataTablename . '(userdata_user, ' . $sqlkeys . ') VALUES(' . $userid . ',' . $sqlvalues . ')';
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem setting the user data: could not write the data', 'warning');
			$this->messages->setMessage('Problem setting the user data: could not write the data', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}

}
?>
