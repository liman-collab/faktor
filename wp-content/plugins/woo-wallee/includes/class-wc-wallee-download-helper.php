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
 * This class provides function to download documents from wallee 
 */
class WC_Wallee_Download_Helper {

	/**
	 * Downloads the transaction's invoice PDF document.
	 */
	public static function download_invoice($order_id){
	    $transaction_info = WC_Wallee_Entity_Transaction_Info::load_by_order_id($order_id);
		if (!is_null($transaction_info->get_id()) && in_array($transaction_info->get_state(),
				array(
				    \Wallee\Sdk\Model\TransactionState::COMPLETED,
				    \Wallee\Sdk\Model\TransactionState::FULFILL,
				    \Wallee\Sdk\Model\TransactionState::DECLINE 
				))) {
			
		    $service = new \Wallee\Sdk\Service\TransactionService(WC_Wallee_Helper::instance()->get_api_client());
			$document = $service->getInvoiceDocument($transaction_info->get_space_id(), $transaction_info->get_transaction_id());
			self::download($document);
		}
	}

	/**
	 * Downloads the transaction's packing slip PDF document.
	 */
	public static function download_packing_slip($order_id){
	    $transaction_info = WC_Wallee_Entity_Transaction_Info::load_by_order_id($order_id);
	    if (!is_null($transaction_info->get_id()) && $transaction_info->get_state() == \Wallee\Sdk\Model\TransactionState::FULFILL) {
			
	        $service = new \Wallee\Sdk\Service\TransactionService(WC_Wallee_Helper::instance()->get_api_client());
			$document = $service->getPackingSlip($transaction_info->get_space_id(), $transaction_info->get_transaction_id());
			self::download($document);
		}
	}

	/**
	 * Sends the data received by calling the given path to the browser and ends the execution of the script
	 *
	 * @param string $path
	 */
	public static function download(\Wallee\Sdk\Model\RenderedDocument $document){
		header('Pragma: public');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="' . $document->getTitle() . '.pdf"');
		header('Content-Description: ' . $document->getTitle());
		echo base64_decode($document->getData());
		exit();
	}
}