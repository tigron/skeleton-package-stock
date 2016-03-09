<?php
/**
 * Purchase_Order_Item
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Package\Stock\Purchase\Order;

use \Skeleton\Database\Database;

class Item {

	use \Skeleton\Object\Get;
	use \Skeleton\Pager\Page;
	use \Skeleton\Object\Delete;
	use \Skeleton\Object\Save;
	use \Skeleton\Object\Model;

	private static $class_configuration = [
		'database_table' => 'purchase_order_item',
	];

	/**
	 * Deliver
	 *
	 * @access public
	 * @param string $amount
	 * @param object $trigger
	 * @param string $comment
	 */
	public function deliver($amount, $trigger, $comment = '') {
		$stock_object = $this->get_stock_object();
		\Skeleton\Package\Stock\Stock::change($stock_object, $amount, $trigger, $comment);
		$this->delivered += $amount;
		$this->save();
		$purchase_order = \Skeleton\Package\Stock\Purchase\Order::get_by_id($this->purchase_order_id);
		$purchase_order->check_delivered();
	}

	/**
	 * Get Stock object
	 *
	 * @access public
	 * @return \Skeleton\Package\Stock\Object $object
	 */
	public function get_stock_object() {
		$classname = $this->stock_object_classname;
		return $classname::get_by_id($this->stock_object_id);
	}

	/**
	 * Get by purchase_order
	 *
	 * @access public
	 * @param \Skeleton\Package\Stock\Purchase\Order $purchase_order
	 * @return array $purchase_order_items
	 */
	public static function get_by_purchase_order(\Skeleton\Package\Stock\Purchase\Order $purchase_order) {
		$db = Database::get();
		$ids = $db->get_column('SELECT id FROM purchase_order_item WHERE purchase_order_id=?', [ $purchase_order->id ]);
		$objects = [];
		foreach ($ids as $id) {
			$objects[] = self::get_by_id($id);
		}
		return $objects;
	}

	/**
	 * Get backorder by supplier
	 *
	 * @access public
	 * @param \Skeleton\Package\Stock\Supplier $supplier
	 * @return array $purchase_order_items
	 */
	public static function get_backorder_by_supplier(\Skeleton\Package\Stock\Supplier $supplier) {
		$db = Database::get();
		$ids = $db->get_column('SELECT purchase_order_item.id FROM purchase_order_item, purchase_order WHERE purchase_order_item.purchase_order_id=purchase_order.id AND supplier_id=? AND amount != purchase_order_item.delivered', [ $supplier->id ]);
		$objects = [];
		foreach ($ids as $id) {
			$purchase_order_item = self::get_by_id($id);
			$objects[] = $purchase_order_item;
		}
		return $objects;
	}

	/**
	 * Count backorder
	 *
	 * @access public
	 * @param \Skeleton\Package\Stock\Object $object
	 * @return int $backorder
	 */
	public static function count_backorder(\Skeleton\Package\Stock\Object $object) {
		$db = Database::get();
		$count = $db->get_one('SELECT SUM(amount-delivered) FROM purchase_order_item WHERE stock_object_id=? AND stock_object_classname=? AND amount != delivered', [ $object->id, get_class($object) ]);
		if ($count === null) {
			return 0;
		} else {
			return $count;
		}
	}
}
