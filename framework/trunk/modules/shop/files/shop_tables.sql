
CREATE TABLE IF NOT EXISTS `shop_cart` (
  `cart_id` int(12) NOT NULL auto_increment,
  `cart_session` varchar(32) collate latin1_general_ci NOT NULL,
  `cart_product` int(12) NOT NULL,
  `cart_qty` int(4) NOT NULL,
  PRIMARY KEY  (`cart_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `shop_categories` (
  `category_id` int(12) NOT NULL auto_increment,
  `category_name` varchar(255) collate latin1_general_ci NOT NULL,
  `category_description` text collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `shop_images` (
  `image_id` int(12) NOT NULL auto_increment,
  `image_product` int(12) NOT NULL,
  `image_file` varchar(255) collate latin1_general_ci NOT NULL,
  `image_description` text collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `shop_orders` (
  `order_id` int(12) NOT NULL auto_increment,
  `order_user` int(12) NOT NULL,
  `order_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `shop_products` (
  `product_id` int(12) NOT NULL auto_increment,
  `product_name` varchar(255) collate latin1_general_ci NOT NULL,
  `product_description` text collate latin1_general_ci NOT NULL,
  `product_price` float NOT NULL,
  PRIMARY KEY  (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `shop_products_to_categories` (
  `productcategories_product` int(12) NOT NULL,
  `productcategories_category` int(12) NOT NULL,
  PRIMARY KEY  (`productcategories_product`,`productcategories_category`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


CREATE TABLE IF NOT EXISTS `shop_products_to_orders` (
  `productorder_id` int(12) NOT NULL auto_increment,
  `productorder_product` int(12) NOT NULL,
  `productorder_name` varchar(255) collate latin1_general_ci NOT NULL,
  `productorder_qty` int(4) NOT NULL,
  `productorder_price` float NOT NULL,
  PRIMARY KEY  (`productorder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;
