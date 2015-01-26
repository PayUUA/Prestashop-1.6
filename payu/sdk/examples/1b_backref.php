<?php

require_once("../PayUBackRef.class.php");

$backref = new PayUBackRef("config.php");

if($backref->checkResponse()){
    echo "SUCCESSFUL\n";
    echo $backref->getPayURefNo();
    /*
     * Your code here
     */
} else {
    echo "UNSUCCESSFUL\n";
    echo $backref->getError();
    /*
     * Your code here
     */
}

?>
