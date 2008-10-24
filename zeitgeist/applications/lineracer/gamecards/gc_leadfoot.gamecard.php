<?php

class gc_leadfoot
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
		
		$currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'][0] *= 2;
		$currentGamestates['playerdata'][$currentGamestates['currentPlayer']]['vector'][1] *= 2;

		$this->objects->storeObject('currentGamestates', $currentGamestates, true);
    }

}

?>
