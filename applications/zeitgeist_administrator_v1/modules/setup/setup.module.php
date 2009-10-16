<?php


defined('ZGADMIN_ACTIVE') or die();

class setup
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $managedDatabase;
	protected $configuration;
	protected $user;
	protected $objects;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjectcache::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();

		$mdb_server = $this->configuration->getConfiguration('administrator', 'databases', 'manageddb_server');
		$mdb_username = $this->configuration->getConfiguration('administrator', 'databases', 'manageddb_username');
		$mdb_userpw = $this->configuration->getConfiguration('administrator', 'databases', 'manageddb_userpw');
		$mdb_database = $this->configuration->getConfiguration('administrator', 'databases', 'manageddb_database');
		$this->managedDatabase = new zgDatabase();
		$this->managedDatabase->connect($mdb_server, $mdb_username, $mdb_userpw, $mdb_database);
	}
	
	
	public function index($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_index'));		

		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}
		
	
	public function managemodules($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_managemodules'));

		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}

	
	public function addmodule($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_addmodule'));

		if (!empty($parameters['submit']))
		{
			if ( (!empty($parameters['module_name'])) && (!empty($parameters['module_description'])) )
			{
				$sql = "SELECT * FROM modules WHERE module_name = '" . $parameters['module_name'] . "'";
				$res = $this->managedDatabase->query($sql);
				if ($this->managedDatabase->numRows($res) > 0)
				{
					$this->messages->setMessage('A module with this name already exists in the database. Please choose another name.', 'userwarning');
				}
				else
				{
					$sql = 'INSERT INTO modules(module_name, module_description, module_active) VALUES(';
					
					if (empty($parameters['module_active']))
					{
						$parameters['module_active'] = 0;
					}
					else
					{
						$parameters['module_active'] = 1;
					}
					
					$sql .= "'" . $parameters['module_name'] . "', ";
					$sql .= "'" . $parameters['module_description'] . "', ";
					$sql .= "'" . $parameters['module_active'] . "')";
	
					$res = $this->managedDatabase->query($sql);
					if (!$res)
					{
						$this->messages->setMessage('An error occured while saving the module data. Please contact an administrator.', 'usererror');
					}
					else
					{
						$this->debug->unguard(true);
						$tpl->redirect($tpl->createLink('setup', 'managemodules'));
					}
				}
			}
			else
			{
				$this->messages->setMessage('Please fill out all required fields (name and description).', 'userwarning');
			}
			
		}
		
	    $tpl->assignDataset($parameters);
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}
		
	
	public function editmodule($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_editmodule'));
		
		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['module_id'])) $currentId = $parameters['module_id'];

		if (!empty($parameters['submit']))
		{
			if ( (!empty($parameters['module_name'])) && (!empty($parameters['module_description'])) )
			{
				$sql = 'UPDATE modules SET ';
				
				if (empty($parameters['module_active']))
				{
					$parameters['module_active'] = 0;
				}
				else
				{
					$parameters['module_active'] = 1;
				}
				
				foreach($parameters as $key => $value)
				{
					if (strpos($key, 'module_') !== false)
					{
						$sql .= $key . "='" . $value . "', ";
					}
				}
				
				$sql = substr($sql, 0, -2);
				$sql = $sql . " WHERE module_id = '" . $currentId . "'";

				$res = $this->managedDatabase->query($sql);
				if (!$res)
				{
					$this->messages->setMessage('An error occured while saving the module data. Please contact an administrator.', 'usererror');
				}
				else
				{
					$this->messages->setMessage('Module data has been changed', 'usermessage');
				}
			}
			else
			{
				$this->messages->setMessage('Please fill out all required fields (name and description).', 'userwarning');
			}
			
		}
		
		$sql = "SELECT * FROM modules WHERE module_id='" . $currentId . "'";
		$res = $this->managedDatabase->query($sql);
	    $row = $this->managedDatabase->fetchArray($res);
	    
	    if ($row['module_active'] == '1')
	    {
	    	$row['module_active'] = 'checked="checked"';
	    }
	    
	    $tpl->assignDataset($row);
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}


	public function deletemodule($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		if (!empty($parameters['id']))
		{
			$sql = "DELETE FROM modules WHERE module_id='" . $parameters['id'] . "'";
			$res = $this->managedDatabase->query($sql);
		}
		
		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('setup', 'managemodules'));
				
		$this->debug->unguard(true);
		return true;
	}

	
	public function manageactions($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_manageactions'));

		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function editaction($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_editaction'));
		
		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['action_id'])) $currentId = $parameters['action_id'];

		if (!empty($parameters['submit']))
		{
			if ( (!empty($parameters['action_name'])) && (!empty($parameters['action_description'])) )
			{
				$sql = 'UPDATE actions SET ';
				
				if (empty($parameters['action_requiresuserright']))
				{
					$parameters['action_requiresuserright'] = 0;
				}
				else
				{
					$parameters['action_requiresuserright'] = 1;
				}
				
				foreach($parameters as $key => $value)
				{
					if (strpos($key, 'action_') !== false)
					{
						$sql .= $key . "='" . $value . "', ";
					}
				}
				
				$sql = substr($sql, 0, -2);
				$sql = $sql . " WHERE action_id = '" . $currentId . "'";

				$res = $this->managedDatabase->query($sql);
				if (!$res)
				{
					$this->messages->setMessage('An error occured while saving the action data. Please contact an administrator.', 'usererror');
				}
				else
				{
					$this->messages->setMessage('Action data has been changed', 'usermessage');
				}
			}
			else
			{
				$this->messages->setMessage('Please fill out all required fields (name and description).', 'userwarning');
			}
		}
		
		$sql = "SELECT a.*, m.module_name FROM actions a LEFT JOIN modules m ON a.action_module = m.module_id WHERE a.action_id='" . $currentId . "'";
		$res = $this->managedDatabase->query($sql);
	    $row = $this->managedDatabase->fetchArray($res);
	    
		$sqlModules = "SELECT * FROM modules ORDER BY module_name";
		$resModules = $this->managedDatabase->query($sqlModules);
	    while ($rowModules = $this->managedDatabase->fetchArray($resModules))
	    {
	    	$tpl->assign('modulevalue', $rowModules['module_id']);
	    	$tpl->assign('moduletext', $rowModules['module_name']);
	    	if ($rowModules['module_id'] == $row['action_module'])
	    	{
	    		$tpl->assign('modulecheck', 'selected="selected"');
	    	}
	    	else
	    	{
	    		$tpl->assign('modulecheck', '');
	    	}
	    	
	    	$tpl->insertBlock('moduleloop');
	    }
	    
	    if ($row['action_requiresuserright'] == '1')
	    {
	    	$row['action_requiresuserright'] = 'checked="checked"';
	    }
	    
	    $tpl->assignDataset($row);
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}


	public function addaction($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_addaction'));

		if (!empty($parameters['submit']))
		{
			if ( (!empty($parameters['action_name'])) && (!empty($parameters['action_description'])) )
			{
				$sql = "SELECT * FROM actions WHERE action_name = '" . $parameters['action_name'] . "' AND action_module = '" . $parameters['action_module'] . "'";
				$res = $this->managedDatabase->query($sql);
				if ($this->managedDatabase->numRows($res) > 0)
				{
					$this->messages->setMessage('An action with this name already exists for this module. Please choose another name.', 'userwarning');
				}
				else
				{
					$sql = 'INSERT INTO actions(action_name, action_description, action_module, action_requiresuserright) VALUES(';
					
					if (empty($parameters['action_requiresuserright']))
					{
						$parameters['action_requiresuserright'] = 0;
					}
					else
					{
						$parameters['action_requiresuserright'] = 1;
					}
					
					$sql .= "'" . $parameters['action_name'] . "', ";
					$sql .= "'" . $parameters['action_description'] . "', ";
					$sql .= "'" . $parameters['action_module'] . "', ";
					$sql .= "'" . $parameters['action_requiresuserright'] . "')";
	
					$res = $this->managedDatabase->query($sql);
					if (!$res)
					{
						$this->messages->setMessage('An error occured while saving the action data. Please contact an administrator.', 'usererror');
					}
					else
					{
						$this->debug->unguard(true);
						$tpl->redirect($tpl->createLink('setup', 'manageactions'));
					}
				}
			}
			else
			{
				$this->messages->setMessage('Please fill out all required fields (name and description).', 'userwarning');
			}
			
		}
		
		$sqlModules = "SELECT * FROM modules ORDER BY module_name";
		$resModules = $this->managedDatabase->query($sqlModules);
	    while ($rowModules = $this->managedDatabase->fetchArray($resModules))
	    {
	    	$tpl->assign('modulevalue', $rowModules['module_id']);
	    	$tpl->assign('moduletext', $rowModules['module_name']);
	    	if ( (!empty($parameters['action_module'])) && ($rowModules['module_id'] == $parameters['action_module']) )
	    	{
	    		$tpl->assign('modulecheck', 'selected="selected"');
	    	}
	    	else
	    	{
	    		$tpl->assign('modulecheck', '');
	    	}
	    	
	    	$tpl->insertBlock('moduleloop');
	    }

	    $tpl->assignDataset($parameters);
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}
		
	
	public function deleteaction($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		if (!empty($parameters['id']))
		{
			$sql = "DELETE FROM actions WHERE action_id='" . $parameters['id'] . "'";
			$res = $this->managedDatabase->query($sql);
		}
		
		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('setup', 'manageactions'));
				
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function manageuserroles($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_manageuserroles'));

		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function edituserrole($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_edituserrole'));
		
		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['userrole_id'])) $currentId = $parameters['userrole_id'];

		if (!empty($parameters['submit']))
		{
			if ( (!empty($parameters['userrole_name'])) && (!empty($parameters['userrole_description'])) )
			{
				$sql = 'UPDATE userroles SET ';
				
				foreach($parameters as $key => $value)
				{
					if (strpos($key, 'userrole_') !== false)
					{
						$sql .= $key . "='" . $value . "', ";
					}
				}
				
				$sql = substr($sql, 0, -2);
				$sql = $sql . " WHERE userrole_id = '" . $currentId . "'";

				$res = $this->managedDatabase->query($sql);
				if (!$res)
				{
					$this->messages->setMessage('An error occured while saving the action data. Please contact an administrator.', 'usererror');
				}
				else
				{
					$this->messages->setMessage('Action data has been changed', 'usermessage');
				}
			}
			else
			{
				$this->messages->setMessage('Please fill out all required fields (name and description).', 'userwarning');
			}
			
			if (!empty($parameters['userrights']))
			{
				$sql = "DELETE FROM userroles_to_actions WHERE userroleaction_userrole='" . $currentId . "'";
				$res = $this->managedDatabase->query($sql);
				
				foreach ($parameters['userrights'] as $key => $value)
				{
					$sql = "INSERT INTO userroles_to_actions(userroleaction_userrole, userroleaction_action) VALUES('" . $currentId . "', '" . $key . "')";
					$res = $this->managedDatabase->query($sql);
				}
			}
		}
		
		$sql = "SELECT * FROM userroles WHERE userrole_id='" . $currentId . "'";
		$res = $this->managedDatabase->query($sql);
	    $row = $this->managedDatabase->fetchArray($res);

	    $userrole = array();
		$sqlUserrole = "SELECT uta.* FROM userroles_to_actions uta WHERE uta.userroleaction_userrole = '" . $currentId . "'";
		$resUserrole = $this->managedDatabase->query($sqlUserrole);
	    while ($rowUserrole = $this->managedDatabase->fetchArray($resUserrole))
	    {
	    	$userrole[$rowUserrole['userroleaction_action']] = $rowUserrole['userroleaction_userrole'];
	    }

		$sqlUserrights = "SELECT a.* FROM actions a WHERE a.action_requiresuserright = '1'";
		$resUserrights = $this->managedDatabase->query($sqlUserrights);
	    while ($rowUserrights = $this->managedDatabase->fetchArray($resUserrights))
	    {
	    	$tpl->assign('right_id', $rowUserrights['action_id']);
	    	$tpl->assign('right_name', $rowUserrights['action_name']);
	    	$tpl->assign('right_description', $rowUserrights['action_description']);
	    	if (!empty($userrole[$rowUserrights['action_id']]))
	    	{
	    		$tpl->assign('right_active', 'checked="checked"');
	    	}
	    	else
	    	{
	    		$tpl->assign('right_active', '');
	    	}

	    	$tpl->insertBlock('rightsloop');
	    }
	    
	    $tpl->assignDataset($row);
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}
	

	public function adduserrole($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('setup', 'templates', 'setup_adduserrole'));

		if (!empty($parameters['submit']))
		{
			if ( (!empty($parameters['userrole_name'])) && (!empty($parameters['userrole_description'])) )
			{
				$sql = "SELECT * FROM userroles WHERE userrole_name = '" . $parameters['userrole_name'] . "'";
				$res = $this->managedDatabase->query($sql);
				if ($this->managedDatabase->numRows($res) > 0)
				{
					$this->messages->setMessage('An userrole with this name already exists in the database. Please choose another name.', 'userwarning');
				}
				else
				{
					$sql = 'INSERT INTO userroles(userrole_name, userrole_description) VALUES(';
										
					$sql .= "'" . $parameters['userrole_name'] . "', ";
					$sql .= "'" . $parameters['userrole_description'] . "')";
	
					$res = $this->managedDatabase->query($sql);
					if (!$res)
					{
						$this->messages->setMessage('An error occured while saving the action data. Please contact an administrator.', 'usererror');
					}
					else
					{
						if (!empty($parameters['userrights']))
						{
							$currentId = $this->managedDatabase->insertId();
							
							foreach ($parameters['userrights'] as $key => $value)
							{
								$sql = "INSERT INTO userroles_to_actions(userroleaction_userrole, userroleaction_action) VALUES('" . $currentId . "', '" . $key . "')";
								$res = $this->managedDatabase->query($sql);
							}
						}
						
						$this->debug->unguard(true);
						$tpl->redirect($tpl->createLink('setup', 'manageuserroles'));
					}
				}
			}
			else
			{
				$this->messages->setMessage('Please fill out all required fields (name and description).', 'userwarning');
			}
		}

		$sqlUserrights = "SELECT a.* FROM actions a WHERE a.action_requiresuserright = '1'";
		$resUserrights = $this->managedDatabase->query($sqlUserrights);
	    while ($rowUserrights = $this->managedDatabase->fetchArray($resUserrights))
	    {
	    	$tpl->assign('right_id', $rowUserrights['action_id']);
	    	$tpl->assign('right_name', $rowUserrights['action_name']);
	    	$tpl->assign('right_description', $rowUserrights['action_description']);

	    	$tpl->insertBlock('rightsloop');
	    }
	    		
	    $tpl->assignDataset($parameters);
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}

	
	public function deleteuserrole($parameters=array())
	{
		$this->debug->guard();
		
		$tpl = new zgaTemplate();
		if (!empty($parameters['id']))
		{
			$sql = "DELETE FROM userroles WHERE userrole_id='" . $parameters['id'] . "'";
			$res = $this->managedDatabase->query($sql);
		}
		
		$this->debug->unguard(true);
		$tpl->redirect($tpl->createLink('setup', 'manageuserroles'));
				
		$this->debug->unguard(true);
		return true;
	}
	
}
?>