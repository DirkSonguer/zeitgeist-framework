<?php


defined('ZGEXAMPLES_ACTIVE') or die();

class main
{
	public $debug;
	public $messages;
	public $database;
	public $configuration;

	public function __construct()
	{
		$this->debug = zgDebug::init();
	}
	

	// this is the main action for this module - the home page	
	public function index($parameters=array())
	{
		/*
		 * Welcome to Zeitgeist, a PHP based multi purpose framework for 
		 * web applications.
		 * 
		 * Following are examples to show you how to use Zeitgeist. 
		 * This collection serves as tutorial as well as reference manual 
		 * and style guide.
		 * 
		 * The idea is that you read through the source code to understand
		 * how Zeitgeist works.
		 * 
		 * However, keep in mind, that:
		 * 
		 * 1. This is not a real application but merely a collection of 
		 * examples and tutorials.
		 * 
		 * 2. Many functionalities are used in a dumbed down way.
		 * These are examples after all. However they try to present a good 
		 * approach of how to use framework module.
		 * 
		 * 3. If you want to see a more "real world" application, take a look 
		 * at the Zeitgeist Administrator application.
		 * 
		*/

		echo '<h1>Zeitgeist Examples</h1>';
		echo '<p>Take a look at the source.</p>';

		echo '<ul>';
		echo '<li><a href="./index.php?module=debug">Debug examples</a></li>';
		echo '<li><a href="./index.php?module=messages">Message examples</a></li>';
		echo '<li><a href="./index.php?module=configuration">Configuration examples</a></li>';
		echo '<li><a href="./index.php?module=templates">Template examples</a></li>';
		echo '<li><a href="./index.php?module=dataserver">Dataserver examples</a></li>';
		echo '</ul>';
	
		return true;
	}

}
?>
