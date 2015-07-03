<?php
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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionIndex"><?php __('menuVouchers'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionCreate"><?php __('lblAddVoucher'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoVoucherListTitle', true, false), __('infoVoucherListDesc', true, false)); 
	?>
	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch', false, true); ?>" />
		</form>
		<br class="clear_both" />
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.code = "<?php __('lblVoucherCode', false, true); ?>";
	myLabel.discount = "<?php __('lblDiscount', false, true); ?>";
	myLabel.type = "<?php __('lblType', false, true); ?>";
	myLabel.valid = "<?php __('lblValid', false, true); ?>";
	myLabel.date_time_valid = "<?php __('lblDateTimeValid', false, true); ?>";
	myLabel.exported = "<?php __('lblExport', false, true); ?>";
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	</script>
	<?php
}
?>