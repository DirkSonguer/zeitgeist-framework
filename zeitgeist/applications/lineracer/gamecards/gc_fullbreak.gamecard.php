<?php

class gc_fullbreak
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
		
		$currentGamestates['playerdata'][$currentGamestates['round']['currentPlayer']]['vector'][0] = 0;
		$currentGamestates['playerdata'][$currentGamestates['round']['currentPlayer']]['vector'][1] = 0;

		$this->objects->storeObject('currentGamestates', $currentGamestates, true);
    }

}

?>
