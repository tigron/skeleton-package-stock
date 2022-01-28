<?php
/**
 * Stock object interface
 *
 * @author Gerry Demaret <gerry@tigron.be>
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 */

namespace Skeleton\Package\Stock;

interface Item {

	/**
	 * Get name
	 *
	 * @access public
	 * @return string $name
	 */
	public function get_name();

	/**
	 * Get purchase price
	 *
	 * @access public
	 * @return int $purchase_price
	 */
	public function get_purchase_price();

	/**
	 * Get stock
	 *
	 * @access public
	 * @return int $stock
	 */
	public function get_stock();

	/**
	 * Check if object has stock
	 *
	 * @access public
	 * @return bool $has_stock
	 */
	public function has_stock();

	/**
	 * Get backorder count
	 *
	 * @access public
	 * @return int $backorder_count
	 */
	public function get_backorder_count();

	/**
	 * Get to deliver count
	 *
	 * @access public
	 * @return int $to_deliver_count
	 */
	public function get_to_deliver_count();

}
