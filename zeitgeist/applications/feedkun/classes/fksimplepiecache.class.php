<?php

defined('FEEDKUN_ACTIVE') or die();

class fkSimplepieCache extends SimplePie_Cache
{
	private static $instance = false;

	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;

	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->user = zgUserhandler::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * Create a new SimplePie_Cache object
	 *
	 * @static
	 * @access public
	 */
	function create($location, $filename, $extension)
	{
		if (self::$instance === false)
		{
			self::$instance = new fkSimplepieCache();
		}

		return self::$instance;
	}



	function save($data)
	{
		$this->debug->guard();

		$this->debug->unguard(false);
		return true;
	}

	function load()
	{
		$this->debug->guard();

		$this->debug->unguard(false);
		return false;
	}

	function mtime()
	{
		$this->debug->guard();

		$this->debug->unguard(false);
		return false;
	}

	function touch()
	{
		$this->debug->guard();

		$this->debug->unguard(false);
		return false;
	}

	function unlink()
	{
		$this->debug->guard();

		$this->debug->unguard(false);
		return false;
	}
}

?>
