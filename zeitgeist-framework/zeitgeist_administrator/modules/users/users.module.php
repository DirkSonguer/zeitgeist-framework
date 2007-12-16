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
	
}
?>