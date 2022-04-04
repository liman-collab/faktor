<?php

require_once get_template_directory() . '/helpers/bzapi/classes/BzRequest.php';
require_once get_template_directory() . '/helpers/logger/Logger.php';

use BzApi\BzRequest;

function bz_api_request($order, $shippingMethod, $shippingTotal)
{
    $log = [
        'products' => []
    ];
    $userId = "" . mt_rand(100000, 999999);
    $request1 = new BzRequest();
    $request1->addCommand(BzRequest::CMD_CREATE_SESSION, array(BzRequest::PARAM_KUERZEL => BZUSER, BzRequest::PARAM_PASSWORT => BZPASS));

    if ($_POST['ship_to_different_address']) {
        $request1->addCommand(BzRequest::CMD_ADD_PRIVATECUSTOMER,
            array(
                BzRequest::PARAM_PRIVATKUNDENNUMMER => $userId,
                BzRequest::PARAM_NAME => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
                BzRequest::PARAM_ADRESSE => $order->get_shipping_address_1(),
                BzRequest::PARAM_PLZ => $order->get_shipping_postcode(),
                BzRequest::PARAM_ORT => $order->get_shipping_city(),
                BzRequest::PARAM_TRANSAKTION => 'bex-' . mt_rand(1000000000, 9999999999),
            )
        );
    } else {
        $request1->addCommand(BzRequest::CMD_ADD_PRIVATECUSTOMER,
            array(
                BzRequest::PARAM_PRIVATKUNDENNUMMER => $userId,
                BzRequest::PARAM_NAME => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                BzRequest::PARAM_ADRESSE => $order->get_billing_address_1(),
                BzRequest::PARAM_PLZ => $order->get_billing_postcode(),
                BzRequest::PARAM_ORT => $order->get_billing_city(),
                BzRequest::PARAM_TRANSAKTION => 'bex-' . mt_rand(1000000000, 9999999999),
            )
        );
    }

    if ($shippingMethod && $shippingTotal > 0) {
        $addOrderBookitData = array(
            BzRequest::PARAM_LIEFERADRESSE => $userId,
            BzRequest::PARAM_VERSANDART => $shippingMethod,
            BzRequest::PARAM_VERSANDSPESEN => $shippingTotal
        );
    } else {
        $addOrderBookitData = array(
            BzRequest::PARAM_LIEFERADRESSE => $userId
        );
    }

    $request1->addCommand(BzRequest::CMD_ADD_ORDER_BOOKIT, $addOrderBookitData);
//    $request1->addCommand(BzRequest::CMD_RELEASE_ORDER, array());
    $request1->addCommand(BzRequest::CMD_DROP_SESSION);
    $request1->execute();

    $log['request1'] = $request1->getResult();

    $auftrag = $request1->getResult()[2]['output_parameters'][0];

    $orderDetailReq = new BzRequest();
    $orderDetailReq->addCommand(BzRequest::CMD_CREATE_SESSION, array(BzRequest::PARAM_KUERZEL => BZUSER, BzRequest::PARAM_PASSWORT => BZPASS));
    foreach ($order->get_items() as $item_id => $item) {
        $itemFormat = $item->get_meta('Format');
        if ($itemFormat == 'Print') {
            $isbn = get_field('artikel_isbn', $item->get_product_id());
        } else {
            $isbn = get_field('artikel_isbn_kombi', $item->get_product_id());
        }
        $orderDetailReq->addCommand(BzRequest::CMD_ADD_DETAIL_BOOKIT, array(
            BzRequest::PARAM_AUFTRAG => $auftrag,
            BzRequest::PARAM_ARTIKEL => $isbn,
            BzRequest::PARAM_MENGE => $item->get_quantity(),
            BzRequest::PARAM_ENDPREIS => toFloat($item->get_total())
        ));

        $log['products'][$item->get_product_id()] = [
            'name' => $item->get_name(),
            'format' => $itemFormat,
            'isbn' => $isbn
        ];
    }
    $orderDetailReq->addCommand(BzRequest::CMD_RELEASE_ORDER, array(
        BzRequest::PARAM_AUFTRAG => $auftrag
    ));
    $orderDetailReq->addCommand(BzRequest::CMD_DROP_SESSION);
    $orderDetailReq->execute();
    $orderDetailResponse = $orderDetailReq->getResult();

    if ($orderDetailResponse[1]['statusMessage'] !== 'Ok') {
        $url = isset($_SERVER['HTTPS']) ? "https" : "http";
        $url .= '://' . $_SERVER['HTTP_HOST'] . '/wp-admin/post.php?post=' . $order->get_id() . '&action=edit';
        $content = "Fehler bei der Übermittlung der Bestellung <a href='".$url."' target='_blank'>#".$order->get_id()."</a> an buchzentrum.";
        wp_mail(get_bloginfo('admin_email'), 'BuchZentrum', $content, array('Content-Type: text/html; charset=UTF-8'));
    }

    $log['orderDetailReq'] = $request1->getResult();

    Logger::getInstance()->write($order->get_id(), json_encode($log));
}

function toFloat($nr)
{
    return number_format((float)$nr, 2, '.', '');
}