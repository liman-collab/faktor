<?php

namespace FSProVendor\WPDesk\License\Page;

class LicensePagea {

	private $logo_url = 'assets/images/logo-fs.svg';

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_reminder_script' ) );
		add_action( 'admin_footer', array( $this, 'display_html_element' ) );
	}

	public function enqueue_reminder_script() {
		if ( $this->should_display() ) {
			wp_enqueue_script(
				'wpdesk-activation-reminder',
				plugins_url( 'flexible-shipping-pro/vendor_prefixed/wpdesk/wp-wpdesk-license/src/Page/popup.js' ),
				array(),
				'423',
				true
			);

			wp_enqueue_style(
				'wpdesk-activation-reminder',
				plugins_url( 'flexible-shipping-pro/vendor_prefixed/wpdesk/wp-wpdesk-license/src/Page/popup.css' ),
				array(),
				'423'
			);
		}
	}

	public function display_html_element() {
		if ( $this->should_display() ) {
			$logo_url          = plugins_url( 'flexible-shipping-pro' . '/' . $this->logo_url );
			$cookie_name       = md5( site_url() . 'flexible-shipping-pro' );
			$subscriptions_url = admin_url( 'admin.php?page=wpdesk-licenses' );
			$read_more_url     = 'https://flexibleshipping.com';
			echo "<span class=\"wpdesk-activation-reminder\" data-plugin_title=\"Flexible Shipping PRO\" data-plugin_dir=\"flexible-shipping-pro\" data-logo_url=\"$logo_url\" data-cookie_name=\"$cookie_name\" data-subscriptions_url=\"$subscriptions_url\" data-buy_plugin_url=\"https://flexibleshipping.com/products/flexible-shipping-pro-woocommerce/?utm_source=fs-pro&utm_medium=button&utm_campaign=license-popup\" data-read_more_url=\"$read_more_url\" data-how_to_activate_link=\"https://docs.flexibleshipping.com/article/11-how-to-activate-wp-desk-plugin-subscription/?utm_source=fs-pro&utm_medium=button&utm_campaign=license-popup\"></span>";
		}
	}

	private function should_display() {
		return get_locale() !== 'pl_PL' && ! $this->is_plugin_activated();
	}

	private function is_plugin_activated() {
		return get_option( 'api_flexible-shipping-pro_activated', '' ) === 'Activated';
	}

}

if ( defined( 'ABSPATH' ) ) {
	new LicensePagea();
}
