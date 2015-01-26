<?php

require_once('PayUBase.class.php');

/**
 * PayU Instant Delivery Information
 *
 * Sends delivery notification via HTTP
 * 
 */
class PayUIdn extends PayUBase{
    public $missing = array();
    
    protected $targetUrl;
    protected $formData;
    protected $hashData;

    protected $hashFields = array(
        "MERCHANT",
        "ORDER_REF",
        "ORDER_AMOUNT",
        "ORDER_CURRENCY",
        "IDN_DATE"
    );

    protected $validFields = array(
        "MERCHANT" => array("type"=>"single", "paramName"=>"merchantId", "required"=>true),
        "ORDER_REF" => array("type"=>"single", "paramName"=>"orderRef", "required"=>true),
        "ORDER_AMOUNT" => array("type"=>"single", "paramName"=>"amount", "required"=>true),
        "ORDER_CURRENCY" => array("type"=>"single", "paramName"=>"currency", "required"=>true),
        "IDN_DATE" => array("type"=>"single", "paramName"=>"idnDate", "required"=>true),
        "REF_URL" => array("type"=>"single", "paramName"=>"refUrl"),
    );

    /*
     * Constructor of PayUIdn class
     * 
     * @param mixed $config Configuration array or filename
     * @param boolean $debug Debug mode
     */
    public function __construct($config, $debug = false){
        $this->hashData = array();
        $this->formData = array();

        parent::__construct($config, $debug);
        $this->fieldData['MERCHANT'] = $this->merchantId;
        $this->targetUrl = $this->idnUrl;
    }

    /*
     * Creates associative array for the received data
     * 
     * @param array $data Processed data
     */
    protected function nameData($data){
        return array(
            "ORDER_REF"=>$data[0],
            "RESPONSE_CODE"=>$data[1],
            "RESPONSE_MSG"=>$data[2],
            "IDN_DATE"=>$data[3],
            "ORDER_HASH"=>$data[4],
        );
    }
    
    /*
     * Sends notification via cURL
     * 
     * @param array $data (Optional) Data array to be sent
     */
    public function requestCurl($data = false){
        if(!$data){
            if (!$this->prepareFields("ORDER_HASH")) {
                return false;
            }

            $data = $this->formData;
        }
        return parent::requestCurl($this->targetUrl, "POST", $this->formData);
    }      
    
    /*
     * Generates a ready-to-insert HTML FORM
     * 
     * @param string $formName The ID parameter of the form
     * @param string $submitElement The type of the submit element ('button' or 'link')
     * @param string $submitElementText The lebel for the submit element
     * @param boolean $tags Display open/close TAGs
     */
    public function createHtmlForm($formName, $submitElement=false, $submitElementText=false, $tags = true){
        if (!$this->prepareFields("ORDER_HASH")) {
                return false;
            }
        return parent::createHtmlForm($formName, $this->formData, $submitElement, $submitElementText, $tags);
    }
    
    /*
     * Creates an array from useful REQUEST variables
     * 
     */
    public function receiveResponse(){
        return array(
            "ORDER_REF" => $_REQUEST['ORDER_REF'],
            "RESPONSE_CODE" => $_REQUEST['RESPONSE_CODE'],
            "RESPONSE_MSG" => $_REQUEST['RESPONSE_MSG'],
            "IDN_DATE" => $_REQUEST['IDN_DATE'],
            "ORDER_HASH" => $_REQUEST['ORDER_HASH']
        );
    }
    
    /*
     * Returns a list of missing required fields
     * 
     */
    public function getMissing(){
        return $this->missing;
    }
}

?>
