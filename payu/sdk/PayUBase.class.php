<?php

/**
 * Base class for PayU implementation SDK
 *
 */
class PayUBase {
    public $merchantId;
    public $secretKey;
    public $settingsFile;
    public $debug;
    public $targetUrl;
    public $formData = array();
    public $hashData = array();
    public $validFields = array();
    public $hashFields = array();
    public $fieldData = array();
    public $products = array();
    public $missing = array();
    
    private $baseUrl;
    
    public $settings = array(
        'MERCHANT' => 'merchantId',
        'SECRET_KEY' => 'secretKey',
        'BASE_URL' => 'baseUrl',
        'ALU_URL' => 'aluUrl',
        'LU_URL' => 'luUrl',
        'IOS_URL' => 'iosUrl',
        'IDN_URL' => 'idnUrl',
        'IRN_URL' => 'irnUrl',
    );
    
    /*
     * Constructor of Base class
     * Cannot be instantiated
     * 
     * @param mixed $config Configuration array or filename
     * @param boolean $debug Debug mode
     */
    public function __construct($config, $debug=false){
        $this->debug = $debug;
        //print_r($config);exit;
        if(file_exists(realpath(dirname(__FILE__)) . "/defaults.php")){
			require_once(realpath(dirname(__FILE__)) . "/defaults.php");
			$this->processConfig($defaults);
		} else {
			echo "defaults.php not found";
			exit();
		}
        
        if(is_array($config)){
            $this->processConfig($config);
        } elseif(is_string($config)){
            if(file_exists($config)){
                require_once($config);
                $this->processConfig($config);
            }
        } else {
            echo "Unable to read settings!";
            exit();
        }
    }
    
    /*
     * Set config options
     * 
     * @param array $config Array with config options
     */
    private function processConfig($config){
        foreach($config as $setting=>$value){
            if(array_key_exists($setting, $this->settings)){
			
                $prop = $this->settings[$setting];
                $this->$prop = $config[$setting];
            }
        }
    }
    
    /*
     * HMAC HASH creation
     * RFC 2104
     * http://www.ietf.org/rfc/rfc2104.txt
     * 
     * @param string $key Secret key for encryption
     * @param string $data String to encode
     */
    protected function hmac($key, $data) {
	
        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;
        return md5($k_opad . pack("H*", md5($k_ipad . $data)));
    }

    /*
     * Creates the strings HASH-ready form
     * 
     * @param string $str String to be modified
     */
    protected function stringWithLength($str){
        $str = strlen(StripSlashes($str)).$str;
        return $str;
    }
    
    /*
     * Create HASH code for an array (1-dimension only)
     * 
     * @param array $hashData Array of ordered fields to be HASH-ed
     */
    protected function createHashString($hashData){
        $hashString = "";
        foreach ($hashData as $field){
            if(is_array($field)){
                echo "No multi-dimension array allowed!";
                exit();
            }
            $hashString .= self::stringWithLength($field);
        }

		$hashCode = $this->hmac($this->secretKey, $hashString);
		//print "sk:".$this->secretKey;exit;
        if($this->debug){
            print_r($this->hashFields);
            print_r($hashData);
            echo "\n\nHASH String: <b>".$hashString."\n\n</b>";
			echo "\n\nHASH Code: <b>".$hashCode."\n\n</b>";
        }
        
        return $hashCode;
    }
    
    /*
     * Creates hidden HTML field
     * 
     * @param string $name Name of the field. ID parameter will be generated without "[]"
     * @param sting $value Value of the field 
     */
    public function createHiddenField($name, $value){
        if(substr($name, -2, 2) == "[]"){
           $id = substr($name, 0, -2);
        } else {
            $id = $name;
        }
        $value = addslashes($value);
        return "\n<input type='hidden' name='$name' id='$id' value='$value' />";
    }
    
