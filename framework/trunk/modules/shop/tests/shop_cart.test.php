<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../../../tests/_configuration.php');

class testShopCart extends UnitTestCase
{
	public $database;
	public $user;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$this->user = zgUserhandler::init();

		$shop = new zgShop();
		$this->assertNotNull($shop);
		unset($shop);
    }
	
	function setup_shopdata()
	{
		$this->database->query('TRUNCATE TABLE shop_cart');
		$this->database->query('TRUNCATE TABLE shop_products');
		$this->database->query('TRUNCATE TABLE shop_orders');
		$this->database->query('TRUNCATE TABLE shop_products_to_orders');
	}

	function test_addToCart()
	{
		$shop = new zgShop();
		$this->assertNotNull($shop);
		
		// test adding product, qty 1
		$this->setup_shopdata();

		$productid = rand(100,500);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '5')");

		$ret = $shop->addToCart($productid);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM shop_cart");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		
		$row = $this->database->fetchArray($res);
		$this->assertEqual($row['cart_product'], $productid);
		$this->assertEqual($row['cart_qty'], 1);

		$res = $this->database->query("SELECT * FROM shop_products WHERE product_id = '" . $productid . "'");
		$row = $this->database->fetchArray($res);
		$this->assertEqual($row['product_qty'], 4);


		// test adding product, qty 5
		$this->setup_shopdata();

		$productid = rand(100,500);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '5')");

		$ret = $shop->addToCart($productid, 5);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM shop_cart");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		
		$res = $this->database->query("SELECT * FROM shop_products WHERE product_id = '" . $productid . "'");
		$row = $this->database->fetchArray($res);
		$this->assertEqual($row['product_qty'], 0);


		// test adding product, qty 10 (too much)
		$this->setup_shopdata();

		$productid = rand(100,500);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '5')");

		$ret = $shop->addToCart($productid, 10);
		$this->assertFalse($ret);
		
		$res = $this->database->query("SELECT * FROM shop_cart");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		unset($shop);
    }

	function test_deleteFromCart()
	{
		$shop = new zgShop();
		$this->assertNotNull($shop);
		
		// test deleting product, qty 1-1
		$this->setup_shopdata();

		$productid = rand(100,500);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '5')");

		$ret = $shop->addToCart($productid);
		$ret = $shop->deleteFromCart($productid);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM shop_cart");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);
		
		// test deleting product, qty 5-1
		$this->setup_shopdata();

		$productid = rand(100,500);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '5')");

		$ret = $shop->addToCart($productid, 5);
		$ret = $shop->deleteFromCart($productid, 1);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM shop_cart");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		
		$res = $this->database->query("SELECT * FROM shop_cart WHERE cart_product = '" . $productid . "'");
		$row = $this->database->fetchArray($res);
		$this->assertEqual($row['cart_qty'], 4);


		// test deleting product, qty 5-10
		$this->setup_shopdata();

		$productid = rand(100,500);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '5')");

		$ret = $shop->addToCart($productid, 5);
		$ret = $shop->deleteFromCart($productid, 10);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM shop_cart");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		unset($shop);
    }

	function test_clearCart()
	{
		$shop = new zgShop();
		$this->assertNotNull($shop);
		
		$this->setup_shopdata();

		$productid = rand(100,500);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '5')");
		$ret = $shop->addToCart($productid);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . ($productid+1) . "', 'test2', 'test product', '1,50', '5')");
		$ret = $shop->addToCart($productid+1);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . ($productid+2) . "', 'test3', 'test product', '1,50', '5')");
		$ret = $shop->addToCart($productid+2);

		$res = $this->database->query("SELECT * FROM shop_cart");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 3);
		
		$shop->clearCart();
		
		$res = $this->database->query("SELECT * FROM shop_cart");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		unset($shop);
    }
	
	function test_getCartContent()
	{
		$shop = new zgShop();
		$this->assertNotNull($shop);
		
		$this->setup_shopdata();

		$productid = rand(100,500);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '5')");
		$ret = $shop->addToCart($productid);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . ($productid+1) . "', 'test2', 'test product', '1,50', '5')");
		$ret = $shop->addToCart($productid+1);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . ($productid+2) . "', 'test3', 'test product', '1,50', '5')");
		$ret = $shop->addToCart($productid+2);
		
		$ret = $shop->getCartContent();
		$this->assertEqual(count($ret), 3);
		$this->assertEqual($ret[0]['cart_product'], $productid);
		$this->assertEqual($ret[1]['cart_product'], ($productid+1));
		$this->assertEqual($ret[2]['cart_product'], ($productid+2));

		unset($shop);
    }	

	function test_purchaseCartContent()
	{
		$shop = new zgShop();
		$this->assertNotNull($shop);
		
		$this->setup_shopdata();

		$productid = rand(100,500);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . $productid . "', 'test1', 'test product', '1,50', '1')");
		$ret = $shop->addToCart($productid, 1);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . ($productid+1) . "', 'test2', 'test product', '1,50', '5')");
		$ret = $shop->addToCart($productid+1, 5);
		$this->database->query("INSERT INTO shop_products(product_id, product_name, product_description, product_price, product_qty) VALUES('" . ($productid+2) . "', 'test3', 'test product', '1,50', '10')");
		$ret = $shop->addToCart($productid+2, 10);
		
		$ret = $shop->purchaseCartContent();
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM shop_orders");
		$row = $this->database->fetchArray($res);
		$this->assertEqual($row['order_user'], $this->user->getUserID());

		$res = $this->database->query("SELECT * FROM shop_products_to_orders");
		$row = $this->database->fetchArray($res);
		$this->assertEqual($row['productorder_name'], 'test1');
		$row = $this->database->fetchArray($res);
		$this->assertEqual($row['productorder_name'], 'test2');
		$row = $this->database->fetchArray($res);
		$this->assertEqual($row['productorder_name'], 'test3');

		$res = $this->database->query("SELECT * FROM shop_cart");
		$this->assertEqual($this->database->numRows($res), 0);

		unset($shop);
    }	

}

?>
