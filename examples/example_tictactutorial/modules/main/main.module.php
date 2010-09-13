<?php

defined('TICTACTUTORIAL_ACTIVE') or die();

class main
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function index($parameters=array())
	{
		$this->debug->guard();

		// create a new template object
		$tpl = new zgTemplate();

		// load the template file
		// we're using the template path defined in the application
		// configuration (/configuration(application.ini), loaded
		// in the index.php (line 54)
		// note that the name is static in this example it could
		// be also defined in the configuration (either for the
		// application or module)
		// the load method loads the content of the template file
		// into the object.
		$tpl->load($this->configuration->getConfiguration('application', 'application', 'templatepath') . '/main_index.tpl.html');

		// this shows the final template in the browser
		// all template commands and control codes will be stripped
		// before output
		$tpl->show();

		$this->debug->unguard(true);
		return true;
	}
}
?>
