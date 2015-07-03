<?php
if (isset($_GET['product_id']) && (int) $_GET['product_id'] > 0)
{
	if($tpl['arr']['set_different_sizes'] == 'F')
	{
		?>
		<span class="fdPriceLabel"><?php echo pjUtil::formatCurrencySign(round($tpl['arr']['price'], 2), $tpl['option_arr']['o_currency']);?></span>
		<input type="hidden" id="fdPrice_<?php echo $_GET['index'];?>" data-type="input" name="price_id[<?php echo $_GET['index'];?>]" value="<?php echo $tpl['arr']['price'];?>" />
		<?php
	}else{
		?>
		<select id="fdPrice_<?php echo $_GET['index'];?>" name="price_id[<?php echo $_GET['index'];?>]" data-type="select" class="fdSize pj-form-field w140">
			<option value="">-- <?php __('lblChoose'); ?>--</option>
			<?php
			foreach($tpl['price_arr'] as $v)
			{
				?><option value="<?php echo $v['id']?>" data-price="<?php echo $v['price'];?>"><?php echo pjSanitize::clean($v['price_name'])?>: <?php echo pjUtil::formatCurrencySign(round($v['price'], 2), $tpl['option_arr']['o_currency']);; ?></option><?php
			} 
			?>
		</select>
		<?php
	}
} else {
	?>
	<select id="fdPrice_<?php echo $_GET['index'];?>" name="price_id[<?php echo $_GET['index'];?>]" data-type="select" class="fdSize pj-form-field w140">
		<option value="">-- <?php __('lblChoose'); ?>--</option>
	</select>
	<?php
}
?>