<div class="fdLoader"></div>
<?php
include PJ_VIEWS_PATH . 'pjFront/elements/locale.php'; 
$index = $_GET['index'];
?>
<div class="fdContainerInner">
	<div id="fdMain_<?php echo $index; ?>" class="fdMain">
		<div class="fdFormContainer">
			<div class="fdFormHeading"><?php echo strtoupper(__('front_login_to_account', true, false)); ?></div>
			<form id="fdLoginForm_<?php echo $index;?>" action="" method="post" class="fdForm">
				<input type="hidden" name="address" value="<?=$_SESSION['order_data']['o_address']?>" />
				<input type="hidden" name="d_address_1" value="" />
				<input type="hidden" name="d_address_2" value="" />
				<input type="hidden" name="d_city" value="" />
				<input type="hidden" name="d_country_id" value="" />
				<input type="hidden" name="d_date" value="" />
				<input type="hidden" name="d_hour" value="" />
				<input type="hidden" name="d_location_id" value="<?=$_SESSION['order_data']['o_location_id']?>" />
				<input type="hidden" name="d_minute" value="" />
				<input type="hidden" name="d_notes" value="" />
				<input type="hidden" name="loadTypes" value="1" />
				<input type="hidden" name="p_date" value="<?=$_SESSION['order_data']['o_date']?>" />
				<input type="hidden" name="p_hour" value="<?=$_SESSION['order_data']['p_hour']?>" />
				<input type="hidden" name="p_location_id" value="<?=$_SESSION['order_data']['o_location_id']?>" />
				<input type="hidden" name="p_minute" value="<?=$_SESSION['order_data']['p_minute']?>" />
				<input type="hidden" name="type" value="pickup" />
				<input type="hidden" name="user_id" value="<?=$_SESSION['order_data']['o_user_id']?>" />
				<p class="fdParagraph">
					<label class="fdTitle"><?php __('front_email_address'); ?> <span class="fdRed">*</span>:</label>
					<input type="text" name="login_email" id="email" class="fdText fdW100p" data-required="<?php __('front_email_address_required');?>" data-email="<?php __('front_email_not_valid');?>"/>
				</p>
				<p class="fdParagraph">
					<label class="fdTitle"><span class="fdBlock fdFloatLeft"><?php __('front_password'); ?> <span class="fdRed">*</span>:</span><span class="fdBlock fdFloatRight"><a class="fdForogtPassword" href="#"><?php __('front_forgot_password');?></a></span></label>
					<input type="password" name="login_password" class="fdText fdW100p" data-required="<?php __('front_password_required');?>"/>
				</p>
			</form>
			<div class="fdOverflow fdButtonContainer">
				<a href="#" class="fdButton fdNormalButton fdButtonGetCategories fdFloatLeft"><?php __('front_button_back');?></a>
				<a href="#"  class="fdButton fdOrangeButton fdButtonNext fdButtonLogin fdFloatRight"><?php __('front_button_login');?></a>
			</div>
			<div id="fdLoginMessage_<?php echo $index;?>" class="fdLoginMessage"></div>
			<div class="fdOverflow fdButtonContainer">
				<!--<label><?php __('front_do_not_have_account');?>&nbsp;<a class="fdContinue" href="#"><?php __('front_continue');?></a></label>-->
				<label><?php __('front_do_not_have_account');?>&nbsp;<a class="fdButtonPostPrice" href="#"><?php __('front_continue');?></a></label>
			</div>
		</div>
	</div>
	<div id="fdCart_<?php echo $index; ?>" class="fdCart"><?php include PJ_VIEWS_PATH . 'pjFront/elements/cart.php'; ?></div>
</div>
