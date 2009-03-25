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

require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/shop/zgshop_productfunctions.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/shop/zgshop_categoryfunctions.class.php');
require_once (ZEITGEIST_ROOTDIRECTORY . 'modules/shop/zgshop_cartfunctions.class.php');

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

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjects::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();

		$this->cartfunctions = new zgshopCartfunctions();
		$this->categoryfunctions = new zgshopCategoryfunctions();
		$this->productfunctions = new zgshopProductfunctions();
	}


	public function addToCart($productid, $quantity=1)
	{
		$this->debug->guard(true);

		$ret = $this->cartfunctions->addToCart($productid, $quantity);

		$this->debug->unguard($ret);
		return $ret;
	}


	public function deleteFromCart($productid, $quantity=0)
	{
		$this->debug->guard(true);

		$ret = $this->cartfunctions->deleteFromCart($productid, $quantity);

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