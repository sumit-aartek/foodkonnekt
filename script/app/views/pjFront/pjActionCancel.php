<div style="margin: 0 auto; width: 450px">
	<?php
	$cancel_err = __('cancel_err', true, false);
	$payment_methods = __('payment_methods', true, false);
	if (isset($tpl['status']))
	{
		switch ($tpl['status'])
		{
			case 1:
				?><p><?php echo $cancel_err[1]; ?></p><?php
				break;
			case 2:
				?><p><?php echo $cancel_err[2]; ?></p><?php
				break;
			case 3:
				?><p><?php echo $cancel_err[3]; ?></p><?php
				break;
			case 4:
				?><p><?php echo $cancel_err[4]; ?></p><?php
				break;
		}
	} else {
		
		if (isset($_GET['err']))
		{
			switch ((int) $_GET['err'])
			{
				case 200:
					?><p><?php echo $cancel_err[200]; ?></p><?php
					break;
			}
		}
		if (isset($tpl['arr']))
		{
			$name_titles = __('personal_titles', true, false);
			$datetime = date($tpl['option_arr']['o_datetime_format'], strtotime($tpl['arr']['type'] == 'pickup' ? $tpl['arr']['p_dt'] : $tpl['arr']['d_dt']));
			
			$row = array();
			if (isset($tpl['arr']['product_arr']))
			{
				foreach ($tpl['arr']['product_arr'] as $v)
				{
					$extra = array();
					foreach ($v['extra_arr'] as $e)
					{
						$extra[] = stripslashes(sprintf("%u x %s", $e['cnt'], $e['name']));
					}
					$row[] = stripslashes(sprintf("%u x %s", $v['cnt'], $v['name'])) . (count($extra) > 0 ? sprintf(" (%s)", join("; ", $extra)) : NULL);
				}
			}
			$order_data = count($row) > 0 ? join("<br/>", $row) : NULL;
			?>
			<table cellspacing="2" cellpadding="5" style="width: 100%">
				<thead>
					<tr>
						<th colspan="2" style="text-transform: uppercase; font-weight:bold; text-align: left"><?php __('front_order_details'); ?></th>
					</tr>
				</thead>
				<tbody>	
					<tr>
						<td><?php __('front_order_id'); ?></td>
						<td><?php echo $tpl['arr']['uuid']; ?></td>
					</tr>
					<tr>
						<td><?php __('front_location'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['location']); ?></td>
					</tr>
					<tr>
						<td><?php __('front_date_time'); ?></td>
						<td><?php echo $datetime; ?></td>
					</tr>
					<tr>
						<td><?php __('front_order_details'); ?></td>
						<td><?php echo $order_data; ?></td>
					</tr>
					<tr>
						<td><?php __('front_payment_medthod');?></td>
						<td><?php $payment_methods = __('payment_methods', true, false); echo $payment_methods[$tpl['arr']['payment_method']]; ?></td>
					</tr>
					<tr style="display: <?php echo $tpl['arr']['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
						<td><?php __('front_cc_type'); ?></td>
						<td><?php $cc_types = __('cc_types', true, false); echo $cc_types[$tpl['arr']['cc_type']]; ?></td>
					</tr>
					<tr style="display: <?php echo $tpl['arr']['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
						<td><?php __('front_cc_number'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['cc_num']); ?></td>
					</tr>
					<tr style="display: <?php echo $tpl['arr']['payment_method'] != 'creditcard' ? 'none' : NULL; ?>">
						<td><?php __('front_cc_exp'); ?></td>
						<td><?php echo $tpl['arr']['cc_exp']; ?></td>
					</tr>
					<tr>
						<td><?php __('front_price'); ?></td>
						<td><?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['arr']['price']), 2), $tpl['option_arr']['o_currency'], " "); ?></td>
					</tr>
					<?php
					if($tpl['arr']['type'] == 'delivery')
					{ 
						?>
						<tr>
							<td><?php __('front_delivery_fee'); ?></td>
							<td><?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['arr']['price_delivery']), 2), $tpl['option_arr']['o_currency'], " "); ?></td>
						</tr>
						<?php
					} 
					?>
					<tr>
						<td><?php __('front_subtotal'); ?></td>
						<td><?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['arr']['subtotal']), 2), $tpl['option_arr']['o_currency'], " "); ?></td>
					</tr>
					<tr>
						<td><?php __('front_tax'); ?></td>
						<td><?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['arr']['tax']), 2), $tpl['option_arr']['o_currency'], " "); ?></td>
					</tr>
					<tr>
						<td><?php __('front_total'); ?></td>
						<td><?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['arr']['total']), 2), $tpl['option_arr']['o_currency'], " "); ?></td>
					</tr>					
					<tr>
						<td><?php __('front_order_created'); ?></td>
						<td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['created'])) . ' ' . date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['created'])); ?></td>
					</tr>
					<?php
					if($tpl['arr']['payment_method'] == 'paypal')
					{ 
						?>
						<tr>
							<td><?php __('front_txn_id'); ?></td>
							<td><?php echo stripslashes($tpl['arr']['txn_id']); ?></td>
						</tr>
						<tr>
							<td><?php __('front_processed_on'); ?></td>
							<td><?php echo !empty($tpl['arr']['processed_on']) ? date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['processed_on'])) . ' ' . date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['processed_on'])) : null; ?></td>
						</tr>
						<?php
					} 
					
					if($tpl['arr']['type'] == 'delivery')
					{ 
						?>
						<tr>
							<td colspan="2" style="text-transform: uppercase; font-weight:bold; text-align: left"><?php __('front_delivery_address'); ?></td>
						</tr>
						<tr>
							<td><?php __('front_address_line_1'); ?></td>
							<td><?php echo stripslashes($tpl['arr']['d_address_1']); ?></td>
						</tr>
						<tr>
							<td><?php __('front_address_line_2'); ?></td>
							<td><?php echo stripslashes($tpl['arr']['d_address_2']); ?></td>
						</tr>
						<tr>
						<td><?php __('front_city'); ?></td>
							<td><?php echo stripslashes($tpl['arr']['c_city']); ?></td>
						</tr>
						<tr>
							<td><?php __('front_state'); ?></td>
							<td><?php echo stripslashes($tpl['arr']['c_state']); ?></td>
						</tr>
						<tr>
							<td><?php __('front_zip'); ?></td>
							<td><?php echo stripslashes($tpl['arr']['c_zip']); ?></td>
						</tr>
						<tr>
							<td><?php __('front_country'); ?></td>
							<td><?php echo stripslashes($tpl['arr']['d_country']); ?></td>
						</tr>
						<?php
					} 
					?>
					<tr>
						<td colspan="2" style="text-transform: uppercase; font-weight:bold; text-align: left"><?php __('front_personal_details'); ?></td>
					</tr>
					<tr>
						<td><?php __('front_title'); ?></td>
						<td><?php echo $name_titles[$tpl['arr']['c_title']]; ?></td>
					</tr>
					<tr>
						<td><?php __('front_name'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['c_name']); ?></td>
					</tr>
					<tr>
						<td><?php __('front_phone'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['c_phone']); ?></td>
					</tr>
					<tr>
						<td><?php __('front_email'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['c_email']); ?></td>
					</tr>
					<tr>
						<td><?php __('front_company'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['c_company']); ?></td>
					</tr>
					<tr>
						<td><?php __('front_notes'); ?></td>
						<td><?php echo isset($tpl['arr']['c_notes']) ? nl2br(pjSanitize::clean($tpl['arr']['c_notes'])) : null;?></td>
					</tr>
					<tr>
						<td><?php __('front_address_line_1'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['c_address_1']); ?></td>
					</tr>
					<tr>
						<td><?php __('front_address_line_2'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['c_address_2']); ?></td>
					</tr>
					<tr>
						<td><?php __('front_city'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['c_city']); ?></td>
					</tr>
					<tr>
						<td><?php __('front_state'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['c_state']); ?></td>
					</tr>
					<tr>
						<td><?php __('front_zip'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['c_zip']); ?></td>
					</tr>
					<tr>
						<td><?php __('front_country'); ?></td>
						<td><?php echo stripslashes($tpl['arr']['c_country']); ?></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2">
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjFront&amp;action=pjActionCancel" method="post">
								<input type="hidden" name="order_cancel" value="1" />
								<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
								<input type="hidden" name="hash" value="<?php echo $_GET['hash']; ?>" />
								<input type="submit" value="<?php __('front_button_confirm'); ?>" />
							</form>
						</td>
					</tr>
				</tfoot>
			</table>
			<?php
		}
	}
	?>
</div>
	