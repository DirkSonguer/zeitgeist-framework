<?php

class testShopCategories extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$shop = new zgShop();
		$this->assertNotNull($shop);
		unset($shop);
    }
	
	function setup_shopdata()
	{
		$this->database->query('TRUNCATE TABLE shop_cart');
		$this->database->query('TRUNCATE TABLE shop_products');
		$this->database->query('TRUNCATE TABLE shop_categories');
		$this->database->query('TRUNCATE TABLE shop_products_to_categories');
	}

	function test_getProductsInCategory()
	{
		$shop = new zgShop();
		$this->assertNotNull($shop);
		
		$this->setup_shopdata();
		
		$ret = $shop->getProductsInCategory(1);
		$this->assertFalse($ret);

		$productid = rand(100,500);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '5')");
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . ($productid+1) . "', 'test2', 'test product', '1,50', '5')");

		$categoryid = rand(100,500);
		$this->database->query("INSERT INTO shop_products_to_categories(productcategories_product, productcategories_category) VALUES('" . $productid . "', '$categoryid')");
		$this->database->query("INSERT INTO shop_products_to_categories(productcategories_product, productcategories_category) VALUES('" . ($productid+1) . "', '$categoryid')");

		$ret = $shop->getProductsInCategory($categoryid);
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret[0]['product_name'], 'test1');
		$this->assertEqual($ret[1]['product_name'], 'test2');

		unset($shop);
    }

	function test_getCategoryData()
	{
		$shop = new zgShop();
		$this->assertNotNull($shop);
		
		$this->setup_shopdata();
		
		$ret = $shop->getCategoryData(1);
		$this->assertFalse($ret);

		$categoryid = rand(100,500);
		$this->database->query("INSERT INTO shop_categories(category_id, category_name, category_description) VALUES('" . $categoryid . "', 'test1', 'test category')");

		$ret = $shop->getCategoryData($categoryid);
		$this->assertEqual(count($ret), 3);
		$this->assertEqual($ret['category_name'], 'test1');
		$this->assertEqual($ret['category_description'], 'test category');

		unset($shop);
    }
	
	function test_getAllCategories()
	{
		$shop = new zgShop();
		$this->assertNotNull($shop);
		
		$this->setup_shopdata();
		
		$ret = $shop->getAllCategories();
		$this->assertFalse($ret);

		$categoryid = rand(100,500);
		$this->database->query("INSERT INTO shop_categories(category_id, category_name, category_description) VALUES('" . $categoryid . "', 'test1', 'test category')");
		$this->database->query("INSERT INTO shop_categories(category_id, category_name, category_description) VALUES('" . ($categoryid+1) . "', 'test2', 'test category')");

		$ret = $shop->getAllCategories();
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret[0]['category_name'], 'test1');
		$this->assertEqual($ret[1]['category_name'], 'test2');

		unset($shop);
    }	
}

?>
