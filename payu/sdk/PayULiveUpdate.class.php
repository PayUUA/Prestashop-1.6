<?php

require_once('PayUBase.class.php');
require_once('PayUProduct.class.php');

/**
 * PayU LiveUpdate
 *
 * Sending orders via HTTP request
 * 
 */
class PayULiveUpdate extends PayUBase{
    /*
     * Constructor of PayULiveUpdate class
     * 
     * @param mixed $config Configuration array or filename
     * @param boolean $debug Debug mode
     */
    public function __construct($config, $debug = false){
        $this->hashData = array();
        $this->formData = array();

        $this->validFields = array(
            "MERCHANT" => array("type" => "single", "paramName" => "merchantId", "required" => true),
            "ORDER_REF" => array("type" => "single", "required" => true),
            "ORDER_DATE" => array("type" => "single", "required" => true),
            "ORDER_PNAME" => array("type" => "product", "paramName" => "name", "required" => true),
            "ORDER_PCODE" => array("type" => "product", "paramName" => "code", "required" => true),
            "ORDER_PGROUP" => array("type" => "product", "paramName" => "group"),
            "ORDER_PINFO" => array("type" => "product", "paramName" => "info"),
            "ORDER_PRICE" => array("type" => "product", "paramName" => "price", "required" => true),
            "ORDER_QTY" => array("type" => "product", "paramName" => "qty", "required" => true),
            "ORDER_VAT" => array("type" => "product", "default" => "0", "paramName" => "vat", "required" => true),
            "ORDER_VER" => array("type" => "product", "default" => "", "paramName" => "ver", "required" => true),
            
            "ORDER_SHIPPING" => array("type" => "single", "default" => "0"),
            "PRICES_CURRENCY" => array("type" => "single", "default" => "UAH"),
            "DISCOUNT" => array("type" => "single", "default" => "0"),
            
            "DESTINATION_CITY" => array("type" => "single"),
            "DESTINATION_STATE" => array("type" => "single"),
            "DESTINATION_COUNTRY" => array("type" => "single"),
            
            "PAY_METHOD" => array("type" => "single", "default" => "CCVISAMC"),
            "LANGUAGE" => array("type" => "single", "default" => "RU"),
            
            "AUTOMODE" => array("type" => "single", "default" => "1"), //0 or 1
            "TESTORDER" => array("type" => "single", "default" => "FALSE"), //(string)true or false
            "DEBUG" => array("type" => "single", "default" => "0"), //0 or 1

            "ORDER_TIMEOUT" => array("type" => "single"),
            "TIMEOUT_URL" => array("type" => "single"),
            "BACK_REF" => array("type" => "single"),
            
            "BILL_FNAME" => array("type" => "single", "required" => true),
            "BILL_LNAME" => array("type" => "single", "required" => true),
            "BILL_COMPANY" => array("type" => "single"),
            "BILL_EMAIL" => array("type" => "single", "required" => true),
            "BILL_PHONE" => array("type" => "single", "required" => true),
            "BILL_FAX" => array("type" => "single"),
            "BILL_ADDRESS" => array("type" => "single", "required" => true),
            "BILL_ADDRESS2" => array("type" => "single"),
            "BILL_ZIPCODE" => array("type" => "single", "required" => true),
            "BILL_CITY" => array("type" => "single", "required" => true),
            "BILL_STATE" => array("type" => "single"),
            "BILL_COUNTRYCODE" => array("type" => "single", "required" => true),
            
            "DELIVERY_FNAME" => array("type" => "single", "required" => true),
            "DELIVERY_LNAME" => array("type" => "single", "required" => true),
            "DELIVERY_COMPANY" => array("type" => "single"),
            "DELIVERY_PHONE" => array("type" => "single", "required" => true),
            "DELIVERY_ADDRESS" => array("type" => "single", "required" => true),
            "DELIVERY_ADDRESS2" => array("type" => "single"),
            "DELIVERY_ZIPCODE" => array("type" => "single", "required" => true),
            "DELIVERY_CITY" => array("type" => "single", "required" => true),
            "DELIVERY_STATE" => array("type" => "single", "required" => true),
            "DELIVERY_COUNTRYCODE" => array("type" => "single", "required" => true),
        );
        
        $this->hashFields = array(
            "MERCHANT",
            "ORDER_REF",
            "ORDER_DATE",
            "ORDER_PNAME",
            "ORDER_PCODE",
            "ORDER_PINFO",
            "ORDER_PRICE",
            "ORDER_QTY",
            "ORDER_VAT",
			"ORDER_VER",
            "ORDER_SHIPPING",
            "PRICES_CURRENCY",
            "DISCOUNT",
            "DESTINATION_CITY",
            "DESTINATION_STATE",
            "DESTINATION_COUNTRY",
            "PAY_METHOD",
            "ORDER_PGROUP"
        );

        $this->setDefaults(array(
            $this->validFields
        ));

        parent::__construct($config, $debug);
        $this->fieldData['MERCHANT'] = $this->merchantId;
        $this->targetUrl = $this->luUrl;
    }

    /*
     * Generates a ready-to-insert HTML FORM
     * 
     * @param string $formName The ID parameter of the form
     * @param string $submitElement The type of the submit element ('button' or 'link')
     * @param string $submitElementText The lebel for the submit element
     * @param boolean $tags Display open/close TAGs
     */
    public function createHtmlForm($formName, $submitElement=false, $submitElementText=false, $tags=true, $iframe= false){
        if(!$this->prepareFields("ORDER_HASH")){
            return false;
        }
        return parent::createHtmlForm($formName, $this->formData, $submitElement, $submitElementText, $tags,$iframe = false);
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
     * Returns a list of missing required fields
     * 
     */
    public function getMissing(){
        return $this->missing;
    }
}

?>
