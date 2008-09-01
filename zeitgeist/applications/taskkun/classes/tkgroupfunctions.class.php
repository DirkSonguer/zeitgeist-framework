<?php

defined('TASKKUN_ACTIVE') or die();

class tkGroupfunctions
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * updates a group with given data
	 *
	 * instance-safe!
	 *
	 * @param array $groupdata array with the group data
	 *
	 * @return boolean
	 */
	public function updateGroup($groupdata=array())
	{
		$this->debug->guard();

		if ( (!is_array($groupdata)) || (empty($groupdata['group_description'])) || (empty($groupdata['group_name']))  || (empty($groupdata['group_id'])))
		{
			$this->debug->write('Problem updating group: given array does not contain valid groupdata', 'warning');
			$this->messages->setMessage('Problem updating group: given array does not contain valid groupdata', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$userfunctions = new tkUserfunctions();

		$sql = "UPDATE groups SET group_name='" . $groupdata['group_name'] . "', group_description='" . $groupdata['group_description'] . "' ";
		$sql .= "WHERE group_id='" . $groupdata['group_id'] . "' AND group_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem updating group: could not write groupdata to database', 'warning');
			$this->messages->setMessage('Problem updating group: could not write groupdata to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * adds a group with given group data
	 *
	 * instance-safe!
	 *
	 * @param array $groupdata array with group data
	 *
	 * @return return
	 */
	public function addGroup($groupdata=array())
	{
		$this->debug->guard();
		
		if ( (!is_array($groupdata)) || (empty($groupdata['group_description'])) || (empty($groupdata['group_name'])) )
		{
			$this->debug->write('Problem creating group: given array does not contain valid groupdata', 'warning');
			$this->messages->setMessage('Problem creating group: given array does not contain valid groupdata', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$userfunctions = new tkUserfunctions();

		$sql = "INSERT INTO groups(group_name, group_description, group_instance) VALUES('" . $groupdata['group_name'] . "', ";
		$sql .= "'" . $groupdata['group_description'] . "', '" . $userfunctions->getUserInstance($this->user->getUserID()) . "')";

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem creating group: could not write groupdata to database', 'warning');
			$this->messages->setMessage('Problem creating group: could not write groupdata to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * deletes a group with a given id
	 *
	 * instance-safe!
	 *
	 * @param integer $groupid id of the group to be deleted
	 *
	 * @return boolean
	 */
	public function deleteGroup($groupid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT group_id FROM groups WHERE group_id='" . $groupid . "' AND group_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		$numTasks = $this->database->numRows($res);

		if ($numTasks == 0)
		{
			$this->debug->write('The task is out of bounds of the instance', 'warning');
			$this->messages->setMessage('The task is out of bounds of the instance', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM groups WHERE group_id='" . $groupid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting group: ' . $groupid, 'warning');
			$this->messages->setMessage('Problem deleting group: ' . $groupid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM users_to_groups WHERE usergroup_group='" . $groupid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting users to group: ' . $groupid, 'warning');
			$this->messages->setMessage('Problem users to group: ' . $groupid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * gets data of a given group
	 *
	 * instance-safe!
	 *
	 * @param integer $groupid
	 *
	 * @return boolean
	 */
	public function getGroupdata($groupid)
	{
		$this->debug->guard();

		$userfunctions = new tkUserfunctions();

		$sql = "SELECT * FROM groups WHERE group_id='" . $groupid . "' AND group_instance='" . $userfunctions->getUserInstance($this->user->getUserID()) . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting data for group: ' . $groupid, 'warning');
			$this->messages->setMessage('Problem getting data for group: ' . $groupid, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$row = $this->database->fetchArray($res);

		$this->debug->unguard($row);
		return $row;
	}


	/**
	 * gets all groups for the current user
	 *
	 * instance-safe!
	 *
	 * @return array
	 */
	public function getGroupsForUser()
	{
		$this->debug->guard();

		$sql = "SELECT g.* FROM groups g LEFT JOIN users_to_groups u2g ON g.group_id = u2g.usergroup_group ";
		$sql .= "WHERE u2g.usergroup_user='" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting groups from database', 'warning');
			$this->messages->setMessage('Problem getting groups from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$groups = array();
		while($row = $this->database->fetchArray($res))
		{
			$groups[] = $row;
		}

		$this->debug->unguard($groups);
		return $groups;
	}


	/**
	 * gets the number of all groups for the current user
	 *
	 * instance-safe!
	 *
	 * @return array
	 */
	public function getNumberofGroupsForUser()
	{
		$this->debug->guard();

		$sql = "SELECT g.* FROM groups g LEFT JOIN users_to_groups u2g ON g.group_id = u2g.usergroup_group ";
		$sql .= "WHERE u2g.usergroup_user='" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting groups from database', 'warning');
			$this->messages->setMessage('Problem getting groups from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$groupcount = $this->database->numRows($res);

		$this->debug->unguard($groupcount);
		return $groupcount;
	}
}
?>