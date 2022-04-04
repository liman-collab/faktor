<?php

namespace BzApi;

require_once dirname(__FILE__).'/../config/bzapi_config.php';

use BzApi\BzRequest;
use Isotope\Model\OrderStatus;
use ManualOrders\tl_manualorders_order;

class BzClient
{

	public function handlePostCheckout($order, $args)
	{
		// add user to newsletter recipients if needed
	  if($order->getBillingAddress()->__get('addToNewsletter'))
	  {
	  	$db = \Database::getInstance();
	  	$email = $order->getBillingAddress()->__get('email');
	  	// fixed newsletter verteiler
	  	$newsletter_id = 12;
	  	$recipients = $db->prepare("Select * from tl_newsletter_recipients where email=? AND pid=?")->execute($email, $newsletter_id);
	  	if ($recipients->numRows == 0) 
	  	{
	  		$time = time();
	  		$db->execute(sprintf('INSERT INTO tl_newsletter_recipients(pid, tstamp, email, active, addedOn, ip) VALUES (%d, %d, \'%s\', 1, %d, \'%s\');', 
	  			$newsletter_id, $time, $email, $time, \Environment::get('ip')));
	  	}
	  	unset($email);
	  }
		/*
		 * Collect shipping and billing adress data
		 */
		$createShippingCustomerArgs = array();
		// multiply adress id with 2: even ids are from isotope, odd ones from manual orders
		$createShippingCustomerArgs[BzRequest::PARAM_PRIVATKUNDENNUMMER] = $args['shipping_address_id']*2;
		// check if firm is set: if yes, use 'vorname' as adress line
		if ($args['shipping_address_company'])
		{
			$createShippingCustomerArgs[BzRequest::PARAM_ANREDE] = 'Firma';
			$createShippingCustomerArgs[BzRequest::PARAM_NAME] = $args['shipping_address_company'];
			$createShippingCustomerArgs[BzRequest::PARAM_VORNAME] = $args['shipping_address_firstname'] . ' ' . $args['shipping_address_lastname'];
		}
		else
		{
			$createShippingCustomerArgs[BzRequest::PARAM_NAME] = $args['shipping_address_lastname'];
			$createShippingCustomerArgs[BzRequest::PARAM_VORNAME] = $args['shipping_address_firstname'];
		}
		$createShippingCustomerArgs[BzRequest::PARAM_ADRESSE] = $args['shipping_address_street_1'];
		$createShippingCustomerArgs[BzRequest::PARAM_PLZ] = $args['shipping_address_postal'];
		$createShippingCustomerArgs[BzRequest::PARAM_ORT] = $args['shipping_address_city'];
		
		if ($args['shipping_address_country'] == 'Schweiz')
		{
			$createShippingCustomerArgs[BzRequest::PARAM_LAND] = 'CH';
		}
		else
		{
			// TODO: implement non swiss orders
		}
		
		if($args['billing_address_id'] === $args['shipping_address_id'])
		{
			$useBillingAddress = false;
			$createBillingCustomerArgs = $createShippingCustomerArgs;
		}
		else
		{
			$useBillingAddress = true;
			$createBillingCustomerArgs = array();
			$createBillingCustomerArgs[BzRequest::PARAM_PRIVATKUNDENNUMMER] = $args['billing_address_id'];
			// check if firm is set: if yes, use 'vorname' as adress line
			if ($args['billing_address_company'])
			{
				$createBillingCustomerArgs[BzRequest::PARAM_ANREDE] = 'Firma';
				$createBillingCustomerArgs[BzRequest::PARAM_NAME] = $args['billing_address_company'];
				$createBillingCustomerArgs[BzRequest::PARAM_VORNAME] = $args['billing_address_firstname'] . ' ' . $args['billing_address_lastname'];
			}
			else
			{
				$createBillingCustomerArgs[BzRequest::PARAM_NAME] = $args['billing_address_lastname'];
				$createBillingCustomerArgs[BzRequest::PARAM_VORNAME] = $args['billing_address_firstname'];
			}
			$createBillingCustomerArgs[BzRequest::PARAM_ADRESSE] = $args['billing_address_street_1'];
            $createBillingCustomerArgs[BzRequest::PARAM_PLZ] = $args['billing_address_postal'];
            $createBillingCustomerArgs[BzRequest::PARAM_ORT] = $args['billing_address_city'];
			if ($args['billing_address_country'] == 'Schweiz')
			{
				$createBillingCustomerArgs[BzRequest::PARAM_LAND] = 'CH';
			}
			else
			{
				// TODO: implement non swiss orders
			}
		}
		
		/*
		 * Collect order data
		 */
		$orderId = sprintf('faktor-webshop-order-%d', $args['order_id']); // !! used to receive order status after failure
		$orderArgs = array(
			BzRequest::PARAM_LIEFERADRESSE => $createShippingCustomerArgs[BzRequest::PARAM_PRIVATKUNDENNUMMER],
			BzRequest::PARAM_RECHNUNGSADRESSE => $createBillingCustomerArgs[BzRequest::PARAM_PRIVATKUNDENNUMMER],
			BzRequest::PARAM_TRANSAKTION => $orderId, 
			BzRequest::PARAM_VERSANDSPESEN => round($order->getShippingMethod()->getPrice()/1.025, 2),
		);
		
		// map the shops shipping type to the bz shipping types
		$map = $createShippingCustomerArgs[BzRequest::PARAM_LAND] == 'CH' ? BzRequest::$MAP_VERSANDARTEN['CH'] : BzRequest::$MAP_VERSANDARTEN['WORLD'];
		$shippingType = null;
		$shippingLabel = $order->getShippingMethod()->label;
		
		if(array_key_exists($shippingLabel, $map))
		{
			$shippingType = $map[$shippingLabel];
		}
		if($shippingType)
		{
			$orderArgs[BzRequest::PARAM_VERSANDART] = $shippingType;
		}
		else
		{
			$orderArgs[BzRequest::PARAM_VERSANDART] = $map['B-Post'];
		}
		
		foreach($order->getItems() as $item)
		{
			$orderItemsArgs[] = array(
				BzRequest::PARAM_TRANSAKTION => $orderId,
				BzRequest::PARAM_ARTIKEL => $item->sku,
				BzRequest::PARAM_MENGE => $item->quantity,
				BzRequest::PARAM_ENDPREIS => $item->price == 0 ? 0.01 : $item->price, // always use the webshop's price
			);
		}
		
		/* 
		 * Add Shipping Fee 
		 */
		 
		$feeArgs;
		
		/*
		 * Issue the order in one request
		 */
		$orderRequest = new BzRequest();
		
		$orderRequest->addCommand(BzRequest::CMD_CREATE_SESSION, array(
			BzRequest::PARAM_KUERZEL => BZUSER,
			BzRequest::PARAM_PASSWORT => BZPASS
		));
		$orderRequest->addCommand(BzRequest::CMD_ADD_PRIVATECUSTOMER, $createShippingCustomerArgs);
		if($useBillingAddress)
		{
			$orderRequest->addCommand(BzRequest::CMD_ADD_PRIVATECUSTOMER, $createBillingCustomerArgs);
		}
		
		$orderRequest->addCommand(BzRequest::CMD_ADD_ORDER_BOOKIT, $orderArgs);
		foreach($orderItemsArgs as $itemArgs)
		{
			$orderRequest->addCommand(BzRequest::CMD_ADD_DETAIL_BOOKIT, $itemArgs);
		}
		$orderRequest->addCommand(BzRequest::CMD_RELEASE_ORDER);
		$orderRequest->addCommand(BzRequest::CMD_DROP_SESSION);
		
		// boom and wait...
		$requestResult = $orderRequest->execute();
		
		// set order status and error message
		if($orderRequest->isOK())
		{
			// this does not work: $order->updateOrderStatus(OrderStatus::findByName('abgeschlossen'));
			\Database::getInstance()->prepare(
					'UPDATE tl_iso_product_collection SET order_status = (SELECT id FROM tl_iso_orderstatus WHERE name = ?) WHERE id = ?'
				)->execute('abgeschlossen', $args['order_id']);
		}
		else
		{
			
			// this does not work: $order->notes = $orderRequest->getErrorString();
			// this does not work: $order->updateOrderStatus(OrderStatus::findByName('BZ Fehler'));
			
			\Database::getInstance()->prepare(
					'UPDATE tl_iso_product_collection SET order_status = (SELECT id FROM tl_iso_orderstatus WHERE name = ?), notes = ? WHERE id = ?'
				)->execute('BZ Fehler', $orderRequest->getErrorString(), $args['order_id']);
				$objMail = new \Email();
				$objMail->fromName = CLIENT_ID;
				$objMail->from = 'noreply@faktor.ch';
				$objMail->subject = 'Beim Bestellen via BZ ist ein Fehler aufgetreten';
				$objMail->text = sprintf("Fehlertext:\n\n %s", $orderRequest->getErrorString());
				if(\Config::get('adminEmail'))
				{
					$retval = $objMail->sendTo(\Config::get('adminEmail'));
				}
				else
				{
					$retval = $objMail->sendTo('info@faktor.ch');
				}
		}
		// this does not work: $order->save();
	
	}

