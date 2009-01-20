<?php

class gc_doubleradius
{
	protected $objects;
	protected $configuration;

	public function __construct()
	{
		$this->objects = zgObjects::init();
		$this->configuration = zgConfiguration::init();
	}
	
	function execute()
	{
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not handle race events: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not handle race events: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$currentGamestates['currentRadius'] *= 2;
		$currentGamestates['currentRadius'] *= 2;

		$this->objects->storeObject('currentGamestates', $currentGamestates, true);
    }

}

?>