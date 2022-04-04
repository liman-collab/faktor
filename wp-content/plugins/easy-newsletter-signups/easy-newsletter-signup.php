<?php
/**
 * Plugin Name: Easy Newsletter Signups
 * Plugin URI: https://wordpress.org/plugins/easy-newsletter-signups
 * Description: Easy Newsletter Signups is easy to use and light weight newsletter plugin. it allow you to collect subscribers by simple put a newsletter signup or email subscribe form onto your WordPress site.
 * Version: 1.0.3
 * Author: AlphaBPO
 * Author URI: http://www.alphabpo.com
 * Text Domain: wpens
 * Domain Path: languages
 *
 * License: GPLv2 or later
 * Domain Path: languages
 *
 * @package Easy Newsletter Signups
 * @category Core
 * @author Alpha BPO
 */

// Create a helper function for easy SDK access.
function ens_fs() {
   global $ens_fs;

   if ( ! isset( $ens_fs ) ) {
       // Include Freemius SDK.
       require_once dirname(__FILE__) . '/freemius/start.php';

       $ens_fs = fs_dynamic_init( array(
           'id'                  => '2010',
           'slug'                => 'easy-newsletter-signups',
           'type'                => 'plugin',
           'public_key'          => 'pk_cca706011993de499fdf4b28b119e',
           'is_premium'          => false,
           'has_addons'          => false,
           'has_paid_plans'      => false,
           'menu'                => array(
               'slug'           => 'wpens-list',
               'first-path'     => 'admin.php?page=wpens-list',
               'account'        => false,
               'contact'        => false,
               'support'        => false,
           ),
       ) );
   }

   return $ens_fs;
}

// Init Freemius.
ens_fs();
// Signal that SDK was initiated.
do_action( 'ens_fs_loaded' );

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Basic plugin definitions 
 * 
 * @package Easy Newsletter Signups
 * @since 1.0.0
 */
if( !defined( 'WPENS_VERSION' ) ) {
	define( 'WPENS_VERSION', '1.0.3' ); // plugin version
}
if( !defined( 'WPENS_PLUGIN_DIR' ) ) {
	define( 'WPENS_PLUGIN_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'WPENS_ADMIN_DIR' ) ) {
	define( 'WPENS_ADMIN_DIR', WPENS_PLUGIN_DIR . '/includes/admin' ); // plugin admin dir
}
if( !defined( 'WPENS_PLUGIN_URL' ) ) {
	define( 'WPENS_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}

/**
 * Load Text Domain
 * 
 * Locales found in:
 *   - WP_LANG_DIR/easy-online-booking/wpeob-LOCALE.mo
 *   - WP_LANG_DIR/plugins/wpeob-LOCALE.mo
 * 
 * @package Easy Newsletter Signups
 * @since 1.0.0
 */
function wpens_load_plugin_textdomain() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'wpens' );

	load_textdomain( 'wpens', WP_LANG_DIR . '/easy-newsletter-signups/wpens-' . $locale . '.mo' );
	load_plugin_textdomain( 'wpens', false, WPENS_PLUGIN_DIR . '/languages' );
}
add_action( 'load_plugins', 'wpens_load_plugin_textdomain' );

/**
 * Activation hook
 * 
 * Register plugin activation hook.
 * 
 * @package Easy Newsletter Signups
 * @since 1.0.0
 */

register_activation_hook( __FILE__, 'wpens_plugin_install' );

/**
 * Deactivation hook
 *
 * Register plugin deactivation hook.
 * 
 * @package Easy Newsletter Signups
 * @since 1.0.0
 */

register_deactivation_hook( __FILE__, 'wpens_plugin_uninstall' );

/**
 * Plugin Setup Activation hook call back 
 *
 * Initial setup of the plugin setting default options 
 * and database tables creations.
 * 
 * @package Easy Newsletter Signups
 * @since 1.0.0
 */
function wpens_plugin_install() {
	
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'ens_subscribers';

	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		first_name varchar(255) NULL,
		last_name varchar(255) NULL,
		email varchar(255) NOT NULL,
		comments text NULL,
		user_ip varchar(100) NULL,
		date timestamp NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

/**
 * Plugin Setup (On Deactivation)
 *
 * Does the drop tables in the database and
 * delete  plugin options.
 *
 * @package Easy Newsletter Signups
 * @since 1.0.0
 */
function wpens_plugin_uninstall() {
	
	global $wpdb;

	/*$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'ens_subscribers';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );*/
}

/**
* Change Footer text for Reviews
 */
add_filter( 'admin_footer_text', 'wpens_remove_footer_admin' );
function wpens_remove_footer_admin() {
	$screen =  get_current_screen();
	if( $screen->id == "toplevel_page_wpens-list" ){
		echo '<span id="footer-thankyou">';
		echo sprintf( __('If you like %1sEasy Newsletter Signups%2s please leave us a %3s★★★★★%4s rating. A huge thanks in advance!', 'wpens'),
			'<strong>', '</strong>',
			'<a href="https://wordpress.org/support/plugin/easy-newsletter-signups/reviews/?rate=5#new-post" target="_blank" class="ens-rating-link">',
			'</a>'
		 );
		echo '</span>';
	}
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wpens_add_action_links' );

function wpens_add_action_links( $links ) {
	$pluginLinks = array(
		'<a target="_blank" style="color: #1f855b;" href="http://visitnetworld.com/our-product/plugin/ens-pro/preview/"><strong>Pro Version</strong></a>',
	);
	return array_merge( $links, $pluginLinks );
}

/**
 * Initialize all global variables
 * 
 * @package Easy Newsletter Signups
 * @since 1.0.0
 */

global $wpens_scripts,$wpens_admin,$wpens_newsletter;


//Includes all scripts class file
require_once( WPENS_PLUGIN_DIR . '/includes/class-wpens-scripts.php');

//Includes shortcode class file
require_once ( WPENS_PLUGIN_DIR . '/includes/class-wpens-shortcodes.php');

//Includes public class file
require_once ( WPENS_PLUGIN_DIR . '/includes/class-wpens-public.php');

//Includes Admin file
require_once ( WPENS_ADMIN_DIR . '/class-wpens-admin.php');