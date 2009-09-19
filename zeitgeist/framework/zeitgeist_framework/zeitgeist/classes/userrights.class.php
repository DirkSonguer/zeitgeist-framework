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

class zgUserrights
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
	 * Returns all userrights for a given user
	 *
	 * @param integer $userid id of the user
	 *
	 * @return array
	 */
	public function loadUserrights($userid)
	{
		$this->debug->guard();

		$userrightsTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userrights');
		$sql = "SELECT * FROM " . $userrightsTablename . " WHERE userright_user = '" . $userid . "'";

		if ($res = $this->database->query($sql))
		{
			$ret = array();
			while ($row = $this->database->fetchArray($res))
			{
				$ret[$row['userright_action']] = true;
			}

			$rolefunctions = new zgUserroles();
			$roles = $rolefunctions->loadUserroles($userid);
			if ((is_array($roles)) && (count($roles) > 0))
			{
				foreach ($roles as $roleid => $value)
				{
					$rights = $this->_getUserrightsForRoles($roleid);
					if ((is_array($rights)) && (count($rights) > 0))
					{
						$ret = $ret + $rights;
					}
				}
			}

			if (count($ret) < 1)
			{
				$this->debug->write('Possible problem getting userrights for a user: the user seems to have no assigned rights', 'warning');
				$this->messages->setMessage('Possible problem getting userrights for a user: the user seems to have no assigned rights', 'warning');
				$this->debug->unguard(false);
				return false;
			}

			$this->debug->unguard($ret);
			return $ret;
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
	 * Stores the according userrights to the database
	 *
	 * @param integer $userid id of the user
	 * @param array $userrights array containing all rights
	 *
	 * @return boolean
	 */
	public function saveUserrights($userid, $userrights)
	{
		$this->debug->guard();
		
		if ((!is_array($userrights)) || (count($userrights) < 1))
		{
			$this->debug->write('Problem setting the user rights: array not valid', 'warning');
			$this->messages->setMessage('Problem setting the user rights: array not valid', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$userrightsTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userrights');
		$sql = 'DELETE FROM ' . $userrightsTablename . " WHERE userright_user='" . $userid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem setting the user rights: could not clean up the rights table', 'warning');
			$this->messages->setMessage('Problem setting the user rights: could not clean up the rights table', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		foreach ($userrights as $key => $value)
		{
			$sql = 'INSERT INTO ' . $userrightsTablename . "(userright_action, userright_user) VALUES('" . $key . "', '" . $userid . "')";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Problem setting the user rights: could not insert the rights into the database', 'warning');
				$this->messages->setMessage('Problem setting the user rights: could not insert the rights into the database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}

		$this->debug->unguard(true);
		return true;
	}
	
	
	/**
	 * Loads all userrights for a given role
	 *
	 * @param integer $roleid id of the role
	 * 
	 * @return array
	 */
	protected function _getUserrightsForRoles($roleid)
	{
		$this->debug->guard();

		$rolestoactionsTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_userroles_to_actions');
		$sql = "SELECT * FROM " . $rolestoactionsTablename . " WHERE userroleaction_userrole = '" . $roleid . "'";

		if ($res = $this->database->query($sql))
		{
			$ret = array();
			while ($row = $this->database->fetchArray($res))
			{
				$ret[$row['userroleaction_action']] = true;
			}

			if (count($ret) == 0)
			{
				$this->debug->write('Possible problem getting the rights for the roles of a user: there seems to be no rights assigned with the roles', 'warning');
				$this->messages->setMessage('Possible problem getting the rights for the roles of a user: there seems to be no rights assigned with the roles', 'warning');
				$this->debug->unguard(false);
				return false;
			}

			$this->debug->unguard($ret);
			return $ret;
		}
		else
		{
			$this->debug->write('Problem getting userrole for a user: could not find the userrole', 'warning');
			$this->messages->setMessage('Problem getting userrole for a user: could not find the userrole', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(false);
		return false;
	}

}
?>
