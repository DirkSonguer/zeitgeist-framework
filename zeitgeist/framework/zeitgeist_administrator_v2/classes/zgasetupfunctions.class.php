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

class zgaSetupfunctions
{
	protected $debug;
	protected $messages;
	protected $projectfunctions;
	protected $projectDatabase;
	protected $configuration;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->projectfunctions = new zgaProjectfunctions();
		$activeproject = $this->projectfunctions->getActiveProject();

		$this->projectDatabase = new zgDatabase();
		$this->projectDatabase->connect($activeproject['project_dbserver'], $activeproject['project_dbuser'], $activeproject['project_dbpassword'], $activeproject['project_dbdatabase'], false, true);
	}
	

	public function getAllModules()
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM modules m";
	
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get module data from project database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not get module data from project database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$modules = array();
		while ($row = $this->projectDatabase->fetchArray($res))
		{
			$modules[] = $row;
		}		

		$this->debug->unguard($modules);
		return $modules;
	}


	public function getModule($moduleid)
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM modules WHERE module_id = '" . $moduleid . "'";
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get module data from project database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not get module data from project database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->projectDatabase->fetchArray($res);

		$this->debug->unguard($ret);
		return $ret;
	}


	public function activateModule($moduleid)
	{
		$this->debug->guard();
		
		$sql = "UPDATE modules SET module_active = '1' WHERE module_id = '" . $moduleid . "'";
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not activate module: could not connect to database', 'warning');
			$this->messages->setMessage('Could not activate module: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function deactivateModule($moduleid)
	{
		$this->debug->guard();
		
		$sql = "UPDATE modules SET module_active = '0' WHERE module_id = '" . $moduleid . "'";
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not activate module: could not connect to database', 'warning');
			$this->messages->setMessage('Could not activate module: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function saveModule($moduledata)
	{
		$this->debug->guard();

		if ( (empty($moduledata['module_name'])) || (empty($moduledata['module_description']))  )
		{
			$this->debug->write('Could not save module data: missing module information', 'warning');
			$this->messages->setMessage('Could not save module data: missing module information', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if (!empty($moduledata['module_active']))
		{
			$moduledata['module_active'] = '1';
		}
		else
		{
			$moduledata['module_active'] = '0';
		}
		
		if (empty($moduledata['module_id']))
		{
			$sql = "INSERT INTO modules(module_name, module_description, module_active)";
			$sql .= " VALUES('" . $moduledata['module_name'] . "', '" . $moduledata['module_description'] . "', '" . $moduledata['module_active'] . "')";

			$res = $this->projectDatabase->query($sql);
			if (!$res)
			{
				$this->debug->write('Could not save module data: could not save module data to database', 'warning');
				$this->messages->setMessage('Could not save module data: could not save module data to database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		else
		{
			$sql = "UPDATE modules SET module_name = '" . $moduledata['module_name'] . "', ";
			$sql .= "module_description = '" . $moduledata['module_description'] . "', ";
			$sql .= "module_active = '" . $moduledata['module_active'] . "' ";
			$sql .= "WHERE module_id = '" . $moduledata['module_id'] . "' ";

			$res = $this->projectDatabase->query($sql);
			if (!$res)
			{
				$this->debug->write('Could not save module data: could not update module data in database', 'warning');
				$this->messages->setMessage('Could not save module data: could not update module data in database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}

		$this->debug->unguard(true);
		return true;
	}


	public function deleteModule($moduleid)
	{
		$this->debug->guard();

		$sql = "DELETE FROM modules WHERE module_id = '" . $moduleid . "'";
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not delete module data from project database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not get module data from project database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DELETE FROM actions WHERE action_module = '" . $moduleid . "'";
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not delete action data for module from project database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not delete action data for module from project database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}		

		$this->debug->unguard(true);
		return true;
	}
	
	
	public function getAllActions()
	{
		$this->debug->guard();
		
		$sql = "SELECT a.*, m.module_name FROM actions a ";
		$sql .= "LEFT JOIN modules m ON a.action_module = m.module_id";
	
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get action data from project database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not get action data from project database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$actions = array();
		while ($row = $this->projectDatabase->fetchArray($res))
		{
			$actions[] = $row;
		}		

		$this->debug->unguard($actions);
		return $actions;
	}
	
	
	public function getAllUserroles()
	{
		$this->debug->guard();
		
		$sql = "SELECT * FROM userroles ur ";
	
		$res = $this->projectDatabase->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get userrole data from project database: could not connect to database', 'warning');
			$this->messages->setMessage('Could not get userrole data from project database: could not connect to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$userroles = array();
		while ($row = $this->projectDatabase->fetchArray($res))
		{
			$userroles[] = $row;
		}		

		$this->debug->unguard($userroles);
		return $userroles;
	}
		
}
?>
