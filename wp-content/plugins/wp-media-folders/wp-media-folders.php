<?php
/*
  Plugin Name: WP Media folders
  Plugin URI: https://wordpress.org/plugins/wp-media-folders/
  Description: WP media Folders adds the ability to rename and move files under real folders
  Author: Damien Barrère
  Version: 1.1.10
  Text Domain: wp-media-folders
  Domain Path: /languages
  Licence : GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
  Copyright : Copyright (C) 2018 Damien Barrère All right reserved
 */

// Prohibit direct script loading
defined('ABSPATH') || die('No direct script access allowed!');

define('WP_MEDIA_FOLDERS_VERSION', '1.1.10');

// This functionality is only needed on backend
if (!is_admin()) {
    return;
}

//Check plugin requirements
if (version_compare(PHP_VERSION, '5.4', '<')) {
    if (!function_exists('wp_media_folders_disable_plugin')) {
        /**
         * Disable the plugin
         *
         * @return void
         */
        function wp_media_folders_disable_plugin()
        {
            if (current_user_can('activate_plugins') && is_plugin_active(plugin_basename(__FILE__))) {
                deactivate_plugins(__FILE__);
                unset($_GET['activate']);
            }
        }
    }

    if (!function_exists('wp_media_folders_show_error')) {
        /**
         * Display erros
         *
         * @return void
         */
        function wp_media_folders_show_error()
        {
            echo '<div class="error"><p><strong>WP Media Folders</strong> need at least PHP 5.4 version, please update php before installing the plugin.</p></div>';
        }
    }

    //Add actions
    add_action('admin_init', 'wp_media_folders_disable_plugin');
    add_action('admin_notices', 'wp_media_folders_show_error');

    //Do not load anything more
    return;
}

// Show disclaimer if not already accepted
global $pagenow;

if (!get_option('wp-media-folders-disclaimer-confirmed', false) && $pagenow !== 'options-general.php') {
    add_action(
        'admin_notices',
        function () {
            if (current_user_can('manage_options')) {
                echo '<div class="error">'
                    . '<p>'
                    . esc_html__('Thanks for having installed WP Media Folders!', 'wp-media-folders').'<br/>'
                    . '<b>' . esc_html__('Please read the instruction carefuly to understand how the plugin works and what it does', 'wp-media-folders').'</b>&nbsp;'
                    . '<a href="'.admin_url('options-general.php?page=wp-media-folders-settings').'" class="button button-primary">'
                    . esc_html__('Read disclaimer', 'wp-media-folders').'</a>'
                    . '</p>'
                    . '</div>';
            }
        },
        3
    );

    add_action('wp_ajax_wpmfs_disclaimer', function () {
        check_ajax_referer('wpmfs_nonce', 'nonce');
        update_option('wp-media-folders-disclaimer-confirmed', true);
    });
}

require_once 'classes' . DIRECTORY_SEPARATOR . 'wp-media-folders.php';
require_once 'classes' . DIRECTORY_SEPARATOR . 'helper.php';
require_once 'classes' . DIRECTORY_SEPARATOR . 'queue.php';

new WPMediaFolders(__FILE__);
