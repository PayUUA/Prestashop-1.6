<?php
/**
 * PayU Instant Payment Notification
 *
 * Processes notifications sent via HTTP POST request
 * 
 */

require_once('PayUBase.class.php');

class PayUIpn extends PayUBase{
    /*
     * Constructor of PayUIpn class
     * 
     * @param mixed $config Configuration array or filename
     * @param boolean $debug Debug mode
     */
    public function __construct($config, $debug=false){
        parent::__construct($config, $debug);
    }
    
    /*
     * Validate recceived data against HMAC HASH
     * 
     */
    public function validateReceived(){
		

        if($this->createHashString($this->flatArray($_POST, array("HASH"))) == $_POST['HASH']){
              return true;
        } else {
            return false;
        }
    }
    
    /*
     * Creates INLINE string for corfirmation
     * 
     * @param boolean $echo Displays strings when true
     */
    public function confirmReceived($echo=false){
        $serverDate = date("YmdHis");
        $hashArray = array(
            $_POST['IPN_PID'][0],
            $_POST['IPN_PNAME'][0],
            $_POST['IPN_DATE'],
            $serverDate
        );
        $hash = $this->createHashString($hashArray);
		
		
			$string = "<EPAYMENT>".$serverDate."|".$hash."</EPAYMENT>";
			
        
        if($echo){
            echo $string;
        }

        return $string;
    }
}

?>
