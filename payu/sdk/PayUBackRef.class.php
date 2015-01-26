<?php

require_once("PayUBase.class.php");

/**
 * PayU BACK_REF
 * 
 * Processes information sent via HTTP GET on the returning site after a payment
 *
 */
class PayUBackRef extends PayUBase{
    
    private $backref;
    private $request;
    private $error;
    private $returnVars = array(
        "RC", "RT", "3dsecure", "date", "payrefno", "ctrl"
    );
        
    /*
     * Constructor of PayUBackRef class
     * 
     * @param mixed $config Configuration array or filename
     * @param string $backref BACK_REF URL sent
     * @param boolean $debug Debug mode
     */
    public function __construct($config, $backref=false, $debug=false){
        parent::__construct($config, $debug);
        $this->backref = $backref;
        
        if($backref){
            $this->createRequestUriGiven();
        } else {
            $this->createRequestUriNotGiven();
        }
    }
    
    /*
     * Creates request URI from the given BACK_REF
     * Useful when using special URLs (ie.: https://something.com:4848/)
     * 
     */
    private function createRequestUriGiven(){
	    $this->request = $this->backref;
        if (count($_GET) > 6) {
            $this->request .= "&";
        } else {
            $this->request .= "?";
        }
        foreach ($this->returnVars as $var) {
            $this->request .= $var . "=" . urlencode($_GET[$var]) . "&";
        }
        $this->request = substr($this->request, 0, -1);
    }
    
    /*
     * Creates request URI from HTTP SERVER VARS.
     * Handles http and https
     * 
     */
    private function createRequestUriNotGiven(){
        if (isset($_SERVER['HTTP_FRONT_END_HTTPS']) and $_SERVER['HTTP_FRONT_END_HTTPS'] == "On") {
            $this->request = "https://";
        } else {
            $this->request = "http://";
        }

        $this->request .= $_SERVER['HTTP_HOST'];

        if (!in_array($_SERVER['SERVER_PORT'], array(80, 443))) {
            $this->request .= ":".$_SERVER['SERVER_PORT'];
        }

        $this->request .= $_SERVER['REQUEST_URI'];
    }
    
    /*
     * Validates CTRL variable
     * 
     */
    private function checkCtrl(){
        $requestURL = substr($this->request, 0, -38); //az utolso 38 karakter a CTRL paraméter
        $hashInput = strlen($requestURL).$requestURL;
	
        if ($_GET['ctrl'] == $this->hmac($this->secretKey, $hashInput)) {
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * Check card authorization response
     * 000 and 001 is successful, unsuccessful otherwise
     * 
     */
    public function checkResponse(){
        if(!$this->checkCtrl()){
            $this->error = "INVALID CTRL";
            return false;
        }
        
        if(in_array(substr($_GET['RT'], 0, 3),array("000","001"))){
            return true; 
        } else {
            $this->error = "CARD UNAUTHORIZED";
            return false;
        }
    }
    
    /*
     * Get PayU reference number after chaeckResponse()
     * 
     */
    public function getPayURefNo(){
        return $_GET['payrefno'];
    }

    /*
     * Returns a list of errors if there was any
     * 
     */
    public function getError(){
        return $this->error;
    }
}

?>
