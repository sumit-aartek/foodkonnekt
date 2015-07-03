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
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
		
	pjUtil::printNotice(__('infoOrdersTitle', true), __('infoOrdersDesc', true));
	
	if (isset($tpl['arr']))
	{
		if (is_array($tpl['arr']))
		{
			$count = count($tpl['arr']);
			if ($count > 0)
			{
				?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form">
					<input type="hidden" name="options_update" value="1" />
					<input type="hidden" name="next_action" value="pjActionOrders" />
					<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
						<thead>
							<tr>
								<th><?php __('lblOption'); ?></th>
								<th><?php __('lblValue'); ?></th>
							</tr>
						</thead>
						<tbody>
	
				<?php
				for ($i = 0; $i < $count; $i++)
				{
					if ($tpl['arr'][$i]['tab_id'] != 2 || (int) $tpl['arr'][$i]['is_visible'] === 0) continue;
					
					$rowClass = NULL;
					$rowStyle = NULL;
					if (in_array($tpl['arr'][$i]['key'], array('o_paypal_address')))
					{
						$rowClass = " boxPaypal";
						$rowStyle = "display: none";
						if ($tpl['option_arr']['o_allow_paypal'] == 'Yes')
						{
							$rowStyle = NULL;
						}
					}
					if (in_array($tpl['arr'][$i]['key'], array('o_authorize_merchant_id', 'o_authorize_transkey', 'o_authorize_timezone', 'o_authorize_md5_hash')))
					{
						$rowClass = " boxAuthorize";
						$rowStyle = "display: none";
						if ($tpl['option_arr']['o_allow_authorize'] == 'Yes')
						{
							$rowStyle = NULL;
						}
					}
					if (in_array($tpl['arr'][$i]['key'], array('o_bank_account')))
					{
						$rowClass = " boxBankAccount";
						$rowStyle = "display: none";
						if ($tpl['option_arr']['o_allow_bank'] == 'Yes')
						{
							$rowStyle = NULL;
						}
					}
					
					?>
					<tr class="pj-table-row-odd<?php echo $rowClass; ?>" style="<?php echo $rowStyle; ?>">
						<td width="50%">
							<?php __('opt_' . $tpl['arr'][$i]['key']); ?>
						</td>
						<td>
							<?php
							switch ($tpl['arr'][$i]['type'])
							{
								case 'string':
									?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
									break;
								case 'text':
									?><textarea name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field" style="width: 400px; height: 80px;"><?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?></textarea><?php
									break;
								case 'int':
									?>
									<?php
									
									if($tpl['arr'][$i]['key'] == 'o_tax_payment')
									{ 
										?>
										<span class="pj-form-field-custom pj-form-field-custom-before">
											<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text">%</abbr></span>
											<input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w60 align_right" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" />
										</span>
										<?php 
									} else { 
										?>
										<input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-int w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" />
										<?php
									} 
									break;
								case 'float':
									?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-float w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
									break;
								case 'enum':
									?><select name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field">
									<?php
									$default = explode("::", $tpl['arr'][$i]['value']);
									$enum = explode("|", $default[0]);
									
									$enumLabels = array();
									if (!empty($tpl['arr'][$i]['label']) && strpos($tpl['arr'][$i]['label'], "|") !== false)
									{
										$enumLabels = explode("|", $tpl['arr'][$i]['label']);
									}
									
									foreach ($enum as $k => $el)
									{
										if ($default[1] == $el)
										{
											?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
										} else {
											?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
										}
									}
									?>
									</select>
									<?php
									break;
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>
						</tbody>
					</table>
					
					<p><input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" /></p>
				</form>
				
				<?php
			}
		}
	}
}
?>