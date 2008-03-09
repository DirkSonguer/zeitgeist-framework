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

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgShop::init();
 */
class zgShop
{
	protected $debug;
	protected $messages;
	protected $objects;
	protected $configuration;
	protected $database;

	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjectcache::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function addToCart($productid, $quantity=1)
	{
		$this->debug->guard(true);


		$this->debug->unguard(true);
		return true;
	}


	public function deleteFromCart($productid, $quantity=1)
	{
		$this->debug->guard(true);


		$this->debug->unguard(true);
		return true;
	}


	public function getProductData($productid)
	{
		$this->debug->guard(true);


		$this->debug->unguard(true);
		return true;
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