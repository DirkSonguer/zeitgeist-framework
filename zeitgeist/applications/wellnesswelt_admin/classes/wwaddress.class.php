<?php

defined('WELLNESSWELT_ACTIVE') or die();

class wwAddress
{
	private $user;
	private $basepath;
	private $templatepath;
	private $database;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->user = zgUserhandler::init();
		
		parent::__construct();

		$this->database = new zgDatabase();
		$this->database->connect(WW_DB_DBSERVER, WW_DB_USERNAME, WW_DB_USERPASS, WW_DB_DATABASE);
	}



}

?>
