<div class="fdLoader"></div>

<?php

include PJ_VIEWS_PATH . 'pjFront/elements/locale.php'; 
$controller->_get('p_location_id');
$index = $_GET['index'];

$STORAGE = @$_SESSION[$controller->defaultStore];
$FORM = isset($_SESSION[$controller->defaultForm]) ? $_SESSION[$controller->defaultForm] : array();
$CLIENT = $controller->isFrontLogged() ? @$_SESSION[$controller->defaultClient] : array();

?>
<div class="fdContainerInner">

	<div id="fdCart_<?php echo $index; ?>" class="fdCart">
		<?php include PJ_VIEWS_PATH . 'pjFront/elements/cart.php'; ?>
	</div> <!-- /.fdCart -->
	
	<div id="fdMain_<?php echo $index; ?>" class="fdMain">
		<div class="fdFormContainer">
			<?php
			if($tpl['status'] == 'OK')
			{ 
				?>
				<div class="fdForm">
					
					<?php
					ob_start();
					if (in_array($tpl['option_arr']['o_bf_include_address_1'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_address_line_1'); ?>:</label>
							<label class="fdContent"><?php echo isset($FORM['c_address_1']) ? htmlspecialchars(stripslashes(@$FORM['c_address_1'])) : htmlspecialchars(stripslashes(@$CLIENT['c_address_1'])); ?></label>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_address_2'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_address_line_2'); ?>:</label>
							<label class="fdContent"></label>
							<?php echo isset($FORM['c_address_2']) ? htmlspecialchars(stripslashes(@$FORM['c_address_2'])) : htmlspecialchars(stripslashes(@$CLIENT['c_address_2'])); ?>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_city'); ?>:</label>
							<label class="fdContent"></label>
							<?php echo isset($FORM['c_city']) ? htmlspecialchars(stripslashes(@$FORM['c_city'])) : htmlspecialchars(stripslashes(@$CLIENT['c_city'])); ?>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_state'); ?>:</label>
							<label class="fdContent"><?php echo isset($FORM['c_state']) ? htmlspecialchars(stripslashes(@$FORM['c_state'])) : htmlspecialchars(stripslashes(@$CLIENT['c_state'])); ?></label>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_zip'); ?>:</label>
							<label class="fdContent"><?php echo isset($FORM['c_zip']) ? htmlspecialchars(stripslashes(@$FORM['c_zip'])) : htmlspecialchars(stripslashes(@$CLIENT['c_zip'])); ?></label>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_country'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_country'); ?>:</label>
							<label class="fdContent"><?php echo pjSanitize::clean($tpl['country_arr']['country_title']);?></label>
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
						<?php echo $ob_address;?>
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
						$name_titles = __('personal_titles', true, false);
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_title'); ?>:</label>
							<label class="fdContent"><?php echo isset($FORM['c_title']) ? $name_titles[$FORM['c_title']] : htmlspecialchars(stripslashes($name_titles[@$CLIENT['c_title']]));?></label>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_name'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_name'); ?>:</label>
							<label class="fdContent"><?php echo isset($FORM['c_name']) ? htmlspecialchars(stripslashes(@$FORM['c_name'])) : htmlspecialchars(stripslashes(@$CLIENT['c_name'])); ?></label>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_email'); ?>:</label>
							<label class="fdContent"><?php echo isset($FORM['c_email']) ? htmlspecialchars(stripslashes(@$FORM['c_email'])) : htmlspecialchars(stripslashes(@$CLIENT['c_email'])); ?></label>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_phone'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_phone'); ?>:</label>
							<label class="fdContent"><?php echo isset($FORM['c_phone']) ? htmlspecialchars(stripslashes(@$FORM['c_phone'])) : htmlspecialchars(stripslashes(@$CLIENT['c_phone'])); ?></label>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_company'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_company'); ?>:</label>
							<label class="fdContent"><?php echo isset($FORM['c_company']) ? htmlspecialchars(stripslashes(@$FORM['c_company'])) : htmlspecialchars(stripslashes(@$CLIENT['c_company'])); ?></label>
						</p>
						<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_notes'], array(2, 3)))
					{ 
						?>
						<p class="fdParagraph">
							<label class="fdTitle"><?php __('front_notes'); ?>:</label>
							<label class="fdContent"><?php echo isset($FORM['c_notes']) ? htmlspecialchars(stripslashes(@$FORM['c_notes'])) : htmlspecialchars(stripslashes(@$CLIENT['c_notes'])); ?></label>
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
							<label class="fdTitle"><?php __('front_payment_medthod'); ?>:</label>
							<label class="fdContent">
								<?php
								$payment_methods = __('payment_methods', true, false);
								echo $payment_methods[$FORM['payment_method']]; 
								?>
							</label>
						</p>
						<?php
					}
					?>
					<div id="fdCCData_<?php echo $index;?>" style="display: <?php echo isset($FORM['payment_method']) && $FORM['payment_method'] == 'creditcard' ? 'block' : 'none'; ?>">
					
					</div>
					<p class="fdParagraph" style="display:none;">
						<span id="fdOrderMessage_<?php echo $index; ?>" class="fdContent fdOrderMessage"></span>
					</p>
				</div>
				<div class="fdOverflow fdButtonContainer">
					<a href="#" class="fdButton fdNormalButton fdButtonGetCheckout fdFloatLeft"><?php __('front_button_back');?></a>
					<?php					
					$payment_mode = $payment_methods[$FORM['payment_method']];
					?>
					<a href="#" data-tax="<?php echo number_format($tax, 2); ?>" data-amt="<?php echo number_format($total, 2); ?>" data-pay="<?php echo $payment_mode; ?>" data-location="<?php echo $tpl['ldata']; ?>" class="fdButton fdOrangeButton fdButtonNext fdButtonConfirm fdFloatRight"><?php __('front_button_confirm');?></a>
				</div>
				<?php
			}else{
				?>
				<div class="fdForm fdSystemMessage">
					<?php
					$front_messages = __('front_messages', true, false);
					$system_msg = str_replace("[STAG]", "<a href='#' class='fdStartOver'>", $front_messages[13]);
					echo "It may be after conformed ";
					$system_msg = str_replace("[ETAG]", "</a>", $system_msg); 
					echo $system_msg; 
					?>
				</div>
				<?php
			} 
			?>
		</div>
	</div> <!-- /.fdMain -->
	
</div> <!-- /.fdContainerInner -->