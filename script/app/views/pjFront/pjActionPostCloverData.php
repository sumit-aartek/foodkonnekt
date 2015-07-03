<div class="fdContainerInner">
	<?php
	$voucher = array();
	if(isset($_SESSION['voucher']))
	{
		$voucher = $_SESSION['voucher'];
	}
	$FORM = $_SESSION['form'];
	$p_location_id = $tpl['lc_arr']['location_id'];
	$payment_method = $tpl['lc_arr']['payment'];
	$cart_box = $tpl['lc_arr']['cart_box'];
	$total = $tpl['lc_arr']['price'];
	$tax = $tpl['lc_arr']['tax'];
	$msg = array();
	$msg['p_location_id'] = $p_location_id;
	$msg['payment_method'] = $payment_method;
	$msg['cart_info'] = $cart_box;
	$msg['voucher'] = $voucher;
	$msg['form'] = $FORM;
	include PJ_VIEWS_PATH . 'pjFront/elements/createCustomer.php';
	
	echo '<pre>';
	print_r($msg);
	echo '</pre>';
	$_SESSION['cardData'] = array(
		'total' => $total,
		'o_user_id' => $_SESSION['order_data']['o_user_id'],
		'o_m_name' => $_SESSION['order_data']['o_user_name'],
		'clover_order_id' => $lastOrderId,
		'clover_mid' => $CLOVER_MID,
		'clover_access_token' => $ACCESS_TOKEN
	);
	?>
	<input type="hidden" id="finalTotal" value="<?php echo base64_encode(round($total, 2)); ?>">
	<input type="hidden" id="o_user_id" value="<?php echo base64_encode($_SESSION['order_data']['o_user_id']); ?>">
	<input type="hidden" id="o_merchant_name" value="<?php echo base64_encode($_SESSION['order_data']['o_user_name']); ?>">
	<input type="hidden" id="clover_order_id" name="clover_order_id" value="<?php echo $lastOrderId; ?>" />
	<input type="hidden" id="clover_merchant_id" name="clover_merchant_id" value="<?php echo $CLOVER_MID; ?>" />
	<input type="hidden" id="clover_access_token" name="clover_access_token" value="<?php echo $ACCESS_TOKEN; ?>" />
</div>