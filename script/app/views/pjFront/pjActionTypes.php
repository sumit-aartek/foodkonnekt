<div class="fdLoader"></div>
<?php
include PJ_VIEWS_PATH . 'pjFront/elements/locale.php'; 
$index = $_GET['index'];

$STORAGE = &$_SESSION[$controller->defaultStore];
$CLIENT = $controller->isFrontLogged() ? @$_SESSION[$controller->defaultClient] : array();

$isPickup = !isset($STORAGE['type']) || $STORAGE['type'] == 'pickup';
$isDelivery = isset($STORAGE['type']) && $STORAGE['type'] == 'delivery';
?>
<div class="fdContainerInner">
	<div id="fdMain_<?php echo $index; ?>" class="fdMain">
		<div class="fdTypesContainer">
			<?php
			if($tpl['status'] == 'OK')
			{ 
				?>
				<form id="fdTypeForm_<?php echo $index; ?>" action="" method="post" class="fdMainWidth">
					<input type="hidden" name="loadTypes" value="1" />
					<input type="hidden" name="user_id" value="<?=$_SESSION['order_data']['o_user_id']?>" />
					<div class="fdTypeHeading">
						<label for="fdTypePickup_<?php echo $index; ?>" class="fdType fdTypePickup<?php echo $isPickup ? ' fdTypeFocus' : NULL; ?>">
							<abbr class="left"></abbr> 
							<abbr class="middle"><?php echo strtoupper(__('front_pickup')); ?></abbr>
							<abbr class="right"></abbr>
						</label>
						
						<span class="fdFloatRight fdBlock" style="overflow: hidden; height: 0">
							<input type="radio" name="type" id="fdTypePickup_<?php echo $index; ?>" value="pickup"<?php echo $isPickup ? ' checked="checked"' : NULL; ?> />
							<input type="radio" name="type" id="fdTypeDelivery_<?php echo $index; ?>" value="delivery"<?php echo $isDelivery ? ' checked="checked"' : NULL; ?> />
						</span>
						<br class="fdClearBoth" />
					</div>
					<div class="fdPickup fdHeading" style="display: <?php echo @$STORAGE['type'] == 'delivery' ? 'none' : NULL; ?>"><?php echo strtoupper(__('front_pickup_address', true, false)); ?></div>
					<div class="fdPickup fdForm" style="display: <?php echo @$STORAGE['type'] == 'delivery' ? 'none' : NULL; ?>">
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_location'); ?><span class="fdRed">*</span>:</label>
							<select name="p_location_id" class="fdSelect fdW100p" data-err="<?php __('front_location_required'); ?>">
								<option value="">-- <?php __('front_choose'); ?> --</option>
								<?php
								$selected = '';
								foreach ($tpl['location_arr'] as $location)
								{
									if(@$STORAGE['p_location_id'] == $location['id'] || @$_SESSION['order_data']['o_location_id'] == $location['id'])
									{
										$selected = 'selected="selected"';
									}
									?><option value="<?php echo $location['id']; ?>"<?php echo $selected; ?>><?php echo stripslashes($location['name']); ?></option><?php
								}
								?>
							</select>
						</p>
						<p class="fdParagraph" style="display:none;">
							<label class="fdTitle"><?php __('front_address'); ?>: <span id="fdPickupAddressLabel_<?php echo $index;?>"></span></label>
							<input type="hidden" id="fdPickupAddress_<?php echo $index;?>" name="address" class="fdText fdW100p" readonly="readonly" />
						</p>
						<div class="fdTypeMapWrap">
							<div id="fdTypeMap_<?php echo $index; ?>" class="fdTypeMap"></div>
						</div>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_pickup_datetime'); ?> <span class="fdRed">*</span>:</label>
							<span class="fdInlineBlock">
								<!--<input type="text" name="p_date" id="fd_p_date_<?php echo $index; ?>" class="fdText fdPointer fdW70 fdDatepick fdFloatLeft fdMr5 fdPickupDate" readonly="readonly" value="<?php echo htmlspecialchars(stripslashes(@$STORAGE['p_date'])); ?>" data-err="<?php __('front_pickup_date_required'); ?>" />-->
								<input type="text" name="p_date" id="fd_p_date_<?php echo $index; ?>" class="fdText fdPointer fdW70 fdDatepick fdFloatLeft fdMr5 fdPickupDate" readonly="readonly" value="<?php echo htmlspecialchars(stripslashes(@$STORAGE['p_date'])); ?>" />
								<span class="fdInlineBlock fdPickupTime">
									<?php
									$p_hour = isset($STORAGE['p_hour']) ? $STORAGE['p_hour'] : NULL;
									$p_minute = isset($STORAGE['p_minute']) ? $STORAGE['p_minute'] : NULL; 
									
									$opts = array('start' => 0, 'end' => 23, 'skip' => array());
									$options = array('start' => 0, 'end' => 59, 'skip' => array());
									
									if (isset($tpl['wt_arr']))
									{
										$date = pjUtil::formatDate(@$STORAGE['p_date'], $tpl['option_arr']['o_date_format']);
										
										$opts['start'] = (int) $tpl['wt_arr']['start_hour'];
										$opts['end'] = (int) $tpl['wt_arr']['end_hour'];
										$opts['skip'] = array();
										
										if ((int) $tpl['wt_arr']['end_hour'] == (int) $p_hour)
										{
											$options['end'] = $tpl['wt_arr']['end_minutes'];
										} elseif ((int) $tpl['wt_arr']['start_hour'] == (int) $p_hour) {
											$options['start'] = $tpl['wt_arr']['start_minutes'];
										}
										$options['skip'] = array();
										if (strtotime($date) == strtotime(date("Y-m-d")))
										{
											list($hour, $minute) = explode("-", date("G-i"));
											foreach (range(0, 23) as $i)
											{
												if ($i < $hour)
												{
													$opts['skip'][] = $i;
												}
											}
											if ((int) $p_hour <= (int) $hour)
											{
												$minute = (int) $minute;
												for ($i = 0; $i < 60; $i += 5)
												{
													if ($i < $minute)
													{
														$options['skip'][] = $i;
													}
												}
											}
											if (count($options['skip']) === 12 && count($opts['skip']) > 0)
											{
												$el = $opts['skip'][count($opts['skip'])-1] + 1;
												if ($el <= 23)
												{
													$opts['skip'][] = $el;
													$options['skip'] = array();
												}
											}
										}
									}
									$pjTimeHour = pjTime::factory();
									$pjTimeMin = pjTime::factory();
									
									$pjTimeHour
											->attr('name', 'p_hour')
											->attr('id', 'p_hour' . $index)
											->attr('class', 'fdSelect fdFloatLeft fdW70 fdMr5')
											->prop('start', $opts['start'])
											->prop('end', $opts['end'])
											->prop('skip', $opts['skip'])
											->prop('selected', $p_hour);
									$pjTimeMin
										->attr('name', 'p_minute')
										->attr('id', 'p_minute' . $_GET['index'])
										->attr('class', 'fdSelect fdW70 fdFloatLeft')
										->prop('step', 5)
										->prop('start', $options['start'])
										->prop('end', $options['end'])
										->prop('skip', $options['skip'])
										->prop('selected', $p_minute);
										
									echo $pjTimeHour->hour();
									echo $pjTimeMin->minute();
									?>
								</span>
							</span>
						</p>
						
					</div>
					
					<div class="fdDelivery fdHeading" style="display: <?php echo @$STORAGE['type'] == 'delivery' ? NULL : 'none'; ?>"><?php echo strtoupper(__('front_delivery_area', true, false)); ?></div>
					<div class="fdDelivery fdForm" style="display: <?php echo @$STORAGE['type'] == 'delivery' ? NULL : 'none'; ?>">
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_location'); ?><span class="fdRed">*</span>:</label>
							<select name="d_location_id" class="fdSelect fdW100p" data-err="<?php __('front_delivery_area_required');?>">
								<option value="">-- <?php __('front_choose'); ?> --</option>
								<?php
								foreach ($tpl['location_arr'] as $location)
								{
									?><option value="<?php echo $location['id']; ?>"<?php echo @$STORAGE['p_location_id'] == $location['id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($location['name']); ?></option><?php
								}
								?>
							</select>
						</p>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_delivery_info'); ?></label>
						</p>
						<p class="fdParagraph fdDeliveryNote" style="display: none"></p>
						<div class="fdTypeMapWrap fdMb20">
							<div id="fdDeliveryMap_<?php echo $index; ?>" class="fdTypeMap"></div>
						</div>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_delivery_datetime'); ?> <span class="fdRed">*</span>:</label>
							<span class="fdInlineBlock">
								<input type="text" name="d_date" id="fd_d_date_<?php echo $index; ?>" class="fdText fdPointer fdW70 fdDatepick fdFloatLeft fdMr5 fdDeliveryDate" readonly="readonly" value="<?php echo htmlspecialchars(stripslashes(@$STORAGE['d_date'])); ?>" data-err="<?php __('front_delivery_date_required'); ?>" />
								<span class="fdInlineBlock fdDeliveryTime">
									<?php
									$d_hour = isset($STORAGE['d_hour']) ? $STORAGE['d_hour'] : NULL;
									$d_minute = isset($STORAGE['d_minute']) ? $STORAGE['d_minute'] : NULL;
									
									$pjTimeHour = pjTime::factory();
									$pjTimeMin = pjTime::factory();
									
									$pjTimeHour
											->attr('name', 'd_hour')
											->attr('id', 'd_hour' . $index)
											->attr('class', 'fdSelect fdFloatLeft fdW70 fdMr5')
											->prop('selected', $d_hour);
									$pjTimeMin
										->attr('name', 'd_minute')
										->attr('id', 'd_minute' . $index)
										->attr('class', 'fdSelect fdW70 fdFloatLeft')
										->prop('step', 5)
										->prop('selected', $d_minute);
										
									echo $pjTimeHour->hour();
									echo $pjTimeMin->minute();
									?>
								</span>
							</span>
						</p>
						<?php
						if(isset($tpl['order_arr']) && count($tpl['order_arr']) > 0)
						{ 
							?>
							<p class="fdParagraph">
								<label class="fdTitle"><?php __('front_select_previous');?></label>
								<select id="fdPreviousAddr_<?php echo $index;?>" name="previous_address" class="fdSelect fdW100p">
									<option value="" data-add1="" data-add2="" data-city="" data-state="" data-zip="" data-country="">-- <?php __('front_choose'); ?> --</option>
									<?php
									foreach($tpl['order_arr'] as $v)
									{
										$order_detail = __('front_order', true, false) . ' ' . (!empty($v['uuid']) ? $v['uuid'] : strtotime($v['created'])) . ' ' . __('front_from', true, false) . ' ' . date($tpl['option_arr']['o_datetime_format'], strtotime($v['created']));
										?><option value="<?php echo $v['id']; ?>" data-add1="<?php echo pjSanitize::clean(@$v['d_address_1']);?>" data-add2="<?php echo pjSanitize::clean(@$v['d_address_2']);?>" data-city="<?php echo pjSanitize::clean(@$v['d_city']);?>" data-state="<?php echo pjSanitize::clean(@$v['d_state']);?>" data-zip="<?php echo pjSanitize::clean(@$v['d_zip']);?>" data-country="<?php echo pjSanitize::clean(@$v['d_country_id']);?>"><?php echo stripslashes($order_detail); ?></option><?php
									}
									?>
								</select>
							</p>
							<?php
						} 
						?>
						<?php
						if (in_array($tpl['option_arr']['o_df_include_address_1'], array(2, 3)))
						{ 
							?>
							<p class="fdParagraph">
								<label class="fdTitle"><?php __('front_address_line_1'); ?> <?php if ((int) $tpl['option_arr']['o_df_include_address_1'] === 3){ ?><span class="fdRed">*</span><?php }?>:</label>
								<input type="text" name="d_address_1" class="fdText fdW100p<?php echo (int) $tpl['option_arr']['o_df_include_address_1'] === 3 ? ' fdRequired' : NULL; ?>" value="<?php echo isset($STORAGE['d_address_1']) ? htmlspecialchars(stripslashes(@$STORAGE['d_address_1'])) : htmlspecialchars(stripslashes(@$CLIENT['d_address_1'])); ?>" data-err="<?php __('front_address1_required');?>"/>
							</p>
							<?php
						}
						if (in_array($tpl['option_arr']['o_df_include_address_2'], array(2, 3)))
						{ 
							?>
							<p class="fdParagraph">
								<label class="fdTitle"><?php __('front_address_line_2'); ?> <?php if ((int) $tpl['option_arr']['o_df_include_address_2'] === 3){ ?><span class="fdRed">*</span><?php }?>:</label>
								<input type="text" name="d_address_2" class="fdText fdW100p<?php echo (int) $tpl['option_arr']['o_df_include_address_2'] === 3 ? ' fdRequired' : NULL; ?>" value="<?php echo isset($STORAGE['d_address_2']) ? htmlspecialchars(stripslashes(@$STORAGE['d_address_2'])) : htmlspecialchars(stripslashes(@$CLIENT['d_address_2'])); ?>" data-err="<?php __('front_address2_required');?>"/>
							</p>
							<?php
						}
						if (in_array($tpl['option_arr']['o_df_include_city'], array(2, 3)))
						{ 
							?>
							<p class="fdParagraph">
								<label class="fdTitle"><?php __('front_city'); ?> <?php if ((int) $tpl['option_arr']['o_df_include_city'] === 3){ ?><span class="fdRed">*</span><?php }?>:</label>
								<input type="text" name="d_city" class="fdText fdW100p<?php echo (int) $tpl['option_arr']['o_df_include_city'] === 3 ? ' fdRequired' : NULL; ?>" value="<?php echo isset($STORAGE['d_city']) ? htmlspecialchars(stripslashes(@$STORAGE['d_city'])) : htmlspecialchars(stripslashes(@$CLIENT['d_city'])); ?>" data-err="<?php __('front_city_required');?>"/>
							</p>
							<?php
						}
						if (in_array($tpl['option_arr']['o_df_include_state'], array(2, 3)))
						{ 
							?>
							<p class="fdParagraph">
								<label class="fdTitle"><?php __('front_state'); ?> <?php if ((int) $tpl['option_arr']['o_df_include_state'] === 3){ ?><span class="fdRed">*</span><?php }?>:</label>
								<input type="text" name="d_state" class="fdText fdW100p<?php echo (int) $tpl['option_arr']['o_df_include_state'] === 3 ? ' fdRequired' : NULL; ?>" value="<?php echo isset($STORAGE['d_state']) ? htmlspecialchars(stripslashes(@$STORAGE['d_state'])) : htmlspecialchars(stripslashes(@$CLIENT['d_state'])); ?>" data-err="<?php __('front_state_required');?>"/>
							</p>
							<?php
						}
						if (in_array($tpl['option_arr']['o_df_include_zip'], array(2, 3)))
						{ 
							?>
							<p class="fdParagraph">
								<label class="fdTitle"><?php __('front_zip'); ?> <?php if ((int) $tpl['option_arr']['o_df_include_zip'] === 3){ ?><span class="fdRed">*</span><?php }?>:</label>
								<input type="text" name="d_zip" class="fdText fdW100p<?php echo (int) $tpl['option_arr']['o_df_include_zip'] === 3 ? ' fdRequired' : NULL; ?>" value="<?php echo isset($STORAGE['d_zip']) ? htmlspecialchars(stripslashes(@$STORAGE['d_zip'])) : htmlspecialchars(stripslashes(@$CLIENT['d_zip'])); ?>" data-err="<?php __('front_zip_required');?>"/>
							</p>
							<?php
						}
						if (in_array($tpl['option_arr']['o_df_include_country'], array(2, 3)))
						{ 
							?>
							<p class="fdParagraph">
								<label class="fdTitle"><?php __('front_country'); ?> <?php if ((int) $tpl['option_arr']['o_df_include_country'] === 3){ ?><span class="fdRed">*</span><?php }?>:</label>
								<select name="d_country_id" class="fdSelect fdW100p<?php echo (int) $tpl['option_arr']['o_df_include_country'] === 3 ? ' fdRequired' : NULL; ?>" data-err="<?php __('front_country_required');?>">
									<option value="">-- <?php __('front_choose'); ?> --</option>
									<?php
									foreach ($tpl['country_arr'] as $country)
									{
										?><option value="<?php echo $country['id']; ?>"<?php echo isset($STORAGE['d_country_id']) ? (@$STORAGE['d_country_id'] == $country['id'] ? ' selected="selected"' : NULL) : (@$CLIENT['d_country_id'] == $country['id'] ? ' selected="selected"' : NULL); ?>><?php echo stripslashes($country['country_title']); ?></option><?php
									}
									?>
								</select>
							</p>
							<?php
						}
						if (in_array($tpl['option_arr']['o_df_include_notes'], array(2, 3)))
						{ 
							?>
							<p class="fdParagraph">
								<label class="fdTitle"><?php __('front_special_instructions'); ?> <?php if ((int) $tpl['option_arr']['o_df_include_notes'] === 3){ ?><span class="fdRed">*</span><?php }?>:</label>
								<textarea name="d_notes" class="fdTextarea fdW100p fdH150<?php echo (int) $tpl['option_arr']['o_df_include_notes'] === 3 ? ' fdRequired' : NULL; ?>" data-err="<?php __('front_special_required');?>"><?php echo isset($STORAGE['d_notes']) ? htmlspecialchars(stripslashes(@$STORAGE['d_notes'])) : htmlspecialchars(stripslashes(@$CLIENT['d_notes'])); ?></textarea>
							</p>
							<?php
						} 
						?>
					</div>
					<?php					
					$button_class = ' fdButtonGetLogin';					
					if($controller->isFrontLogged())
					{
						$button_class = ' fdButtonGetCategories'; 
					}
					?>
					<div class="fdOverflow fdButtonContainer">
						<a href="#" class="fdButton fdNormalButton fdFloatLeft<?php echo $button_class;?>"><?php __('front_button_back');?></a>
						<a href="#" class="fdButton fdOrangeButton fdButtonNext fdFloatRight fdButtonPostPrice"><?php __('front_button_continue');?></a>
					</div>
				</form>
				<?php
			}else{
				?>
				<div class="fdForm fdSystemMessage">
					<?php
					$front_messages = __('front_messages', true, false);
					$system_msg = str_replace("[STAG]", "<a href='". PJ_INSTALL_URL ."'>", $front_messages[13]);
					$system_msg = str_replace("[ETAG]", "</a>", $system_msg); 
					echo $system_msg; 
					echo " we are at actionType";
					?>
				</div>
				<?php
			} 
			?>
		</div>
	</div>
	<div id="fdCart_<?php echo $index; ?>" class="fdCart"><?php include PJ_VIEWS_PATH . 'pjFront/elements/cart.php'; ?></div>
</div>