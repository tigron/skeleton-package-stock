<?php
/**
 * Module Stock
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Package\Stock\Web\Module\Purchase;

use \Skeleton\Pager\Web\Pager;
use \Skeleton\Core\Web\Template;
use \Skeleton\Core\Web\Session;
use \Skeleton\Package\Crud\Web\Module\Crud;
use \Skeleton\Package\Stock\Supplier;

class Order extends Crud {

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

	/**
	 * Create
	 *
	 * @access public
	 */
	public function display_create() {
		$template = Template::get();

		$suppliers = Supplier::get_all();
		$template->assign('suppliers', $suppliers);
	}

	/**
	 * Create step 2
	 *
	 * @access public
	 */
	public function display_create_step2() {
		if (!isset($_POST['supplier_id']) AND !isset($_SESSION['skeleton-package-stock']['po']['supplier_id'])) {
			Session::redirect($this->get_module_path() . '?action=create');
		}

		if (!isset($_SESSION['skeleton-package-stock']['po'])) {
			$_SESSION['skeleton-package-stock']['po'] = [];
		}
		if (isset($_POST['supplier_id'])) {
			$_SESSION['skeleton-package-stock']['po']['supplier_id'] = $_POST['supplier_id'];
			unset($_SESSION['skeleton-package-stock']['po']['items']);
			$supplier = Supplier::get_by_id($_SESSION['skeleton-package-stock']['po']['supplier_id']);
			$purchase_list = $supplier->get_purchase_list();

			foreach ($purchase_list as $item) {
				$this->add_to_basket($item, 1, $item->get_purchase_price());
			}
		}

		$supplier = Supplier::get_by_id($_SESSION['skeleton-package-stock']['po']['supplier_id']);

		$classname = \Skeleton\Package\Stock\Config::$object_stock_interface;
		$pager = new Pager($classname);
		$pager->add_join('object_supplier', 'object_id', $classname::trait_get_database_table() . '.id');
		$pager->add_condition('object_supplier.supplier_id', $supplier->id);
		$pager->add_condition('object_supplier.object_classname', $classname);

		$fields = $classname::get_object_fields();
		$search_fields = [];
		foreach ($fields as $field) {
			if (substr($field['field'], -3) == '_id') {
				continue;
			}
			if ($field['field'] == 'id') {
				continue;
			}
			$search_fields[] = $field['field'];
		}

		if (isset($_POST['search'])) {
			$pager->set_search($_POST['search'], $search_fields);
		}

		$pager->page();

		$template = Template::get();
		$template->assign('supplier', $supplier);
		$template->assign('pager', $pager);
	}

	/**
	 * Create step 3
	 *
	 * @access public
	 */
	public function display_create_step3() {
		$purchase_order = new \Skeleton\Package\Stock\Purchase\Order();
		$purchase_order->supplier_id = $_SESSION['skeleton-package-stock']['po']['supplier_id'];
		$purchase_order->save();

		foreach ($_SESSION['skeleton-package-stock']['po']['items'] as $item) {
			$purchase_order_item = new \Skeleton\Package\Stock\Purchase\Order\Item();
			$purchase_order_item->purchase_order_id = $purchase_order->id;
			$purchase_order_item->stock_object_classname = $item['object_classname'];
			$purchase_order_item->stock_object_id = $item['object_id'];
			$purchase_order_item->price = $item['price'];
			$purchase_order_item->amount = $item['count'];
			$purchase_order_item->save();
		}
		Session::redirect($this->get_module_path());
	}

	/**
	 * Change comments
	 *
	 * @access public
	 */
	public function display_change_comments() {
		$template = Template::get();

		$purchase_order = \Skeleton\Package\Stock\Purchase\Order::get_by_id($_GET['id']);
		$purchase_order->load_array($_POST['object']);
		$purchase_order->save();

		Session::redirect($this->get_module_path() . '?action=edit&id=' . $purchase_order->id);
	}

	/**
	 * Download PDF
	 *
	 * @access public
	 */
	public function display_download_excel() {
		$purchase_order = \Skeleton\Package\Stock\Purchase\Order::get_by_id($_GET['id']);
		$purchase_order->export();
	}

	/**
	 * add_delivery
	 *
	 * @access public
	 */
	public function display_add_delivery() {
		$purchase_order = \Skeleton\Package\Stock\Purchase\Order::get_by_id($_GET['id']);

		if (isset($_POST['purchase_order'])) {
			$purchase_order->load_array($_POST['purchase_order']);
			$purchase_order->save();
		}

		if (isset($_POST['delivery'])) {
			foreach ($_POST['delivery'] as $purchase_order_item_id => $amount) {
				$purchase_order_item = \Skeleton\Package\Stock\Purchase\Order\Item::get_by_id($purchase_order_item_id);
				$purchase_order_item->deliver($amount, $purchase_order, 'Delivery for PO' . $purchase_order->id);
			}
		}
		Session::redirect($this->get_module_path() . '?action=edit&id=' . $purchase_order->id);
	}

	/**
	 * Add to basket
	 *
	 * @access public
	 */
	public function display_add_to_basket() {
		$classname = $_POST['object_classname'];
		$class = $classname::get_by_id( $_POST['object_id'] );
		$this->add_to_basket($class, $_POST['count'], $_POST['price']);
	}

	/**
	 * Add a product to a basket
	 *
	 * @access private
	 * @param Product $product
	 * @param int $count
	 */
	public function add_to_basket(\Skeleton\Package\Stock\Object $object, $count, $price) {

		if (!isset($_SESSION['skeleton-package-stock']['po']['items'])) {
			$_SESSION['skeleton-package-stock']['po']['items'] = [];
		}
		$classname = get_class($object);
		if (!isset($_SESSION['skeleton-package-stock']['po']['items'][ $classname . '_' . $object->id ])) {
			$_SESSION['skeleton-package-stock']['po']['items'][ $classname . '_' . $object->id ] = [
				'price' => $price,
				'count' => 0,
				'name' => $object->get_name(),
				'object_classname' => $classname,
				'object_id' => $object->id,
			];
		}

		$_SESSION['skeleton-package-stock']['po']['items'][$classname . '_' . $object->id]['count'] += $count;
		$_SESSION['skeleton-package-stock']['po']['items'][$classname . '_' . $object->id]['price'] = $price;
	}

	/**
	 * Remove from basket
	 *
	 * @access public
	 */
	public function display_remove_from_basket() {
		unset($_SESSION['skeleton-package-stock']['po']['items'][ $_POST['object_classname'] . '_' . $_POST['object_id']]);
	}

	/**
	 * Load basket
	 *
	 * @access public
	 */
	public function display_load_basket() {
		$this->template = "@skeleton-package-stock\purchase_order\basket.twig";
	}

	/**
	 * Get pager
	 *
	 * @access public
	 * @return Skeleton\Pager\Web\Pager $pager
	 */
	public function get_pager() {
		$pager = new Pager('\Skeleton\Package\Stock\Purchase\Order');
		$pager->add_sort_permission('id');
		$pager->add_sort_permission('company');
		$pager->add_sort_permission('email');

		if (isset($_POST['delivered'])) {
			if ($_POST['delivered'] == 1) {
				$pager->add_condition('purchase_order.delivered', 1);
			} elseif ($_POST['delivered'] == 0) {
				$pager->add_condition('purchase_order.delivered', 0);
			} else {
				$pager->clear_condition('purchase_order.delivered');
			}
		}


		$pager->set_sort('id');
		$pager->set_direction('desc');
		return $pager;
	}

}
