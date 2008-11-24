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

class zgsCartfunctions
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
		$this->objects = zgObjects::init();
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


	public function clearCart()
	{
		$this->debug->guard(true);


		$this->debug->unguard(true);
		return true;
	}


	public function getCartContent()
	{
		$this->debug->guard(true);


		$this->debug->unguard(true);
		return true;
	}


}
?>