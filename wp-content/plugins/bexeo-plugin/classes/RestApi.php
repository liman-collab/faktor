<?php

class RestApi
{
    public function __construct($api = 'api')
    {
        register_rest_route($api, 'save-custom-order', ['methods' => WP_REST_Server::CREATABLE, 'callback' => [$this, 'save_custom_order'], 'permission_callback' => '__return_true']);
        register_rest_route($api, 'product-variations', ['methods' => WP_REST_Server::READABLE, 'callback' => [$this, 'get_product_variations'], 'permission_callback' => '__return_true']);
    }

    public function save_custom_order()
    {
        $order = Orders::createWCOrder($_POST['products'], $_POST['shipping_post']);

        $order_id = $order->get_id();

        /**
         * billing fields
         */
        add_post_meta($order_id, '_billing_first_name', $_POST["billing_first_name"], true);
        add_post_meta($order_id, '_billing_last_name', $_POST["billing_last_name"], true);
        add_post_meta($order_id, '_billing_address_1', $_POST["billing_address_1"], true);
        if (isset($_POST['billing_address_2'])) {
            add_post_meta($order_id, '_billing_address_2', $_POST["billing_address_2"], true);
        }
        add_post_meta($order_id, '_billing_city', $_POST["billing_city"], true);
        add_post_meta($order_id, '_billing_state', $_POST["billing_state"], true);
        add_post_meta($order_id, '_billing_postcode', $_POST["billing_postcode"], true);
        add_post_meta($order_id, '_billing_company', $_POST["billing_company"], true);
        add_post_meta($order_id, '_billing_country', $_POST["billing_country"], true);
        add_post_meta($order_id, '_billing_email', $_POST["billing_email"], true);
        add_post_meta($order_id, '_billing_phone', $_POST["billing_phone"], true);

        if ($_POST['use_diff_shipping'] === 'true') {
            add_post_meta($order_id, '_shipping_first_name', $_POST["shipping_first_name"], true);
            add_post_meta($order_id, '_shipping_last_name', $_POST["shipping_last_name"], true);
            add_post_meta($order_id, '_shipping_address_1', $_POST["shipping_address_1"], true);
            if (isset($_POST['shipping_address_2'])) {
                add_post_meta($order_id, '_shipping_address_2', $_POST["shipping_address_2"], true);
            }
            add_post_meta($order_id, '_shipping_city', $_POST["shipping_city"], true);
            add_post_meta($order_id, '_shipping_state', $_POST["shipping_state"], true);
            add_post_meta($order_id, '_shipping_postcode', $_POST["shipping_postcode"], true);
            add_post_meta($order_id, '_shipping_company', $_POST["shipping_company"], true);
            add_post_meta($order_id, '_shipping_country', $_POST["shipping_country"], true);
            add_post_meta($order_id, '_shipping_email', $_POST["shipping_email"], true);
            add_post_meta($order_id, '_shipping_phone', $_POST["shipping_phone"], true);
        }

        new_order($order_id);
        new_order_request($order_id, $_POST);

        wp_send_json_success(['order_id' => $order_id]);
    }

    function get_product_variations()
    {
        $product = wc_get_product($_GET['product_id']);
        $availVariations = $product->get_available_variations();
        $variations_id = wp_list_pluck($availVariations, 'variation_id');
        $variations = [];

        foreach ($variations_id as $id) {
            $variation = wc_get_product($id);
            $variations[] = [
                'name' => explode(' - ', $variation->get_name())[1],
                'id' => $id,
                'price' => $variation->get_price()
            ];
        }

        wp_send_json_success(['variations' => $variations]);
    }
}