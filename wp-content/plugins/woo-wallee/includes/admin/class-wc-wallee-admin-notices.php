<?php
if (!defined('ABSPATH')) {
	exit();
}
/**
 * wallee WooCommerce
 *
 * This WooCommerce plugin enables to process payments with wallee (https://www.wallee.com).
 *
 * @author wallee AG (http://www.wallee.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache Software License (ASL 2.0)
 */
/**
 * WC Wallee Admin Notices class
 */
class WC_Wallee_Admin_Notices {

	public static function init(){
		add_action('admin_notices', array(
			__CLASS__,
			'manual_tasks_notices' 
		));
		add_action('admin_notices', array(
		    __CLASS__,
		    'round_subtotal_notices'
		));
	}

	public static function manual_tasks_notices(){
	    $number_of_manual_tasks = WC_Wallee_Service_Manual_Task::instance()->get_number_of_manual_tasks();
		if ($number_of_manual_tasks == 0) {
			return;
		}
		$manual_taks_url = self::get_manual_tasks_url();
		require_once WC_WALLEE_ABSPATH.'/views/admin-notices/manual-tasks.php';
	}

	/**
	 * Returns the URL to check the open manual tasks.
	 *
	 * @return string
	 */
	protected static function get_manual_tasks_url(){
	    $manual_task_url = WC_Wallee_Helper::instance()->get_base_gateway_url();
	    $space_id = get_option(WooCommerce_Wallee::CK_SPACE_ID);
		if (!empty($space_id)) {
			$manual_task_url .= '/s/' . $space_id . '/manual-task/list';
		}
		
		return $manual_task_url;
	}

	public static function round_subtotal_notices(){
	    $screen = get_current_screen();
	    if($screen->id == 'woocommerce_page_wc-settings'){
            if ( wc_tax_enabled() && ('yes' === get_option( 'woocommerce_tax_round_at_subtotal' ))){
                if('yes' === get_option( WooCommerce_Wallee::CK_ENFORCE_CONSISTENCY )) {
                    $errorMessage = __("'WooCommerce > Settings > Wallee > Enforce Consistency' and 'WooCommerce > Settings > Tax > Rounding' are both enabled. Please disable at least one of them.", 'woo-wallee');
                    WooCommerce_Wallee::instance()->log($errorMessage, WC_Log_Levels::ERROR);
                    require_once WC_WALLEE_ABSPATH.'/views/admin-notices/round-subtotal-warning.php';
                }
            }
	    }
	}
	
	public static function migration_failed_notices(){
	    require_once WC_WALLEE_ABSPATH.'views/admin-notices/migration-failed.php';
	}
	
	public static function plugin_deactivated(){
	    require_once WC_WALLEE_ABSPATH.'views/admin-notices/plugin-deactivated.php';
	}
}
WC_Wallee_Admin_Notices::init();
