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

class zgshopProductfunctions
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


	/**
	 * Gets the data of a given product
	 *
	 * @param int $productid id of the product
	 *
	 * @return array
	 */
	public function getProductData($productid)
	{
		$this->debug->guard(true);

		$sql = "SELECT * FROM shop_products WHERE product_id='" . $productid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get product data: could not get data from the database', 'warning');
			$this->messages->setMessage('Could not get product data: could not get data from the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$productdata = array();
		$productdata = $this->database->fetchArray($res);

		if (count($productdata) < 1)
		{
			$this->debug->write('Could not get product data: product not found in database', 'warning');
			$this->messages->setMessage('Could not get product data: product not found in database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($productdata);
		return $productdata;
	}


	/**
	 * Gets the data for all products currently in the shop
	 * Returns an array containing the products
	 *
	 * @return array
	 */
	public function getAllProducts()
	{
		$this->debug->guard(true);

		$sql = "SELECT * FROM shop_products";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get product list: could not get data from the database', 'warning');
			$this->messages->setMessage('Could not get product list: could not get data from the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$productdata = array();
		while ($row = $this->database->fetchArray($res))
		{
			$productdata[] = $row;
		}

		$this->debug->unguard($productdata);
		return $productdata;
	}

	
	/**
	 * Gets the images for a given product
	 *
	 * @param int $productid id of the product
	 *
	 * @return array
	 */
	public function getImagesForProduct($productid)
	{
		$this->debug->guard(true);

		$sql = "SELECT * FROM shop_images WHERE image_product = '" . $productid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get image list: could not get data from the database', 'warning');
			$this->messages->setMessage('Could not get image list: could not get data from the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$imagedata = array();
		while ($row = $this->database->fetchArray($res))
		{
			$imagedata[] = $row;
		}

		$this->debug->unguard($imagedata);
		return $imagedata;
	}
	
}
?>