    /*
     * Generates a ready-to-insert HTML FORM
     * 
     * @param string $formName The ID parameter of the form
     * @param array $formData Array of data to be added as hidden fields to the form
     * @param string $submitElement The type of the submit element ('button' or 'link')
     * @param string $submitElementText The lebel for the submit element
     * @param boolean $tags Display open/close TAGs
     */
    public function createHtmlForm($formName, $formData=array(), $submitElement=false, $submitElementText=false, $tags = true, $iframe = true){
        $form = "";
        if($tags){
			
            $form .= "\n\n\n<form action='".$this->baseUrl.$this->targetUrl."' method='POST' id='$formName'>";
        }
        
        foreach ($formData as $name => $field){
            if(is_array($field)){
                foreach ($field as $subField){
                    $form .= $this->createHiddenField($name."[]",$subField);
                }
            } else {
                $form .= $this->createHiddenField($name,$field);
            }
        }
        
        if($submitElement && $submitElementText){
            if($submitElement == "link"){
                $form .= "\n<a href='javascript:document.getElementById(\"$formName\").submit()'>".addslashes($submitElementText)."</a>";
            } elseif ($submitElement == "button"){
                $form .= "\n<button type='submit'>".addslashes($submitElementText)."</button>";
            }
        }
        if($tags){
			
            $form .= "\n</form>";
			
        }
        
        return $form;
    }

    /*
     * Generates raw data array with HMAC HASH code for custom processing
     * 
     * @param string $hashFieldName Index-name of the generated HASH field in the associative array
     */
    public function createPostArray($hashFieldName = "ORDER_HASH"){
        if(!$this->prepareFields($hashFieldName)){
            return false;
        }
        
        return $this->formData;
    }
    
    /*
     * Sends a HTTP request via cURL or file_get_contents() and returns the response
     *
     * @param string $url Base URL for request
     * @param array $getParams Parameters to send
     */
    protected function createSimpleRequest($url,$getParams=array()){
        if($this->checkCurl()){
            $response = $this->requestCurl($url,"POST",$getParams);
        } else {
            $response = $this->requestSafe($url."?".http_build_query($getParams), "POST");
        }
        
        return $response;
    }
    
    /*
     * Sends a HTTP request via file_get_contents() and returns the response
     * 
     * @param string $url Base URL for request
     * @param string $method Method to use when sending request (ie: GET, POST)
     */
    private function requestSafe($url, $method="GET"){
        $url = $this->baseUrl.$url;
        $aContext = array(
            'http' => array(
                //'proxy' => 'proxy:8080',
                'method' => $method,
                'request_fulluri' => true,
            ),
        );
        $cxContext = stream_context_create($aContext);

        return file_get_contents($url, False, $cxContext);
    }
    
