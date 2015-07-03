<?php
    
    /* 
     * This is the PHP class file which contains methods specific to card
     * processing, to construct an empty variable and to process a payment
     * to the payment gateway, using either curl or SOAP.  These functions
     * are called from the submit_txn.php file.
     * 
     * For questions regarding this sample code or for other code samples, 
     * contact developersupport@tgatepayments.com.
    *
     * Copyright 2010, TGATE Payments.
     */ 

class TGATE_CardTxn {

  function _construct() {
    //construct an empty variable
    $this->postdata = '';

  }  /*  end function  */

  function CURLTxn($txn_type,$card_number,$expy_dt,$mag_data,$name,$amt,$invoice,$pnref,$zip,$street,$cvnum,$ext_data) {
  //values passed from submit_txn.php
   $this->base = 'https://gatewaystage.itstgate.com/SmartPayments/transact.asmx/ProcessCreditCard';
   $this->userid = 'mKon5277';
   $this->password = 'M2581fk4';
   $data = '';

   $this->postdata = array('UserName'=>$this->userid,
              	  	'Password'=>$this->password,
              	  	'TransType'=>$txn_type,
              	  	'CardNum'=>$card_number,
	      	        'ExpDate'=>$expy_dt,
		      	'MagData'=>$mag_data,
		       	'NameOnCard'=>$name,
		       	'Amount'=>$amt,
		       	'InvNum'=>$invoice,
		       	'PNRef'=>$pnref,
		     	'Zip'=>$zip,
		      	'Street'=>$street,
		     	'CVNum'=>$cvnum,
		    	'ExtData'=>$ext_data);

		// concatenate this->postdata and put into variable
		while(list($key, $value) = each($this->postdata)) 
    		{  
			$data .= $key . '=' . urlencode($value) . '&';
		}  	//end the while loop

		// Remove the last "&" from the string
		$data = substr($data, 0, -1);

		// make appropriate copy of data for error-reporting purposes
		$copy_post_data = $this->postdata;
		
		// mask the CVNum as of card industry best practices
		$copy_post_data['CVNum'] = '****';
		$copy_post_data['UserName'] = '*******';
		$copy_post_data['Password'] = '*******';
		$copy_post_data['CardNum'] = '*******' . substr($copy_post_data['CardNum'], -4);
		$copy_post_data['MagData'] = '*******';

    /*un-comment below to create a log file as well as fclose below and other references to fp*/
    /*the log-file is pci-compliant as it does not store or display actual transaction data */ 

/*      $fp = fopen('c:\temp\curl_log.txt','w') or die(php_errormsg);  */
    

    $ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL,$this->base);
	curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	/* curl_setopt($ch, CURLOPT_WRITEHEADER, $fp); */
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 300);
	curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE,FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);
	/* curl_setopt($ch, CURLOPT_STDERR,$fp);  */

  $result = curl_exec($ch);

  $commError = curl_error($ch);
  $commInfo = @curl_getinfo($ch);

		curl_close($ch);

    /* fclose($fp) or die($php_errormsg);  */

    return $result;

  }

  function SOAPTxn($txn_type,$card_number,$expy_dt,$mag_data,$name,$amt,$invoice,$pnref,$zip,$street,$cvnum,$ext_data) {

   $this->base = 'https://gatewaystage.itstgate.com/smartPayments/transact.asmx?WSDL';
   $this->userid = 'USERID';
   $this->password = 'PASSWORD';

   $this->postdata = array('UserName'=>$this->userid,
              	  	'Password'=>$this->password,
              	  	'TransType'=>$txn_type,
              	  	'CardNum'=>$card_number,
	      	        'ExpDate'=>$expy_dt,
		      	'MagData'=>$mag_data,
		 	'NameOnCard'=>$name,
		        'Amount'=>$amt,
		        'InvNum'=>$invoice,
		        'PNRef'=>$pnref,
		        'Zip'=>$zip,
		        'Street'=>$street,
		        'CVNum'=>$cvnum,
		        'ExtData'=>$ext_data);

  /*  set up parameters to send txn to cynergydata */

  $client = new SOAPClient($this->base, array('connection_timeout' =>60));
 
  /*  now POST the txn  */

  $result = $client->ProcessCreditCard($this->postdata);
  return $result;

  }  /*  end of function  */

}  /*  end of class  */

 
?>
