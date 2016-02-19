<?php
/**
 * Stock object interface
 *
 * @author Gerry Demaret <gerry@tigron.be>
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 */

namespace Skeleton\Package\Stock;

interface Object {

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


}
