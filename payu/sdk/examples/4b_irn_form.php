<?php
        require_once('../PayUIrn.class.php');


        $irn = new PayUIrn('config.php');

        /*
         * Set needed fields
         * Complete list: Technical documentation 4.2
         * 
         */
        $irn->setField("ORDER_REF", "10841370");
        $irn->setField("ORDER_AMOUNT", "9906");
        $irn->setField("AMOUNT", "100");
        $irn->setField("ORDER_CURRENCY", "UAH");
        $irn->setField("IRN_DATE", date("Y-m-d H:i:s"));
        
        //WARNING! If REF_URL is set processing response must be done on the target address !!!
        $irn->setField("REF_URL", "http://localhost/sdk/examples/4c_irn_process.php");
        
        //query server via FORM submit
        echo $irn->createHtmlForm("test_form","link","Next");
        
        //create an array from the response
        $data = $irn->processResponse($resp);
        
        //check if received data is valid
        echo $irn->checkResponseHash($data);
        
        print_r($irn->getMissing());

      
        
        
?>
