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

		$sql = "SELECT p.* FROM shop_products p ";
		$sql .= "LEFT JOIN shop_products_to_categories p2c ON p.product_id = p2c.productcategories_product ";
		$sql .= "WHERE p2c.productcategories_category = '" . $categoryid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get product list in category: could not get data from the database', 'warning');
			$this->messages->setMessage('Could not get product list in category: could not get data from the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$productdata = array();
		while ($row = $this->database->fetchArray($res))
		{
			$productdata[] = $row;
		}

		if (count($productdata) < 1)
		{
			$this->debug->write('Could not get product list in category: category not found in database', 'warning');
			$this->messages->setMessage('Could not get product list in category: category not found in database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->debug->unguard($productdata);
		return $productdata;
	}


	public function getCategoryData($categoryid)
	{
		$this->debug->guard(true);

		$sql = "SELECT * FROM shop_categories WHERE category_id='" . $categoryid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get category data: could not get data from the database', 'warning');
			$this->messages->setMessage('Could not get category data: could not get data from the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$categorydata = array();
		$categorydata = $this->database->fetchArray($res);

		if (count($categorydata) < 1)
		{
			$this->debug->write('Could not get category data: category not found in database', 'warning');
			$this->messages->setMessage('Could not get category data: category not found in database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($categorydata);
		return $categorydata;
	}


	public function getAllCategories()
	{
		$this->debug->guard(true);

		$sql = "SELECT * FROM shop_categories";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get category list: could not get data from the database', 'warning');
			$this->messages->setMessage('Could not get category list: could not get data from the database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$categorydata = array();
		while ($row = $this->database->fetchArray($res))
		{
			$categorydata[] = $row;
		}

		$this->debug->unguard($categorydata);
		return $categorydata;
	}

}
?>