--
-- Table structure for table `#__tswrent_brands`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL ,
  `description` text NOT NULL,
  `webpage` varchar(255) NOT NULL DEFAULT ' ',
  `brand_logo` varchar(255),
  `state` tinyint unsigned NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  `checked_out` int unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime,
  PRIMARY KEY (`id`)
  KEY 'idx_title' (`title`),
  KEY `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_brand_supplier_relation`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_brand_supplier_relation` (
  `brand_id` int unsigned NOT NULL DEFAULT 0,
  `supplier_id` int unsigned NOT NULL DEFAULT 0,
  
  PRIMARY KEY (`brand_id`,`supplier_id`)
  KEY `idx_brand_id` (`brand_id`),
  KEY `idx_supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_config`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company` varchar(255) NOT NULL DEFAULT '',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_contacts`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `prename` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `address` text NOT NULL,
  `postalcode` int unsigned NOT NULL DEFAULT '0000',
  `city` varchar(100),
  `telephone` varchar(255)NOT NULL DEFAULT '',
  `mobile` varchar(255)NOT NULL DEFAULT '',
  `email_to` varchar(255)NOT NULL DEFAULT '',
  `iban` varchar(255)NOT NULL DEFAULT '',
  `state` tinyint unsigned NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime NOT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  `checked_out` int unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime,

  PRIMARY KEY (`id`)
  KEY `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_contact_relation`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_contact_relation` (
  `contact_id` int unsigned DEFAULT 0,
  `brand_id` int unsigned DEFAULT 0,
  `supplier_id` int unsigned DEFAULT 0,
  `customer_id` int unsigned DEFAULT 0,
  `tswrent` tinyint unsigned DEFAULT 0,
  
  PRIMARY KEY (`contact_id`,`brand_id`,`customer_id`,`supplier_id`,`tswrent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_customer`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_customers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `description` text NOT NULL,
  `address` text NOT NULL,
  `postalcode` int unsigned NOT NULL DEFAULT '0000',
  `city` varchar(100),
  `telephone` varchar(255)NOT NULL DEFAULT '',
  `mobile` varchar(255)NOT NULL DEFAULT '',
  `email_to` varchar(255)NOT NULL DEFAULT '',
  `webpage`varchar(255) NOT NULL DEFAULT '',
  `customer_logo` varchar(255),
  `state` tinyint unsigned NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  `checked_out` int unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime ,

  PRIMARY KEY (`id`)
  KEY `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_graduations`
--
CREATE TABLE IF NOT EXISTS `#__tswrent_graduations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL ,
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `description` text NOT NULL,
  `graduations` text NOT NULL,
  `state` tinyint unsigned NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  `checked_out` int unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime ,

  PRIMARY KEY (`id`)
  KEY `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_orders`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `description` text NOT NULL,
  `contact_id` int NOT NULL DEFAULT 0,
  `customer_id` int NOT NULL DEFAULT 0,
  `c_contact_id` int NOT NULL DEFAULT 0,
  `venue_name` varchar(255),
  `address` text NOT NULL,
  `postalcode` int unsigned NOT NULL DEFAULT '0000',
  `city` varchar(100),
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `days` int unsigne NOT NULL DEFAULT 0,
  `hours` int unsigne NOT NULL DEFAULT 0,
  `orderstate` tinyint unsigned NOT NULL DEFAULT 0,
  `graduation_id` int unsigne NOT NULL DEFAULT 0,
  `factor` decimal(3,1) unsigne NOT NULL DEFAULT 0,
  `orderdiscount` decimal(3,1) unsigned NOT NULL DEFAULT 0,
  `order_total_price` decimal(10,1) unsigned NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  `checked_out` int unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime,
  
  PRIMARY KEY (`id`),
  KEY `idx_customer` (`customer`),
  KEY `idx_contact` (`contact`),
  KEY `idx_startdate` (`startdate`),
  KEY `idx_enddate` (`enddate`),
  KEY `idx_orderstate` (`orderstate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_order_product`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_order_product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `description` text,
  `reserved` int unsigned NOT NULL,
  `productdiscount` int unsigned NOT NULL DEFAULT 0,
  `price` decimal(10,2) unsigned NOT NULL DEFAULT 0,
  `price_total` decimal(10,2) unsigned NOT NULL DEFAULT 0,

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
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `catid` int unsigned NOT NULL DEFAULT 0,
  `description` text,
  `productimage` varchar(255) NOT NULL Default'',
  `brand_id` tinyint unsigned NOT NULL ,
  `price` decimal(10,2) unsigned NOT NULL,
  `unit` varchar(255) NOT NULL DEFAULT '',
  `weight` decimal(7,2) unsigned NOT NULL DEFAULT 0,
  `stock` int unsigned NOT NULL DEFAULT 0,
  `consumable`tinyint NOT NULL DEFAULT 0,
  `state` tinyint unsigned NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  `checked_out` int unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime ,

  PRIMARY KEY (`id`),
  KEY `idx_brand_id` (`brand_id`),
  KEY `idx_catid` (`catid`),
  KEY `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `#__tswrent_suppliers`
--

CREATE TABLE IF NOT EXISTS `#__tswrent_suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `description` text,
  `address` text NOT NULL,
  `postalcode` int unsigned NOT NULL DEFAULT '0000',
  `city` varchar(100),
  `telephone` varchar(255)NOT NULL DEFAULT '',
  `mobile` varchar(255)NOT NULL DEFAULT '',
  `email_to` varchar(255)NOT NULL DEFAULT '',
  `webpage`varchar(255) NOT NULL DEFAULT '',
  `supplier_logo` varchar(255),
  `state` tinyint unsigned NOT NULL DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime DEFAULT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  `checked_out` int unsigned NOT NULL DEFAULT 0,
  `checked_out_time` datetime ,

  PRIMARY KEY (`id`),
  KEY `idx_state` (`state`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Load default value for `#__tswrent_graduations`
--

INSERT IGNORE INTO `#__tswrent_graduations` (`id`, `title`,  `graduations`) VALUES
(1, 'Default',  '{\"graduations0\":{\"duration\":\"1\",\"unit\":\"1\",\"factor\":\"1\"},\"graduations1\":{\"duration\":\"2\",\"unit\":\"1\",\"factor\":\"1.5\"},\"graduations2\":{\"duration\":\"3\",\"unit\":\"1\",\"factor\":\"2\"},\"graduations5\":{\"duration\":\"4\",\"unit\":\"1\",\"factor\":\"2.5\"},\"graduations4\":{\"duration\":\"5\",\"unit\":\"1\",\"factor\":\"3\"},\"graduations3\":{\"duration\":\"6\",\"unit\":\"1\",\"factor\":\"3.5\"},\"graduations14\":{\"duration\":\"1\",\"unit\":\"0\",\"factor\":\"4\"},\"graduations13\":{\"duration\":\"2\",\"unit\":\"0\",\"factor\":\"6\"},\"graduations12\":{\"duration\":\"3\",\"unit\":\"0\",\"factor\":\"7\"},\"graduations11\":{\"duration\":\"4\",\"unit\":\"0\",\"factor\":\"8\"},\"graduations10\":{\"duration\":\"5\",\"unit\":\"0\",\"factor\":\"9\"},\"graduations9\":{\"duration\":\"6\",\"unit\":\"0\",\"factor\":\"10\"},\"graduations8\":{\"duration\":\"7\",\"unit\":\"0\",\"factor\":\"11\"},\"graduations7\":{\"duration\":\"8\",\"unit\":\"0\",\"factor\":\"12\"},\"graduations6\":{\"duration\":\"9\",\"unit\":\"0\",\"factor\":\"13\"}}');

