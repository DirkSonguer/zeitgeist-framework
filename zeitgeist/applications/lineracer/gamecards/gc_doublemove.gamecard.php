<?php

class gc_doublemove
{
	protected $objects;
	protected $configuration;

	public function __construct()
	{
		$this->objects = zgObjectcache::init();
		$this->configuration = zgConfiguration::init();
	}
	
	function execute()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
		
		echo "hello!";
    }

}

?>
