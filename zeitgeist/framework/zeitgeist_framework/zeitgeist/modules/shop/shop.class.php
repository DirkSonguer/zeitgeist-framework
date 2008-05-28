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

require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/shop/zgsproductfunctions.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/shop/zgscategoryfunctions.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/shop/zgscartfunctions.class.php');

class zgShop
{
	protected $debug;
	protected $messages;
	protected $objects;
	protected $configuration;
	protected $database;

	protected $categoryfunctions;
	protected $cartfunctions;
	protected $productfunctions;

	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjectcache::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();

		$this->cartfunctions = new zgsCartfunctions();
		$this->categoryfunctions = new zgsCategoryfunctions();
		$this->productfunctions = new zgsProductfunctions();
	}


	public function addToCart($productid, $quantity=1)
	{
		$this->debug->guard(true);

		$ret = $this->cartfunctions->addToCart($productid, $quantity=1);

		$this->debug->unguard($ret);
		return $ret;
	}


	public function deleteFromCart($productid, $quantity=1)
	{
		$this->debug->guard(true);

		$ret = $this->cartfunctions->deleteFromCart($productid, $quantity=1);

		$this->debug->unguard($ret);
		return $ret;
	}


	public function clearCart()
	{
		$this->debug->guard(true);

		$ret = $this->cartfunctions->clearCart();

		$this->debug->unguard($ret);
		return $ret;
	}


	public function getCartContent()
	{
		$this->debug->guard(true);

		$cartcontent = $this->cartfunctions->getCartContent();

		$this->debug->unguard($cartcontent);
		return $cartcontent;
	}


	public function getProductData($productid)
	{
		$this->debug->guard(true);

		$productdata = $this->productfunctions->getProductData($productid);

		$this->debug->unguard($productdata);
		return $productdata;
	}


	public function getProductsInCategory($categoryid)
	{
		$this->debug->guard(true);

		$products = $this->categoryfunctions->getProductsInCategory($categoryid);

		$this->debug->unguard(true);
		return true;
	}


	public function getCategoryData($categoryid)
	{
		$this->debug->guard(true);

		$categorydata = $this->categoryfunctions->getCategoryData($categoryid);

		$this->debug->unguard($categorydata);
		return $categorydata;
	}


	public function getAllCategories()
	{
		$this->debug->guard(true);

		$categories = $this->categoryfunctions->getAllCategories();

		$this->debug->unguard($categories);
		return $categories;
	}

}
?>