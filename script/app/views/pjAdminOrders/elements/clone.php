<tr class="fdLine" data-index="{INDEX}">
	<td width="166">
		<select id="fdProduct_{INDEX}" data-index="{INDEX}" name="product_id[{INDEX}]" class="pj-form-field fdProduct w160">
			<option value="">-- <?php __('lblChoose'); ?>--</option>
			<?php
			foreach ($tpl['product_arr'] as $p)
			{
				?><option value="<?php echo $p['id']; ?>"><?php echo stripslashes($p['name']); ?></option><?php
			}
			?>
		</select>
	</td>
	<td width="145" id="fdPriceTD_{INDEX}">
		<select id="fdPrice_{INDEX}" name="price_id[{INDEX}]" data-type="select" class="fdSize pj-form-field w140">
			<option value="">-- <?php __('lblChoose'); ?>--</option>
		</select>
	</td>
	<td width="70" class="splitter">
		<input type="text" id="fdProductQty_{INDEX}" name="cnt[{INDEX}]" class="pj-form-field w50 float_left pj-field-count" value="1" />
	</td>
	<td  width="236" colspan="3" class="splitter fdPL5">
		<table id="fdExtraTable_{INDEX}" class="pj-extra-table" cellpadding="0" cellspacing="0" style="width: auto">							
			<tbody>
			</tbody>
		</table>
		<input type="button" value="<?php __('btnAddExtra');?>" data-index="{INDEX}" class="pj-button float_left pj-add-extra" />
	</td>
	<td width="70" class="fdPL5">
		<span id="fdTotalPrice_{INDEX}" class="fdPriceLabel"><?php echo pjUtil::formatCurrencySign(number_format(0, 2), $tpl['option_arr']['o_currency']);?></span>
	</td>
	<td width="30">
		<a href="#" class="pj-remove-product"></a>
	</td>
</tr>
