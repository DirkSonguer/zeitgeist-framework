<?php

class gc_dash
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
		
		$currentGamestates['playerdata'][$currentGamestates['move']['currentPlayer']]['vector'][0] *= 2;
		$currentGamestates['playerdata'][$currentGamestates['move']['currentPlayer']]['vector'][1] *= 2;

		$this->objects->storeObject('currentGamestates', $currentGamestates, true);
    }

}

?>
