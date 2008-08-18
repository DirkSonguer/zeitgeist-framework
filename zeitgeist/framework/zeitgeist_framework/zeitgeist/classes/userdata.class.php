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
	public function getUserdata($userid)
	{
		$this->debug->guard();

		$userdataTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userdata');
		$sql = "SELECT * FROM " . $userdataTablename . " WHERE userdata_user = '" . $userid . "'";

		if ($res = $this->database->query($sql))
		{

			if ($this->database->numRows() > 0)
			{
				$ret = array();
				$row = $this->database->fetchArray($res);
				$ret = $row;
			}
			else
			{
				$this->debug->write('Possible problem getting userdata for a user: the user seems to habe no assigned data', 'warning');
				$this->messages->setMessage('Possible problem getting userdata for a user: the user seems to habe no assigned data', 'warning');

				$this->debug->unguard(false);
				return false;
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
	public function setUserdata($userid, $userdata)
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
