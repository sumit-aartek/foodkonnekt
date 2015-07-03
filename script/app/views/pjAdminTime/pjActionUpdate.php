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
	?>
	
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php __('lblUpdate', false, true); ?></a></li>
		</ul>
		<div id="tabs-1">
			<?php
			$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
			$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
			
			list($sh, $sm,) = explode(":", $tpl['arr']['start_time']);
			list($eh, $em,) = explode(":", $tpl['arr']['end_time']);
				
			?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionIndex" method="post" class="form pj-form" id="frmTimeCustom">
				<input type="hidden" name="custom_time" value="1" />
				<input type="hidden" name="location_id" value="<?php echo $tpl['arr']['location_id'];?>" />
				<p>
					<label class="title"><?php __('lblDate'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date" id="date" class="pj-form-field pointer w80 datepick required" value="<?php echo pjUtil::formatDate($tpl['arr']['date'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblType'); ?></label>
					<span class="inline_block">
						<select name="type" id="type" class="pj-form-field required">
							<?php
							foreach (__('types', true, false) as $k => $v)
							{
								?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['type'] == $k ? ' selected="selected"' : null;?>><?php echo stripslashes($v); ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblStartTime'); ?></label>
					<span class="inline_block">
					<?php
					echo pjTime::factory()
						->attr('name', 'start_hour')
						->attr('id', 'start_hour')
						->attr('class', 'pj-form-field')
						->prop('selected', $sh)
						->hour();
					?>
					<?php
					echo pjTime::factory()
						->attr('name', 'start_minute')
						->attr('id', 'start_minute')
						->attr('class', 'pj-form-field')
						->prop('selected', $sm)
						->prop('step', 5)
						->minute();
					?>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblEndTime'); ?></label>
					<span class="inline_block">
					<?php
					echo pjTime::factory()
						->attr('name', 'end_hour')
						->attr('id', 'end_hour')
						->attr('class', 'pj-form-field')
						->prop('selected', $eh)
						->hour();
					?>
					<?php
					echo pjTime::factory()
						->attr('name', 'end_minute')
						->attr('id', 'end_minute')
						->attr('class', 'pj-form-field')
						->prop('selected', $eh)
						->prop('step', 5)
						->minute();
					?>
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblIsDayOff'); ?></label>
					<span class="left"><input type="checkbox" name="is_dayoff" id="is_dayoff" value="T"<?php echo $tpl['arr']['is_dayoff'] == 'T' ? ' checked="checked"' : NULL; ?> /></span>
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
				</p>
			</form>
		</div>
	</div>
	<?php
	
}
?>
<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.date = "<?php __('lblDate', false, true); ?>";
myLabel.type = "<?php __('lblType', false, true); ?>";
myLabel.validate_time = "<?php __('lblValidateTime', false, true); ?>";
myLabel.start_time = "<?php __('lblStartTime', false, true); ?>";
myLabel.end_time = "<?php __('lblEndTime', false, true); ?>";
myLabel.is_day_off = "<?php __('lblIsDayOff', false, true); ?>";
</script>