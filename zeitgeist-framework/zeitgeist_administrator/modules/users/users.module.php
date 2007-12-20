<?php


defined('ZGADMIN_ACTIVE') or die();

class users
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
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_index'));		

		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}
	
	
	public function edituser($parameters=array())
	{
		$this->debug->guard();
		
		$currentId = 1;
		if (!empty($parameters['id'])) $currentId = $parameters['id'];
		if (!empty($parameters['user_id'])) $currentId = $parameters['user_id'];
		
		$tpl = new zgaTemplate();
		$tpl->load($this->configuration->getConfiguration('users', 'templates', 'users_edituser'));		

		$sqlUser = "SELECT u.*, ur.* FROM users AS u, userroles_to_users AS uru LEFT JOIN userroles ur ON ur.userrole_id = uru.userroleuser_userrole WHERE u.user_id = uru.userroleuser_user AND u.user_id = '" . $currentId . "'";
		$resUser = $this->managedDatabase->query($sqlUser);
	    $rowUser = $this->managedDatabase->fetchArray($resUser);

		$sqlUserroles = "SELECT * FROM userroles";
		$resUserroles = $this->managedDatabase->query($sqlUserroles);
	    while ($rowUserroles = $this->managedDatabase->fetchArray($resUserroles))
	    {
	    	$tpl->assign('userrolevalue', $rowUserroles['userrole_id']);
	    	$tpl->assign('userroletext', $rowUserroles['userrole_name']);
	    	if ($rowUserroles['userrole_id'] == $rowUser['userrole_id'])
	    	{
	    		$tpl->assign('userrolecheck', 'checked="checked"');
	    	}
	    	else
	    	{
	    		$tpl->assign('userrolecheck', '');
	    	}
	    	
	    	$tpl->insertBlock('userroleloop');
	    }

		$sqlUserdataTable = "EXPLAIN userdata";
		$resUserdataTable = $this->managedDatabase->query($sqlUserdataTable);
		while ($rowUserdataTable = $this->managedDatabase->fetchArray($resUserdataTable))
		{
			$userdataTable[$rowUserdataTable['Field']] = $rowUserdataTable['Type'];
		}
	    
		$sqlUserdata = "SELECT * FROM userdata";
		$resUserdata = $this->managedDatabase->query($sqlUserdata);
	    $rowUserdata = $this->managedDatabase->fetchArray($resUserdata);

	    foreach ($rowUserdata as $dataKey => $dataValue)
	    {
	    	if ($userdataTable[$dataKey] == 'text')
	    	{
		    	$tpl->assign('userdatakey', $dataKey);
		    	$formData = '<textarea name="userdata[' . $dataKey . ']" class="formtext" style="width:450px;">' . $dataValue . '</textarea>';
		    	$tpl->assign('userdatavalue', $formData);
	    	}
	    	elseif ($userdataTable[$dataKey] == 'tinyint(1)')
	    	{
	    		$checked = '';
	    		if ($dataValue == '1') $checked = 'checked="checked" ';
		    	$tpl->assign('userdatakey', $dataKey);
		    	$formData = '<input type="checkbox" name="userdata[' . $dataKey . ']" ' . $checked . 'value="' . $dataValue . '">';
		    	$tpl->assign('userdatavalue', $formData);
	    	}
	    	elseif ($userdataTable[$dataKey] == 'date')
	    	{
		    	$tpl->assign('userdatakey', $dataKey);
		    	$formData = '<input type="text" maxlength="10" name="userdata[' . $dataKey . ']" value="' . $dataValue . '" class="formtext" style="width:450px;" />';
		    	$tpl->assign('userdatavalue', $formData);
	    	}
	    	else
	    	{
	    		if (strpos($userdataTable[$dataKey], '(') !== false)
	    		{
		    		$typeLength = substr($userdataTable[$dataKey], strpos($userdataTable[$dataKey], '(')+1, -1);
	    		}
	    		else
	    		{
	    			$typeLength = '30';
	    		}

		    	$tpl->assign('userdatakey', $dataKey);
		    	$formData = '<input type="text" maxlength="' . $typeLength . '" name="userdata[' . $dataKey . ']" value="' . $dataValue . '" class="formtext" style="width:450px;" />';
		    	$tpl->assign('userdatavalue', $formData);
	    	}
	    	
	    	$tpl->insertBlock('userdataloop');
	    }
	    
	    $tpl->assignDataset($rowUser);
		$tpl->show();
		
		$this->debug->unguard(true);
		return true;
	}
}
?>