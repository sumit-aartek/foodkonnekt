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
			
	if (isset($tpl['arr']))
	{
		if (is_array($tpl['arr']))
		{
			$count = count($tpl['arr']) - 1;
			if ($count > 0)
			{
				?>
				<div class="clear_both">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form">
						<input type="hidden" name="options_update" value="1" />
						<input type="hidden" name="next_action" value="pjActionClientDetails" />
						<input type="hidden" name="tab_id" value="<?php echo isset($_GET['tab_id']) && !empty($_GET['tab_id']) ? $_GET['tab_id'] : 'tabs-1'; ?>" />
						
						<div id="tabs">
							<ul>
								<li><a href="#tabs-1"><?php __('menuOrderForm');?></a></li>
								<li><a href="#tabs-2"><?php __('menuDeliveryForm');?></a></li>
							</ul>
							<div id="tabs-1">
								<?php
								pjUtil::printNotice(__('infoOrderFormTitle', true), __('infoOrderFormDesc', true));
								?>
								<table class="pj-table b10" cellpadding="0" cellspacing="0" style="width: 100%">
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
								if ($tpl['arr'][$i]['tab_id'] != 4 || (int) $tpl['arr'][$i]['is_visible'] === 0) continue;
								
								?>
								<tr class="pj-table-row-odd">
									<td width="30%">
										<?php __('opt_' . $tpl['arr'][$i]['key']); ?>
									</td>
									<td>
										<?php
										switch ($tpl['arr'][$i]['type'])
										{
											case 'string':
												?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w200" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
												break;
											case 'text':
												?><textarea name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field" style="width: 400px; height: 80px;"><?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?></textarea><?php
												break;
											case 'int':
												?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-int w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
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
								<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
							</div><!-- tabs-1 -->
							
							<div id="tabs-2">
								<?php
								pjUtil::printNotice(__('infoDeliveryFormTitle', true), __('infoDeliveryFormDesc', true));
								?>
								<table class="pj-table b10" cellpadding="0" cellspacing="0" style="width: 100%">
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
								if ($tpl['arr'][$i]['tab_id'] != 6 || (int) $tpl['arr'][$i]['is_visible'] === 0) continue;
								
								?>
								<tr class="pj-table-row-odd">
									<td width="30%">
										<?php __('opt_' . $tpl['arr'][$i]['key']); ?>
									</td>
									<td>
										<?php
										switch ($tpl['arr'][$i]['type'])
										{
											case 'string':
												?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w200" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
												break;
											case 'text':
												?><textarea name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field" style="width: 400px; height: 80px;"><?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?></textarea><?php
												break;
											case 'int':
												?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-int w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
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
								
								<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
							</div><!-- tabs-2 -->
						</div><!-- #tabs -->
					</form>
				</div>	
								
				<?php
			}
		}
	}
}
?>
<script type="text/javascript">
(function ($) {
$(function() {
	<?php
	if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
	{		
		$tab_id = $_GET['tab_id'];
		$tab_id = $tab_id < 0 ? 0 : $tab_id; 
		?>$("#tabs").tabs("option", "selected", <?php echo str_replace("tabs-", "", $tab_id) - 1;?>);<?php
	}
	?>
});
})(jQuery_1_8_2);
</script>