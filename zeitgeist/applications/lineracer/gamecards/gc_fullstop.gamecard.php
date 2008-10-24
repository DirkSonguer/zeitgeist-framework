<?php

class gc_fullstop
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
		if ( (!$currentGamestates = $this->objects->getObject('currentGamestates')) )
		{
			$this->debug->write('Could not handle race events: gamestates are not loaded', 'warning');
			$this->messages->setMessage('Could not handle race events: gamestates are not loaded', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'][0] = 0;
		$currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'][1] = 0;

		$this->objects->storeObject('currentGamestates', $currentGamestates, true);
    }

}

?>
