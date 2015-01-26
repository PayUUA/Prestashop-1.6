<?php

require_once('../PayUIpn.class.php');

$ipn = new PayUIpn('config.php');
if($ipn->validateReceived()){
    //SIKERES
    echo $ipn->confirmReceived();
    
    /*
     * Store or process returned data
     * Product codes used when refunding are sent here!
     * Complete list of fields in Technical documentation 2.1
     */
    //print_r($_POST);
    file_put_contents("ipn.log", print_r($_REQUEST,1),FILE_APPEND);
    /*
     * Your code here
     */
}




?>
