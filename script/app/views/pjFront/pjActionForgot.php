<div class="fdLoader"></div>
<?php
include PJ_VIEWS_PATH . 'pjFront/elements/locale.php'; 
$index = $_GET['index'];
?>
<div class="fdContainerInner">
	<div id="fdMain_<?php echo $index; ?>" class="fdMain">
		<div class="fdFormContainer">
			<div class="fdFormHeading"><?php echo strtoupper(__('front_forgot_password', true, false)); ?></div>
			<form id="fdForgotForm_<?php echo $index;?>" action="" method="post" class="fdForm">
				<p class="fdParagraph">
					<label class="fdTitle"><?php __('front_email_address'); ?> <span class="fdRed">*</span>:</label>
					<input type="text" name="email" class="fdText fdW100p" data-required="<?php __('front_email_address_required');?>" data-email="<?php __('front_email_not_valid');?>"/>
				</p>
			</form>
			<div class="fdOverflow fdButtonContainer">
				<a href="#" class="fdButton fdNormalButton fdButtonGetLogin fdFloatLeft"><?php __('front_button_back');?></a>
				<a href="#" class="fdButton fdOrangeButton fdButtonNext fdButtonSend fdFloatRight"><?php __('front_button_send');?></a>
			</div>
			<div id="fdForgotMessage_<?php echo $index;?>" class="fdLoginMessage"></div>
		</div>
	</div>
	<div id="fdCart_<?php echo $index; ?>" class="fdCart"><?php include PJ_VIEWS_PATH . 'pjFront/elements/cart.php'; ?></div>
</div>