<?php
/**
 * Purchase_Order
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Package\Stock\Purchase;

use \Skeleton\Database\Database;

class Order {

	use \Skeleton\Object\Get;
	use \Skeleton\Pager\Page;
	use \Skeleton\Object\Delete;
	use \Skeleton\Object\Save;
	use \Skeleton\Object\Model;

	private static $class_configuration = [
		'database_table' => 'purchase_order',
	];

	/**
	 * Get price
	 *
	 * @access public
	 * @return double $price
	 */
	public function get_price() {
		$items = $this->get_purchase_order_items();
		$price = 0;
		foreach ($items as $item) {
			$price += $item->amount*$item->price;
		}
		return $price;
	}

	/**
	 * Check delivered
	 *
	 * @access public
	 */
	public function check_delivered() {
		$purchase_order_items = $this->get_purchase_order_items();
		$delivered = true;
		foreach ($purchase_order_items as $purchase_order_item) {
			if ($purchase_order_item->delivered < $purchase_order_item->amount) {
				$delivered = false;
			}
		}
		$this->delivered = $delivered;
		$this->save();
	}

	/**
	 * Get purchase_order_items
	 *
	 * @access public
	 * @return array $purchase_order_items
	 */
	public function get_purchase_order_items() {
		return \Skeleton\Package\Stock\Purchase\Order\Item::get_by_purchase_order($this);
	}
}
