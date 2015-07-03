<?php
//Set DB login Details--------------
$servername = "localhost";
$username   = "desisaud_foodkon";
$password   = "PK5LeRWz;(9W";
$dbname     = "desisaud_foodkonnekt";

//Create DB connection---------------------
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

//echo $CLOVER_MID;
$lineItemUrl = $ordercreatedURL . '/line_items?access_token=' . $ACCESS_TOKEN;
function file_post_lineItems($lineItemUrl, $data)
{
    $opts    = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => $data
        )
    );
    $context = stream_context_create($opts);
    return file_get_contents($lineItemUrl, false, $context);
}
$total      = round($total, 2);
$totalPrice = $total * 100;
$productid = '';

foreach ($cart_box['cart'] as $hash => $item)
{
    foreach ($cart_box['product_arr'] as $product)
	{
        if ($product['id'] == $item['product_id'])
		{
            echo '<br>';
            $productid  = $product['id'];            
            
            $sqll = "SELECT CLOVER_LINE_ITEM_ID, PHPJABBER_PRODUCT_ID, CLOVER_MID FROM  clover_phpjabber_lineitem_product_map WHERE PHPJABBER_PRODUCT_ID='$productid' and CLOVER_MID='$CLOVER_MID'";
            $resultt         = $conn->query($sqll);
            $cloverProductId = '';
            if ($resultt->num_rows > 0) {
                // output data of each row
                while ($row = $resultt->fetch_assoc()) {
                    // echo "<br> CLOVER_LINE_ITEM_ID: ". $row["CLOVER_LINE_ITEM_ID"]. " - PHPJABBER_PRODUCT_ID: ". $row["PHPJABBER_PRODUCT_ID"]. " - CLOVER_MID:" . $row["CLOVER_MID"] . "<br>";
                    $cloverProductId = $row["CLOVER_LINE_ITEM_ID"];
                }
            } else {
                //echo "0 results from lineitem";
            }
            
            // echo "<br>clover product ID:".$cloverProductId."<br>";
            echo "<br><br>";
            /*  $cloverProductId="2ZV3NENW9XXEE";*/
            /*   }
            else
            {
            $cloverProductId="4Q7WW0BGPKANR";
            }*/
            $data_string = '{"unitQty": "2","createdTime": "","modifications": [{ "modifier": { "id": "SR9THYCQQ03XP","price": "088", "name": "","modifierGroup": {"id": "SR9THYCQQ03XP"  }, "alternateName": ""
                                                     }, "amount": "088", "id": "","name": "","alternateName": "" } ], "taxRates": [ { "id": "", "rate": "077", "items": [ { "id": "' . $cloverProductId . '"} ],
                                                     "isDefault": false, "name": "" } ],  "userData": "",  "discountAmount": "", "alternateName": "","exchanged": false, "refunded": false, "price": "",
                                                     "binName": "", "isRevenue": false, "unitName": "", "discounts": [ {"amount": "", "id": "","percentage": "", "name": "","discount": { "id": ""  } } ], "item": {
                                                     "id": "' . $cloverProductId . '" }, "name": "", "printed": false,"note": "","itemCode": ""}';
            $itemQty     = $item['cnt'];
            for ($x = 0; $x < $itemQty; $x++) {
                $response = file_post_lineItems($lineItemUrl, $data_string);
                $res      = json_decode($response);
                foreach ($item['extra_arr'] as $extra_id => $extra) { //echo '<br>extra:    ';
                    //	echo $extra_id;
                    $extraQua = $extra['qty'];
                    //	echo $extra['name'];
                    $i++;
                    
                    $sql = "SELECT 	PHPJABBER_EXTRA_ID, CLOVER_MODIFIRE_ID, CLOVER_MID, CLOVER_MODIFIRE_GROUP_ID FROM clover_phpjabber_modifire_extra_map WHERE PHPJABBER_EXTRA_ID='$extra_id' and CLOVER_MID='$CLOVER_MID'";
                    $result                = $conn->query($sql);
                    $cloverModifierId      = '';
                    $cloverModifierGroupId = '';
                    if ($result->num_rows > 0) {
                        // output data of each row
                        while ($row = $result->fetch_assoc()) {
                            // echo "<br> CLOVER_LINE_ITEM_ID: ". $row["CLOVER_LINE_ITEM_ID"]. " - PHPJABBER_PRODUCT_ID: ". $row["PHPJABBER_PRODUCT_ID"]. " - CLOVER_MID:" . $row["CLOVER_MID"] . "<br>";
                            $cloverModifierId      = $row["CLOVER_MODIFIRE_ID"];
                            $cloverModifierGroupId = $row["CLOVER_MODIFIRE_GROUP_ID"];
                        }
                    } else {
                        echo "0 results";
                    }                    
                    for ($i = 0; $i < $extraQua; $i++) {
                        addModifier($res->id, $ordercreatedURL, $cloverModifierId, $cloverModifierGroupId, $ACCESS_TOKEN);
                    }
                }
                //  addModifier($res->id,$ordercreatedURL);
            }
        }
    }
}
$mproduct_id = $voucher['voucher_product_id'];
$mcategory_id = $voucher['voucher_category_id'];
$voucher_type = $voucher['voucher_type'];
$itemCode    = $voucher['voucher_code'];
$discount = '';
if ($itemCode !== false) {
    $voucher_discount = $voucher['voucher_discount'];
    if (!empty($mproduct_id)) {
        $discount = addDiscountWithItem($res->id, $ordercreatedURL, $voucher_discount, $voucher_type, $ACCESS_TOKEN);
    } elseif (!empty($mcategory_id)) {        
        $discount = addDiscountWithCategory($res->id, $ordercreatedURL, $voucher_discount, $voucher_type, $ACCESS_TOKEN);
    }
}
$msg['item_dis_id'] = $productid;
$msg['discount'] = $discount;
$msg['lineitem'] = $response;
//Close connection----------
$conn->close();
?>