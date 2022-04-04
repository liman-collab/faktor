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
 * Abstract webhook processor for order related entities.
 */
abstract class WC_Wallee_Webhook_Order_Related_Abstract extends WC_Wallee_Webhook_Abstract {

	/**
	 * Processes the received order related webhook request.
	 *
     * @param WC_Wallee_Webhook_Request $request
     * @throws Exception
     */
    public function process(WC_Wallee_Webhook_Request $request){

        WC_Wallee_Helper::instance()->start_database_transaction();
		$entity = $this->load_entity($request);
		try {
		    WC_Wallee_Helper::instance()->lock_by_transaction_id($request->get_space_id(), $this->get_transaction_id($entity));
			$order = WC_Order_Factory::get_order($this->get_order_id($entity));
			if ($order !== false && $order->get_id()) {
				$this->process_order_related_inner($order, $entity);
			}
			WC_Wallee_Helper::instance()->commit_database_transaction();
		}
		catch (Exception $e) {
		    WC_Wallee_Helper::instance()->rollback_database_transaction();
			throw $e;
		}
	}

	/**
	 * Loads and returns the entity for the webhook request.
	 *
	 * @param WC_Wallee_Webhook_Request $request
	 * @return object
	 */
	abstract protected function load_entity(WC_Wallee_Webhook_Request $request);

	/**
	 * Returns the order's increment id linked to the entity.
	 *
	 * @param object $entity
	 * @return string
	 */
	abstract protected function get_order_id($entity);

	/**
	 * Returns the transaction's id linked to the entity.
	 *
	 * @param object $entity
	 * @return int
	 */
	abstract protected function get_transaction_id($entity);

	/**
	 * Actually processes the order related webhook request.
	 *
	 * This must be implemented
	 *
	 * @param WC_Order $order
	 * @param Object $entity
	 */
	abstract protected function process_order_related_inner(WC_Order $order, $entity);
}