<?php

class testShopProducts extends UnitTestCase
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
		$this->database->query('TRUNCATE TABLE shop_images');
	}

	function test_getProductData()
	{
		$shop = new zgShop();
		$this->assertNotNull($shop);
		
		$this->setup_shopdata();

		$productid = rand(100,500);
		$ret = $shop->getProductData($productid);
		$this->assertFalse($ret);

		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '5')");

		$ret = $shop->getProductData($productid);
		$this->assertTrue($ret);
		$this->assertEqual($ret['product_description'], 'test product');

		unset($shop);
    }

	function test_getAllProducts()
	{
		$shop = new zgShop();
		$this->assertNotNull($shop);
		
		$this->setup_shopdata();

		$productid = rand(100,500);
		$ret = $shop->getAllProducts();
		$this->assertEqual(count($ret), 0);

		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '5')");
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . ($productid+1) . "', 'test2', 'test product', '1,50', '5')");
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . ($productid+2) . "', 'test3', 'test product', '1,50', '5')");

		$ret = $shop->getAllProducts();
		$this->assertEqual(count($ret), 3);
		$this->assertEqual($ret[0]['product_name'], 'test1');
		$this->assertEqual($ret[1]['product_name'], 'test2');
		$this->assertEqual($ret[2]['product_name'], 'test3');

		unset($shop);
    }

	function test_getImagesForProduct()
	{
		$shop = new zgShop();
		$this->assertNotNull($shop);
		
		$this->setup_shopdata();

		$productid = rand(100,500);
		$ret = $shop->getImagesForProduct($productid);
		$this->assertEqual(count($ret), 0);

		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '5')");
		$this->database->query("INSERT INTO shop_images(image_id, image_product, image_file, image_description) VALUES('1', '" . $productid . "', 'test1.jpg', 'test product')");
		$this->database->query("INSERT INTO shop_images(image_id, image_product, image_file, image_description) VALUES('2', '" . $productid . "', 'test2.jpg', 'test product')");

		$ret = $shop->getImagesForProduct($productid);
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret[0]['image_file'], 'test1.jpg');
		$this->assertEqual($ret[1]['image_file'], 'test2.jpg');

		unset($shop);
    }
}

?>
