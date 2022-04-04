<?php

/*
 * @package DiscountsManager
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// clear the database
//$books = get_posts(['post_type' => 'book', 'number_posts' => -1]);
//foreach($books as $book){
//    wp_delete_post($book->ID, false);
//}

// use $wpdb to exec sql directly
global $wpdb;
$wpdb->query('delete from ' . $wpdb->prefix . 'posts where post_type = "book"');
$wpdb->query('delete from ' . $wpdb->prefix . 'postmeta where post_id not in (select id from ' . $wpdb->prefix . 'posts)');
$wpdb->query('delete from ' . $wpdb->prefix . 'term_relationships where object_id not in (select id from ' . $wpdb->prefix . 'posts)');
$wpdb->query('drop table ' . $wpdb->prefix . 'bex_discounts');
$wpdb->query('drop table ' . $wpdb->prefix . 'bex_discount_details');
$wpdb->query('drop table ' . $wpdb->prefix . 'bex_activationcodes');