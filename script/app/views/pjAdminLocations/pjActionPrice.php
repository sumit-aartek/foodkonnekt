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
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionIndex"><?php __('menuLocations'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionCreate"><?php __('lblAddLocation'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionUpdate&amp;id=<?php echo $_GET['id']; ?>"><?php __('lblUpdateLocation'); ?></a></li>
		</ul>
	</div>
	
	<?php

	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/menu_location.php';
	pjUtil::printNotice(__('infoDeliveryPricesTitle', true, false), __('infoDeliveryPricesDesc', true, false)); 
	?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionPrice" method="post" id="frmUpdatePrices" class="form pj-form">
		<input type="hidden" name="price_update" value="1" />
		<input type="hidden" name="location_id" value="<?php echo $_GET['id']; ?>" />
		
		<table id="tblPrices" class="pj-table b20" cellpadding="0" cellspacing="0" style="width: 100%">
			<thead>
				<tr>
					<th><?php __('lblPriceFrom', false, true); ?></th>
					<th><?php __('lblPriceTo', false, true); ?></th>
					<th><?php __('lblPricePrice', false, true); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($tpl['arr'] as $price)
				{
					?>
					<tr>
						<td>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="total_from[]" class="pj-form-field required number w80" value="<?php echo $price['total_from']; ?>" />
							</span>
						</td>
						<td>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="total_to[]" class="pj-form-field required number w80" value="<?php echo $price['total_to']; ?>" />
							</span>
						</td>
						<td>
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="price[]" class="pj-form-field required number w80" value="<?php echo $price['price']; ?>" />
							</span>
						</td>
						<td>
							<input type="button" value="<?php __('btnRemove'); ?>" class="pj-button pj-remove-price" />
						</td>
					</tr>
					<?php
				} 
				?>
			</tbody>
		</table>
		<p>
			<input type="button" value="<?php __('btnAddPrice'); ?>" class="pj-button pj-add-price" />
		</p>
		<p>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		</p>
	</form>
	
	<table id="tblClonePrices" style="display: none">
		<tbody>
			<tr>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="total_from[]" class="pj-form-field required number w80" />
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="total_to[]" class="pj-form-field required number w80" />
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="price[]" class="pj-form-field required number w80" />
					</span>
				</td>
				<td>
					<input type="button" value="<?php __('btnRemove'); ?>" class="pj-button pj-remove-price" />
				</td>
		</tbody>
	</table>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.field_required = "<?php __('fd_field_required'); ?>";
	</script>
	<?php
}
?>