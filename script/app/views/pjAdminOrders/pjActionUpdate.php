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
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionCreate"><?php __('lblAddOrder'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id'];?>"><?php __('lblUpdateOrder'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoUpdateOrderTitle', true, false), __('infoUpdateOrderDesc', true, false)); 
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionUpdate" method="post" class="form pj-form" id="frmUpdateOrder">
		<input type="hidden" name="order_update" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']?>" />
		
		<fieldset class="fieldset white">
			<legend><?php __('lblProducts'); ?></legend>
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
					foreach ($tpl['product_arr'] as $k => $product)
					{
						foreach ($tpl['oi_arr'] as $oi)
						{
							if ($oi['type'] == 'product' && $oi['foreign_id'] == $product['id'])
							{
								?>
								<tr class="fdLine" data-index="<?php echo $oi['hash']; ?>">
									<td width="166">
										<select id="fdProduct_<?php echo $oi['hash']; ?>" data-index="<?php echo $oi['hash']; ?>" name="product_id[<?php echo $oi['hash']; ?>]" class="pj-form-field fdProduct w160">
											<option value="">-- <?php __('lblChoose'); ?>--</option>
											<?php
											foreach ($tpl['product_arr'] as $p)
											{
												?><option value="<?php echo $p['id']; ?>" <?php echo $p['id'] == $product['id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($p['name']); ?></option><?php
											}
											?>
										</select>
									</td>
									<td width="145" id="fdPriceTD_<?php echo $oi['hash']; ?>">
										<?php
											if(empty($oi['price_id']))
											{
												?>
													<span class="fdPriceLabel"><?php echo pjUtil::formatCurrencySign(number_format($product['price'], 2), $tpl['option_arr']['o_currency']);?></span>
													<input type="hidden" id="fdPrice_<?php echo $oi['hash']; ?>" data-type="input" name="price_id[<?php echo $oi['hash']; ?>]" value="<?php echo $product['price'];?>" />
												<?php
											} else {
												if(isset($oi['price_arr']) && $oi['price_arr'])
												{
													?>
													<select id="fdPrice_<?php echo $oi['hash']; ?>" name="price_id[<?php echo $oi['hash']; ?>]" data-type="select" class="fdSize pj-form-field w140">
														<option value="">-- <?php __('lblChoose'); ?>--</option>
														<?php
														foreach ($oi['price_arr'] as $pr)
														{
															?><option value="<?php echo $pr['id']; ?>"<?php echo $pr['id'] == $oi['price_id'] ? ' selected="selected"' : NULL; ?> data-price="<?php echo $pr['price'];?>"><?php echo stripslashes($pr['price_name']).": ".pjUtil::formatCurrencySign(number_format($pr['price'], 2), $tpl['option_arr']['o_currency']); ?></option><?php
														} 
														?>
													</select>
													<?php
												} else {
													?><input type="hidden" id="fdPrice_<?php echo $oi['hash']; ?>" name="price_id[<?php echo $oi['hash']; ?>]" value="" /><?php
												}
											}
											?>
									</td>
									<td width="70" class="splitter">
										<input type="text" id="fdProductQty_<?php echo $oi['hash']; ?>" name="cnt[<?php echo $oi['hash']; ?>]" class="pj-form-field w50 float_left pj-field-count" value="<?php echo $oi['cnt']; ?>" />
									</td>
									<td  width="236" colspan="3" class="splitter fdPL5">
										<table id="fdExtraTable_<?php echo $oi['hash']; ?>" class="pj-extra-table" cellpadding="0" cellspacing="0" style="width: auto">							
											<tbody>
												<?php
												foreach ($tpl['extra_arr'] as $extra)
												{
													foreach ($tpl['oi_arr'] as $oi_sub)
													{
														if ($oi_sub['type'] == 'extra' && $oi_sub['hash'] == $oi['hash'] && $oi_sub['foreign_id'] == $extra['id'])
														{
															?>
															<tr>
																<td width="145">
																	<select name="extra_id[<?php echo $oi['hash']; ?>][<?php echo $oi_sub['id']; ?>]" data-index="<?php echo $oi['hash']; ?>_<?php echo $oi_sub['id']; ?>" class="fdExtra fdExtra_<?php echo $oi['hash']; ?> pj-form-field w130">
																		<option value="">-- <?php __('lblChoose'); ?>--</option>
																		<?php
																		foreach ($tpl['extra_arr'] as $e)
																		{
																			if (in_array($e['id'], $product['allowed_extras']))
																			{
																				?><option value="<?php echo $e['id']; ?>"<?php echo $e['id'] == $extra['id'] ? ' selected="selected"' : NULL; ?> data-price="<?php echo $e['price'];?>"><?php echo stripslashes($e['name']); ?>: <?php echo pjUtil::formatCurrencySign($e['price'], $tpl['option_arr']['o_currency'])?></option><?php
																			}
																		}
																		?>
																	</select>
																</td>
																<td class="w70"><input type="text" id="fdExtraQty_<?php echo $oi['hash']; ?>_<?php echo $oi_sub['id']; ?>" name="extra_cnt[<?php echo $oi['hash']; ?>][<?php echo $oi_sub['id']; ?>]" class="pj-form-field w50 float_left pj-field-count" value="<?php echo $oi_sub['cnt']; ?>" /></td>
																<td class="w30"><a href="#" class="pj-remove-extra"></a></td>
															</tr>
															<?php
														}
													}
												} 
												?>
											</tbody>
										</table>
										<input type="button" value="<?php __('btnAddExtra');?>" data-index="<?php echo $oi['hash']; ?>" class="pj-button float_left pj-add-extra" />
									</td>
									<td width="70" class="fdPL5">
										<span id="fdTotalPrice_<?php echo $oi['hash']; ?>" class="fdPriceLabel">&nbsp;</span>
									</td>
									<td width="30">
										<?php
										if($k > 0)
										{ 
											?><a href="#" class="pj-remove-product"></a><?php
										}else{
											echo '&nbsp;';
										} 
										?>
									</td>
								</tr>
								<?php
							}
						}
					}
					?>
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
									?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['type'] == $k ? ' selected="selected"' : NULL;?>><?php echo stripslashes($v); ?></option><?php
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
									?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['status'] == $k ? ' selected="selected"' : NULL;?>><?php echo stripslashes($v); ?></option><?php
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
								<option value="1"<?php echo $tpl['arr']['is_paid'] == 1 ? ' selected="selected"' : NULL;?>><?php echo $_yesno['T']; ?></option>
								<option value="0"<?php echo $tpl['arr']['is_paid'] == 0 ? ' selected="selected"' : NULL;?>><?php echo $_yesno['F']; ?></option>
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
									?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['payment_method'] == $k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
								}
								?>
							</select>
						</span>
					</p>
					<?php $isCC = $tpl['arr']['payment_method'] == 'creditcard'; ?>
					<p class="boxCC" style="display: <?php echo !$isCC ? 'none' : NULL; ?>;">
						<label class="title"><?php __('lblCCType'); ?></label>
						<span class="inline-block">
							<select name="cc_type" class="pj-form-field w150">
								<option value="">---</option>
								<?php
								foreach (__('cc_types', true, false) as $k => $v)
								{
									?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['cc_type'] == $k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
								}
								?>
							</select>
						</span>
					</p>
					<p class="boxCC" style="display: <?php echo !$isCC ? 'none' : NULL; ?>;">
						<label class="title"><?php __('lblCCNum'); ?></label>
						<span class="inline-block">
							<input type="text" name="cc_num" id="cc_num" class="pj-form-field w136" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['cc_num'])); ?>"/>
						</span>
					</p>
					<p class="boxCC" style="display: <?php echo !$isCC ? 'none' : NULL; ?>;">
						<?php
						$ey = $em = NULL;
						if (strpos($tpl['arr']['cc_exp'], "/") !== false)
						{
							list($em, $ey) = explode("/", $tpl['arr']['cc_exp']);
						} 
						?>
						<label class="title"><?php __('lblCCExp'); ?></label>
						<span class="inline-block">
							<select name="cc_exp_month" class="pj-form-field">
								<option value="">---</option>
								<?php
								$month_arr = __('months', true, false);
								ksort($month_arr);
								foreach ($month_arr as $key => $val)
								{
									?><option value="<?php echo $key;?>"<?php echo $em == $key ? ' selected="selected"' : NULL; ?>><?php echo $val;?></option><?php
								}
								?>
							</select>
							<select name="cc_exp_year" class="pj-form-field">
								<option value="">---</option>
								<?php
								$y = (int) date('Y');
								for ($i = $y; $i <= $y + 10; $i++)
								{
									?><option value="<?php echo $i; ?>"<?php echo $ey == $i ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option><?php
								}
								?>
							</select>
						</span>
					</p>
					<p class="boxCC" style="display: <?php echo !$isCC ? 'none' : NULL; ?>;">
						<label class="title"><?php __('lblCCCode'); ?></label>
						<span class="inline-block">
							<input type="text" name="cc_code" id="cc_code" class="pj-form-field w100" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['cc_code'])); ?>"/>
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblClient'); ?></label>
						<span class="inline_block">
							<label class="title"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionUpdate&id=<?php echo $tpl['arr']['client_id'];?>"><?php echo $tpl['arr']['client_name']; ?></a></label>
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblIpAddress'); ?></label>
						<span class="inline_block">
							<label class="title"><?php echo $tpl['arr']['ip'];?></label>
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblDateTime'); ?></label>
						<span class="inline_block">
							<label class="title"><?php echo pjUtil::formatDate(date("Y-m-d", strtotime($tpl['arr']['created'])), "Y-m-d", $tpl['option_arr']['o_date_format']);?>, <?php echo pjUtil::formatTime(date("H:i:s", strtotime($tpl['arr']['created'])), "H:i:s", $tpl['option_arr']['o_time_format']);?></label>
						</span>
					</p>
					<p>
						<label class="title">&nbsp;</label>
						<span class="inline_block">
							<input type="button" value="<?php __('btnPrint');?>" class="pj-button" onclick="window.open('<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionPrintOrder&amp;id=<?php echo $tpl['arr']['id']; ?>&hash=<?php echo sha1($tpl['arr']['id'].$tpl['arr']['created'].PJ_SALT)?>')"/>
							<input type="button" id="btnEmail" data-id="<?php echo $tpl['arr']['id'];?>" value="<?php __('btnEmail');?>" class="pj-button" />
						</span>
					</p>
				</div>
				<div class="float_left w50p overflow">
					<p>
						<label class="title"><?php __('lblPrice'); ?></label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="price" name="price" class="pj-form-field number w80 required" readonly="readonly" value="<?php echo $tpl['arr']['price']; ?>" />
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblDelivery'); ?></label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="price_delivery" name="price_delivery" class="pj-form-field number w80" readonly="readonly" value="<?php echo $tpl['arr']['price_delivery']; ?>" />
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblDiscount'); ?></label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="discount" name="discount" class="pj-form-field number w80" readonly="readonly" value="<?php echo $tpl['arr']['discount']; ?>" />
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblSubTotal'); ?></label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="subtotal" name="subtotal" class="pj-form-field number w80" readonly="readonly" value="<?php echo $tpl['arr']['subtotal']; ?>" />
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblTax'); ?></label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="tax" name="tax" class="pj-form-field number w80" readonly="readonly" value="<?php echo $tpl['arr']['tax']; ?>" />
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblTotal'); ?></label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" id="total" name="total" class="pj-form-field number w80" readonly="readonly" value="<?php echo $tpl['arr']['total']; ?>"/>
						</span>
					</p>
					<p>
						<label class="title"><?php __('lblVoucher'); ?></label>
						<span class="inline-block">
							<input type="text" name="voucher_code" id="voucher_code" class="pj-form-field w100" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['voucher_code'])); ?>"/>
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
		<fieldset class="fieldset white pickup" style="display: <?php echo $tpl['arr']['type'] != 'pickup' ? 'none' : NULL; ?>;">
			<legend><?php __('lblPickupAddress'); ?></legend>
			<p>
				<label class="title"><?php __('lblLocation');?></label>
				<span class="inline-block">
					<select name="p_location_id" id="p_location_id" class="pj-form-field w400 required">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach ($tpl['location_arr'] as $location)
						{
							?><option value="<?php echo $location['id']; ?>"<?php echo $location['id'] == $tpl['arr']['location_id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($location['name']); ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<?php
			$date_time = !empty($tpl['arr']['p_dt']) ?  pjUtil::formatDate(date('Y-m-d', strtotime($tpl['arr']['p_dt'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($tpl['arr']['p_dt'])), 'H:i:s', $tpl['option_arr']['o_time_format']) : ''; 
			?>
			<p>
				<label class="title"><?php __('lblPickerDateTime'); ?></label>
				<span class="block overflow">
					<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
						<input type="text" name="p_dt" id="p_dt" class="pj-form-field pointer w150 datetimepick required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>" value="<?php echo $date_time;?>"/>
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</span>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
			</p>
		</fieldset>
		<fieldset class="fieldset white delivery" style="display: <?php echo $tpl['arr']['type'] != 'delivery' ? 'none' : NULL; ?>;">
			<legend><?php __('lblDeliveryAddress'); ?></legend>
			<p>
				<label class="title"><?php __('lblLocation');?></label>
				<span class="inline-block">
					<select name="d_location_id" id="d_location_id" class="pj-form-field w400 required">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach ($tpl['location_arr'] as $location)
						{
							?><option value="<?php echo $location['id']; ?>"<?php echo $location['id'] == $tpl['arr']['location_id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($location['name']); ?></option><?php
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
						<input type="text" name="d_address_1" id="d_address_1" class="pj-form-field w400<?php echo $tpl['option_arr']['o_df_include_address_1'] == 3 ? ' required' : NULL; ?>" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['d_address_1'])); ?>"/>
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
						<input type="text" name="d_address_2" id="d_address_2" class="pj-form-field w400<?php echo $tpl['option_arr']['o_df_include_address_2'] == 3 ? ' required' : NULL; ?>" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['d_address_2'])); ?>"/>
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
						<input type="text" name="d_city" id="d_city" class="pj-form-field w300<?php echo $tpl['option_arr']['o_df_include_city'] == 3 ? ' required' : NULL; ?>" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['d_city'])); ?>"/>
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
						<input type="text" name="d_state" id="d_state" class="pj-form-field w300<?php echo $tpl['option_arr']['o_df_include_state'] == 3 ? ' required' : NULL; ?>" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['d_state'])); ?>"/>
					</span>
				</p>
				<?php
			}
			if (in_array($tpl['option_arr']['o_df_include_state'], array(2, 3)))
			{ 
				?>
				<p>
					<label class="title"><?php __('lblZip'); ?></label>
					<span class="inline-block">
						<input type="text" name="d_zip" id="d_zip" class="pj-form-field w300<?php echo $tpl['option_arr']['o_df_include_zip'] == 3 ? ' required' : NULL; ?>" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['d_zip'])); ?>"/>
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
						<select name="d_country_id" id="d_country_id" class="pj-form-field w300<?php echo $tpl['option_arr']['o_df_include_country'] == 3 ? ' required' : NULL; ?>">
							<option value="">-- <?php __('lblChoose'); ?>--</option>
							<?php
							foreach ($tpl['country_arr'] as $v)
							{
								?><option value="<?php echo $v['id']; ?>"<?php echo $v['id'] == $tpl['arr']['d_country_id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v['country_title']); ?></option><?php
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
						<textarea name="d_notes" id="d_notes" class="pj-form-field w500 h120<?php echo $tpl['option_arr']['o_df_include_notes'] == 3 ? ' required' : NULL; ?>"><?php echo stripslashes($tpl['arr']['d_notes']); ?></textarea>
					</span>
				</p>
				<?php
			}
			$date_time = !empty($tpl['arr']['d_dt']) ? pjUtil::formatDate(date('Y-m-d', strtotime($tpl['arr']['d_dt'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ' ' . pjUtil::formatTime(date('H:i:s', strtotime($tpl['arr']['d_dt'])), 'H:i:s', $tpl['option_arr']['o_time_format']) : ''; 
			?>
			<p>
				<label class="title"><?php __('lblDeliveryDateTime'); ?></label>
				<span class="block overflow">
					<span class="pj-form-field-custom pj-form-field-custom-after float_left r5">
						<input type="text" name="d_dt" id="d_dt" class="pj-form-field pointer w150 datetimepick required" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" lang="<?php echo $jqTimeFormat; ?>" value="<?php echo $date_time;?>"/>
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</span>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
			</p>
		</fieldset>
	
	</form>
	<table style="display: none" id="boxProductClone">
		<tbody>
		<?php
		include PJ_VIEWS_PATH . 'pjAdminOrders/elements/clone.php'; 
		?>
		</tbody>
	</table>
	
	<div id="dialogReminderEmail" title="<?php __('lblReminderEmail', false, true); ?>" style="display: none"></div>
	
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