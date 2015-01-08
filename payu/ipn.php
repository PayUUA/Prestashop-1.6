<?php
if(count($_POST)){
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/payu.php');
include(dirname(__FILE__).'/payu.scls.php');
include(dirname(__FILE__).'/sdk/PayUIpn.class.php');
include(dirname(__FILE__).'/../../init.php');
$payu = new payu();

$ord = isset($_POST["REFNOEXT"]) ? $_POST["REFNOEXT"] : 0;
$order = new Order(intval($ord[0]));
$ordercurr = isset($_POST["CURRENCY"])? $_POST["CURRENCY"] : "HUF";

$ordercurr = strtolower($ordercurr);

if (!Validate::isLoadedObject($order) OR !$order->id)
	die('Invalid order');
	
$option  = array( 	'MERCHANT' => $payu->Payu_getVar("merchant_".$ordercurr), 
					'SECRET_KEY' => $payu->Payu_getVar("secret_key_".$ordercurr)
				);
	//print_r($_SERVER);exit;				
$ipn = new PayUIpn($option);

if($ipn->validateReceived()){
    //SIKERES
	$orderid = isset($_POST['REFNOEXT']) ? $_POST['REFNOEXT'] : 0;
	$orderNewState = 1;

	if($orderid > 0){
		$invDate = date("Y-m-d H:i:s");
		$query = 'UPDATE '._DB_PREFIX_.'order_history SET id_order_state = '.(int)$orderNewState.' WHERE id_order = '.(int)$orderid.' ';
		Db::getInstance()->execute($query);
		
		
		$query2 = "UPDATE "._DB_PREFIX_."orders SET invoice_date = '".$invDate."' WHERE id_order = ".(int)$orderid." ";
		
		Db::getInstance()->execute($query2);
		
		
		echo $ipn->confirmReceived();
    }
	
    //echo $ipn->confirmReceived();
    
}else{
echo "failed";
}

//$payu = new payu();


/*

$payansewer = PayuCLS::getInst()->setOptions( $option )->IPN();
echo $payansewer;
*/

$extraVars = "";

$id_order_state = _PS_OS_PAYMENT_;

$history = new OrderHistory();
$history->id_order = intval($order->id);
$history->changeIdOrderState(intval($id_order_state), intval($order->id));
$history->addWithemail(true, $extraVars);
}else{

print "no post";

}
