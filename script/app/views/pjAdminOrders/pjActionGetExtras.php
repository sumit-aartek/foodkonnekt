<?php
mt_srand();
$index = 'x_' . mt_rand();
?>
<tr>
	<td width="145">
		<select name="extra_id[<?php echo $_GET['index']; ?>][<?php echo $index; ?>]" data-index="<?php echo $_GET['index']; ?>_<?php echo $index; ?>" class="fdExtra fdExtra_<?php echo $_GET['index']; ?> pj-form-field w130">
			<option value="">-- <?php __('lblChoose'); ?>--</option>
			<?php
			if (isset($tpl['extra_arr']))
			{
				foreach ($tpl['extra_arr'] as $e)
				{
					?><option value="<?php echo $e['id']; ?>" data-price="<?php echo $e['price'];?>"><?php echo stripslashes($e['name']); ?>: <?php echo pjUtil::formatCurrencySign($e['price'], $tpl['option_arr']['o_currency'])?></option><?php
				}
			}
			?>
		</select>
	</td>
	<td class="w70"><input type="text" id="fdExtraQty_<?php echo $_GET['index']; ?>_<?php echo $index; ?>" name="extra_cnt[<?php echo $_GET['index']; ?>][<?php echo $index; ?>]" class="pj-form-field w50 float_left pj-field-count" value="1" /></td>
	<td class="w30"><a href="#" class="pj-remove-extra"></a></td>
</tr>