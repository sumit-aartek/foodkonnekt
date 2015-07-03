<?php
function file_post_DiscountWithItem($url, $data)
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
function addDiscountWithItem($itemId, $modUrl, $voucher_discount, $voucher_type, $ACCESS_TOKEN)
{    
    $OrderDiscount_string = '';
	$url = $modUrl . '/line_items/' . $itemId . '/discounts/?access_token=' . $ACCESS_TOKEN;
    switch ($voucher_type) {
        case 'percent':
            $discount = $voucher_discount;
            $discount = round($discount, 0);
            $OrderDiscount_string = '{
				"amount": "",
				"percentage": "' . $discount . '",
				"name": "Discount:Item Discount",
				"discount": {"id": ""}
			}';
            break;
        case 'amount':			
            $OrderDiscount_string = '{
				"amount": "-' . $voucher_discount * 100 . '",
				"percentage": "",
				"name": "Discount:Item Discount",
				"discount": {"id": ""}
			}';
            break;
    }
    $response = file_post_DiscountWithItem($url, $OrderDiscount_string);
	return $response. '-Order String : '. $OrderDiscount_string;
}
?>