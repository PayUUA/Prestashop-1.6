<?php

include(dirname(__FILE__).'/../../config/config.inc.php');

include(dirname(__FILE__).'/payu.php');
include(dirname(__FILE__).'/payu.scls.php');


$payu = new payu();

require_once(dirname(__FILE__)."/sdk/PayUBackRef.class.php");

$order_currency = isset($_GET['curr']) ? $_GET['curr'] : "huf";

$option  = array( 	'MERCHANT' => $payu->Payu_getVar("merchant_".$order_currency), 
					'SECRET_KEY' => $payu->Payu_getVar("secret_key_".$order_currency) );
					
$backref = new PayUBackRef($option);
$db = Db::getInstance();
$orderid = isset($_GET['orderid']) ? $_GET['orderid'] : 0;

$display = new FrontController();



if($orderid ==0){
	

}elseif(isset($_GET['timeout'])){
	$display->setTemplate(dirname(__FILE__).'/payment_timeout.tpl');

}elseif($backref->checkResponse()){

	$display->setTemplate(dirname(__FILE__).'/payment_backref_success.tpl');
 
	

} else {
	$display->setTemplate(dirname(__FILE__).'/payment_backref_unsuccess.tpl');

    
}

$display->run();

?>
