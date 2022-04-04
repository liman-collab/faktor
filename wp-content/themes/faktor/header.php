<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2.0">
    <meta name="theme-color" content="#E4021C">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php do_action('storefront_before_site'); ?>

<div id="page" class="hfeed site">
    <?php do_action('storefront_before_header'); ?>

    <header id="masthead" class="site-header" role="banner" style="<?php storefront_header_styles(); ?>">

        <div class="col-full">
            <a class="skip-link screen-reader-text" href="#site-navigation">Skip to navigation</a>
            <a class="skip-link screen-reader-text" href="#content">Skip to content</a>
            <?php storefront_site_branding(); ?>
<!--            <div class="site-search">-->
<!--                <div class="widget woocommerce widget_product_search">-->
<!--                    --><?php //get_product_search_form(); ?>
<!--                </div>-->
<!--            </div>-->
        </div>
<!--        <div class="storefront-primary-navigation mobile-show">-->
<!--            <div class="col-full">-->
<!--                --><?php //storefront_primary_navigation(); ?>
<!--                --><?php //storefront_header_cart(); ?>
<!--            </div>-->
<!--        </div>-->

    </header><!-- #masthead -->

    <?php
    /**
     * Functions hooked in to storefront_before_content
     *
     * @hooked storefront_header_widget_region - 10
     * @hooked woocommerce_breadcrumb - 10
     */
    do_action('storefront_before_content');
    ?>

    <div id="content" class="site-content" tabindex="-1">
        <div class="col-full">

<?php
do_action('storefront_content_top');
