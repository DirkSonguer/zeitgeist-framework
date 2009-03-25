<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Shop class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST SHOP
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgshopCategoryfunctions
{
	protected $debug;
	protected $messages;
	protected $objects;
	protected $configuration;
	protected $database;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjects::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function getProductsInCategory($categoryid)
	{
		$this->debug->guard(true);


		$this->debug->unguard(true);
		return true;
	}


	public function getCategoryData($categoryid)
	{
		$this->debug->guard(true);


		$this->debug->unguard(true);
		return true;
	}


	public function getAllCategories()
	{
		$this->debug->guard(true);


		$this->debug->unguard(true);
		return true;
	}

}
?>