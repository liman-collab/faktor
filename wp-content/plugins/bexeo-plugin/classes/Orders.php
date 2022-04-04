<?php

class Orders
{
    static function deleteWCOrders($ids){
        foreach($ids as $id){
            wp_delete_post($id, true);
        }
    }

    static function createWCOrder($products, $shippingPost){
        $metas = [];
        $order_userid = get_current_user_id();
        // build order data
        $order_data = array(
            'post_name' => 'order-' . time(), //'order-jun-19-2014-0648-pm'
            'post_type' => 'shop_order',
            'post_title' => 'Order &ndash; ' . time(), //'June 19, 2014 @ 07:19 PM'
            'post_status' => 'wc-processing',
            'ping_status' => 'Processing',
            'post_excerpt' => '',
            'post_author' => $order_userid,
            'post_password' => uniqid('order_'),   // Protects the post just in case
            'post_date' => date('Y-m-d H:i:s e'), //'order-jun-19-2014-0648-pm'
            'comment_status' => 'open'
        );

        // create order
        $order_id = wp_insert_post($order_data, true);

        $order = wc_get_order($order_id);

        $i = 0;
        foreach($products as $productObj){
            $id = !empty($productObj['variation_id']) ? $productObj['variation_id'] : $productObj['product_id'];
            $product = wc_get_product($id);
            $product->set_price((float)$productObj['price']);

            $terms = get_the_terms( $product->get_id(), 'pa_order-type' );

            $metas[$i++] = !empty($productObj['variation_name']) ? $productObj['variation_name'] : $terms[0]->name;

            // Add the product to the order
            $order->add_product($product, (int)$productObj['qty']);
        }

        $i = 0;
        foreach($order->get_items() as $key => $item){
            $item->add_meta_data('Format', $metas[$i++]);
        }

        $order->calculate_totals(); // updating totals

        $order->save(); // Save the order data
        return $order;
    }
}