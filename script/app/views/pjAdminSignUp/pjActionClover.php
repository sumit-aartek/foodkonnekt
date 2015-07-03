<div>
<form method="post" name="cloverFrom" action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSignUp&amp;action=pjActionForm" autocomplete="off">
	<input type="hidden" name="merchant_id" value="<?php echo $tpl['arr']['merchant_id'] ?>" />
	<input type="hidden" name="employee_id" value="<?php echo $tpl['arr']['employee_id'] ?>" />
	<input type="hidden" name="client_id" value="<?php echo $tpl['arr']['client_id'] ?>" />
	<input type="hidden" name="access_token_key" id="token_key" value="" />	
</form>
</div>