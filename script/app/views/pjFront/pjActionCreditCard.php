<div>
	<?php
	$payment_method = $_GET['payment'];
	$location_id = $_GET['location_id'];
	$amount = $_GET['price'];
	$user_id = $_SESSION['order_data']['o_user_id'];
	?>
	<input type="hidden" name="total" id="#total" value="<?=$amount?>" />
	<input type="hidden" name="o_user_id" id="#o_user_id" value="<?=$user_id?>" />
	<input type="hidden" name="clover_order_id" id="#clover_order_id" value="<?=$amount?>" />
	<input type="hidden" name="clover_merchant_id" id="#clover_merchant_id" value="<?=$amount?>" />
	<input type="hidden" name="clover_access_token" id="#clover_access_token" value="<?=$amount?>" />
</div>