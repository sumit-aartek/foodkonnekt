<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionIndex" method="post" class="form pj-form" id="frmDefaultTime">
	<input type="hidden" name="working_time" value="1" />
	<input type="hidden" name="location_id" value="<?php echo $tpl['arr']['id'];?>" />
	
	<table class="pj-table b20" cellpadding="0" cellspacing="0" style="width: 100%">
		<thead>
			<tr>
				<th colspan="4"><?php __('lblWorkingTimePickup', false, true); ?></th>
			</tr>
			<tr>
				<th><?php __('lblDayOfWeek', false, true); ?></th>
				<th><?php __('lblStartTime', false, true); ?></th>
				<th><?php __('lblEndTime', false, true); ?></th>
				<th><?php __('lblIsDayOff', false, true); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$days = __('voucher_days', true, false);
			foreach (pjUtil::getWeekdays() as $k)
			{
				if (isset($tpl['wt_arr']) && count($tpl['wt_arr']) > 0)
				{
					$hour_from = substr($tpl['wt_arr']['p_'.$k.'_from'], 0, 2);
					$hour_to = substr($tpl['wt_arr']['p_'.$k.'_to'], 0, 2);
					$minute_from = substr($tpl['wt_arr']['p_'.$k.'_from'], 3, 2);
					$minute_to = substr($tpl['wt_arr']['p_'.$k.'_to'], 3, 2);
					$checked = NULL;
					$disabled = false;
					if (is_null($tpl['wt_arr']['p_'.$k.'_from']))
					{
						$checked = ' checked="checked"';
						$disabled = true;
					}
				}else{
					$hour_from = NULL;
					$hour_to = NULL;
					$minute_from = NULL;
					$minute_to = NULL;
					$checked = NULL;
					$disabled = false;
				}
				?>
				<tr class="pj-table-row-odd">
					<td><?php echo $days[$k]; ?></td>
					<td>
						<span class="inline_block">
							<?php
							$pjHourFrom = pjTime::factory()
										->attr('name', 'p_'.$k . '_hour_from')
										->attr('id', 'p_'.$k . '_hour_from')
										->attr('class', 'pj-form-field')
										->prop('selected', $hour_from);
							if($disabled == true){
								$pjHourFrom->attr('disabled', 'disabled');
							}
							echo $pjHourFrom->hour();
							$pjMinuteFrom = pjTime::factory()
										->attr('name', 'p_'.$k . '_minute_from')
										->attr('id', 'p_'.$k . '_minute_from')
										->attr('class', 'pj-form-field')
										->prop('selected', $minute_from)
										->prop('step', 5);
							if($disabled == true){
								$pjMinuteFrom->attr('disabled', 'disabled');
							}
							echo $pjMinuteFrom->minute();
							?>
						</span>
					</td>
					<td>
						<span class="inline_block">
							<?php
							$pjHourTo = pjTime::factory()
										->attr('name', 'p_'.$k . '_hour_to')
										->attr('id', 'p_'.$k . '_hour_to')
										->attr('class', 'pj-form-field')
										->prop('selected', $hour_to);
							if($disabled == true){
								$pjHourTo->attr('disabled', 'disabled');
							}
							echo $pjHourTo->hour();
							$pjMinuteTo = pjTime::factory()
										->attr('name', 'p_'.$k . '_minute_to')
										->attr('id', 'p_'.$k . '_minute_to')
										->attr('class', 'pj-form-field')
										->prop('selected', $minute_to)
										->prop('step', 5);
							if($disabled == true){
								$pjMinuteTo->attr('disabled', 'disabled');
							}
							echo $pjMinuteTo->minute();
							?>
						</span>
					</td>
					<td><input type="checkbox" class="working-day" id="p_<?php echo $k; ?>_dayoff" name="p_<?php echo $k; ?>_dayoff" value="T"<?php echo $checked; ?> /></td>
				</tr>
				<?php
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4"><input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" /></td>
			</tr>
		</tfoot>
	</table>
	
	<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
		<thead>
			<tr>
				<th colspan="4"><?php __('lblWorkingTimeDelivery', false, true); ?></th>
			</tr>
			<tr>
				<th><?php __('lblDayOfWeek', false, true); ?></th>
				<th><?php __('lblStartTime', false, true); ?></th>
				<th><?php __('lblEndTime', false, true); ?></th>
				<th><?php __('lblIsDayOff', false, true); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$days = __('voucher_days', true, false);
			foreach (pjUtil::getWeekdays() as $k)
			{
				if (isset($tpl['wt_arr']) && count($tpl['wt_arr']) > 0)
				{
					$hour_from = substr($tpl['wt_arr']['d_'.$k.'_from'], 0, 2);
					$hour_to = substr($tpl['wt_arr']['d_'.$k.'_to'], 0, 2);
					$minute_from = substr($tpl['wt_arr']['d_'.$k.'_from'], 3, 2);
					$minute_to = substr($tpl['wt_arr']['d_'.$k.'_to'], 3, 2);
					$checked = NULL;
					$disabled = false;
					if (is_null($tpl['wt_arr']['d_'.$k.'_from']))
					{
						$checked = ' checked="checked"';
						$disabled = true;
					}
				}else{
					$hour_from = NULL;
					$hour_to = NULL;
					$minute_from = NULL;
					$minute_to = NULL;
					$checked = NULL;
					$disabled = false;
				}
				?>
				<tr class="pj-table-row-odd">
					<td><?php echo $days[$k]; ?></td>
					<td>
						<span class="inline_block">
							<?php
							$pjHourFrom = pjTime::factory()
										->attr('name', 'd_'.$k . '_hour_from')
										->attr('id', 'd_'.$k . '_hour_from')
										->attr('class', 'pj-form-field')
										->prop('selected', $hour_from);
							if($disabled == true){
								$pjHourFrom->attr('disabled', 'disabled');
							}
							echo $pjHourFrom->hour();
							$pjMinuteFrom = pjTime::factory()
										->attr('name', 'd_'.$k . '_minute_from')
										->attr('id', 'd_'.$k . '_minute_from')
										->attr('class', 'pj-form-field')
										->prop('selected', $minute_from)
										->prop('step', 5);
							if($disabled == true){
								$pjMinuteFrom->attr('disabled', 'disabled');
							}
							echo $pjMinuteFrom->minute();
							?>
						</span>
					</td>
					<td>
						<span class="inline_block">
							<?php
							$pjHourTo = pjTime::factory()
										->attr('name', 'd_'.$k . '_hour_to')
										->attr('id', 'd_'.$k . '_hour_to')
										->attr('class', 'pj-form-field')
										->prop('selected', $hour_to);
							if($disabled == true){
								$pjHourTo->attr('disabled', 'disabled');
							}
							echo $pjHourTo->hour();
							$pjMinuteTo = pjTime::factory()
										->attr('name', 'd_'.$k . '_minute_to')
										->attr('id', 'd_'.$k . '_minute_to')
										->attr('class', 'pj-form-field')
										->prop('selected', $minute_to)
										->prop('step', 5);
							if($disabled == true){
								$pjMinuteTo->attr('disabled', 'disabled');
							}
							echo $pjMinuteTo->minute();
							?>
						</span>
					</td>
					<td><input type="checkbox" class="working-day" id="d_<?php echo $k; ?>_dayoff" name="d_<?php echo $k; ?>_dayoff" value="T"<?php echo $checked; ?> /></td>
				</tr>
				<?php
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4"><input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" /></td>
			</tr>
		</tfoot>
	</table>
</form>