<?php

class testShop extends UnitTestCase
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
	}

	function test_addToCart()
	{
		$shop = new zgShop();
		$this->assertNotNull($shop);
		
		$productid = rand(100,500);

		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price) VALUES('test1', 'test product', '1,50')");

		$this->setup_shopdata();

		unset($shop);
    }

		
}

?>
