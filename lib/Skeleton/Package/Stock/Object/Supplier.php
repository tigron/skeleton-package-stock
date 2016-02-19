<?php
/**
 * Object_Supplier
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Package\Stock\Object;

use \Skeleton\Database\Database;

class Supplier {

	use \Skeleton\Object\Get;
	use \Skeleton\Pager\Page;
	use \Skeleton\Object\Delete;
	use \Skeleton\Object\Save;
	use \Skeleton\Object\Model;

	private static $class_configuration = [
		'database_table' => 'object_supplier',
	];


	/**
	 * Set for Skeleton\Package\Stock\Object
	 *
	 * @access public
	 * @param Skeleton\Package\Stock\Object $object
	 */
	public static function set_for_object(\Skeleton\Package\Stock\Object $object, \Skeleton\Package\Stock\Supplier $supplier = null) {
		if ($supplier === null) {
			try {
				$object_supplier = self::get_by_object($object);
				$object_supplier->delete();
			} catch (\Exception $e) { }

		} else {
			try {
				$object_supplier = self::get_by_object($object);
			} catch (\Exception $e) {
				$object_supplier = new self();
				$object_supplier->object_classname = get_class($object);
				$object_supplier->object_id = $object->id;
			}
			$object_supplier->supplier_id = $supplier->id;
			$object_supplier->save();
		}
	}

	/**
	 * Get for Skeleton\Package\Stock\Object
	 *
	 * @access public
	 * @param Skeleton\Package\Stock\Object $object
	 * @return Skeleton\Package\Stock\Supplier $supplier
	 */
	public static function get_by_object(\Skeleton\Package\Stock\Object $object) {
		$db = Database::get();
		$id = $db->get_one('SELECT id FROM object_supplier WHERE object_classname=? AND object_id=?', [ get_class($object), $object->id ]);
		if ($id === null) {
			throw new \Exception('No object_supplier found');
		}
		return self::get_by_id($id);
	}

}
