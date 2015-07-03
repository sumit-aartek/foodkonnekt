<?php
$CloverCustomerId = '';
$servername       = "localhost";
$username         = "desisaud_foodkon";
$password         = "PK5LeRWz;(9W";
$dbname           = "desisaud_foodkonnekt";

//Create DB connection--------------------------------
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT  o.CLOVER_MID, o.CLOVER_ACCESS_TOKEN_ID , employee_id FROM foodkonnektfood_delivery_locations_map o WHERE o.PHPJABBER_LOCATION_ID=" . $p_location_id;
$result       = $conn->query($sql);

$CLOVER_MID   = '';
$ACCESS_TOKEN = '';
$employee_id ='';
if ($result->num_rows > 0) {    
    while ($row = $result->fetch_assoc()) {        
        $CLOVER_MID   = $row["CLOVER_MID"];
        $ACCESS_TOKEN = $row["CLOVER_ACCESS_TOKEN_ID"];
		$employee_id = $row["employee_id"];
    }
} else {
}

$urlPrefix = 'https://api.clover.com:443/v3/merchants/' . $CLOVER_MID;
$url       = $urlPrefix . '/customers/?access_token=' . $ACCESS_TOKEN;
$CloverCustomerId = '';
if ($controller->isFrontLogged())
{
	$sqlClient = "SELECT CloverCustomerID FROM clover_phpjabber_customer_client_map WHERE phpJabberCId = " . $CLIENT['id'];    
    $result    = $conn->query($sqlClient);	
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $CloverCustomerId = $row["CloverCustomerID"];            
        }
    } else {
        echo "<br>No Clover id found";
    }    
} else {
    //echo "on customer".$url;
    function file_post_contents1($url, $data)
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
    $data_string      = '{"id": "", "lastName": "","customerSince": "","phoneNumbers": [ {"id": "","phoneNumber":"' . $FORM['c_phone'] . '"}], "marketingAllowed": false,"firstName": "' . $FORM['c_name'] . '","emailAddresses": [{ "id": "","emailAddress":"' . $FORM['c_email'] . '"}]}';
    $response1        = file_post_contents1($url, $data_string);
    $result           = json_decode($response1);
    $CloverCustomerId = $result->id;
    
    $sql = "SELECT id FROM foodkonnektfood_delivery_clients ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    $PHPJABBER_CUSTOMER_ID = '';
    $size = $result->num_rows;
    if ($result->num_rows > 0) {        
        while ($row = $result->fetch_assoc()) {            
            $PHPJABBER_CUSTOMER_ID = $row["id"];            
            $PHPJABBER_CUSTOMER_ID = $PHPJABBER_CUSTOMER_ID + 1;
        }
    } else {
        //   echo "0 results";
    }
    
    $sql = "INSERT INTO clover_phpjabber_customer_client_map (phpJabberCId, CloverCustomerID)VALUES('" . $PHPJABBER_CUSTOMER_ID . "', '" . $CloverCustomerId . "')";
    if ($conn->query($sql) === TRUE) {
        //echo "New records created successfully in multy lang";
    } else {
        //echo "Error: " . $sql . "<br>" . $conn->error;
    }    
} //End of file.
//Close DB connection---------------------
$msg['marchant_id'] = $CLOVER_MID;
$msg['access_token'] = $ACCESS_TOKEN;
$msg['client_id'] = $CloverCustomerId;

$conn->close();

//Include external files-------------------------
include PJ_VIEWS_PATH . 'pjFront/elements/curl.php';
include PJ_VIEWS_PATH . 'pjFront/elements/addModifier.php';
include PJ_VIEWS_PATH . 'pjFront/elements/addDiscountWithItem.php';
include PJ_VIEWS_PATH . 'pjFront/elements/addDiscountWithCategory.php';
include PJ_VIEWS_PATH . 'pjFront/elements/lineItem.php';
include PJ_VIEWS_PATH . 'pjFront/elements/addPayment.php';
?>