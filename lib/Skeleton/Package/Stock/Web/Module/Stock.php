<?php
/**
 * Module Stock
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Package\Stock\Web\Module;

use \Skeleton\Pager\Web\Pager;
use \Skeleton\Core\Web\Template;
use \Skeleton\Core\Web\Session;
use \Skeleton\Package\Crud\Web\Module\Crud;

class Stock extends Crud {

	/**
	 * Login required ?
	 * Default = yes
	 *
	 * @access public
	 * @var bool $login_required
	 */
	public $login_required = false;

	/**
	 * Template to use
	 *
	 * @access public
	 * @var string $template
	 */
	public $template = '@skeleton-package-stock\content.twig';

	public function display() {
		parent::display();

		/**
		 * Initialize the template object
		 */
		$template = Template::Get();
		$pager = $this->get_pager();

	}

	public function display_edit() {
		parent::display_edit();
		$template = Template::get();

		$classname = \Skeleton\Package\Stock\Config::$object_stock_interface;
		$product = $classname::get_by_id($_GET['id']);

		if (isset($_POST['product_supplier_id'])) {
			try {
				$product_supplier = \Skeleton\Package\Stock\Supplier::get_by_id($_POST['product_supplier_id']);
				\Skeleton\Package\Stock\Supplier::set_for_object($product, $product_supplier);
			} catch (\Exception $e) {
				$product_supplier = null;
				\Skeleton\Package\Stock\Supplier::set_for_object($product);
			}

			Session::set_sticky('updated', true);
			Session::redirect($this->get_module_path() . '?action=edit&id=' . $product->id);
		}

		$suppliers = \Skeleton\Package\Stock\Supplier::get_all();
		$template->assign('suppliers', $suppliers);

		$supplier = \Skeleton\Package\Stock\Supplier::get_for_object($product);
		$template->assign('product_supplier', $supplier);

		try {
			$stock = \Skeleton\Package\Stock\Stock::get_last_by_object($product);
			$template->assign('stock', $stock->total);
		} catch (\Exception $e) {
			$template->assign('stock', 0);
		}
		$backorder = \Skeleton\Package\Stock\Purchase\Order\Item::count_backorder($product);
		$template->assign('backorder', $backorder);


		$pager = new Pager('\Skeleton\Package\Stock\Stock');
		$pager->add_sort_permission('created');
		$pager->add_sort_permission('id');
		$pager->add_condition('stock_object_id', $product->id);
		$pager->set_sort('id');
		$pager->set_direction('desc');
		$pager->page();

		$template->assign('pager', $pager);
	}

	/**
	 * Get pager
	 *
	 * @access public
	 * @return Skeleton\Pager\Web\Pager $pager
	 */
	public function get_pager() {
		$pager = new Pager(\Skeleton\Package\Stock\Config::$object_stock_interface);
		$pager->add_sort_permission('id');
		return $pager;
	}

	/**
	 * Add a movement
	 *
	 * @access public
	 */
	public function display_add_movement() {
		$classname = \Skeleton\Package\Stock\Config::$object_stock_interface;
		$product = $classname::get_by_id($_GET['id']);
		\Skeleton\Package\Stock\Stock::change($product, $_POST['stock_movement']['movement'], $_SESSION['user'], $_POST['stock_movement']['comment']);

		Session::redirect('/' . $this->get_path() . '?action=edit&id=' . $product->id);
	}

}
