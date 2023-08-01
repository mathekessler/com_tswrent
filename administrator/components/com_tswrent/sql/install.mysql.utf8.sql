CREATE TABLE IF NOT EXISTS `#__tswrent_brand` (
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
  `website`text NOT NULL,
  `supplier_id` text NOT NULL,

  PRIMARY KEY (`id`),
  KEY `idx_supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__tswrent_order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  `published` tinyint unsigned NOT NULL DEFAULT 0,
  `customer` varchar(255) NOT NULL DEFAULT '',
  `startdate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `graduation` varchar(255) NOT NULL DEFAULT '',
  `orderdiscount` int(4) unsigned NOT NULL DEFAULT 0,

  PRIMARY KEY (`id`),
  KEY `idx_customer` (`customer`),
  KEY `idx_startdate` (`startdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS `#__tswrent_product` (
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

  PRIMARY KEY (`id`),
  KEY `idx_brand_id` (`brand_id`),
  KEY `idx_catid` (`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__tswrent_supplier` (
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
  `postcode` int(100),
  `telephone` varchar(255)NOT NULL DEFAULT '',
  `mobile` varchar(255)NOT NULL DEFAULT '',
  `website`text NOT NULL DEFAULT '',
  `email_to` varchar(255)NOT NULL DEFAULT '',
  `brand_id` tinyint(4) NOT NULL DEFAULT 0,

  PRIMARY KEY (`id`),
  KEY `idx_brand_id`(`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
