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
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$jqTimeFormat = pjUtil::jqTimeFormat($tpl['option_arr']['o_time_format']);
	
	$_yesno = __('_yesno', true, false);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionIndex"><?php __('menuOrders'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionCreate"><?php __('lblAddOrder'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoAddOrderTitle', true, false), __('infoAddOrderDesc', true, false)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionCreate" method="post" class="form pj-form" id="frmCreateOrder">
		<input type="hidden" name="order_create" value="1" />
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('lblOrderDetails');?></a></li>
				<li><a href="#tabs-2"><?php __('lblClientDetails');?></a></li>
			</ul>
			<div id="tabs-1">
				<fieldset class="fieldset white">
					<legend><?php __('lblProducts'); ?></legend>
					<!-- <div id="fdProductList"></div> -->
					
					<table class="fdOrderList" id="fdOrderList">
						<thead>
							<tr class="fdLine">
								<th width="166"><?php __('lblProduct');?></th>
								<th width="145"><?php __('lblSizeAndPrice');?></th>
								<th width="70" class="splitter"><?php __('lblQty');?></th>
								<th width="145" class="fdPL5"><?php __('lblExtra');?></th>
								<th width="70"><?php __('lblQty');?></th>
								<th width="30" class="splitter">&nbsp;</th>
								<th width="70" class="fdPL5"><?php __('lblTotal');?></th>
								<th width="30">&nbsp;</th>
							</tr>
						</thead>
						<tbody class="main-body">
							<?php
							$index = "new_" . mt_rand(0, 999999); 
							?>
							<tr class="fdLine" data-index="<?php echo $index;?>">
								<td width="166">
									<select id="fdProduct_<?php echo $index;?>" data-index="<?php echo $index;?>" name="product_id[<?php echo $index;?>]" class="pj-form-field fdProduct w160">
										<option value="">-- <?php __('lblChoose'); ?>--</option>
										<?php
										foreach ($tpl['product_arr'] as $p)
										{
											?><option value="<?php echo $p['id']; ?>"><?php echo stripslashes($p['name']); ?></option><?php
										}
										?>
									</select>
								</td>
								<td width="145" id="fdPriceTD_<?php echo $index;?>">
									<select id="fdPrice_<?php echo $index;?>" name="price_id[<?php echo $index;?>]" data-type="select" class="fdSize pj-form-field w140">
										<option value="">-- <?php __('lblChoose'); ?>--</option>
									</select>
								</td>
								<td width="70" class="splitter">
									<input type="text" id="fdProductQty_<?php echo $index;?>" name="cnt[<?php echo $index;?>]" class="pj-form-field w50 float_left pj-field-count" value="1" />
								</td>
								<td  width="236" colspan="3" class="splitter fdPL5">
									<table id="fdExtraTable_<?php echo $index;?>" class="pj-extra-table" cellpadding="0" cellspacing="0" style="width: auto">							
										<tbody>
										</tbody>
									</table>
									<input type="button" value="<?php __('btnAddExtra');?>" data-index="<?php echo $index;?>" class="pj-button float_left pj-add-extra" />
								</td>
								<td width="70" class="fdPL5">
									<span id="fdTotalPrice_<?php echo $index;?>" class="fdPriceLabel"><?php echo pjUtil::formatCurrencySign(number_format(0, 2), $tpl['option_arr']['o_currency']);?></span>
								</td>
								<td width="30">
									&nbsp;
								</td>
							</tr>
						</tbody>
					</table>
					
					<div class="overflow">
						<input type="button" id="btnAddProduct" value="<?php __('btnAddProduct');?>" class="pj-button float_left" />
					</div>
				</fieldset>
				
				<fieldset class="fieldset white">
					<legend><?php __('lblPaymentsOtherDetails'); ?></legend>
					<div class="overflow">
						<div class="float_left w50p overflow">
							<p>
								<label class="title"><?php __('lblType'); ?></label>
								<span class="inline_block">
									<select name="type" id="type" class="pj-form-field required">
										<option value="">-- <?php __('lblChoose'); ?>--</option>
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
								<label class="title"><?php __('lblStatus'); ?></label>
								<span class="inline_block">
									<select name="status" id="status" class="pj-form-field required">
										<option value="">-- <?php __('lblChoose'); ?>--</option>
										<?php
										foreach (__('order_statuses', true, false) as $k => $v)
										{
											?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
										}
										?>
									</select>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblIsPaid'); ?></label>
								<span class="inline_block">
									<select name="is_paid" id="is_paid" class="pj-form-field required">
										<option value="">-- <?php __('lblChoose'); ?>--</option>
										<option value="1"><?php echo $_yesno['T']; ?></option>
										<option value="0"><?php echo $_yesno['F']; ?></option>
									</select>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblPaymentMethod');?></label>
								<span class="inline-block">
									<select name="payment_method" id="payment_method" class="pj-form-field w150 required">
										<option value="">-- <?php __('lblChoose'); ?>--</option>
										<?php
										foreach (__('payment_methods', true, false) as $k => $v)
										{
											?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
										}
										?>
									</select>
								</span>
							</p>
							<p class="boxCC" style="display: none;">
								<label class="title"><?php __('lblCCType'); ?></label>
								<span class="inline-block">
									<select name="cc_type" class="pj-form-field w150">
										<option value="">---</option>
										<?php
										foreach (__('cc_types', true, false) as $k => $v)
										{
											?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
										}
										?>
									</select>
								</span>
							</p>
							<p class="boxCC" style="display: none;">
								<label class="title"><?php __('lblCCNum'); ?></label>
								<span class="inline-block">
									<input type="text" name="cc_num" id="cc_num" class="pj-form-field w136" />
								</span>
							</p>
							<p class="boxCC" style="display: none;">
								<label class="title"><?php __('lblCCExp'); ?></label>
								<span class="inline-block">
									<select name="cc_exp_month" class="pj-form-field">
										<option value="">---</option>
										<?php
										$month_arr = __('months', true, false);
										ksort($month_arr);
										foreach ($month_arr as $key => $val)
										{
											?><option value="<?php echo $key;?>"><?php echo $val;?></option><?php
										}
										?>
									</select>
									<select name="cc_exp_year" class="pj-form-field">
										<option value="">---</option>
										<?php
										$y = (int) date('Y');
										for ($i = $y; $i <= $y + 10; $i++)
										{
											?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
										}
										?>
									</select>
								</span>
							</p>
							<p class="boxCC" style="display: none">
								<label class="title"><?php __('lblCCCode'); ?></label>
								<span class="inline-block">
									<input type="text" name="cc_code" id="cc_code" class="pj-form-field w100" />
								</span>
							</p>
							<p>
								<label class="title">&nbsp;</label>
								<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
							</p>
						</div>
						<div class="float_left w50p overflow">
							<p>
								<label class="title"><?php __('lblPrice'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="price" name="price" class="pj-form-field number w80 required" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblDelivery'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="price_delivery" name="price_delivery" class="pj-form-field number w80" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblDiscount'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="discount" name="discount" class="pj-form-field number w80" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblSubTotal'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="subtotal" name="subtotal" class="pj-form-field number w80" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblTax'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="tax" name="tax" class="pj-form-field number w80" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblTotal'); ?></label>
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" id="total" name="total" class="pj-form-field number w80" readonly="readonly"/>
								</span>
							</p>
							<p>
								<label class="title"><?php __('lblVoucher'); ?></label>
								<span class="inline-block">
									<input type="text" name="voucher_code" id="voucher_code" class="pj-form-field w100" />
								</span>
							</p>
							<p>
								<label class="title">&nbsp;</label>
								<span class="inline-block">
									<input type="button" id="btnCalc" value="<?php __('btnCalculateTotal');?>" class="pj-button" />
								</span>
							</p>
						</div>
					</div>
				</fieldset>
				<fieldset class="fieldset white pickup" style="display:none">
					<legend><?php __('lblPickupAddress'); ?></legend>
					<p>
						<label class="title"><?php __('lblLocation');?></label>
						<span class="inline-block">
							<select name="p_location_id" id="p_location_id" class="pj-form-field w400">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
								<?php
								foreach ($tpl['location_arr'] as $location)
								{
									?><option value="<?php echo $location['id']; ?>"><?php echo stripslashes($location['name']); ?></option><?php
								}
								?>
							</select>
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblPickerDateTime'); ?></label>
						<span class="block overflow">
							<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
								<input type="text" name="p_dt" id="p_dt" class="pj-form-field pointer w150 datetimepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
								<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
							</span>
						</span>
					</p>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
					</p>
				</fieldset>
				<fieldset class="fieldset white delivery" style="display:none">
					<legend><?php __('lblDeliveryAddress'); ?></legend>
					<p>
						<label class="title"><?php __('lblLocation');?></label>
						<span class="inline-block">
							<select name="d_location_id" id="d_location_id" class="pj-form-field w400">
								<option value="">-- <?php __('lblChoose'); ?>--</option>
								<?php
								foreach ($tpl['location_arr'] as $location)
								{
									?><option value="<?php echo $location['id']; ?>"><?php echo stripslashes($location['name']); ?></option><?php
								}
								?>
							</select>
						</span>
					</p>
					<?php
					if (in_array($tpl['option_arr']['o_df_include_address_1'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblAddress1'); ?></label>
							<span class="inline-block">
								<input type="text" name="d_address_1" id="d_address_1" class="pj-form-field w400<?php echo $tpl['option_arr']['o_df_include_address_2'] == 3 ? ' fdRequired' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_df_include_address_2'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblAddress2'); ?></label>
							<span class="inline-block">
								<input type="text" name="d_address_2" id="d_address_2" class="pj-form-field w400<?php echo $tpl['option_arr']['o_df_include_address_2'] == 3 ? ' fdRequired' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_df_include_city'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblCity'); ?></label>
							<span class="inline-block">
								<input type="text" name="d_city" id="d_city" class="pj-form-field w300<?php echo $tpl['option_arr']['o_df_include_city'] == 3 ? ' fdRequired' : NULL; ?>"/>
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_df_include_state'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblState'); ?></label>
							<span class="inline-block">
								<input type="text" name="d_state" id="d_state" class="pj-form-field w300<?php echo $tpl['option_arr']['o_df_include_state'] == 3 ? ' fdRequired' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_df_include_zip'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblZip'); ?></label>
							<span class="inline-block">
								<input type="text" name="d_zip" id="d_zip" class="pj-form-field w300<?php echo $tpl['option_arr']['o_df_include_zip'] == 3 ? ' fdRequired' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_df_include_country'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblCountry'); ?></label>
							<span class="inline-block">
								<select name="d_country_id" id="d_country_id" class="pj-form-field w300<?php echo $tpl['option_arr']['o_df_include_country'] == 3 ? ' fdRequired' : NULL; ?>">
									<option value="">-- <?php __('lblChoose'); ?>--</option>
									<?php
									foreach ($tpl['country_arr'] as $v)
									{
										?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['country_title']); ?></option><?php
									}
									?>
								</select>
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_df_include_notes'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblSpecialInstructions'); ?></label>
							<span class="inline-block">
								<textarea name="d_notes" id="d_notes" class="pj-form-field w500 h120<?php echo $tpl['option_arr']['o_df_include_notes'] == 3 ? ' fdRequired' : NULL; ?>"></textarea>
							</span>
						</p>
						<?php
					} 
					?>
					<p>
						<label class="title"><?php __('lblDeliveryDateTime'); ?></label>
						<span class="block overflow">
							<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
								<input type="text" name="d_dt" id="d_dt" class="pj-form-field pointer w150 datetimepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>"/>
								<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
							</span>
						</span>
					</p>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
					</p>
				</fieldset>
			</div>
			<div id="tabs-2">
				<div class="overflow">
					<?php
					if(!empty($tpl['client_arr']))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblExistingClient'); ?></label>
							<span class="inline-block">
								<select name="client_id" id="client_id" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_country'] == 3 ? ' required' : NULL; ?>">
									<option value="">-- <?php __('lblChoose'); ?>--</option>
									<?php
									foreach ($tpl['client_arr'] as $v)
									{
										?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['c_name']); ?> (<?php echo stripslashes($v['c_email']); ?>)</option><?php
									}
									?>
								</select>
							</span>
						</p>
						<?php
					} 
					if (in_array($tpl['option_arr']['o_bf_include_title'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblTitle'); ?></label>
							<span class="inline-block">
								<select name="c_title" id="c_title" class="pj-form-field w150<?php echo $tpl['option_arr']['o_bf_include_title'] == 3 ? ' required' : NULL; ?>">
									<option value="">-- <?php __('lblChoose'); ?>--</option>
									<?php
									$title_arr = pjUtil::getTitles();
									$name_titles = __('personal_titles', true, false);
									foreach ($title_arr as $v)
									{
										?><option value="<?php echo $v; ?>"><?php echo $name_titles[$v]; ?></option><?php
									}
									?>
								</select>
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_name'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblName'); ?></label>
							<span class="inline-block">
								<input type="text" name="c_name" id="c_name" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_name'] == 3 ? ' required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblEmail'); ?></label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
								<input type="text" name="c_email" id="c_email" class="pj-form-field w300 email<?php echo $tpl['option_arr']['o_bf_include_email'] == 3 ? ' required' : NULL; ?>" placeholder="info@domain.com" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_phone'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblPhone'); ?></label>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
								<input type="text" name="c_phone" id="c_phone" class="pj-form-field w150<?php echo $tpl['option_arr']['o_bf_include_phone'] == 3 ? ' required' : NULL; ?>" placeholder="(123) 456-7890" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_notes'], array(2, 3)))
					{ 
						?>
						<p>
							<label class="title"><?php __('lblNotes'); ?></label>
							<span class="inline-block">
								<textarea name="c_notes" id="c_notes" class="pj-form-field w500 h120<?php echo $tpl['option_arr']['o_bf_include_notes'] == 3 ? ' required' : NULL; ?>"></textarea>
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_company'], array(2, 3)))
					{
						?>
						<p>
							<label class="title"><?php __('lblCompany'); ?></label>
							<span class="inline-block">
								<input type="text" name="c_company" id="c_company" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_company'] == 3 ? ' required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_address_1'], array(2, 3)))
					{
						?>
						<p>
							<label class="title"><?php __('lblAddressLine1'); ?></label>
							<span class="inline-block">
								<input type="text" name="c_address_1" id="c_address_1" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_address_1'] == 3 ? ' required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_address_2'], array(2, 3)))
					{
						?>
						<p>
							<label class="title"><?php __('lblAddressLine2'); ?></label>
							<span class="inline-block">
								<input type="text" name="c_address_2" id="c_address_2" class="pj-form-field w400<?php echo $tpl['option_arr']['o_bf_include_address_2'] == 3 ? ' required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3)))
					{
						?>
						<p>
							<label class="title"><?php __('lblCity'); ?></label>
							<span class="inline-block">
								<input type="text" name="c_city" id="c_city" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_city'] == 3 ? ' required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3)))
					{
						?>
						<p>
							<label class="title"><?php __('lblState'); ?></label>
							<span class="inline-block">
								<input type="text" name="c_state" id="c_state" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_state'] == 3 ? ' required' : NULL; ?>" />
							</span>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3)))
					{
						?>
						<p>
							<label class="title"><?php __('lblZip'); ?></label>
							<span class="inline-block">
								<input type="text" name="c_zip" id="c_zip" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_zip'] == 3 ? ' required' : NULL; ?>" />
							</span>
						</p>
						<?php
					} 
					if (in_array($tpl['option_arr']['o_bf_include_country'], array(2, 3)))
					{
						?>
						<p>
							<label class="title"><?php __('lblCountry'); ?></label>
							<span class="inline-block">
								<select name="c_country" id="c_country" class="pj-form-field w300<?php echo $tpl['option_arr']['o_bf_include_country'] == 3 ? ' required' : NULL; ?>">
									<option value="">-- <?php __('lblChoose'); ?>--</option>
									<?php
									foreach ($tpl['country_arr'] as $v)
									{
										?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['country_title']); ?></option><?php
									}
									?>
								</select>
							</span>
						</p>
						<?php
					}
					?>
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
					</p>
				</div>
			</div>
		</div>
	</form>
	<table style="display: none" id="boxProductClone">
		<tbody>
		<?php
		include PJ_VIEWS_PATH . 'pjAdminOrders/elements/clone.php'; 
		?>
		</tbody>
	</table>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.currency = "<?php echo $tpl['option_arr']['o_currency'];?>";
	</script>
	<?php
	if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
	{		
		$tab_id = $_GET['tab_id'];
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