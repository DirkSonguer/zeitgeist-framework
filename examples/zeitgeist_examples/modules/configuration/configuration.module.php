<?php

defined('ZGEXAMPLES_ACTIVE') or die();

class configuration
{
	public $debug;
	public $configuration;

	public function __construct()
	{
		$this->debug = zgDebug::init();

		// The configuration system is a singleton as we only need one global
		// configuration system to hold all configurations

		// You can bind it by calling the init() method
		$this->configuration = zgConfiguration::init();
	}


	// This is the main action for this module
	public function index($parameters=array())
	{
		$this->debug->guard();

		// Load the example configuration
		// The configuration system uses pretty standard ini files but with some neat twists
		// Take a look at the file to see the content
		$this->configuration->loadConfiguration('config_example', './_additional_material/example_configuration.ini');

		// Gets a dedicated configuration variable by addressing it
		// Parameters are: CONFIGURATIONHANDLE, BLOCK, VARIABLE
		$var1 = $this->configuration->getConfiguration('config_example', 'block', 'var1');
		$var2 = $this->configuration->getConfiguration('config_example', 'block', 'var2');
		echo '<p>'.$var1.', '.$var2.'</p>';

		// Use only the block name to get the complete block contents
		$configArray = $this->configuration->getConfiguration('config_example', 'block');
		echo '<p>Just the block information:</p>';
		var_dump($configArray);

		// Use just the configuration handle to get the complete
		// configuration content
		$configArray = $this->configuration->getConfiguration('config_example');
		echo '<p>Complete configuration:</p>';
		var_dump($configArray);

		// Each module has an associated configuration file with the name
		// of the module. It can be accessed like any other configuration
		$configArray = $this->configuration->getConfiguration('configuration');
		echo '<p>Module configuration:</p>';
		var_dump($configArray);

		// Note that the ./_additional_files/example_configuration.ini contains
		// a dynamic link to the module configuration. Dynamic links are just
		// references to another configuration item.
		// The referenced item has to be loaded previously of course

		$this->debug->unguard(true);
		return true;
	}

}
?>
