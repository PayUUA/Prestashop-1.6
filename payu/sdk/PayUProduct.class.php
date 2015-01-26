<?php

/**
 * PayUProduct
 * 
 * Helper object containing information about a product
 *
 */
class PayUProduct {
    public $name;
    public $group;
    public $code;
    public $info;
    public $price;
    public $qty;
    public $vat;
    public $ver;
    
    /*
     * Constructor for PayUProduct
     * 
     * Creates an object for a product for later processing
     * 
     * @param array $productParams Sets object properties according to variables passed in this array
     */
    public function __construct($productParams = array()){
        foreach ($productParams as $var=>$param){
            if (property_exists($this, $var)) {
                $this->$var = $param;
            }
        }
    }
}


?>
