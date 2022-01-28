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
	 * Get an overview of the purchase list
	 *
	 * @access public
	 * @return array $purchase_list
	 */
	public function get_purchase_list_overview() {
		$purchase_list = $this->get_purchase_list();
		$overview = [];
		foreach ($purchase_list as $product) {
			if (!isset($overview[ get_class($product) . '-' . $product->id])) {
				$overview[ get_class($product) . '-' . $product->id] = [
					'product' => $product,
					'count' => 0
				];
			}

			$overview[ get_class($product) . '-' . $product->id]['count']++;
		}
		return $overview;
	}

	/**
	 * Get purchase_list
	 * Get a list of products that needs to be delivered and are not in stock
	 *
	 * @access public
	 * @return array $products
	 */
	public function get_purchase_list() {
		$delivery_items = \Skeleton\Package\Delivery\Item::get_undelivered();

		$to_deliver = [];
		foreach ($delivery_items as $delivery_item) {
			if (!isset($to_deliver[ $delivery_item->deliverable_object_classname . '-' . $delivery_item->deliverable_object_id ])) {
				$to_deliver[ $delivery_item->deliverable_object_classname . '-' . $delivery_item->deliverable_object_id ] = 0;
			}

			$to_deliver[ $delivery_item->deliverable_object_classname . '-' . $delivery_item->deliverable_object_id ]++;
		}

		foreach ($this->get_backorder() as $purchase_order_item) {
			$product = $purchase_order_item->get_stock_object();
			if (!isset($to_deliver[ get_class($product) . '-' . $product->id])) {
				continue;
			}

			$backordered = $purchase_order_item->amount - $purchase_order_item->delivered;
			$to_deliver[get_class($product) . '-' . $product->id] -= $backordered;
			if ($to_deliver[get_class($product) . '-' . $product->id] < 0) {
				unset($to_deliver[get_class($product) . '-' . $product->id]);
			}
		}

		$purchase_list = [];

		foreach ($to_deliver as $key => $count) {
			list($classname, $id) = explode('-', $key);
			$class = $classname::get_by_id($id);

			$supplier = self::get_for_object($class);
			if ($supplier === null) {
				continue;
			}
			if ($supplier->id != $this->id) {
				continue;
			}

			$stock = Stock::get($class);

			if ($stock > $count) {
				continue;
			}

			$to_deliver_count = $count - $stock;
			for ($i = 1; $i <= $to_deliver_count; $i++) {
				$purchase_list[] = $class;
			}
		}
		return $purchase_list;
	}

	/**
	 * Get backorders
	 *
	 * @access public
	 * @return array $objects
	 */
	public function get_backorder() {
		return \Skeleton\Package\Stock\Purchase\Order\Item::get_backorder_by_supplier($this);
	}

	/**
	 * Set for Skeleton\Package\Stock\Item
	 *
	 * @access public
	 * @param Skeleton\Package\Stock\Item $object
	 */
	public static function set_for_object(\Skeleton\Package\Stock\Item $object, Supplier $supplier = null) {
		\Skeleton\Package\Stock\Item\Supplier::set_for_object($object, $supplier);
	}

	/**
	 * Unset for object
	 *
	 * @access public
	 * @param Skeleton\Package\Stock\Item $object
	 */
	public static function unset_for_object(\Skeleton\Package\Stock\Item $object) {
		try {
			$object_supplier = \Skeleton\Package\Stock\Item\Supplier::get_by_object($object);
			$object_supplier->delete();
		} catch (\Exception $e) {
		}
	}

	/**
	 * Get for Skeleton\Package\Stock\Item
	 *
	 * @access public
	 * @param Skeleton\Package\Stock\Item $object
	 * @return Skeleton\Package\Stock\Supplier $supplier
	 */
	public static function get_for_object(\Skeleton\Package\Stock\Item $object) {
		try {
			$object_supplier = \Skeleton\Package\Stock\Item\Supplier::get_by_object($object);
			return self::get_by_id($object_supplier->supplier_id);
		} catch (\Exception $e) {
			return null;
		}
	}

	public static function get_by_id($id) {
		$class = new self($id);
		return $class;
	}
}
