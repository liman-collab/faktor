<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<style>
    table[id*='template_header']{
        background-color: #e5323d !important;
    }

    h2 {
        color: #636363 !important;
    }

    a {
        color: #15c !important;
    }
</style>
    <!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
        <title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
    </head>
    <body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
<div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
    <tr>
    <td align="center" valign="top">
    <div id="template_header_image">
        <?php
        if ( $img = get_option( 'woocommerce_email_header_image' ) ) {
            echo '<p style="margin-top:0;"><img src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name', 'display' ) . '" /></p>';
        }
        ?>
    </div>
    <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container">
    <tr>
        <td align="center" valign="top">
            <!-- Header -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header">
                <tr>
                    <td id="header_wrapper">
                        <div>
                            <img style="width: 100px;margin-bottom: 15px;" src="<?php echo get_template_directory_uri() ?>/assets/images/faktor.png" alt="">
                        </div>
                        <h1><?php echo $email_heading; ?></h1>
                    </td>
                </tr>
            </table>
            <!-- End Header -->
        </td>
    </tr>
    <tr>
    <td align="center" valign="top">
    <!-- Body -->
    <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
    <tr>
    <td valign="top" id="body_content">
    <!-- Content -->
    <table border="0" cellpadding="20" cellspacing="0" width="100%">
    <tr>
    <td valign="top">
    <div id="body_content_inner">
<?php
/*
 * @hooked WC_Emails::email_header() Output the email header
 */
//do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Guten Tag %s', 'woocommerce' ), esc_html( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ) ); ?></p>
<?php /* translators: %s: Order number */ ?>
<p><?php printf( ucfirst(esc_html__( 'Just to let you know &mdash; we\'ve received your order #%s, and it is now being processed:', 'woocommerce' )), esc_html( $order->get_order_number() ) ); ?></p>

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
