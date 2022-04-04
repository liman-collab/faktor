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
 * This exception indicating an error with the transaction amount
 *
 * @author Nico Eigenmann
 */
class WC_Wallee_Exception_Invalid_Transaction_Amount extends Exception
{

    private $item_total;
    private $order_total;
    
    public function __construct ($item_total, $order_total) {
        parent::__construct("The item total '".$item_total."' does not match the order total '".$order_total."'.");
        $this->item_total = $item_total;
        $this->order_total = $order_total;
    }

    public function get_item_total(){
        return $this->item_total;
    }
    
    public function get_order_total(){
        return $this->order_total;
    }
    
}
