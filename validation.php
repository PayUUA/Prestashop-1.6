<?php
//ini_set ('error_reporting', 0);

include(dirname(__FILE__).'/../../config/config.inc.php');

include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/payu.php');
include(dirname(__FILE__).'/payu.scls.php');
$cart = new Cart(intval($cookie->id_cart));
$payu = new payu();
$address = new Address(intval($cart->id_address_invoice));
$country = new Country(intval($address->id_country));
$customer = new Customer(intval($cart->id_customer));

$currency_order = new Currency(intval($cart->id_currency));
$merchant_curr_data = $currency_order->iso_code;
$payuTotalOrderAmount = floatval($cart->getOrderTotal(true, 3));
$authorized = false;
foreach (Module::getPaymentModules() as $module)
	if ($module['name'] == 'payu')
	{
		$authorized = true;
		break;
	}

$button = "<div style='position:absolute; top:50%; left:50%; margin:-40px 0px 0px -60px; '>".
		  "<div><img src='./img/payu.png' width='120px' style='margin:0px 5px;'></div>".
		  "<div><img src='./img/loader.gif' width='120px' style='margin:5px 5px;'></div>".
		  "</div>".
		  "<script>
		  	setTimeout( subform, 5000 );
		  	function subform(){ document.getElementById('PayUForm').submit(); }
		  </script>";

$option  = array( 	'merchant' => $payu->Payu_getVar("merchant_".$merchant_curr_data), 
					'secretkey' => $payu->Payu_getVar("secret_key_".$merchant_curr_data), 
					'debug' => $payu->Payu_getVar("debug_mode"),
					'button' => $button );

if ( $payu->Payu_getVar("system_url") != '' ) $option['luUrl'] = $payu->Payu_getVar("system_url");

$forSend = array();

	$forSend['ORDER_PNAME'][] = "Rendelés - ".$cart->id;
	$forSend['ORDER_PCODE'][] = time();
	$forSend['ORDER_PINFO'][] = "";
	$forSend['ORDER_PRICE'][] = $payuTotalOrderAmount;
	$forSend['ORDER_QTY'][] = 1;
	$forSend['ORDER_VAT'][] =  0;
	
$user = $customer;

if(isset($user->email) and $user->email !=""){
$vanemail = $user->email;
}else{
$vanemail = "01@01.hu";
}
if(isset($address->phone) and $address->phone !=""){
$vantel = $address->phone;
}else{
$vantel = "01";
}


$forSend += array(
					'BILL_FNAME' => $user->firstname,
					'BILL_LNAME' => $user->lastname,
					'BILL_ADDRESS' => $address->address1,
					'BILL_ADDRESS2' => $address->address2,
					'BILL_ZIPCODE' => $address->postcode,
					'BILL_CITY' => $address->city,
					'BILL_PHONE' => $vantel,
					'BILL_EMAIL' =>$vanemail
					);

$mailVars = array();

$payu->validateOrder($cart->id, 1, $payuTotalOrderAmount, $payu->displayName, NULL, NULL, (int)$currency->id, false, $customer->secure_key);

$order = new Order($payu->currentOrder);

$orderID = $payu->currentOrder;
$forSend['BACK_REF'] = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/payu/backref.php?orderid='.$orderID.'&curr='.$currency->iso_code.'';

$forSend['TIMEOUT_URL'] = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/payu/backref.php?timeout=1&orderid='.$orderID.'&curr='.$currency->iso_code.'';
$forSend['AUTOMODE'] = 1; //AUTOMODE

$forSend += array (
					'ORDER_REF' => $orderID, # Uniqe order 
					'ORDER_SHIPPING' => 0, # Shipping cost
					'PRICES_CURRENCY' => strtoupper($merchant_curr_data),  # Currency
					'LANGUAGE' => $payu->Payu_getVar("language"),
				  );
				  
				 

$pay = PayuCLS::getInst()->setOptions( $option )->setData( $forSend )->LU();

echo $pay;
//exit;
?>
