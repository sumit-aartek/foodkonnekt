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
	$statuses = __('order_statuses', true, false);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionIndex"><?php __('menuOrders'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionCreate"><?php __('lblAddOrder'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoOrdersListTitle', true, false), __('infoOrdersListDesc', true, false)); 
	?>
	
	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w200"/>
		</form>
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all"><?php __('lblAll'); ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="confirmed"><?php echo $statuses['confirmed']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="pending"><?php echo $statuses['pending']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="cancelled"><?php echo $statuses['cancelled']; ?></a>
		</div>
		<br class="clear_both" />
	</div>
	
	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['client_id']) && (int) $_GET['client_id'] > 0)
	{
		?>pjGrid.queryString += "&client_id=<?php echo (int) $_GET['client_id']; ?>";<?php
	}
	if (isset($_GET['type']) && !empty($_GET['type']))
	{
		?>pjGrid.queryString += "&type=<?php echo $_GET['type']; ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.name = "<?php __('lblName', false, true); ?>";
	myLabel.phone = "<?php __('lblPhone', false, true); ?>";
	myLabel.date_time = "<?php __('lblDateTime', false, false); ?>";
	myLabel.total = "<?php __('lblTotal', false, true); ?>";
	myLabel.type = "<?php __('lblType', false, true); ?>";
	myLabel.pickup = "<?php __('lblPickup', false, true); ?>";
	myLabel.delivery = "<?php __('lblDelivery', false, true); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.exported = "<?php __('lblExport', false, true); ?>";
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	myLabel.pending = "<?php echo $statuses['pending']; ?>";
	myLabel.confirmed = "<?php echo $statuses['confirmed']; ?>";
	myLabel.cancelled = "<?php echo $statuses['cancelled']; ?>";
	</script>
	<?php
}
?>