	public function orderManually($arrOrder)
	{
		/*
		 * Collect shipping and billing adress data
		 */
		$createShippingCustomerArgs = array();
		$createShippingCustomerArgs[BzRequest::PARAM_PRIVATKUNDENNUMMER] = MANUALORDER_CUSTOMER_ID_OFFSET+$arrOrder['id']*2;
		// check if firm is set: if yes, use 'vorname' as adress line
		
		if ($arrOrder['shipping_address_company'])
		{
			$createShippingCustomerArgs[BzRequest::PARAM_ANREDE] = 'Firma';
			$createShippingCustomerArgs[BzRequest::PARAM_NAME] = $arrOrder['shipping_address_company'];
			$createShippingCustomerArgs[BzRequest::PARAM_VORNAME] = $arrOrder['shipping_address_first_name'] . ' ' . $arrOrder['shipping_address_last_name'];
		}
		else
		{
			$createShippingCustomerArgs[BzRequest::PARAM_NAME] = $arrOrder['shipping_address_last_name'];
			$createShippingCustomerArgs[BzRequest::PARAM_VORNAME] = $arrOrder['shipping_address_first_name'];
		}
		$createShippingCustomerArgs[BzRequest::PARAM_ADRESSE] = $arrOrder['shipping_address_street'];
		$createShippingCustomerArgs[BzRequest::PARAM_PLZ] = $arrOrder['shipping_address_zip'];
		$createShippingCustomerArgs[BzRequest::PARAM_ORT] = $arrOrder['shipping_address_city'];
		
		// TODO: manual orders only for switzerland?
		$createShippingCustomerArgs[BzRequest::PARAM_LAND] = 'CH';
		
		if(!$arrOrder['use_different_billing_address'])
		{
			$useBillingAddress = false;
			$createBillingCustomerArgs = $createShippingCustomerArgs;
		}
		else
		{
			$useBillingAddress = true;
			$createBillingCustomerArgs = array();
			$createBillingCustomerArgs[BzRequest::PARAM_PRIVATKUNDENNUMMER] = MANUALORDER_CUSTOMER_ID_OFFSET+$arrOrder['id']*2+1;
			// check if firm is set: if yes, use 'vorname' as adress line
			if ($arrOrder['billing_address_company'])
			{
				$createBillingCustomerArgs[BzRequest::PARAM_ANREDE] = 'Firma';
				$createBillingCustomerArgs[BzRequest::PARAM_NAME] = $arrOrder['billing_address_company'];
				$createBillingCustomerArgs[BzRequest::PARAM_VORNAME] = $arrOrder['billing_address_first_name'] . ' ' . $arrOrder['billing_address_last_name'];
			}
			else
			{
				$createBillingCustomerArgs[BzRequest::PARAM_NAME] = $arrOrder['billing_address_last_name'];
				$createBillingCustomerArgs[BzRequest::PARAM_VORNAME] = $arrOrder['billing_address_first_name'];
			}
			$createBillingCustomerArgs[BzRequest::PARAM_ADRESSE] = $arrOrder['billing_address_street'];
			$createBillingCustomerArgs[BzRequest::PARAM_PLZ] = $arrOrder['billing_address_zip'];
			$createBillingCustomerArgs[BzRequest::PARAM_ORT] = $arrOrder['billing_address_city'];
			// TODO: manual orders only for switzerland?
			$createBillingCustomerArgs[BzRequest::PARAM_LAND] = 'CH';
		}
		/*
		 * Collect order data
		 */
		$orderId = sprintf('faktor-manual-order-%d', $arrOrder['id']); // !! used to receive order status after failure
		$orderArgs = array(
			BzRequest::PARAM_LIEFERADRESSE => $createShippingCustomerArgs[BzRequest::PARAM_PRIVATKUNDENNUMMER],
			BzRequest::PARAM_RECHNUNGSADRESSE => $createBillingCustomerArgs[BzRequest::PARAM_PRIVATKUNDENNUMMER],
			BzRequest::PARAM_TRANSAKTION => $orderId,
			BzRequest::PARAM_VERSANDSPESEN => round($arrOrder['shipping_fee']/1.025, 2), 
		);
		
		// map the shops shipping type to the bz shipping types (they have to have the same name)
		$shippingType = null;
		$shippingRow = \Database::getInstance()->execute(sprintf('SELECT label FROM tl_iso_shipping WHERE Id = %d', $arrOrder['shipping_type']))->fetchAssoc();
		$map = $createShippingCustomerArgs[BzRequest::PARAM_LAND] == 'CH' ? BzRequest::$MAP_VERSANDARTEN['CH'] : BzRequest::$MAP_VERSANDARTEN['WORLD'];
		if(array_key_exists($shippingRow['label'], $map))
		{
			$shippingType = $map[$shippingRow['label']];
		}
		if($shippingType)
		{
			$orderArgs[BzRequest::PARAM_VERSANDART] = $shippingType;
		}
		
		foreach($arrOrder['items'] as $item)
		{
			$orderItemsArgs[] = array(
				BzRequest::PARAM_TRANSAKTION => $orderId,
				BzRequest::PARAM_ARTIKEL => $item['sku'],
				BzRequest::PARAM_MENGE => $item['amount'],
				BzRequest::PARAM_ENDPREIS => $item['price'] == 0 ? 0.01 : $item['price'], // override BZ's price
			);
		}
		
		/*
		 * Issue the order in one request
		 */
		$orderRequest = new BzRequest();
		
		$orderRequest->addCommand(BzRequest::CMD_CREATE_SESSION, array(
			BzRequest::PARAM_KUERZEL => BZUSER,
			BzRequest::PARAM_PASSWORT => BZPASS
		));
		$orderRequest->addCommand(BzRequest::CMD_ADD_PRIVATECUSTOMER, $createShippingCustomerArgs);
		if($useBillingAddress)
		{
			$orderRequest->addCommand(BzRequest::CMD_ADD_PRIVATECUSTOMER, $createBillingCustomerArgs);
		}
		
		$orderRequest->addCommand(BzRequest::CMD_ADD_ORDER_BOOKIT, $orderArgs);
		foreach($orderItemsArgs as $itemArgs)
		{
			$orderRequest->addCommand(BzRequest::CMD_ADD_DETAIL_BOOKIT, $itemArgs);
		}
		$orderRequest->addCommand(BzRequest::CMD_RELEASE_ORDER);
		$orderRequest->addCommand(BzRequest::CMD_DROP_SESSION);
		
	//var_dump($orderRequest);die();
		
		// boom and wait...
		$requestResult = $orderRequest->execute();
		
		// set order status and error message
		if($orderRequest->isOK())
		{
			$dbResult = \Database::getInstance()->prepare(
				'UPDATE tl_manualorders_order SET order_status = ?, error = "" WHERE id = ?'
				)->execute(tl_manualorders_order::ORDER_STATUS_CLOSED, $arrOrder['id']);
		}
		else
		{
			$dbResult = \Database::getInstance()->prepare(
				'UPDATE tl_manualorders_order SET order_status = ?, error = ? WHERE id = ?'
				)->execute(tl_manualorders_order::ORDER_STATUS_ERROR, $orderRequest->getErrorString(), $arrOrder['id']);
		}
		//var_dump($dbResult);die();
	}
}

