# skeleton-package-stock

## Description

This library enables stock for products.


## Installation

Installation via composer:

    composer require tigron/skeleton-package-stock

## Howto

Now make sure you implement the following class:

*Skeleton\Package\Stock\Object*

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


Create a module in your application that extends from Skeleton\Package\Stock\Web\Module\Stock

	<?php
	/**
	 * Module Stock
	 *
	 * @author Christophe Gosiau <christophe@tigron.be>
	 * @author Gerry Demaret <gerry@tigron.be>
	 * @author David Vandemaele <david@tigron.be>
	 */

	use Skeleton\Package\Stock\Web\Module\Stock;
	use Skeleton\Core\Web\Template;
	use Skeleton\Pager\Web\Pager;

	class Web_Module_Stock extends Stock {

		/**
		 * The template
		 *
		 * @access public
		 */
		public $template = 'stock.twig';

		/**
		 * Get pager
		 * @access public
		 * @return Pager
		 */
		public function get_pager() {
			$pager = new Pager(\Skeleton\Package\Stock\Config::$object_stock_interface);
			$pager->add_sort_permission('id');
			return $pager;
		}

	}

Create a template for your module that injects the generated templates into your layout

	{% extends "_default/layout.base.twig" %}


	{% block header_js %}
		{% embed "@skeleton-package-login/javascript.twig" %}{% endembed %}
	{% endblock header_js %}

	{% block header_css %}
		{% embed "@skeleton-package-login/css.twig" %}{% endembed %}
	{% endblock header_css %}

	{% block content %}
		{% embed "@skeleton-package-stock/stock/content.twig" %}{% endembed %}
	{% endblock content %}




Create a module in your application that extends from Skeleton\Package\Stock\Web\Module\Supplier

	<?php
	/**
	 * Module Supplier
	 *
	 * @author Christophe Gosiau <christophe@tigron.be>
	 * @author Gerry Demaret <gerry@tigron.be>
	 * @author David Vandemaele <david@tigron.be>
	 */

	use Skeleton\Package\Stock\Web\Module\Supplier;
	use \Skeleton\Pager\Web\Pager;

	class Web_Module_Supplier extends Supplier {

		/**
		 * The template
		 *
		 * @access public
		 */
		public $template = 'supplier.twig';

	}

Create a template for your module that injects the generated templates into your layout

	{% extends "_default/layout.base.twig" %}

	{% block header_js %}
		{% embed "@skeleton-package-stock/supplier/javascript.twig" %}{% endembed %}
	{% endblock header_js %}

	{% block header_css %}
		{% embed "@skeleton-package-stock/supplier/css.twig" %}{% endembed %}
	{% endblock header_css %}

	{% block content %}
		{% embed "@skeleton-package-stock/supplier/content.twig" %}{% endembed %}
	{% endblock content %}


Create a module in your application that extends from Skeleton\Package\Stock\Web\Module\Purchase\Order

	<?php
	/**
	 * Module Login
	 *
	 * @author Christophe Gosiau <christophe@tigron.be>
	 * @author Gerry Demaret <gerry@tigron.be>
	 * @author David Vandemaele <david@tigron.be>
	 */

	use Skeleton\Package\Stock\Web\Module\Purchase\Order;
	use Skeleton\Core\Web\Template;

	class Web_Module_Purchase_Order extends Order {

		/**
		 * The template
		 *
		 * @access public
		 */
		public $template = 'purchase/order.twig';

	}

Create a template for your module that injects the generated templates into your layout

	{% extends "_default/layout.base.twig" %}

	{% block header_js %}
		{% embed "@skeleton-package-stock/purchase_order/javascript.twig" %}{% endembed %}
	{% endblock header_js %}

	{% block header_css %}
		{% embed "@skeleton-package-stock/purchase_order/css.twig" %}{% endembed %}
	{% endblock header_css %}

	{% block content %}
		{% embed "@skeleton-package-stock/purchase_order/content.twig" %}
		{% endembed %}
	{% endblock content %}