    /*
     * Sends a HTTP request via cURL and returns the response 
     * 
     * @param string $url Base URL for request
     * @param string $method Method to use when sending request (ie: GET, POST)
     * $param array $params Array of data to send
     */
    protected function requestCurl($url, $method = "GET", $params = null){
        $ch = curl_init();
        $url = $this->baseUrl.$url;
        curl_setopt($ch, CURLOPT_URL, $url);
        
        if($method == "POST"){
            curl_setopt($ch, CURLOPT_POST, true);
        } else {
            curl_setopt($ch, CURLOPT_POST, false);
        }
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
    
    /*
     * Creates a 1-dimension array from a 2-dimension one
     * 
     * @param array $array Array to be processed
     * @param array $skip Array of keys to be skipped when creating the new array
     */
    protected function flatArray($array, $skip = array()){
        $return = array();
        foreach ($array as $name=>$item){
            if(!in_array($name,$skip)){
                if(is_array($item)){
                    foreach ($item as $subItem){
                        $return[] = $subItem;
                    }
                } else {
                    $return[] = $item;
                }
            }
        }
        return $return;
    }
    
    /*
     * Sets default value for a field
     * 
     * @param array $sets Array of fields and its parameters
     */
    protected function setDefaults($sets){
        foreach($sets as $set){
            foreach ($set as $field=>$fieldParams){
                if($fieldParams['type'] == 'single' && isset($fieldParams['default'])){
                    $this->fieldData[$field] = $fieldParams['default'];
                }
            }
        }
    }
    
    /*
     * Checks if all required fields are set.
     * Returns true or array of missing fields list
     */
    protected function checkRequired(){
        $missing = array();
        foreach ($this->validFields as $field=>$params){
            if(isset($params['required']) && $params['required']){
                if($params['type'] == "single"){
                    if(!isset($this->formData[$field])){
                        $missing[] = $field;
                    }
                } else if($params['type'] == "product"){
                    foreach ($this->products as $prod){
                        $paramName = $params['paramName'];
                        if(!isset($prod->$paramName)){
                            $missing[] = $field;
                        }
                    }
                }
            }
        }

        if($missing != array()){
              return $missing;
        }

        return true;
    }
    
    /*
     * Getter method for fields
     * 
     * @param string $fieldName Name of the field
     */
    public function getField($fieldName){
        return $this->fieldData[$fieldName];
    }
    
    /*
     * Setter method for fields
     * 
     * @param string $fieldName Name of the field to be set
     * @param string $fieldValue Value of the field to be set
     */
    public function setField($fieldName, $fieldValue){
        if(in_array($fieldName, array_keys($this->validFields))){
            $this->fieldData[$fieldName] = $fieldValue;
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * Adds product to the $this->product array
     * 
     * @param mixed $product Array description of product or Product object
     */
    public function addProduct($product){
	
        if(is_array($product)){
            $product = new PayUProduct($product);
        } elseif (is_object($product)){
            //@todo istypeof  PayUProduct
      
        }else{
			echo "Not a valid product!";
            exit();
		}
		$this->products[] = $product;
        
    }
    
    
    /*
     * Finalizes and prepares fields for sending
     * 
     * @param string $hashName Name of the field containing HMAC HASH code
     */
    protected function prepareFields($hashName){
        $this->hashData = array();
        foreach ($this->hashFields as $field){
            $params = $this->validFields[$field];
            if($params['type'] == "single"){
                if(isset($this->fieldData[$field])){
                    $this->hashData[] = $this->fieldData[$field];
                }
            } elseif ($params['type'] == "product"){
                foreach($this->products as $product){
                    if(isset($product->$params["paramName"])){
                        $this->hashData[] = $product->$params["paramName"];
                    }
                }
            }
        }
        
        foreach($this->validFields as $field=>$params){
            if(isset($params["rename"])){
                $field = $params["rename"];
            }
            if($params['type'] == "single"){
                if(isset($this->fieldData[$field])){
                    $this->formData[$field] = $this->fieldData[$field];
                }
            } elseif($params['type'] == "product"){
                if(!isset($this->formData[$field])){
                    $this->formData[$field] = array();
                }
                foreach($this->products as $num=>$product){
                    if(isset($product->$params["paramName"])){
                        $this->formData[$field][$num] = $product->$params["paramName"];
                    }
                }   
            }
        }
        
        if($this->hashData && $hashName){
            $this->formData[$hashName] = $this->createHashString($this->hashData);
        }
        
        $this->missing = $this->checkRequired();
        if($this->missing === true){
            return true;
        } else {
            if ($this->debug) {
                echo "REQUIRED FIELDS MISSING\n";
                echo "More info with getMissing()\n";
            }

            return false;
        }
    }
    
    /*
     * Finds and processes validation response from HTTP response
     * 
     * @param string $resp HTTP response
     */
    public function processResponse($resp){
        preg_match_all("/<EPAYMENT>(.*?)<\/EPAYMENT>/", $resp, $matches);
        $data = explode("|",$matches[1][0]);
        return $this->nameData($data);
    }
    
    /*
     * Validates HASH code of the response
     * 
     * @param array $resp Array with the response data
     */
    public function checkResponseHash($resp){
        $hash = $resp['ORDER_HASH'];
        array_pop($resp);
        $calculated = $this->createHashString($resp);
        if($this->debug){
            echo "\ncalc:".$calculated;
            echo "\nrec: ".$hash."\n";
        }
        if($hash == $calculated){
            return true;
        }
        return false;
    }
    
    /*
     * Check if the cURL PHP extension is available
     */
    protected function checkCurl(){
        if(in_array("curl",  get_loaded_extensions())){
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * Check if the DOM PHP extension is available
     */
    protected function checkDom(){
        if(in_array("dom",  get_loaded_extensions())){
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * Check if the XmlLib PHP extension is available
     */
    protected function checkXmlLib(){
        if(in_array("libxml",  get_loaded_extensions())){
            return true;
        } else {
            return false;
        }
    }
    
}

?>
