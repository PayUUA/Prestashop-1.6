<?php

require_once('../PayUProduct.class.php');
require_once('../PayULiveUpdate.class.php');

/**
 * PayU Automatic LiveUpdate
 *
 */
class PayUAlu extends PayULiveUpdate {
    protected $extraHash = array(
        "ORDER_PGROUP" => array("type"=>"single"),
    );
    protected $required = array(
        "BILL_PHONE" => array("type"=>"single", "required"=>true),
        "BILL_FAX" => array("type"=>"single", "required"=>true),
        "BILL_ADDRESS" => array("type"=>"single", "required"=>true),
        "BILL_ADDRESS2" => array("type"=>"single", "required"=>true),
        "BILL_ZIPCODE" => array("type"=>"single", "required"=>true),
        "BILL_CITY" => array("type"=>"single", "required"=>true),
        "BILL_STATE" => array("type"=>"single", "required"=>true),
        "BILL_COUNTRYCODE" => array("type"=>"single", "required"=>true),
        
        "DELIVERY_LNAME" => array("type"=>"single", "required"=>true),
        "DELIVERY_FNAME" => array("type"=>"single", "required"=>true),
        "DELIVERY_PHONE" => array("type"=>"single", "required"=>true),
        "DELIVERY_ADDRESS" => array("type"=>"single", "required"=>true),
        "DELIVERY_ZIPCODE" => array("type"=>"single", "required"=>true),
        "DELIVERY_CITY" => array("type"=>"single", "required"=>true),
        "DELIVERY_STATE" => array("type"=>"single", "required"=>true),
        "DELIVERY_COUNTRYCODE" => array("type"=>"single", "required"=>true),
    );
    protected $cardFields = array(
        "CC_NUMBER" => array("type"=>"single", "required"=>true),
        "EXP_MONTH" => array("type"=>"single", "required"=>true),
        "EXP_YEAR" => array("type"=>"single", "required"=>true),
        "CC_TYPE" => array("type"=>"single", "required"=>true),
        "CC_CVV" => array("type"=>"single", "required"=>true),
        "CC_OWNER" => array("type"=>"single", "required"=>true),
        "BILL_LNAME" => array("type"=>"single", "required"=>true),
        "BILL_FNAME" => array("type"=>"single", "required"=>true),
        "BILL_EMAIL" => array("type"=>"single", "required"=>true),
        
    );
    
    /*
     * Constructor of PayUAlu class
     * 
     * @param mixed $config Configuration array or filename
     * @param boolean $debug Debug mode
     */
    public function __construct($config, $debug=false){
        parent::__construct($config, $debug);
        $this->targetUrl = $this->aluUrl;
        
        $this->validFields = array_merge($this->validFields, $this->cardFields, $this->required);
        $this->hashFields = array_merge($this->hashFields, array_keys($this->extraHash), array_keys($this->cardFields), array_keys($this->required));
    }
}

?>
