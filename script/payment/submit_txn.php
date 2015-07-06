<?php
require_once '../app/config/config.inc.php';
include_once('rsa.php');
include_once('bigInteger.php');
include_once('Math_BigInteger.php');
$c_order_id = $_POST['c_order_id'];
$c_user_id = $_POST['c_user_id'];
$c_merchant_name = $_POST['c_merchant_name'];
$c_merchant_id = $_POST['c_merchant_id'];
$c_access_teken = $_POST['c_access_token'];
try{
$url = "https://api.clover.com:443/v2/merchant/".$c_merchant_id."/pay?access_token=". $c_access_teken;

$first6 = substr($_POST['CardNum'], 0, 6);
$last4 = substr($_POST['CardNum'], -4);
$prefix = '11111111';
$card = $_POST['CardNum'];
//echo "card no".$card;
//$data= $card.$prefix;
//echo "dard with prefix".$data;
$GenPubKey = '-----BEGIN CERTIFICATE-----
MIIFIjCCBAqgAwIBAgIDCpptMA0GCSqGSIb3DQEBBQUAMDwxCzAJBgNVBAYTAlVT
MRcwFQYDVQQKEw5HZW9UcnVzdCwgSW5jLjEUMBIGA1UEAxMLUmFwaWRTU0wgQ0Ew
HhcNMTMwMjE1MDMzMTMxWhcNMTcwMjE2MTM1NjM3WjCBvTEpMCcGA1UEBRMgWXBH
SjJiRmZoL1hCUUZMaTZ0VlRtSTJiLTEwbnItalAxEzARBgNVBAsTCkdUMTU2MTU3
NzQxMTAvBgNVBAsTKFNlZSB3d3cucmFwaWRzc2wuY29tL3Jlc291cmNlcy9jcHMg
KGMpMTMxLzAtBgNVBAsTJkRvbWFpbiBDb250cm9sIFZhbGlkYXRlZCAtIFJhcGlk
U1NMKFIpMRcwFQYDVQQDEw5hcGkuY2xvdmVyLmNvbTCCASIwDQYJKoZIhvcNAQEB
BQADggEPADCCAQoCggEBAN4OwTp0wmJJ888gVwYm7qyiIyfowed0ggwB16TpsLKN
xk92A+0ZlW1cZcCBE7p+xGULgR7yFGMgktnmhgoXp93KcSotv2TpNpiakXUCHk+J
w0kULIqRqmfPbunXjgfLlB3u+b1MHFwi4B3DMBsC1ANwMiSdZMH8D6JCirlKFDK9
qlSk04CqStbwJ/Mbn80NfxTmZvH66iiQAkGPxxT+GnruYxYBiCBI2V7E3b1MaWVI
VbLbnXYRVLEyIXJpwb61l8sE4VIZ5AOU6hTA4DQTFQESLF9jOENH1W2zC+W/n9WP
RqANblF+lmcRKX5it7tkqALPYL6ra8ZvDdpmEfYereUCAwEAAaOCAakwggGlMB8G
A1UdIwQYMBaAFGtpPWoYQkrdjwJlOf01JIZ4kRYwMA4GA1UdDwEB/wQEAwIFoDAd
BgNVHSUEFjAUBggrBgEFBQcDAQYIKwYBBQUHAwIwGQYDVR0RBBIwEIIOYXBpLmNs
b3Zlci5jb20wQwYDVR0fBDwwOjA4oDagNIYyaHR0cDovL3JhcGlkc3NsLWNybC5n
ZW90cnVzdC5jb20vY3Jscy9yYXBpZHNzbC5jcmwwHQYDVR0OBBYEFI/kaOzeB3iq
MjVs1/evSK98lzf7MAwGA1UdEwEB/wQCMAAweAYIKwYBBQUHAQEEbDBqMC0GCCsG
AQUFBzABhiFodHRwOi8vcmFwaWRzc2wtb2NzcC5nZW90cnVzdC5jb20wOQYIKwYB
BQUHMAKGLWh0dHA6Ly9yYXBpZHNzbC1haWEuZ2VvdHJ1c3QuY29tL3JhcGlkc3Ns
LmNydDBMBgNVHSAERTBDMEEGCmCGSAGG+EUBBzYwMzAxBggrBgEFBQcCARYlaHR0
cDovL3d3dy5nZW90cnVzdC5jb20vcmVzb3VyY2VzL2NwczANBgkqhkiG9w0BAQUF
AAOCAQEAvf6I/Mwc1A9pH0b2pIipc1XnWMkHIvO3ZZlf1OkuCLkFd6TdDQcdxkUV
PqmuIbAWKMzJCKMSa4h1vVOp+UXJCBtBVh1/0LpLiXHps3WOAPWFXUaAkwZQA/lV
vXExOlYA8yZHXDKsFRZXit0HBk0lyuhv88XPAdtDs0yUQLyvLfpyYfxQiyU/DQDm
DHbf445zjEBWemMwLvE98sIn0wQaVy9KdQ+iKrQaBJfF3Uk45QgunRvkrtbQK5B4
DjctG7jr0jg/RVxKVQ/u9/UFCsqh5jseqyFpbowcsG2ncywuZkfZD+SsRYtINuSk
+eFGEvK0D8EPWC3c2yZ087lU2x65aw==
-----END CERTIFICATE-----';
function encrypt($data, $encrypted, $GenPubKey ) 
{
//echo "genpubley is====== ".$GenPubKey.'<br/>';
	if (openssl_public_encrypt($data, $encrypted, $GenPubKey ))
	{
		$data = base64_encode($encrypted);
		
		}
	else
		throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');
	return $data;
}
$encrypted= '';
$data=encrypt($data, $encrypted, $GenPubKey);
$string_data = '{
	"orderId": "'. $c_order_id .'",
	"taxAmount": 9,
	"zip": "'. $_POST['zip'] .'",
	"expMonth": '. $_POST['ExpMonth'] .',
	"cvv": "'. $_POST['CVNum'] .'",
	"amount": '. $_POST['Amount'] .',
	"currency": "usd",
	"last4": "'. $last4 .'",
	"token": "",
	"expYear": '. $_POST['ExpYear'] .',
	"first6": "'. $first6 .'",
	"cardEncrypted": "'. $data.'"
}';

