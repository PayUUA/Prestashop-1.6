<?php
        require_once('../PayUIrn.class.php');


        $irn = new PayUIrn('config.php',true);

        //create an array from the response
        $data = $irn->receiveResponse();
        
        //check if received data is valid
       if($irn->checkResponseHash($data)){
           /*
            * your code here
            */
           //process response data if needed
           //print_r($data);
           file_put_contents("irn.log", print_r($_REQUEST,1),FILE_APPEND);
       }
        
       //print list of missing fields
       //print_r($irn->getMissing());

      
        
        
?>
