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
}
