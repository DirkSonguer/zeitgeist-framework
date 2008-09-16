<?php

defined('WELLNESSWELT_ACTIVE') or die();

class wwWellnessweltUser extends zgUserhandler
{
	private static $instance = false;
	
	/**
	 * Class constructor
	 */
	public function __construct()
	{

		parent::__construct();

		unset($this->database);
		$this->database = new zgDatabase();
		$this->database->connect(WW_DB_DBSERVER, WW_DB_USERNAME, WW_DB_USERPASS, WW_DB_DATABASE);
	}

	/**
	 * Initialize the singleton
	 *
	 * @return object
	 */
	public static function init()
	{
		if (self::$instance === false)
		{
			self::$instance = new wwWellnessweltUser();
		}

		return self::$instance;
	}
	
}

?>
