<?php
$response1=$_SESSION["response1"];
$responseAddress=$_SESSION["responseAddress"];
//echo "Merchant Address".$responseAddress;
//echo "merchant id here".$_SESSION["merchantId"];
$result=json_decode($response1);
$resultAddress=json_decode($responseAddress);
$name = $result->name;
$address =$resultAddress->address1;
//echo "session".$name;
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminMerchant&amp;action=pjActionIndex">Merchant</a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminMerchant&amp;action=pjActionCreate">Add Merchant</a></li>
		</ul>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminMerchant&amp;action=pjActionCreate" method="post" id="frmCreateUser" class="form pj-form" autocomplete="off">
		
		<p>
			<label class="title">Merchant Id</label>
			<span class="inline_block">
				<input type="text" name="merchant_id" id="merchant_name" class="pj-form-field w250 required" />
			</span>
		</p>
		
		<p>
			<label class="title">Access Token Id</label>
			<span class="inline_block">
				<textarea name="merchant_access_token_id" id="merchant_address" class="pj-form-field w250 required"></textarea>
			</span>
		</p>		
		
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="Get Details"  class="pj-button" />
		</p>
	</form>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminMerchant&amp;action=pjActionCreate" method="post" id="frmCreateUser" class="form pj-form" autocomplete="off">
		<input type="hidden" name="merchant_create" value="1" />
		<input type="hidden" name="user_id" value="<?php echo $_SESSION['admin_user']['id']; ?>" />
		<p>
			<label class="title">Merchant Name:-</label>
			<span class="inline_block">
				<input type="text" name="merchant_name" id="merchant_name" class="pj-form-field w250 required" value="<?php echo $name ?>"/>
			</span>
		</p>
		
		<p>
			<label class="title">Merchant Address:-</label>
			<span class="inline_block">
				<textarea name="merchant_address" id="merchant_address" class="pj-form-field w250 required"><?php echo $address ?></textarea>
			</span>
		</p>		
		
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="Add"  class="pj-button" />
		</p>
	</form>
	
	<?php
	unset($_SESSION["response1"]);
	unset($_SESSION["responseAddress"]);
	?>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.email_taken = "<?php __('pj_email_taken', false, true); ?>";
	</script>
	<?php
}
?>
