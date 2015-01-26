<?php
        require_once('../PayUIdn.class.php');


        $irn = new PayUIdn('config.php');

        /*
         * Complete list of fields in Technical documentation 3.1 
         */
        $irn->setField("ORDER_REF", "10841370");
        $irn->setField("ORDER_AMOUNT", "9906");
        $irn->setField("ORDER_CURRENCY", "UAH");
        $irn->setField("IDN_DATE", date("Y-m-d H:i:s"));
        
        //WARNING! REF_URL is needed and processing response must be done on the target address !!!
        //Example code in file 3c_idn_process.php
        $irn->setField("REF_URL", "http://localhost/sdk/examples/3c_idn_process.php");
        
        //query server via FORM submit
        echo $irn->createHtmlForm("test_form","link","Next");
        
        //Print list of missing fields
        //print_r($idn->getMissing());

      
        
        
?>
