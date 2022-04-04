<?php

/**
 * @package DiscountsManager
 */

/*
Plugin Name: Bexeo Plugin
Plugin URI: https://bexeo.com/
Description: A custom discounts plugin for WooCommerce.
Version: 1.0.0
Author: Bexeo GmbH
Author URI: https://bexeo.com/
License: GPLv2 or later
*/

class BexeoPlugin
{
    function __construct()
    {
        // add_action('init', [$this, 'custom_post_type']);
        $this->createSQLSchema();
    }

    function register()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
        add_action('admin_menu', [$this, 'add_admin_pages']);
        add_action('rest_api_init', [$this, 'init_rest_api']);
    }

    function init_rest_api()
    {
        $restApi = new RestApi();
    }

    function add_admin_pages()
    {
//        add_menu_page('Manage Discounts', 'Manage Discounts', 'manage_options', 'discounts_manager', [$this, 'admin_template'], '', 110);
//        add_menu_page('Manage Media', 'Manage Media', 'manage_options', 'manage_media', [$this, 'media_manager_template'], '', 110);
        if(User::user_has_role(get_current_user_id(), 'administrator') || User::user_has_role(get_current_user_id(), 'shop_manager')){
            add_menu_page('Custom Orders', 'Custom Orders', 'read', 'custom-orders', [$this, 'custom_orders_template'], '', 110);
        }
    }

    function custom_orders_template()
    {
        require_once plugin_dir_path(__FILE__) . '/templates/custom-order.php';
    }

    function admin_template()
    {
        require_once plugin_dir_path(__FILE__) . '/templates/index.php';
    }

    function media_manager_template()
    {
        require_once plugin_dir_path(__FILE__) . '/templates/media-manager.php';
    }

    function activate()
    {
        $this->custom_post_type();
        flush_rewrite_rules();
    }

    function deactivate()
    {
        global $wpdb;
        $wpdb->query('delete from wp_posts where post_type = "book"');
        $wpdb->query('delete from wp_postmeta where post_id not in (select id from wp_posts)');
        $wpdb->query('delete from wp_term_relationships where object_id not in (select id from wp_posts)');
        $wpdb->query('drop table wp_bex_discount_details');
        $wpdb->query('drop table wp_bex_discounts');
        $wpdb->query('drop table wp_bex_activatecodes');
    }

    function custom_post_type()
    {
        register_post_type('partners', [
            'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
            'publicly_queryable' => true,  // you should be able to query it
            'show_ui' => true,  // you should be able to edit it in wp-admin
            'exclude_from_search' => true,  // you should exclude it from search results
            'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
            'has_archive' => false,  // it shouldn't have archive page
            'rewrite' => false,  // it shouldn't have rewrite rules
            'label' => 'Partners',
            'supports' => array('title')
        ]);
    }

    function enqueue()
    {
        wp_enqueue_style('bootstrap-style', plugins_url('/assets/bootstrap-5.0.2-dist/css/bootstrap.min.css', __FILE__));
        wp_enqueue_style('airdatepicker-style', plugins_url('/assets/air-datepicker/air-datepicker.min.css', __FILE__));
        wp_enqueue_style('fontawesome-style', plugins_url('/assets/fontawesome/css/all.css', __FILE__));
        wp_enqueue_style('discount-manager-style', plugins_url('/assets/app.css', __FILE__));
//        wp_enqueue_script('fontawesome-script', plugins_url('/assets/fontawesome/js/all.js', __FILE__));
        wp_enqueue_script('airdatepicker-script', plugins_url('/assets/air-datepicker/air-datepicker.min.js', __FILE__));
        wp_enqueue_script('airdatepicker-de', plugins_url('/assets/air-datepicker/i18n/datepicker.de.js', __FILE__));
        wp_enqueue_script('bootstrap-script', plugins_url('/assets/bootstrap-5.0.2-dist/js/bootstrap.min.js', __FILE__));
        wp_enqueue_script('sweetalert-script', plugins_url('/assets/sweetalert2/sweetalert2.min.js', __FILE__));
        wp_enqueue_script('discount-manager-script', plugins_url('/assets/app.js', __FILE__));
    }

    function createSQLSchema()
    {
        global $wpdb;

        // create discounts
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}bex_discounts(
                    id int not null primary key auto_increment,
                    role varchar(64)
                );");

        // create discounts details
        $wpdb->query("create table if not exists {$wpdb->prefix}bex_discount_details(
                    id int not null primary key auto_increment,
                    orders_from int not null,
                    orders_to int not null,
                    discount int not null,
                    discount_id int not null,
                    constraint `fk_discounts_discount_info` foreign key (discount_id) references wp_bex_discounts(id) on delete cascade
                );");

        $wpdb->query("
            create table if not exists {$wpdb->prefix}bex_activationcodes(
                    id int not null primary key auto_increment,
                    code varchar(30) not null unique,
                    product_id int not null,
                    user_id int null,
                    date_used timestamp null,
                    used boolean default false
        );");
    }
}

if (class_exists('BexeoPlugin')) {
    $plugin = new BexeoPlugin;
    $plugin->register();
}

// activation
register_activation_hook(__FILE__, [$plugin, 'activate']);

// deactivation
register_deactivation_hook(__FILE__, [$plugin, 'deactivate']);

/**
 * autoload classes
 */
spl_autoload_register(function ($class_name) {
    $file_path = plugin_dir_path(__FILE__) . "/classes/" . $class_name . ".php";
    if (file_exists($file_path)) {
        require_once $file_path;
    }
});
