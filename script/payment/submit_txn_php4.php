<?php
    /* 
     * This is one of the PHP files to use in comjunction with other
     * PHP sample code files to process a transaction to TGATE's Payment Gateway.
     * 
     * This sample code uses either XMLParser (PHP v4.x) or DOMDocument (PHP v5.x) to 
     * process the XML result message from the gateway.
     * 
     * For questions regarding this sample code or for other code samples, 
     * contact developersupport@tgatepayments.com.
     *
     * Copyright 2010, TGATE Payments.
     */ 

  include 'TGATE_card_txn.class.php';

//Function to use at the start of an xml element
function start($parser,$element_name,$element_attrs)
  {
  switch($element_name)
    {
    case "RESULT":
  		echo "<br>" .'Credit Card Receipt' . "<br>";
		  echo 'Name:    ' . $_POST['NameOnCard'] ."<br>";
		  echo 'Addr:    ' . $_POST['Street'] ."<br>";
		  echo 'Card#:   ' . '************' . substr($_POST['CardNum'], -4) ."<br>";
		  echo 'Amt:     ' . $_POST['Amount'] ."<br>";
      echo "Result : ";
      break;
    case "RESPMSG":
      echo "RespMsg : ";
      break;
    case "MESSAGE":
      echo "Message : ";
      break;
    case "PNREF":
      echo "PNRef : ";
      break;
    case "HOSTCODE":
      echo "HostCode : ";
      break;
    case "AUTHCODE":
      echo "AuthCode : ";
      break;
    case "AVSRESULT":
      echo "AVSResult : ";
      break;
    case "AVSRESULTTXT":
      echo "AVSResultTxt : ";
      break;
    case "GETSTREETMATCHTXT":
      echo "GetStreetMatchTxt : ";
      break;
    case "GETZIPMATCHTXT":
      echo "GetZipMatchTxt : ";
      break;
    case "CVRESULTTXT":
      echo "CVResultTxt : ";
      break;
    case "CVRESULT":
      echo "CVResult : ";
      break;
    case "CVRESULTTXT":
      echo "CVResultTxt : ";
      break;
    case "GETCOMMERCIALCARD":
      echo "CommercialCard : ";
      break;
    case "EXTDATA":
      echo "ExtData : ";
      break;
    case "GETGETORIGRESULT":
      echo "OrigAuthCode : ";
      break;
    }
  }

//Function to use at the end of an xml element to write <br/>
function stop($parser,$element_name)
  {
  echo "<br />";
  }

//Function to use when finding character data
function char($parser,$data)
  {
  echo $data;
  }



  $current_txn = new TGATE_CardTxn();

  $MagData = "";
  $PNRef = "";
  $ExtData = "";

    $xml_result = $current_txn->CURLTxn('Sale',$_POST['CardNum'],$_POST['ExpDate'],$MagData,$_POST['NameOnCard'],$_POST['Amount'],$_POST['InvNum'],$PNRef,$_POST['Zip'],$_POST['Street'],$_POST['CVNum'],$ExtData);
    //print_r($xml_result);    //uncomment to view xml

// xml_parser works with php version 4
$xmlparser = xml_parser_create();
xml_set_element_handler($xmlparser,"start","stop");
xml_set_character_data_handler($xmlparser,"char");
xml_parse($xmlparser,$xml_result);
xml_parser_free($xmlparser);


//  uncomment below to use DOMDocument  works with php v5 and up//
/*
$xmlDoc = new DOMDocument();
$xmlDoc->loadXML($xml_result);

$x = $xmlDoc->documentElement;
foreach ($x->childNodes AS $item)
  {
    switch ($item->nodeName)
    {
    case "Result";
    case "RespMSG":
    case "Message":
    case "Message1":
    case "Message2":
    case "PNRef":
    case "HostCode":
    case "AuthCode":
    case "AVSResult":
    case "AVSResultTXT":
    case "GetStreetMatchTXT":
    case "GetZipMatchTXT":
    case "CVResultTXT":
    case "CVResult":
    case "CVResultTXT":
    case "GetCommercialCard":
    case "ExtData":
    print $item->nodeName . " : " . $item->nodeValue . "<br />";
    break;
    case "GetGetOrigResult":
    print "Orig PNRef" . " : " . $item->nodeValue . "<br />";
    }
  }
*/

?>

