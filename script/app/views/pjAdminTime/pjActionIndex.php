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
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionIndex"><?php __('menuLocations'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionCreate"><?php __('lblAddLocation'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('lblUpdateLocation'); ?></a></li>
		</ul>
	</div>
	<?php
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/menu_location.php';
	
	pjUtil::printNotice(__('infoWTimeTitle', true, false), __('infoWTimeDesc', true, false));
	?>
	
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php __('lblDefault', false, true); ?></a></li>
			<li><a href="#tabs-2"><?php __('lblCustom', false, true); ?></a></li>
		</ul>
		<div id="tabs-1">
		<?php include PJ_VIEWS_PATH . 'pjAdminTime/elements/default.php'; ?>
		</div>
		<div id="tabs-2">
		<?php include PJ_VIEWS_PATH . 'pjAdminTime/elements/custom.php'; ?>
		</div>
	</div>
	
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['id']) && (int) $_GET['id'] > 0)
	{
		?>pjGrid.queryString += "&location_id=<?php echo (int) $_GET['id']; ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.date = "<?php __('lblDate', false, true); ?>";
	myLabel.type = "<?php __('lblType', false, true); ?>";
	myLabel.validate_time = "<?php __('lblValidateTime', false, true); ?>";
	myLabel.start_time = "<?php __('lblStartTime', false, true); ?>";
	myLabel.end_time = "<?php __('lblEndTime', false, true); ?>";
	myLabel.is_day_off = "<?php __('lblIsDayOff', false, true); ?>";
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	</script>
	<?php
	if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
	{
		$tab_id = explode("-", $_GET['tab_id']);
		$tab_id = (int) $tab_id[1] - 1;
		$tab_id = $tab_id < 0 ? 0 : $tab_id;
		?>
		<script type="text/javascript">
		(function ($) {
			$(function () {
				$("#tabs").tabs("option", "selected", <?php echo $tab_id; ?>);
			});
		})(jQuery);
		</script>
		<?php
	}
}
?>