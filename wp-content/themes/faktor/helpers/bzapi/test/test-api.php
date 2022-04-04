<?php 

require_once '../classes/BzRequest.php';

use BzApi\BzRequest;

$request1 = new BzRequest();
$request1->addCommand(BzRequest::CMD_CREATE_SESSION, array(BzRequest::PARAM_KUERZEL => BZUSER, BzRequest::PARAM_PASSWORT => BZPASS));
$request1->addCommand(BzRequest::CMD_ADD_PRIVATECUSTOMER,
		array(
				BzRequest::PARAM_PRIVATKUNDENNUMMER => '123456',
				BzRequest::PARAM_NAME => 'Testname',
				BzRequest::PARAM_ADRESSE => 'Testadresse',
				BzRequest::PARAM_PLZ=>'8000',
				BzRequest::PARAM_ORT => 'Testort',
				BzRequest::PARAM_TRANSAKTION => 'test-123',
		)
		);
$request1->addCommand(BzRequest::CMD_ADD_ORDER_BOOKIT,
  array(
      BzRequest::PARAM_LIEFERADRESSE => '123456',
  )
);
$request1->addCommand(BzRequest::CMD_ADD_DETAIL_BOOKIT, array(
		BzRequest::PARAM_AUFTRAG => '987654',
		BzRequest::PARAM_ARTIKEL => '978-3-905711-35-6',
		BzRequest::PARAM_MENGE => 3
));
$request1->addCommand(BzRequest::CMD_RELEASE_ORDER, array());

$request1->addCommand(BzRequest::CMD_DROP_SESSION);
$res1 = $request1->execute();

var_dump($request1->getResult());

die('david');

echo '<h1>request 1</h1>';
$request1 = new BzRequest();
$request1->addCommand(BzRequest::CMD_CREATE_SESSION, array(BzRequest::PARAM_KUERZEL => BZUSER, BzRequest::PARAM_PASSWORT => BZPASS));
$request1->addCommand(BzRequest::CMD_ADD_PRIVATECUSTOMER, 
  array(
    BzRequest::PARAM_NAME => 'Testname', 
    BzRequest::PARAM_ADRESSE => 'Testadresse', 
    BzRequest::PARAM_PLZ=>'8000', 
    BzRequest::PARAM_ORT => 'Testort',
  	BzRequest::PARAM_TRANSAKTION => 'test-123',
  )
);
$request1->addCommand(BzRequest::CMD_DROP_SESSION);
$res1 = $request1->execute();

var_dump($request1->getRawResponseBody());

echo '<pre>';
var_dump($res1);
echo '</pre>';

echo '<h1>request 2</h1>';
$customer_number = $res1[1]['output_parameters'][0];
$session = $res[0]['output_parameters'][0];

$request2 = new BzRequest();
$request2->addCommand(BzRequest::CMD_CREATE_SESSION, array(BzRequest::PARAM_KUERZEL => BZUSER, BzRequest::PARAM_PASSWORT => BZPASS));
$request2->addCommand(BzRequest::CMD_ADD_ORDER_BOOKIT,
  array(
      BzRequest::PARAM_LIEFERADRESSE => $customer_number,
  )
);
$request2->addCommand(BzRequest::CMD_DROP_SESSION);
$res2 = $request2->execute();

$order_number = $res2[1]['output_parameters'][0];

var_dump($request2->getRawResponseBody());

echo '<pre>';
var_dump($res2);
echo '</pre>';


echo '<h1>request 3</h1>';
$request3 = new BzRequest();
$request3->addCommand(BzRequest::CMD_CREATE_SESSION, array(BzRequest::PARAM_KUERZEL => BZUSER, BzRequest::PARAM_PASSWORT => BZPASS));
$request3->addCommand(BzRequest::CMD_ADD_DETAIL_BOOKIT, array(
    BzRequest::PARAM_AUFTRAG => $order_number,
    BzRequest::PARAM_ARTIKEL => '978-3-905711-35-6',
    BzRequest::PARAM_MENGE => 3
));
$request3->addCommand(BzRequest::CMD_RELEASE_ORDER, array(BzRequest::PARAM_AUFTRAG => $res2[1]['output_parameters'][0]));
$request3->addCommand(BzRequest::CMD_DROP_SESSION);
$res3 = $request3->execute();
echo '<pre>';
var_dump($res3);
echo '</pre>';
