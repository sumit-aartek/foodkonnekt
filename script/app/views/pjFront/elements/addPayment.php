<?php
$user_id = $_SESSION['order_data']['o_user_id'];

//Get data from tender table.
$pjTenderModel = pjTenderModel::factory()
	->where('location_id', $p_location_id)
	->where('tender_type', $payment_method)
	->findAll()
	->getData();	
$tenderId = $pjTenderModel[0]['tender_id'];

$addPaymentUrl = $ordercreatedURL . '/payments?access_token=' . $ACCESS_TOKEN;
function file_post_addPayment($addPaymentUrl, $data)
{
    $opts    = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => $data
        )
    );
    $context = stream_context_create($opts);
    return file_get_contents($addPaymentUrl, false, $context);
}
$total       = round($total, 2);
$totalPrice  = $total * 100; 
$data_string = '{
  "result": "SUCCESS",
  "clientCreatedTime": "",
  "createdTime": "",
  "modifiedTime": "",
  "offline": 1,
  "amount": "' . $totalPrice . '",
  "cashTendered": "' . $totalPrice . '",
  "tipAmount": "",
  "taxAmount": "",
  "tender": {
    "id": "' . $tenderId . '",
    "enabled": 0,
    "supportsTipping": 0,
    "visible": 0,
    "opensCashDrawer": 0,
    "editable": 0
  }
 
}';

$addPaymentResponse = file_post_addPayment($addPaymentUrl, $data_string);
$msg['totalPrice'] = $total;
$msg['tax'] = $tax;
$msg['tenderId'] = $tenderId;
$msg['add_payment'] = $addPaymentResponse;
?>