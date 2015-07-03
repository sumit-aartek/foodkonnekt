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
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionIndex"><?php __('menuVouchers'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionCreate"><?php __('lblAddVoucher'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoAddVoucherTitle', true, false), __('infoAddVoucherDesc', true, false)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionCreate" method="post" id="frmCreateVoucher" class="form pj-form" autocomplete="off">
		<input type="hidden" name="voucher_create" value="1" />
		<p>
			<label class="title"><?php __('lblVoucherCode'); ?></label>
			<span class="inline_block">
				<input type="text" name="code" id="code" class="pj-form-field w150 required" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblType'); ?></label>
			<span class="inline_block">
				<select name="type" id="type" class="pj-form-field w150 required">
					<?php
					foreach (__('voucher_types', true, false) as $k => $v)
					{
						?><option value="<?php echo $k; ?>" data-sign="<?php echo $k == 'amount' ? pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], "") : '%'; ?>"><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblDiscount'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr id="icon_type" class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" id="discount" name="discount" class="pj-form-field number w80"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblValid'); ?></label>
			<span class="inline_block">
				<select name="valid" id="valid" class="pj-form-field w150 required">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach (__('voucher_valids', true, false) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<div id="valid_fixed" class="valid-box" style="display:none;">
			<p>
				<label class="title"><?php __('lblDate'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-after">
					<input type="text" name="f_date" id="f_date" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="" />
					<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblTimeFrom'); ?></label>
				<span class="inline_block">
				<?php
				echo pjTime::factory()
					->attr('name', 'f_hour_from')
					->attr('id', 'f_hour_from')
					->attr('class', 'pj-form-field')
					->hour();
				?>
				<?php
				echo pjTime::factory()
					->attr('name', 'f_minute_from')
					->attr('id', 'f_minute_from')
					->attr('class', 'pj-form-field')
					->prop('step', 5)
					->minute();
				?>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblTimeTo'); ?></label>
				<span class="inline_block">
				<?php
				echo pjTime::factory()
					->attr('name', 'f_hour_to')
					->attr('id', 'f_hour_to')
					->attr('class', 'pj-form-field')
					->hour();
				?>
				<?php
				echo pjTime::factory()
					->attr('name', 'f_minute_to')
					->attr('id', 'f_minute_to')
					->attr('class', 'pj-form-field')
					->prop('step', 5)
					->minute();
				?>
				</span>
				<em style="display: none;"><label id="validate_fixedtime" class="err"><?php __('lblValidateTime', false, true); ?></label></em>
			</p>
		</div>
		<div id="valid_period" class="valid-box" style="display:none;">
			<p>
				<label class="title"><?php __('lblDateTimeFrom'); ?></label>
				<span class="inline_block">
					<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
						<input type="text" name="p_date_from" id="p_date_from" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
					<span class="inline_block">
					<?php
					echo pjTime::factory()
						->attr('name', 'p_hour_from')
						->attr('id', 'p_hour_from')
						->attr('class', 'pj-form-field pj-form-field-select')
						->hour();
					?>
					<?php
					echo pjTime::factory()
						->attr('name', 'p_minute_from')
						->attr('id', 'p_minute_from')
						->attr('class', 'pj-form-field pj-form-field-select')
						->prop('step', 5)
						->minute();
					?>
					</span>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblDateTimeTo'); ?></label>
				<span class="inline_block">
					<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
						<input type="text" name="p_date_to" id="p_date_to" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
					<span class="inline_block">
					<?php
					echo pjTime::factory()
						->attr('name', 'p_hour_to')
						->attr('id', 'p_hour_to')
						->attr('class', 'pj-form-field pj-form-field-select')
						->hour();
					?>
					<?php
					echo pjTime::factory()
						->attr('name', 'p_minute_to')
						->attr('id', 'p_minute_to')
						->attr('class', 'pj-form-field pj-form-field-select')
						->prop('step', 5)
						->minute();
					?>
					</span>
				</span>
				<em style="display: none;"><label id="validate_datetime" class="err"><?php __('lblValidateVoucherDateTime', false, true); ?></label></em>
			</p>
		</div>
		<div id="valid_recurring" class="valid-box" style="display:none;">
			<p>
				<label class="title"><?php __('lblEvery'); ?></label>
				<span class="inline_block">
					<select name="r_every" id="r_every" class="pj-form-field w150">
						<?php
						$days = __('voucher_days', true, false);
						foreach (pjUtil::getWeekdays() as $v)
						{
							?><option value="<?php echo $v; ?>"><?php echo $days[$v]; ?></option><?php
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
					->attr('name', 'r_hour_from')
					->attr('id', 'r_hour_from')
					->attr('class', 'pj-form-field')
					->hour();
				?>
				<?php
				echo pjTime::factory()
					->attr('name', 'r_minute_from')
					->attr('id', 'r_minute_from')
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
					->attr('name', 'r_hour_to')
					->attr('id', 'r_hour_to')
					->attr('class', 'pj-form-field')
					->hour();
				?>
				<?php
				echo pjTime::factory()
					->attr('name', 'r_minute_to')
					->attr('id', 'r_minute_to')
					->attr('class', 'pj-form-field')
					->prop('step', 5)
					->minute();
				?>
				</span>
				<em style="display: none;"><label id="validate_time" class="err"><?php __('lblValidateTime', false, true); ?></label></em>
			</p>
		</div>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
		</p>
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.code_exist = "<?php __('lblVoucherCodeExist', false, true); ?>";
	myLabel.field_required = "<?php __('fd_field_required', false, true); ?>";
	myLabel.validate_datetime = "<?php __('lblValidateVoucherDateTime', false, true); ?>";
	myLabel.validate_time = "<?php __('lblValidateTime', false, true); ?>";
	</script>
	<?php
}
?>