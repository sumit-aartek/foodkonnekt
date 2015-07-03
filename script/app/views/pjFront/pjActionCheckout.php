<div class="fdLoader"></div>
<?php
include PJ_VIEWS_PATH . 'pjFront/elements/locale.php'; 
$index = $_GET['index'];

$STORAGE = @$_SESSION[$controller->defaultStore];
$FORM = isset($_SESSION[$controller->defaultForm]) ? $_SESSION[$controller->defaultForm] : array();
$CLIENT = $controller->isFrontLogged() ? @$_SESSION[$controller->defaultClient] : array();

?>
<div class="fdContainerInner">
	<div id="fdMain_<?php echo $index; ?>" class="fdMain">
		<div class="fdFormContainer">
			<?php
			if($tpl['status'] == 'OK')
			{ 
				?>
				<form id="fdCheckoutForm_<?php echo $index;?>" action="" method="post" class="fdForm">
					<?php
					ob_start();
					if (in_array($tpl['option_arr']['o_bf_include_address_1'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_address_line_1'); ?><?php if ((int) $tpl['option_arr']['o_bf_include_address_1'] === 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<input type="text" name="c_address_1" class="fdText fdAddrField fdW100p<?php echo (int) $tpl['option_arr']['o_bf_include_address_1'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_address_1']) ? htmlspecialchars(stripslashes(@$FORM['c_address_1'])) : htmlspecialchars(stripslashes(@$CLIENT['c_address_1'])); ?>" data-err="<?php __('front_address1_required');?>"/>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_address_2'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_address_line_2'); ?><?php if ((int) $tpl['option_arr']['o_bf_include_address_2'] === 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<input type="text" name="c_address_2" class="fdText fdAddrField fdW100p<?php echo (int) $tpl['option_arr']['o_bf_include_address_2'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_address_2']) ? htmlspecialchars(stripslashes(@$FORM['c_address_2'])) : htmlspecialchars(stripslashes(@$CLIENT['c_address_2'])); ?>" data-err="<?php __('front_address2_required');?>"/>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_city'); ?><?php if ((int) $tpl['option_arr']['o_bf_include_city'] === 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<input type="text" name="c_city" class="fdText fdAddrField fdW100p<?php echo (int) $tpl['option_arr']['o_bf_include_city'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_city']) ? htmlspecialchars(stripslashes(@$FORM['c_city'])) : htmlspecialchars(stripslashes(@$CLIENT['c_city'])); ?>" data-err="<?php __('front_city_required');?>"/>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_state'); ?><?php if ((int) $tpl['option_arr']['o_bf_include_state'] === 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<input type="text" name="c_state" class="fdText fdAddrField fdW50p<?php echo (int) $tpl['option_arr']['o_bf_include_state'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_state']) ? htmlspecialchars(stripslashes(@$FORM['c_state'])) : htmlspecialchars(stripslashes(@$CLIENT['c_state'])); ?>" data-err="<?php __('front_state_required');?>"/>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_zip'); ?><?php if ((int) $tpl['option_arr']['o_bf_include_zip'] === 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<input type="text" name="c_zip" class="fdText fdAddrField fdW50p<?php echo (int) $tpl['option_arr']['o_bf_include_zip'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_zip']) ? htmlspecialchars(stripslashes(@$FORM['c_zip'])) : htmlspecialchars(stripslashes(@$CLIENT['c_zip'])); ?>" data-err="<?php __('front_zip_required');?>"/>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_country'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_country'); ?><?php if ((int) $tpl['option_arr']['o_bf_include_country'] === 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<select name="c_country" class="fdSelect fdAddrField fdW100p<?php echo (int) $tpl['option_arr']['o_bf_include_country'] === 3 ? ' required' : NULL; ?>" data-err="<?php __('front_country_required');?>">
								<option value="">-- <?php __('front_choose'); ?> --</option>
								<?php
								foreach ($tpl['country_arr'] as $country)
								{
									?><option value="<?php echo $country['id']; ?>"<?php echo isset($FORM['c_country']) ? (@$FORM['c_country'] == $country['id'] ? ' selected="selected"' : NULL) : (@$CLIENT['c_country'] == $country['id'] ? ' selected="selected"' : NULL); ?>><?php echo stripslashes($country['country_title']); ?></option><?php
								}
								?>
							</select>
						</p>
						<?php
					}
					$ob_address = ob_get_contents();
					ob_end_clean();
					if (!empty($ob_address))
					{ 
						?>
						<div class="fdFormHeading">
							<span class="fdBlock fdFloatLeft"><?php echo strtoupper(__('front_address', true, false)); ?></span>
						</div>
						<?php
						echo $ob_address;
						if($controller->isFrontLogged())
						{
							?>
							<p class="fdParagraph">
								<span class="fdTitle fdCheckbox">
									<input type="checkbox" name="update_address" id="fdUpdateAddr_<?php echo $index;?>"<?php echo isset($FORM['update_address']) ? ' checked="checked"' : NULL; ?> value="None">
									<label for="fdUpdateAddr_<?php echo $index;?>"><?php echo __('front_save_changes');?></label>
								</span>
							</p>
							<?php
						} 
						?>
						<div class="fdCrossLine"></div> 
						<?php
					} 
					?>
					<div class="fdFormHeading">
						<span class="fdBlock fdFloatLeft"><?php echo strtoupper(__('front_personal_details', true, false)); ?></span>						
					</div>
					<?php
					if (in_array($tpl['option_arr']['o_bf_include_title'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_title'); ?><?php if($tpl['option_arr']['o_bf_include_title'] == 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<select name="c_title" class="fdSelect fdPersonalField fdW50p<?php echo ($tpl['option_arr']['o_bf_include_title'] == 3) ? ' required' : NULL; ?>" data-err="<?php __('front_title_required');?>">
								<option value="">----</option>
								<?php
								$title_arr = pjUtil::getTitles();
								$name_titles = __('personal_titles', true, false);
								foreach ($title_arr as $v)
								{
									?><option value="<?php echo $v; ?>"<?php echo isset($FORM['c_title']) ? (@$FORM['c_title'] == $v ? ' selected="selected"' : NULL) : (@$CLIENT['c_title'] == $v ? ' selected="selected"' : NULL); ?>><?php echo $name_titles[$v]; ?></option><?php
								}
								?>
							</select>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_name'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_name'); ?><?php if($tpl['option_arr']['o_bf_include_name'] == 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<input type="text" name="c_name" class="fdText fdPersonalField fdW100p<?php echo (int) $tpl['option_arr']['o_bf_include_name'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_name']) ? htmlspecialchars(stripslashes(@$FORM['c_name'])) : htmlspecialchars(stripslashes(@$CLIENT['c_name'])); ?>" data-err="<?php __('front_name_required');?>"/>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_email'); ?><?php if($tpl['option_arr']['o_bf_include_email'] == 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<input type="text" name="c_email" class="fdText fdPersonalField fdW100p email<?php echo (int) $tpl['option_arr']['o_bf_include_email'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_email']) ? htmlspecialchars(stripslashes(@$FORM['c_email'])) : htmlspecialchars(stripslashes(@$CLIENT['c_email'])); ?>" data-err="<?php __('front_email_required');?>" data-email="<?php __('front_email_not_valid');?>"/>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_phone'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_phone'); ?><?php if($tpl['option_arr']['o_bf_include_phone'] == 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<input type="text" name="c_phone" class="fdText fdPersonalField fdW50p<?php echo (int) $tpl['option_arr']['o_bf_include_phone'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_phone']) ? htmlspecialchars(stripslashes(@$FORM['c_phone'])) : htmlspecialchars(stripslashes(@$CLIENT['c_phone'])); ?>" data-err="<?php __('front_phone_required');?>"/>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_company'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_company'); ?><?php if($tpl['option_arr']['o_bf_include_company'] == 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<input type="text" name="c_company" class="fdText fdPersonalField fdW100p<?php echo (int) $tpl['option_arr']['o_bf_include_company'] === 3 ? ' required' : NULL; ?>" value="<?php echo isset($FORM['c_company']) ? htmlspecialchars(stripslashes(@$FORM['c_company'])) : htmlspecialchars(stripslashes(@$CLIENT['c_company'])); ?>" data-err="<?php __('front_company_required');?>"/>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_notes'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_notes'); ?><?php if ((int) $tpl['option_arr']['o_bf_include_notes'] === 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<textarea name="c_notes" class="fdTextarea fdPersonalField fdW100p fdH150<?php echo (int) $tpl['option_arr']['o_bf_include_notes'] === 3 ? ' required' : NULL; ?>" data-err="<?php __('front_notes_required');?>"><?php echo isset($FORM['c_notes']) ? htmlspecialchars(stripslashes(@$FORM['c_notes'])) : htmlspecialchars(stripslashes(@$CLIENT['c_notes'])); ?></textarea>
						</p>
						<?php
					}
					if($controller->isFrontLogged())
					{
						?>
						<p class="fdParagraph">
							<span class="fdTitle fdCheckbox">
								<input type="checkbox" name="update_details" id="fdUpdateDetail_<?php echo $index;?>"<?php echo isset($FORM['update_details']) ? ' checked="checked"' : NULL; ?> value="None">
								<label for="fdUpdateDetail_<?php echo $index;?>"><?php echo __('front_save_changes');?></label>
							</span>
						</p>
						<?php
					} 
					?>
					<div class="fdCrossLine"></div> 
					<div class="fdFormHeading">
						<span class="fdBlock fdFloatLeft"><?php echo strtoupper(__('front_payment', true, false)); ?></span>
					</div>
					<?php
					if($tpl['option_arr']['o_payment_disable'] == 'No')
					{
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_payment_medthod'); ?>&nbsp;<span class="fdRed">*</span>:</label>
							<select id="fdPaymentMethod_<?php echo $index;?>" name="payment_method" class="fdSelect fdW50p " data-err="<?php __('front_payment_method_required');?>">
								<option value="">----</option>
								<?php
								foreach (__('payment_methods', true, false) as $k => $v)
								{
									if($tpl['option_arr']['o_allow_' . $k] == 'Yes')
									{
										?><option value="<?php echo $k; ?>"<?php echo isset($FORM['payment_method']) && $FORM['payment_method'] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
									}
								}
								?>
							</select>
						</p>
						<?php
					}
					?>
					<div id="fdCCData_<?php echo $index;?>" style="display: <?php echo isset($FORM['payment_method']) && $FORM['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
					
					</div>
					<div class="fdCrossLine"></div> 
					<?php
					if (in_array($tpl['option_arr']['o_bf_include_captcha'], array(2, 3))){ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_captcha'); ?><?php if($tpl['option_arr']['o_bf_include_captcha'] == 3){ ?>&nbsp;<span class="fdRed">*</span><?php }?>:</label>
							<span class="trContent">
								<input type="text" id="fdCaptcha_<?php echo $index;?>" maxlength="6" name="captcha" class="fdText fdFloatLeft fdR5 fdW90<?php echo ($tpl['option_arr']['o_bf_include_captcha'] == 3) ? ' required' : NULL; ?>" autocomplete="off" data-err="<?php __('front_captcha_required');?>" data-incorrect="<?php __('frotn_incorrect_captcha');?>"/>
								<img src="<?php echo PJ_INSTALL_FOLDER; ?>index.php?controller=pjFront&action=pjActionCaptcha&rand=<?php echo rand(1, 9999); ?>" alt="Captcha" style="border: solid 1px #E0E3E8;"/>
							</span>
						</p>
						<?php
					} 
					?>
					<p class="fdParagraph">
						<span class="fdBlock fdOverflow fdAgreement">
							<input id="fdAgree_<?php echo $index;?>" name="agreement" type="checkbox" checked="checked" class="required" data-err="<?php __('front_agree_required');?>" />&nbsp;<label for="fdAgree_<?php echo $index; ?>"><?php __('front_agree');?></label>&nbsp;<a href="#" id="fdBtnTerms_<?php echo $index;?>"><?php __('front_terms_conditions');?></a>
						</span>
					</p>
					<?php
					if(!empty($tpl['terms_conditions']))
					{
						?>
						<div id="fdTermContainer_<?php echo $index;?>" style="display: none;">
							<p class="fdParagraph fdTermsConditions">
								<?php echo nl2br(pjSanitize::clean($tpl['terms_conditions']));?>
							</p>
						</div>
						<?php
					} 
					?>
				</form>
				<div class="fdOverflow fdButtonContainer">
					<a href="#" class="fdButton fdNormalButton fdFloatLeft fdButtonGetTypes"><?php __('front_button_back');?></a>
					<a href="#" class="fdButton fdOrangeButton fdButtonNext fdButtonGetPreview fdFloatRight"><?php __('front_button_continue');?></a>
				</div>
				<?php
			} else {
				?>
				<div class="fdForm fdSystemMessage">
					<?php
					$front_messages = __('front_messages', true, false);
					$system_msg = str_replace("[STAG]", "<a href='#' class='fdStartOver'>", $front_messages[13]);					
					$system_msg = str_replace("[ETAG]", "</a>", $system_msg); 
					echo $system_msg; 
					?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<div id="fdCart_<?php echo $index; ?>" class="fdCart"><?php include PJ_VIEWS_PATH . 'pjFront/elements/cart.php'; ?></div>
	
</div>