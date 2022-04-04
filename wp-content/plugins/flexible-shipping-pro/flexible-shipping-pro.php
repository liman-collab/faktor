<?php
/**
 * Plugin Name: Flexible Shipping PRO
 * Plugin URI: https://flexibleshipping.com/products/flexible-shipping-pro-woocommerce/?utm_source=fs-pro&utm_medium=link&utm_campaign=plugin-list-page
 * Description: Extends the free version of Flexible Shipping by adding advanced pro features.
 * Version: 2.9.1
 * Author: WP Desk
 * Author URI: https://flexibleshipping.com/?utm_source=fs-pro&utm_medium=link&utm_campaign=plugin-list-author
 * Text Domain: flexible-shipping-pro
 * Domain Path: /lang/
 * Requires at least: 5.2
 * Tested up to: 5.8
 * WC requires at least: 5.4
 * WC tested up to: 5.9
 * Requires PHP: 7.0
 *
 * Copyright 2017 WP Desk Ltd.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package Flexible Shipping Pro
 */

/* THIS VARIABLE CAN BE CHANGED AUTOMATICALLY */
$plugin_version = '2.9.1';

$plugin_name        = 'WooCommerce Flexible Shipping PRO';
$plugin_class_name  = 'WPDesk_Flexible_Shipping_Pro_Plugin';
$plugin_text_domain = 'flexible-shipping-pro';
$product_id         = 'WooCommerce Flexible Shipping PRO';
$plugin_file        = __FILE__;
$plugin_dir         = dirname( __FILE__ );
$plugin_shops       = [
	'pl_PL'   => 'https://www.wpdesk.pl/',
	'default' => 'https://www.flexibleshipping.com',
];

define( 'FLEXIBLE_SHIPPING_PRO_VERSION', $plugin_version );
define( $plugin_class_name, $plugin_version );

$requirements = [
	'php'          => '5.6',
	'wp'           => '4.5',
	'repo_plugins' => [
		[
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
			'version'   => '4.8',
		],
		[
			'name'      => 'flexible-shipping/flexible-shipping.php',
			'nice_name' => 'Flexible Shipping',
			'version'   => '2.1',
		],
	],
];

require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow/src/plugin-init-php52.php';
