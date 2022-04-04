<?php

function get_request()
{
    $createSession = "createSession" . PARAMETER_DELIMITER . "kuerzel=>" . BZUSER . PARAMETER_DELIMITER . "passwort=>" . BZPASS . PARAMETER_DELIMITER . "clientID=>" . CLIENT_ID . PARAMETER_DELIMITER . "clientVersion=>" . CLIENT_VERSION;
    $getOrderBookit = COMMAND_DELIMITER . "getOrderBookit" . PARAMETER_DELIMITER . "bookitauftraege=>978-3-905711-57-8";
    $dropSession = COMMAND_DELIMITER . "dropSession";
    $data = $createSession . $getOrderBookit . $dropSession;
    $ch = curl_init();
// set request paramters:
    $curlOptions = array(
        CURLOPT_URL => URL,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 0,
        CURLOPT_POSTFIELDS => $data, // set request content:
        CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'), // always set this header
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
    );

    curl_setopt_array($ch, $curlOptions);

// issue request and wait, and measure time for logging
    $issuedAt = microtime(true);
    $answer = curl_exec($ch);
    $completedAt = microtime(true);

    $rawResponseBody = utf8_encode($answer);
    return $rawResponseBody;
}

function api_request($order)
{
    $auftrag = mt_rand(1000000000, 9999999999);
    $rechId = strtotime(date('Y-m-d H:i:s')); // from strtotime('2020-06-16')
    $liefId = strtotime(date('Y-m-d H:i:s')); // from strtotime('2020-06-16')
    $createSession = "createSession" . PARAMETER_DELIMITER . "kuerzel=>" . BZUSER . PARAMETER_DELIMITER . "passwort=>" . BZPASS . PARAMETER_DELIMITER . "clientID=>" . CLIENT_ID . PARAMETER_DELIMITER . "clientVersion=>" . CLIENT_VERSION;
    $addPrivatecustomer = COMMAND_DELIMITER . "addPrivatecustomer" . PARAMETER_DELIMITER . "privatkundennummer=>" . $rechId . PARAMETER_DELIMITER . "anrede=>" . $order->get_billing_company() . PARAMETER_DELIMITER . "name=>" . $order->get_billing_first_name() . PARAMETER_DELIMITER . "vorname=>" . $order->get_billing_last_name() . PARAMETER_DELIMITER . "adresse=>" . $order->get_billing_address_1() . PARAMETER_DELIMITER . "plz=>" . $order->get_billing_postcode() . PARAMETER_DELIMITER . "ort=>" . $order->get_billing_city() . PARAMETER_DELIMITER . "land=>" . $order->get_billing_country();
    if ($_POST['ship_to_different_address']) {
        $liefId = 1592280000 + 1;
        $addPrivatecustomer .= COMMAND_DELIMITER . "addPrivatecustomer" . PARAMETER_DELIMITER . "privatkundennummer=>" . $liefId . PARAMETER_DELIMITER . "anrede=>" . $order->get_shipping_company() . PARAMETER_DELIMITER . "name=>" . $order->get_shipping_first_name() . PARAMETER_DELIMITER . "vorname=>" . $order->get_shipping_last_name() . PARAMETER_DELIMITER . "adresse=>" . $order->get_shipping_address_1() . PARAMETER_DELIMITER . "plz=>" . $order->get_shipping_postcode() . PARAMETER_DELIMITER . "ort=>" . $order->get_shipping_city() . PARAMETER_DELIMITER . "land=>" . $order->get_shipping_country();
    }

    $data = $createSession . $addPrivatecustomer;
    // Get and Loop Over Order Items
    foreach ($order->get_items() as $item_id => $item) {
//        $items[$item_id]['product_id'] = $item->get_product_id();
//        $items[$item_id]['variation_id'] = $item->get_variation_id();
//        $items[$item_id]['product'] = $item->get_product();
//        $items[$item_id]['name'] = $item->get_name();
//        $items[$item_id]['quantity'] = $item->get_quantity();
//        $items[$item_id]['subtotal'] = $item->get_subtotal();
//        $items[$item_id]['total'] = $item->get_total();
//        $items[$item_id]['tax'] = $item->get_subtotal_tax();
//        $items[$item_id]['taxclass'] = $item->get_tax_class();
//        $items[$item_id]['taxstat'] = $item->get_tax_status();
//        $items[$item_id]['allmeta'] = $item->get_meta_data();
//        $items[$item_id]['type'] = $item->get_type();
        $isbn = get_field('artikel_isbn', $item->get_product_id());
        $addOrderBookit = COMMAND_DELIMITER . "addOrderBookit" . PARAMETER_DELIMITER . "lieferadresse=>" . $liefId . PARAMETER_DELIMITER . "rechnungsadresse=>" . $rechId . PARAMETER_DELIMITER . "transaktion=>faktor-webshop-order-" . $order->get_id() . PARAMETER_DELIMITER . "versandspesen=>" . $order->get_shipping_total();
        $addDetailBookit = COMMAND_DELIMITER . "addDetailBookit" . PARAMETER_DELIMITER . "transaktion=>faktor-webshop-order-" . $order->get_id() . PARAMETER_DELIMITER . "artikel=>" . $isbn . PARAMETER_DELIMITER . "menge=>" . $item->get_quantity() . PARAMETER_DELIMITER . "endpreis=>" . toFloat($item->get_total()) . PARAMETER_DELIMITER . "auftrag=>" . $auftrag;
        $data .= $addOrderBookit . $addDetailBookit;
    }

    $releaseOrderAndDropSession = COMMAND_DELIMITER . "releaseOrder" . COMMAND_DELIMITER . "dropSession";
    $data .= $releaseOrderAndDropSession;

    $ch = curl_init();
// set request paramters:
    $curlOptions = array(
        CURLOPT_URL => URL,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $data, // set request content:
        CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'), // always set this header
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
    );

    curl_setopt_array($ch, $curlOptions);

// issue request and wait, and measure time for logging
    $issuedAt = microtime(true);
    $answer = curl_exec($ch);
    $completedAt = microtime(true);

    $rawResponseBody = utf8_encode($answer);

    return $rawResponseBody;
}

function toFloat($nr)
{
    return number_format((float)$nr, 2, '.', '');
}