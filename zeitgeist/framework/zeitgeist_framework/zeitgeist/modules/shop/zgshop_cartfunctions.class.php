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

class zgshopCartfunctions
{
	protected $debug;
	protected $messages;
	protected $objects;
	protected $configuration;
	protected $database;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjects::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	public function addToCart($productid, $quantity=1)
	{
		$this->debug->guard(true);

		$sql = "SELECT product_qty FROM shop_products WHERE product_id = '" . $productid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not add product to cart: could not access products database', 'warning');
			$this->messages->setMessage('Could not add product to cart: could not access products database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$row = $this->database->fetchArray($res);
		if ( (empty($row['product_qty'])) || ($row['product_qty'] < $quantity) )
		{
			$this->debug->write('Could not add product to cart: could not find product in database', 'warning');
			$this->messages->setMessage('Could not add product to cart: could not find product in database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "INSERT INTO shop_cart(cart_user, cart_product, cart_qty) VALUES('" . $this->user->getUserID() . "', '" . $productid . "', '" . $quantity . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not add product to cart: could not write to database', 'warning');
			$this->messages->setMessage('Could not add product to cart: could not write to database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "UPDATE shop_products SET product_qty = product_qty - " . $quantity . " WHERE product_id = '" . $productid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not add product to cart: could not decrease qty of product in database', 'warning');
			$this->messages->setMessage('Could not add product to cart: could not decrease qty of product in database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function deleteFromCart($productid, $quantity=0)
	{
		$this->debug->guard(true);

		$sql = "SELECT cart_qty FROM shop_cart WHERE cart_user = '" . $this->user->getUserID() . "' AND cart_product = '" . $productid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not delete product from cart: could not get cart data from database', 'warning');
			$this->messages->setMessage('Could not delete product from cart: could not get cart data from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$row = $this->database->fetchArray($res);
		if (empty($row['cart_qty']))
		{
			$this->debug->write('Could not delete product from cart: could not find product in database', 'warning');
			$this->messages->setMessage('Could not delete product from cart: could not find product in database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if ( ($quantity == 0) || ($row['cart_qty'] < $quantity) )
		{
			$sql = "DELETE FROM shop_cart WHERE cart_user = '" . $this->user->getUserID() . "' AND cart_product = '" . $productid . "'";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Could not delete product from cart: could not delete data from database', 'warning');
				$this->messages->setMessage('Could not delete product from cart: could not delete data from database', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}
		else
		{
			$sql = "UPDATE shop_cart SET cart_qty = cart_qty - " . $quantity . " WHERE cart_user = '" . $this->user->getUserID() . "' AND cart_product = '" . $productid . "'";
			$res = $this->database->query($sql);
			if (!$res)
			{
				$this->debug->write('Could not delete product from cart: could not delete data from database', 'warning');
				$this->messages->setMessage('Could not delete product from cart: could not delete data from database', 'warning');
				$this->debug->unguard(false);
				return false;
			}			
		}

		$this->debug->unguard(true);
		return true;
	}


	public function clearCart()
	{
		$this->debug->guard(true);

		$sql = "DELETE FROM shop_cart WHERE cart_user = '" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not clear cart: could not delete data from database', 'warning');
			$this->messages->setMessage('Could not clear cart: could not delete data from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	public function getCartContent()
	{
		$this->debug->guard(true);

		$sql = "SELECT * FROM shop_cart WHERE cart_user = '" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get cart content: could not get data from database', 'warning');
			$this->messages->setMessage('Could not get cart content: could not get data from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$cartdata = array();
		while($row = $this->database->fetchArray($res))
		{
			$cartdata[] = $row;
		}		

		$this->debug->unguard($cartdata);
		return $cartdata;
	}


}
?>