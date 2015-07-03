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
	$filter = __('filter', true, false);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionIndex"><?php __('menuExtras'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionCreate"><?php __('lblAddExtra'); ?></a></li>
		</ul>
	</div>
	
	<?php pjUtil::printNotice(__('infoExtrasTitle', true, false), __('infoExtrasDesc', true, false)); ?>
	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		<br class="clear_both" />
	</div>
	
	<div id="grid"></div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.extra_name = "<?php __('lblExtraName'); ?>";
	myLabel.products_used = "<?php __('lblProductsUsed'); ?>";
	myLabel.price = "<?php __('lblPrice'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.active = "<?php echo $filter['active']; ?>";
	myLabel.inactive = "<?php echo $filter['inactive']; ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	</script>
	<?php
}
?>