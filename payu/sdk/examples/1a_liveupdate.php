<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="ru" />
    </head>
    <body>
        <?php
        /*
         * Import LiveUpdate class
         */
        require_once('../PayULiveUpdate.class.php');

        
        /*
         * Use config file
         */
        $lu = new PayULiveUpdate('config.php');
        
        /*
         * or ese config array
         */
        /*
        $lu = new PayULiveUpdate(array(
            'MERCHANT' => "MERCHANT_ID",
            'SECRET_KEY' => "S3cr3tK3y" 
        ));
        */
        
        /*
         * Add product with array
         */
        $lu->addProduct(array(
            'name' => 'Demo product 1',
            'group' => '0', // only if group exists in cPanel
            'code' => 'pu0001',
            'info' => 'prodinfo text',
            'price' => 1570, // net price
            'qty' => 2, 
            'vat' => 27, // with VAT
            'ver' => 1
        ));
        
		$lu->addProduct(array(
            'name' => 'Demo product 2',
            'group' => '0', // only if group exists in cPanel
            'code' => 'pu0002',
            'info' => '',
            'price' => 1120, // net price
            'qty' => 1, 
            'vat' => 0, // with VAT
            'ver' => 1
        ));
        /*
         * Set needed fields
         * Complete list: Technical documentation 1.2
         * 
         */
        $lu->setField("BILL_FNAME", "John");
        $lu->setField("BILL_LNAME", "Doe");
        $lu->setField("BILL_EMAIL", "john.doe@example.com"); // must be valid address
        $lu->setField("BILL_PHONE", "01"); //if not available send "01" instead
        $lu->setField("BILL_ADDRESS", "Bill address, street, h.no"); //if not available send "01" instead
        $lu->setField("BILL_CITY", "Budapest"); //if not available send "01" instead
        $lu->setField("BILL_ZIPCODE", "1234"); //if not available send "01" instead
        $lu->setField("BILL_COUNTRYCODE", "UA"); 
        $lu->setField("BILL_STATE", "pest megye"); //if not available send "01" instead
		
		//DELIVERY DATAS
        $lu->setField("DELIVERY_COUNTRYCODE", "UA"); 
        $lu->setField("DELIVERY_FNAME", "John"); //if not available send "01" instead
        $lu->setField("DELIVERY_LNAME", "Doe"); //if not available send "01" instead
        $lu->setField("DELIVERY_ADDRESS", " street, h.no"); //if not available send "01" instead
        $lu->setField("DELIVERY_PHONE", "01"); //if not available send "01" instead
        $lu->setField("DELIVERY_ZIPCODE", "1234"); //if not available send "01" instead
        $lu->setField("DELIVERY_CITY", "Cityname"); //if not available send "01" instead
        $lu->setField("DELIVERY_STATE", "pest2"); //if not available send "01" instead
        $lu->setField("DELIVERY_COUNTRYCODE", "UA"); 
        $lu->setField("BILL_COMPANY", "UA"); 
        
		
        $lu->setField("DISCOUNT", 5); 
        $lu->setField("ORDER_SHIPPING", 10); 
        
        $lu->setField("ORDER_DATE", date("Y-m-d H:i:s"));
        $lu->setField("ORDER_REF", "ORDERREF".time());
        $lu->setField("ORDER_TIMEOUT", 1200);
        
	//ADVISE: keep BACK_REF URL in session
       // $lu->setField("BACK_REF", "http://localhost/sdk/1b_backref.php");
        //$lu->setField("TIMEOUT_URL", "http://localhost/timeout.php");
        $lu->setField("LANGUAGE", "UA");        
        
        /*
         * Generate fields and print form
         */
        $display = $lu->createHtmlForm('myForm', 'link', "Fizetek");
        //"NEXT" can be replaced to any text
        //mb_convert_encoding('Tovább az Árvíztűrő tükörfúrógép oldalára',"ISO-8859-2","UTF-8")
        echo $display;
        
        /*
         * Print missing fields list
         */
        print_r($lu->getMissing());
        
        ?>
    </body>
</html>
