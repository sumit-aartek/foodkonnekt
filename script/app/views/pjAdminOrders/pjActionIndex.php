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
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0, 6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
    $jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
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
			<!--<input type="text" name="q" class="pj-form-field pj-form-field-search w200"/>-->
			<select name="searchType" id="searchType" class="pj-form-field">
				<option value="">-- Search Type --</option>
				<option value="location">By Location</option>
				<option value="type">By Type</option>
				<option value="status">By Status</option>
			</select>
			<div class="valid_period">				
				<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
					<label class="title">From:</label>
					<input type="text" name="o_date_from" id="o_date_from" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="" />
					<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				</span>
				<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
					<label class="title">To:</label>
					<input type="text" name="o_date_to" id="o_date_to" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="" />
					<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				</span>
				<div style="clear:both"></div>
			</div>
			<div id="filter_location" class="filter_box" style="display:none">
				<select name="location_id[]" id="location_id" class="pj-form-field filter_box" style="display:none">				
					<?php foreach($tpl['location'] as $loc) { ?>
						<option value="<?=$loc['id']?>"><?=$loc['name']?></option>
					<?php } ?>
				</select>
				<input type="button" name="search" value="" id="search" class="pj-form-field pj-form-field-search" />
			</div>
			<select name="type" id="filter_type" class="pj-form-field filter_box" style="display:none">
				<option value="">-- <?php __('lblType'); ?> --</option>
				<?php
				foreach (__('types', true, false) as $k => $v)
				{
					?><option value="<?php echo $k; ?>"<?php echo isset($_GET['type']) ? ($_GET['type'] == $k ? ' selected="selected"' : null) : null;?>><?php echo stripslashes($v); ?></option><?php
				}
				?>
			</select>			
			<select name="status" id="filter_status" class="pj-form-field filter_box" style="display:none">
				<option value="">-- Status --</option>
				<option value="all" data-column="status">All</option>
				<option value="confirmed" data-column="status">Confirmed</option>
				<option value="pending" data-column="status">Pending</option>
				<option value="cancelled" data-column="status">Cancelled</option>
			</select>			
		</form>
		<!--<div class="float_right t5">
			<a href="#" class="pj-button btn-all"><?php __('lblAll'); ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="confirmed"><?php echo $statuses['confirmed']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="pending"><?php echo $statuses['pending']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="cancelled"><?php echo $statuses['cancelled']; ?></a>
		</div>-->
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