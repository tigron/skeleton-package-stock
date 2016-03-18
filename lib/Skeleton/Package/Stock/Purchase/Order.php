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
	 * get supplier
	 *
	 * @access public
	 * @return \Skeleton\Package\Stock\Supplier $supplier
	 */
	public function get_supplier() {
		return \Skeleton\Package\Stock\Supplier::get_by_id($this->supplier_id);
	}

	/**
	 * Export
	 *
	 * @access public
	 */
	public function export() {
		$excel = new \PHPExcel();
		$worksheet = $excel->getActiveSheet();

		$row = 1;

		$worksheet->setCellValueByColumnAndRow(0, $row, 'Product');
		$worksheet->setCellValueByColumnAndRow(1, $row, 'price');
		$worksheet->setCellValueByColumnAndRow(2, $row, 'amount');
		$worksheet->setCellValueByColumnAndRow(3, $row, 'Total price');

		$excel->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);

		foreach ($this->get_purchase_order_items() as $purchase_order_item) {
			$row++;

			$worksheet->setCellValueByColumnAndRow(0, $row, $purchase_order_item->get_stock_object()->get_name());
			$worksheet->setCellValueByColumnAndRow(1, $row, $purchase_order_item->price);
			$worksheet->setCellValueByColumnAndRow(2, $row, $purchase_order_item->amount);
			$worksheet->setCellValueByColumnAndRow(3, $row, $purchase_order_item->price*$purchase_order_item->amount);
		}
		$row++;

		$worksheet->setCellValueByColumnAndRow(3, $row, $this->get_price());
		$excel->getActiveSheet()->getStyle($row . ':' . $row)->getFont()->setBold(true);


		$cellIterator = $worksheet->getRowIterator()->current()->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(true);
		/** @var PHPExcel_Cell $cell */
		foreach ($cellIterator as $cell) {
		    $worksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
		}

		$writer = new \PHPExcel_Writer_Excel2007($excel);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="purchase_order_' . $this->id . '.xls"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		exit;
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
