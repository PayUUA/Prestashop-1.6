<?php
        require_once('../PayUIdn.class.php');


        $irn = new PayUIdn('config.php');

        //create an array from the response
        $data = $irn->receiveResponse();
        
        //check if received data is valid
        if($irn->checkResponseHash($data)){
            /*
            * your code here
            */
           //process response data if needed
            //print_r($data);
           // file_put_contents("idn.log", print_r($_REQUEST,1),FILE_APPEND);
        }
        //Print list of missing fields
        //print_r($idn->getMissing());

      
        
        
?>
