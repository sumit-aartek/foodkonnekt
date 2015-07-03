<?php
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionIndex" method="post" class="form pj-form" id="frmTimeCustom">
	<input type="hidden" name="custom_time" value="1" />
	<input type="hidden" name="location_id" value="<?php echo $tpl['arr']['id'];?>" />
	<p>
		<label class="title"><?php __('lblDate'); ?></label>
		<span class="pj-form-field-custom pj-form-field-custom-after">
			<input type="text" name="date" id="date" class="pj-form-field pointer w80 datepick required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>"/>
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
					?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
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
			->hour();
		?>
		<?php
		echo pjTime::factory()
			->attr('name', 'start_minute')
			->attr('id', 'start_minute')
			->attr('class', 'pj-form-field')
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
			->hour();
		?>
		<?php
		echo pjTime::factory()
			->attr('name', 'end_minute')
			->attr('id', 'end_minute')
			->attr('class', 'pj-form-field')
			->prop('step', 5)
			->minute();
		?>
		</span>
	</p>
	<p>
		<label class="title"><?php __('lblIsDayOff'); ?></label>
		<span class="left"><input type="checkbox" name="is_dayoff" id="is_dayoff" value="T" /></span>
	</p>
	<p>
		<label class="title">&nbsp;</label>
		<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
	</p>
</form>

<div id="grid"></div>