<?php

    /* 
     * This PHP file will send a test Sale transaction to TGATE's Payment Gateway using PHP's
     * fsockopen method.  You can uncomment print statements below to provide debug 
     * information.  
     *
     * For questions regarding this sample code or for other code samples, 
     * contact gatewaysupport@tgatepayments.com or call 866-531-1460.
     *
     * Copyright 2010, TGATE Payments.
     */ 
  
  $gateway = "gatewaystage.itstgate.com";
  $port = "443";

  // set up values to create the header for transaction data
  $post_msg = "POST /SmartPayments/transact.asmx/ProcessCreditCard HTTP/1.1\r\n";
  $host_msg = "Host: gatewaystage.itstgate.com\r\n";
  $content_msg = "Content-Type: application/x-www-form-urlencoded\r\n";

  // this is the actual transaction data, substitute data from your web form
  $txn_data = "UserName=xxxx&Password=xxxx&TransType=Sale&CardNum=xxxxx&ExpDate=MMYY&MagData=&NameOnCard=First+Last&Amount=1.00&InvNum=fsockopen&PNRef=&Zip=xxxx&Street=75+W+123rd+Street&CVNum=123&ExtData=";
  $len_msg = "Content-Length: " . strlen($txn_data) ."\r\n";
  $close_msg = "Connection: close\r\n\r\n";

/*   uncomment to see the string setups
  print $post_msg . "<br>";
  print $host_msg . "<br>";
  print $content_msg ."<br>";
  print $len_msg ."<br>";
*/


  //open a secure socket connection to the gateway
  $fp = fsockopen("ssl://" .$gateway,$port);   //simple fsockopen


/*  uncomment below and comment above for error trapping
  $fp = fsockopen("ssl://".$gateway, $port, $errno, $errstr);  //use this to include error trapping    
*/

  if (!$fp) {
    echo "Error " ."$errstr ($errno)\n"; 
  } else {

    // create the header and the output (txn_data) string
    $hdr = $post_msg;
    $hdr .= $host_msg;
    $hdr .= $content_msg;
    $hdr .= $len_msg;
    $hdr .=  $close_msg;  
    $out = $txn_data;

    //setup to stream the data string and write it to the gateway
    stream_set_timeout($fp, 10);
    fwrite($fp,$hdr.$out);

    //  now read the xml result from the gateway and print it to the screen
    while(!feof($fp))	{
	    $result .= fread($fp,128);
	  }

    print $result;

   fclose($fp);

	  }

?>
