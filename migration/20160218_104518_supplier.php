<?php
/**
 * Database migration class
 *
 * @author Gerry Demaret <gerry@tigron.be>
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 */
namespace Skeleton\Package\Stock;
use \Skeleton\Database\Database;

class Migration_20160218_104518_Supplier extends \Skeleton\Database\Migration {

	/**
	 * Migrate up
	 *
	 * @access public
	 */
	public function up() {
		$db = Database::get();
		$db->query("
			CREATE TABLE `supplier` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `company` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `phone` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `fax` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `mobile` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
			  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `street` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `housenumber` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
			  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `zip` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
			  `country_id` int(11) NOT NULL DEFAULT '0',
			  `vat` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `website` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
			  `created` datetime NOT NULL,
			  `updated` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `country_id` (`country_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		", []);

		$db->query("
			CREATE TABLE `object_supplier` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `supplier_id` int(11) NOT NULL,
			  `object_classname` varchar(128) NOT NULL,
			  `object_id` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		", []);

		$db->query("
			CREATE TABLE `purchase_order` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `created` datetime NOT NULL,
			  `supplier_id` int(11) NOT NULL,
			  `delivered` tinyint(4) NOT NULL,
			  `canceled` tinyint(4) NOT NULL,
			  `comments` text NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		", []);

		$db->query("
			CREATE TABLE `purchase_order_item` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `purchase_order_id` int(11) NOT NULL,
			  `stock_object_classname` varchar(128) NOT NULL,
			  `stock_object_id` int(11) NOT NULL,
			  `price` decimal(10,2) NOT NULL,
			  `amount` int(11) NOT NULL,
			  `delivered` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		", []);

		$db->query("
			CREATE TABLE `product_stock` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `stock_object_classname` varchar(64) NOT NULL,
			  `stock_object_id` int(11) NOT NULL,
			  `trigger_object_classname` varchar(64) NOT NULL,
			  `trigger_object_id` int(11) NOT NULL,
			  `total` int(11) NOT NULL,
			  `movement` int(11) NOT NULL,
			  `created` datetime NOT NULL,
			  `comment` varchar(128) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		", []);

	}

	/**
	 * Migrate down
	 *
	 * @access public
	 */
	public function down() {

	}
}
