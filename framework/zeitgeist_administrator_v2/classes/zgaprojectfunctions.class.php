<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Handles application management
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST ADMINISTRATOR
 * @subpackage ZGA SETUPFUNCTIONS
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgaProjectfunctions
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}

	
	public function getAllProjects()
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM zga_projects";
	
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get project data from database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not get project data from database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$projects = array();
		while ($row = $this->database->fetchArray($res))
		{
			$projects[] = $row;
		}		

		$this->debug->unguard($projects);
		return $projects;
	}

	
	public function getActiveProject()
	{
		$this->debug->guard();
		
		$activeproject = $this->user->getUserdata('userdata_activeproject');
		if (!$activeproject)
		{
			$this->debug->write('Could not get active project: user does not have an active project', 'warning');
			$this->messages->setMessage('Could not get active project: user does not have an active project', 'warning');
			$this->debug->unguard(false);
			return false;
		}		
		
		$sql = "SELECT * FROM zga_projects WHERE project_id = '" . $activeproject . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get project data from database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not get project data from database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->database->fetchArray($res);

		$this->debug->unguard($ret);
		return $ret;
	}


	public function getProject($projectid)
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM zga_projects WHERE project_id = '" . $projectid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get project data from database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not get project data from database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->database->fetchArray($res);

		$this->debug->unguard($ret);
		return $ret;
	}


	public function saveProject($projectdata)
	{
		$this->debug->guard();

		if ( (empty($projectdata['project_name'])) || (empty($projectdata['project_dbserver'])) || 
		(empty($projectdata['project_dbuser'])) || (empty($projectdata['project_dbdatabase'])) )
		{
			$this->debug->write('Could not save project data: missing project information', 'warning');
			$this->messages->setMessage('Could not save project data: missing project information', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if (empty($projectdata['project_dbpassword']))
		{
			$projectdata['project_dbpassword'] = '';
		}
		
		if (empty($projectdata['project_id']))
		{
			$sql = "INSERT INTO zga_projects(project_name, project_dbserver, project_dbuser, project_dbpassword, project_dbdatabase)";
			$sql .= " VALUES('" . $projectdata['project_name'] . "', '" . $projectdata['project_dbserver'] . "', '" . $projectdata['project_dbuser'];
			$sql .= "', '" . $projectdata['project_dbpassword'] . "', '" . $projectdata['project_dbdatabase'] . "')";

			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Could not save project data: could not save project data to database', 'warning');
				$this->messages->setMessage('Could not save project data: could not save project data to database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		else
		{
			$sql = "UPDATE zga_projects SET project_name = '" . $projectdata['project_name'] . "', ";
			$sql .= "project_dbserver = '" . $projectdata['project_dbserver'] . "', ";
			$sql .= "project_dbuser = '" . $projectdata['project_dbuser'] . "', ";
			$sql .= "project_dbpassword = '" . $projectdata['project_dbpassword'] . "', ";
			$sql .= "project_dbdatabase = '" . $projectdata['project_dbdatabase'] . "' ";
			$sql .= "WHERE project_id = '" . $projectdata['project_id'] . "'";

			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Could not save project data: could not update project data in database', 'warning');
				$this->messages->setMessage('Could not save project data: could not update project data in database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}

		$this->debug->unguard(true);
		return true;
	}


	public function setActiveProject($projectid)
	{
		$this->debug->guard();
		
		$ret = $this->user->setUserdata('userdata_activeproject', $projectid, true);
		if (!$ret)
		{
			$this->debug->write('Could not set active project: could not write to userdata', 'warning');
			$this->messages->setMessage('Could not set active project: could not write to userdata', 'warning');
			$this->debug->unguard(false);
			return false;
		}		
		
		$this->debug->unguard(true);
		return true;
	}


	public function deleteProject($projectid)
	{
		$this->debug->guard();
		
		$activeproject = $this->user->getUserdata('userdata_activeproject');
		if ( (!$activeproject) || ($projectid == $activeproject) )
		{
			$this->debug->write('Could not delete project: project might be active', 'warning');
			$this->messages->setMessage('Could not delete project: project might be active', 'warning');
			$this->debug->unguard(false);
			return false;
		}		
		
		$sql = "DELETE FROM zga_projects WHERE project_id = '" . $projectid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not delete project data from database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not delete project data from database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}

}
?>
