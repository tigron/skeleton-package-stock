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

class Supplier extends Crud {

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
	 * Get pager
	 *
	 * @access public
	 * @return Skeleton\Pager\Web\Pager $pager
	 */
	public function get_pager() {
		$pager = new Pager('\Skeleton\Package\Stock\Supplier');
		$pager->add_sort_permission('id');
		$pager->add_sort_permission('company');
		$pager->add_sort_permission('email');
		return $pager;
	}

}
