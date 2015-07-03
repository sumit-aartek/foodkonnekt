 <?php
$url         = $urlPrefix . '/orders/?access_token=' . $ACCESS_TOKEN;
$data_string = '';
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
$total           = round($total, 2);
$totalPrice      = $total * 100;
$data_string     = '{
	
  "total": "' . $totalPrice . '",
  "state": "Open",
  "currency": "",
  "title": "",
  "testMode": false,
  "employee": {
    "id": "'.$employee_id.'"
  },
  "note": "",
  "clientCreatedTime": "",
  "createdTime": "",
  "modifiedTime": "",
  "manualTransaction": false,
  "customers": [
    {
  "id": "' . $CloverCustomerId . '",
  "lastName": "",
  "customerSince": "",
  "phoneNumbers": [
    {
      "id": "",
      "phoneNumber": "' . $FORM['c_phone'] . '"
    }
  ],
  "addresses": [
    {
      "id": "",
      "zip": "",
      "state": "",
      "address1": "",
      "address2": "",
      "address3": "",
      "country": "",
      "city": ""
    }
  ],
  "marketingAllowed": false,
  "firstName": "' . $FORM['c_name'] . '",
  "emailAddresses": [
    {
      "id": "",
      "emailAddress": "' . $FORM['c_email'] . '",
      "verifiedTime": ""
    }
  ]
}
  ],
  "groupLineItems": false,
  "isVat": false,
  "taxRemoved": false
}';
//echo "<br>".$data_string;
$response1       = file_post_contents($url, $data_string);
$result          = json_decode($response1);
$ordercreatedURL = $result->href;
$lastOrderId     = $result->id;
$servername      = "localhost";
$username        = "desisaud_foodkon";
$password        = "PK5LeRWz;(9W";
$dbname          = "desisaud_foodkonnekt";

$msg['last_order_id'] = $lastOrderId;

//Create DB connection---------------------
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO clover_phpjabber_order_map (CLOVER_ORDER_ID, CLOVER_MID)VALUES('" . $lastOrderId . "','" . $CLOVER_MID . "')";
if ($conn->multi_query($sql) === TRUE) {
    //echo "New records created successfully";
} else {
    //echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();

$msg['last_order'] = $lastOrderId;
//add coustomer
$urlOrderUpdate = $urlPrefix . '/orders/' . $lastOrderId . '/?access_token=' . $ACCESS_TOKEN;
function file_post_contents2($url, $data)
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
$OrderUpdate_string = '{

  "employee": {
    "id": "T03TVG9M6C7PG"
  },
  "note": "",
  
  "customers": [
    {
  "id": "' . $CloverCustomerId . '"
	}
  ]
 
}';
//echo "<br>OrderUpdate_string:".$OrderUpdate_string;
$response2          = file_post_contents2($urlOrderUpdate, $OrderUpdate_string);
//echo "<br>order update Response:".$response2;
$discount           = '';
$itemCode           = $controller->_get('voucher_code');
$mproduct_id        = $controller->_get('voucher_product_id');
$mcategory_id       = $controller->_get('voucher_category_id');
if ($controller->_get('voucher_code') !== false) {
    if (empty($mproduct_id) & empty($mcategory_id)) {
        $voucher_code     = $controller->_get('voucher_code');
        //add Discount
        $voucher_discount = $controller->_get('voucher_discount');
        switch ($controller->_get('voucher_type')) {
            case 'percent':
                $discount             = $voucher_discount;
                $discount             = round($discount, 0);
                $OrderDiscount_string = '{ "amount": "",
                                                                                                      "percentage": "' . $discount . '",
                                                                                                            "name": "Discount:' . $voucher_code . '",
 												        "discount": {
                                                                                                              "id": ""
                                                                                                                    }
                                                                                                        }';
                break;
            case 'amount':
                $discount             = $voucher_discount * 100;
                $OrderDiscount_string = '{ "amount": "-' . $discount . '",
                                                                                                      "percentage": "",
                                                                                                            "name": "Discount:' . $voucher_code . '",
 												        "discount": {
                                                                                                              "id": ""
                                                                                                                    }
                                                                                                        }';
                break;
        }
        $urlDiscountAdd = $urlPrefix . '/orders/' . $lastOrderId . '/discounts?access_token=' . $ACCESS_TOKEN;
        function file_post_contents3($url, $data)
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
        $response2 = file_post_contents3($urlDiscountAdd, $OrderDiscount_string);
    }
}

$msg['curl'] = $response2;
?>