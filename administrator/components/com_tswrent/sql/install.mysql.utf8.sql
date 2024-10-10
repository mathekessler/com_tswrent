--
-- Table structure for table `#__tswrent_brands`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL DEFAULT 0,
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL DEFAULT 0,
  `published` tinyint(4) NOT NULL DEFAULT 0,
  `webpage`text NOT NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_brand_supplyer_relation`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_brand_supplier_relation` (
  `brand_id` int(10) unsigned NOT NULL DEFAULT 0,
  `supplier_id` int(10) unsigned NOT NULL DEFAULT 0,
  
  PRIMARY KEY (`brand_id`,`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_contacts`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_contacts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL DEFAULT 0,
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL DEFAULT 0,
  `published` tinyint(4) NOT NULL DEFAULT 0,
  `address` text NOT NULL,
  `city` varchar(100),
  `postalcode` int(100),
  `telephone` varchar(255)NOT NULL DEFAULT '',
  `iban` varchar(255)NOT NULL DEFAULT '',
  `mobile` varchar(255)NOT NULL DEFAULT '',
  `email_to` varchar(255)NOT NULL DEFAULT '',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_contact_relation`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_contact_relation` (
  `contact_id` int(10) unsigned NOT NULL DEFAULT 0,
  `brand_id` int(10) unsigned NOT NULL DEFAULT 0,
  `supplier_id` int(10) unsigned NOT NULL DEFAULT 0,
  `customer_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tswrent` tinyint(2) unsigned NOT NULL DEFAULT 0,
  
  PRIMARY KEY (`contact_id`,`brand_id`,`customer_id`,`supplier_id`,`tswrent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_config`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `company` varchar(255) NOT NULL DEFAULT '',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_customer`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_customers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL DEFAULT 0,
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL DEFAULT 0,
  `published` tinyint(4) NOT NULL DEFAULT 0,
  `address` text NOT NULL,
  `city` varchar(100),
  `postalcode` int(100),
  `telephone` varchar(255)NOT NULL DEFAULT '',
  `mobile` varchar(255)NOT NULL DEFAULT '',
  `email_to` varchar(255)NOT NULL DEFAULT '',
  `webpage`text NOT NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `#__tswrent_graduations`
--
CREATE TABLE IF NOT EXISTS `#__tswrent_graduations` (
  `id` int (11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL ,
   `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  `published` tinyint unsigned NOT NULL DEFAULT 0,
  `graduations` text NOT NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;




--
-- Table structure for table `#__tswrent_orders`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  `orderstate` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `contact` int(10) NOT NULL DEFAULT 0,
  `customer` int(10) NOT NULL DEFAULT 0,
  `c_contact` int(10) NOT NULL DEFAULT 0,
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `graduation` int(10) NOT NULL DEFAULT 0,
  `orderdiscount` int(10) unsigned NOT NULL DEFAULT 0,

  PRIMARY KEY (`id`),
  KEY `idx_customer` (`customer`),
  KEY `idx_contact` (`contact`),
  KEY `idx_startdate` (`startdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_order_product`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_order_product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` tinyint(10) unsigned NOT NULL DEFAULT 0,
  `description` text NOT NULL,
  `order_id` tinyint(10) unsigned NOT NULL DEFAULT 0,
  `reserved` int(10) unsigned NOT NULL DEFAULT 0,
  `ordering` int(10) NOT NULL DEFAULT 0,
  `productdiscount` int(10) unsigned NOT NULL DEFAULT 0,
  `price` decimal(10,2) unsigned NOT NULL DEFAULT 0,
  `graduation` tinyint(4) unsigned NOT NULL DEFAULT 0,

  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_products`
--
CREATE TABLE IF NOT EXISTS `#__tswrent_products` (
  `id` int (11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL ,
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `catid` int(10) unsigned NOT NULL DEFAULT 0,
  `description` mediumtext NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL DEFAULT 0,
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL DEFAULT 0,
  `published` tinyint(4) NOT NULL DEFAULT 1,
  `productimage` varchar(255) NOT NULL Default'',
  `brand_id` tinyint(4)unsigned NOT NULL DEFAULT 0,
  `supplier_id` tinyint(4) NOT NULL DEFAULT 0,
  `price` decimal(10,2) unsigned NOT NULL DEFAULT 0,
  `unit` varchar(255) NOT NULL DEFAULT '',
  `weight` decimal(7,2) unsigned NOT NULL DEFAULT 0,
  `stock` int(5) unsigned NOT NULL DEFAULT 0,
  `consumable`tinyint(4) NOT NULL DEFAULT 0,

  PRIMARY KEY (`id`),
  KEY `idx_brand_id` (`brand_id`),
  KEY `idx_supplier_id` (`supplier_id`),
  KEY `idx_catid` (`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_suppliers`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_suppliers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL DEFAULT 0,
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL DEFAULT 0,
  `published` tinyint(4) NOT NULL DEFAULT 0,
  `address` text NOT NULL,
  `city` varchar(100),
  `postalcode` int(100),
  `telephone` varchar(255)NOT NULL DEFAULT '',
  `mobile` varchar(255)NOT NULL DEFAULT '',
  `webpage`text NOT NULL DEFAULT '',
  `email_to` varchar(255)NOT NULL DEFAULT '',
  `supplyer_employee_id` tinyint(4) NOT NULL DEFAULT 0,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;


--
-- Load default value for `#__tswrent_graduations`
--

INSERT IGNORE INTO `#__tswrent_graduations` (`id`, `title`,  `graduations`) VALUES
(1, 'Default',  '{\"graduations0\":{\"duration\":\"1\",\"unit\":\"1\",\"factor\":\"1\"},\"graduations1\":{\"duration\":\"2\",\"unit\":\"1\",\"factor\":\"1.5\"},\"graduations2\":{\"duration\":\"3\",\"unit\":\"1\",\"factor\":\"2\"},\"graduations5\":{\"duration\":\"4\",\"unit\":\"1\",\"factor\":\"2.5\"},\"graduations4\":{\"duration\":\"5\",\"unit\":\"1\",\"factor\":\"3\"},\"graduations3\":{\"duration\":\"6\",\"unit\":\"1\",\"factor\":\"3.5\"},\"graduations14\":{\"duration\":\"1\",\"unit\":\"0\",\"factor\":\"4\"},\"graduations13\":{\"duration\":\"2\",\"unit\":\"0\",\"factor\":\"6\"},\"graduations12\":{\"duration\":\"3\",\"unit\":\"0\",\"factor\":\"7\"},\"graduations11\":{\"duration\":\"4\",\"unit\":\"0\",\"factor\":\"8\"},\"graduations10\":{\"duration\":\"5\",\"unit\":\"0\",\"factor\":\"9\"},\"graduations9\":{\"duration\":\"6\",\"unit\":\"0\",\"factor\":\"10\"},\"graduations8\":{\"duration\":\"7\",\"unit\":\"0\",\"factor\":\"11\"},\"graduations7\":{\"duration\":\"8\",\"unit\":\"0\",\"factor\":\"12\"},\"graduations6\":{\"duration\":\"9\",\"unit\":\"0\",\"factor\":\"13\"}}');