function file_post_contents($url, $data)
{
	$opts    = array(
		'http' => array(
			'method' => 'POST',
			'header' => 'Content-type: application/json',
			'content' => $data
		)
	);
	$context = stream_context_create($opts);
	return file_get_contents($url, false, $context);
}

$response_data = file_post_contents($url, $string_data);
$result = json_decode($response_data);
}
catch(Exception $e){
	echo "Please Re-Enter Card Info Or placed an new order";
}

  /*include 'TGATE_card_txn.class.php';

  $currrent_txn = new TGATE_CardTxn();

  $useCurl = TRUE;
  //$useCurl = FALSE;  //Set $useCurl = FALSE to send txn via SOAP
  $read_xml = TRUE;

  $MagData = "";
  $PNRef = "";
  $ExtData = "";

  if ($useCurl == TRUE) {

    $xml_result = $currrent_txn->CURLTxn('Sale',$_POST['CardNum'],$_POST['ExpYear'],$MagData,$_POST['NameOnCard'],$_POST['Amount'],$_POST['InvNum'],$PNRef,$_POST['Zip'],$_POST['Street'],$_POST['CVNum'],$ExtData);
     }  else  {
    $xml_result = $currrent_txn->SOAPTxn('Sale',$_POST['CardNum'],$_POST['ExpYear'],$MagData,$_POST['NameOnCard'],$_POST['Amount'],$_POST['InvNum'],$PNRef,$_POST['Zip'],$_POST['Street'],$_POST['CVNum'],$ExtData);
    }  //end-if for $use_curl

    // print_r($xml_result);    //uncomment to view xml in page source

  if ($read_xml == TRUE) {  

    $reader = new XMLReader(); 
    $reader->XML($xml_result);
    while ($reader->read()) {

      if($reader->name != 'Response' && $reader->nodeType == XMLReader::ELEMENT )
      {
        $name = $reader->name;
       // echo $name .": " ;
      }
  
      if (in_array($reader->nodeType, array(XMLReader::TEXT, XMLReader::CDATA, XMLReader::WHITESPACE, XMLReader::SIGNIFICANT_WHITESPACE)))
      {
        $value= $reader->value;
        //echo $value ."<br>" ;
      }
		$auth_code = '';
      if(trim($reader->value) != ''){
            if($name == 'Result'){ $result_code = $reader->value;}  //success = 0, any other result = error           
            if($name == 'RespMSG'){ $response_message =  $reader->value;}
            if($name == 'Message'){ $message = $reader->value;}
            if($name == 'PNRef'){ $pnref = $reader->value; }
            if($name == 'HostCode'){ $host_code = $reader->value;}
            if($name == 'AuthCode'){ $auth_code = $reader->value;}
            if($name == 'GetCommercialCard'){ $commercial = $reader->value; }
            if($name == 'ExtData'){ $extdata = $reader->value;}
			
      }   //end if

    }  //end-while

    $reader->close();

	/*	echo 'Credit Card Receipt' . "<br>";
		echo 'Name:    ' . $_POST['NameOnCard'] ."<br>";
		echo 'Addr:    ' . $_POST['Street'] ."<br>";
		echo 'Card#:   ' . '************' . substr($_POST['CardNum'], -4) ."<br>";
		echo 'Amt:     ' . $_POST['Amount'] ."<br>";
		echo 'PNRef:   ' . $pnref ."<br>";
		echo 'Result:  ' . $result_code ."<br>";
		echo 'RespMsg: ' . $response_message ."<br>";
		echo 'Message: ' . $message ."<br>";
		echo 'HostCd:  ' . $host_code ."<br>";
		echo 'AuthCd:  ' . $auth_code ."<br>";
		echo 'Commercial:  ' . $commercial ."<br>";
		echo 'ExtData:  ' . $extdata ."<br>";
        $last4=substr($_POST['CardNum'], -4);
		echo $last4;
		
     }   else    {
	$result = explode("stdClass::__set_state(",$result);
     $hdg = $result[1];
     $body = str_replace(chr(39),"",str_replace("array(","",str_replace("))","",$result[2])));
     $body = str_replace("=>",":",$body);
     $body = str_replace(chr(44),"<br>",$body);
     //echo $body;
    } 
	echo 'Result:  ' . $result_code ."<br>";
	if($result_code==0)
	{*/	
	?>
	<html xmlns="http://www.w3.org/1999/xhtml">  
	<head>
		<title></title>
		<link href="style/receipt.css" rel="stylesheet" type="text/css" />
	</head> 
	<body> 
		<table width="100%" height="100%">   
			<?php if($result->result === 'APPROVED') { ?>
			<tr>       
				<td valign="middle" align="center">    
					<table class="receipt">    
						<tr>
							<td align="left" valign="bottom"><img src="images/tg_logo.png" /></td>
							<td align="right" valign="bottom"><h2><!--[MerchantName]--></h2></td>
						</tr>  
						<tr>   
							<td class="instructions" colspan="2">
								<hr />
								Your payment was successfully processed. Please print this receipt for your records.  
								<hr />
							</td>  
						</tr> 
						<tr>   
							<td align="left" colspan="2">
								<table class="details">
									<tr><td colspan="2"><h3>Payment Details</h3></td></tr>
									<tr><td class="spacerRow"></td></tr>
									<tr>
										<th>Transaction No:</th>
										<td><?php echo $pnref ;?></td>  
									</tr>
									<tr>
										<th>Total Amount:</th>
										<td><?php echo $_POST['Amount'] ;?></td>  
									</tr>
									<tr>
										<th>Invoice No:</th>
										<td><?php echo $_POST['InvNum'] ;?></td>
									</tr> 
									<tr>
										<th>Auth code:</th>
										<td><?php echo $auth_code ;?></td>
									</tr> 
								</table>
							</td> 
						</tr>
						<tr>  
							<td align="center" class="instructions" colspan="2">
								Click <a href="<?php echo PJ_BASE_PATH. $c_merchant_name?>/restaurants/<?=$c_user_id?>">here</a> to return to <!--[MerchantName]-->
							</td>  
						</tr> 
					</table>
					<?php
					$servername = PJ_HOST;
					$username = PJ_USER;
					$password = PJ_PASS;
					$dbname = PJ_DB;
							
					// Create connection
					$conn = new mysqli($servername, $username, $password, $dbname);
					// Check connection
					if ($conn->connect_error) {
						 die("Connection failed: " . $conn->connect_error);
					} 

					$sql = "INSERT INTO foodkonnektfood_delivery_transactionDetails (transaction_no, last_4, invoice_number, phpjabber_customerId, phpjabber_orderID)VALUES('".$pnref."', '".$last4."', '".$_POST['InvNum']."', '".$_POST['clientId']."', '".$_POST['orderId']."')";
					$result = $conn->query($sql);
							?>
				</td>
			</tr>
			<?php } else { ?>
			<tr>
				<td>Your Payment is not done, please try again!</td>
			</tr>
			<?php } ?>
		</table>
	</body>
</html>
	<?php
	/*}
	else{
		echo "Duplicate transaction";
	}*/
?>
