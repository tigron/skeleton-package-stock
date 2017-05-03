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

class Stock {
	use \Skeleton\Object\Get;
	use \Skeleton\Pager\Page;
	use \Skeleton\Object\Delete;
	use \Skeleton\Object\Save;
	use \Skeleton\Object\Model {
		__get as trait_get;
		__isset as trait_isset;
	}

	private static $class_configuration = [
		'database_table' => 'product_stock',
	];

	/**
	 * __Get
	 *
	 * @access public
	 * @param string $key
	 * @return mixed $value
	 */
	public function __get($key) {
		if ($key == 'trigger_object') {
			$classname = $this->trigger_object_classname;
			return $classname::get_by_id($this->trigger_object_id);
		}

		return $this->trait_get($key);
	}

	/**
	 * __isset
	 *
	 * @access public
	 * @param string $key
	 * @return bool $isset
	 */
	public function __isset($key) {
		if ($key == 'trigger_object') {
			return true;
		}

		return $this->trait_isset($key);
	}

	/**
	 * count backorder
	 *
	 * @access public
	 * @return int $backorder
	 */
	public function count_backorder() {
		$classname = $this->stock_object_classname;
		$object = $classname::get_by_id($this->stock_object_id);
		return \Skeleton\Package\Stock\Purchase\Order\Item::count_backorder($object);
	}

	/**
	 * change
	 *
	 * @access public
	 * @param \Skeleton\Package\Stock\Object $object
	 * @param int $amount
	 * @param object $trigger
	 */
	public static function change(\Skeleton\Package\Stock\Object $object, $amount, $trigger, $comment) {
		if ($object->has_stock() === false) {
			return;
		}

		try {
			$last_stock = self::get_last_by_object($object);
			$last_stock = $last_stock->total;
		} catch (\Exception $e) {
			$last_stock = 0;
		}
		$product_stock = new self();
		$product_stock->stock_object_classname = get_class($object);
		$product_stock->stock_object_id = $object->id;
		$product_stock->total = $last_stock + $amount;
		$product_stock->movement = $amount;
		$product_stock->trigger_object_classname = get_class($trigger);
		$product_stock->trigger_object_id = $trigger->id;
		$product_stock->comment = $comment;
		$product_stock->save();
	}

	/**
	 * get
	 *
	 * @access public
	 * @param \Skeleton\Package\Stock\Object $object
	 * @return int $amount
	 */
	public static function get(\Skeleton\Package\Stock\Object $object) {
		try {
			$last_stock = self::get_last_by_object($object);
			$last_stock = $last_stock->total;
		} catch (\Exception $e) {
			$last_stock = 0;
		}
		return $last_stock;
	}

	/**
	 * Get last by object
	 *
	 * @access public
	 * @param \Skeleton\Package\Stock\Object $object
	 * @return \Skeleton\Package\Stock $stock
	 */
	public static function get_last_by_object(\Skeleton\Package\Stock\Object $object) {
		$db = Database::get();
		$id = $db->get_one('SELECT id FROM product_stock WHERE stock_object_classname=? AND stock_object_id=? ORDER BY created DESC LIMIT 1', [ get_class($object), $object->id ]);
		if ($id === null) {
			throw new \Exception('No product_stock found');
		}
		return self::get_by_id($id);
	}

}
