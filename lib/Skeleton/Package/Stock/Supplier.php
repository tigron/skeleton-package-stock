<?php
/**
 * Stock class
 *
 * @author Gerry Demaret <gerry@tigron.be>
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 */

namespace Skeleton\Package\Stock;

use \Skeleton\Database\Database;

class Supplier {
	use \Skeleton\Object\Get;
	use \Skeleton\Pager\Page;
	use \Skeleton\Object\Delete;
	use \Skeleton\Object\Save;
	use \Skeleton\Object\Model;

	private static $class_configuration = [
		'database_table' => 'supplier',
	];

	/**
	 * Set for Skeleton\Package\Stock\Object
	 *
	 * @access public
	 * @param Skeleton\Package\Stock\Object $object
	 */
	public static function set_for_object(\Skeleton\Package\Stock\Object $object, Supplier $supplier = null) {
		\Skeleton\Package\Stock\Object\Supplier::set_for_object($object, $supplier);
	}

	/**
	 * Get for Skeleton\Package\Stock\Object
	 *
	 * @access public
	 * @param Skeleton\Package\Stock\Object $object
	 * @return Skeleton\Package\Stock\Supplier $supplier
	 */
	public static function get_for_object(\Skeleton\Package\Stock\Object $object) {
		try {
			$object_supplier = \Skeleton\Package\Stock\Object\Supplier::get_by_object($object);
			return self::get_by_id($object_supplier->supplier_id);
		} catch (\Exception $e) {
			return null;
		}
	}
}
