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
	$_yesno = __('_yesno', true);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionIndex"><?php __('menuProducts'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionCreate"><?php __('lblAddProduct'); ?></a></li>
		</ul>
	</div>
	
	<?php pjUtil::printNotice(__('infoAddProductTitle', true, false), __('infoAddProductDesc', true, false)); ?>
	
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionCreate" method="post" id="frmCreateProduct" class="form pj-form" autocomplete="off" enctype="multipart/form-data">
		<input type="hidden" name="product_create" value="1" />
		<input type="hidden" id="index_arr" name="index_arr" value="" />
		<input type="hidden" id="remove_arr" name="remove_arr" value="" />
		<input type="hidden" name="user_id" value="<?php echo $_SESSION['admin_user']['id']; ?>" />
		<?php
		foreach ($tpl['lp_arr'] as $v)
		{
		?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('lblName'); ?></label>
				<span class="inline_block">
					<input type="text" id="i18n_name_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" />
					<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					<?php endif; ?>
				</span>
			</p>
			<?php
		}
		foreach ($tpl['lp_arr'] as $v)
		{
		?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('lblDescription'); ?></label>
				<span class="inline_block">
					<textarea id="i18n_description_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][description]" class="pj-form-field w500 h150<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>"></textarea>
					<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					<?php endif; ?>
				</span>
			</p>
			<?php
		}
		?>
		<p>
			<label class="title"><?php __('lblCategory'); ?></label>
			<span class="inline_block" id="boxCategory">
				<?php
				if(!empty($tpl['category_arr']))
				{ 
					?>
					<select name="category_id[]" id="category_id" multiple="multiple" size="5" class="pj-form-field required w300">
						<?php
						foreach ($tpl['category_arr'] as $v)
						{
							?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
						}
						?>
					</select>
					<?php
				}else{
					?>
					<input type="hidden" name="hidden_category_id" id="hidden_category_id" class="required"/>
					<label class="content"><?php echo __('lblNoCategoriesFound', true, false);?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionCreate"><?php __('lblHere');?></a></label>
					<?php
				} 
				?>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblExtras'); ?></label>
			<span class="inline_block" id="boxExtra">
				<?php
				if(!empty($tpl['extra_arr']))
				{ 
					?>
					<select name="extra_id[]" id="extra_id" multiple="multiple" size="5" class="pj-form-field w300">
						<?php
						foreach ($tpl['extra_arr'] as $v)
						{
							?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?> (<?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']); ?>)</option><?php
						}
						?>
					</select>
					<?php
				}else{
					?>
					<label class="content"><?php echo __('lblNoExtrasFound', true, false);?> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionCreate"><?php __('lblHere');?></a></label>
					<?php
				} 
				?>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblSetDifferentSizes'); ?></label>
			<span class="inline_block">
				<span class="block float_left r10 t5">
					<input type="radio" id="set_yes" name="set_different_sizes" value="T"/><label for="set_yes"><?php echo $_yesno['T']; ?></label>
				</span>
				<span class="block float_left r10 t5">
					<input type="radio" id="set_no" name="set_different_sizes" value="F" checked="checked"/><label for="set_no"><?php echo $_yesno['F']; ?></label>
				</span>
			</span>
		</p>
		<p id="signle_price" style="display: block;">
			<label class="title"><?php __('lblPrice'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
				<input type="text" id="price" name="price" class="pj-form-field pj-positive-number w80"/>
			</span>
		</p>
		<div id="multiple_prices" style="display: none;">
			<p class="pj-size-count pj-size-title">
				<label class="title">&nbsp;</label>
				<span class="inline_block">
					<label class="content float_left r218"><?php __('lblSize');?></label>
					<label class="content float_left"><?php __('lblPrice');?></label>
				</span>
			</p>
			<div id="fd_size_list" class="fd-size-list">
				<?php
				$index = 'fd_' . rand(1, 999999); 
				?>
				<div class="fd-size-row" data-index="<?php echo $index;?>">
					<?php
					foreach ($tpl['lp_arr'] as $v)
					{
						?>
						<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
							<label class="title fd-title-<?php echo $index;?>"><?php __('lblSize'); ?> 1:</label>
							<span class="inline_block">
								<input type="text" name="i18n[<?php echo $v['id']; ?>][price_name][<?php echo $index;?>]" class="pj-form-field float_left r3 w200<?php echo (int) $v['is_default'] === 0 ? NULL : ' fdRequired'; ?>" lang="<?php echo $v['id']; ?>"/>
								<span class="pj-multilang-input float_left r10"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
							</span>
						</p>
						<?php
					}
					?>
					<div class="pj-size-count">
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" name="product_price[<?php echo $index;?>]" class="pj-form-field pj-positive-number w80"/>
						</span>
					</div>
				</div>
			</div>
			<p>
				<label class="title">&nbsp;</label>
				<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button pj-add-size" />
			</p>
		</div>
		<p>
			<label class="title"><?php __('lblFeaturedProduct'); ?></label>
			<span class="inline_block">
				<input type="checkbox" id="is_featured" name="is_featured" class="t6"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblImage', false, true); ?></label>
			<span class="inline_block">
				<input type="file" name="image" id="image" class="pj-form-field w300"/>
			</span>
		</p>
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		</p>
	</form>
	<div id="fd_size_clone" style="display: none;">
		<div class="fd-size-row" data-index="{INDEX}">
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title fd-title-{INDEX}"><?php __('lblSize'); ?> {ORDER}:</label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][price_name][{INDEX}]" class="pj-form-field float_left r3 w200<?php echo (int) $v['is_default'] === 0 ? NULL : ' fdRequired'; ?>" lang="<?php echo $v['id']; ?>"/>
						<span class="pj-multilang-input float_left r10"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					</span>
				</p>
				<?php
			}
			?>
			<div class="pj-size-count">
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="product_price[{INDEX}]" class="pj-form-field pj-positive-number w80"/>
				</span>
			</div>
			<div class="size-icons">
				<input type="button" value="<?php __('btnRemove'); ?>" class="pj-button pj-remove-size" />
			</div>
		</div>
	</div>
	<script type="text/javascript">
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
	var pjLocale = pjLocale || {};
	var myLabel = myLabel || {};
	myLabel.choose = "-- <?php __('lblChoose'); ?> --";
	myLabel.size = "<?php __('lblSize'); ?>";
	var locale_array = new Array(); 
	pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	
	myLabel.field_required = "<?php __('fd_field_required'); ?>";
	<?php
	foreach ($tpl['lp_arr'] as $v)
	{
		?>locale_array.push(<?php echo $v['id'];?>);<?php
	} 
	?>
	myLabel.locale_array = locale_array;
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
					$.get("index.php?controller=pjAdminProducts&action=pjActionGetLocale", {
						"locale" : ui.index
					}).done(function (data) {
						cid = $("#category_id").val();
						$("#boxCategory").html(data.category);
						$("#category_id").val(cid);
						$("#category_id").multiselect({noneSelectedText: myLabel.choose});

						eid = $("#extra_id").val();
						$("#boxExtra").html(data.extra);
						$("#extra_id").val(eid);
						$("#extra_id").multiselect({noneSelectedText: myLabel.choose});
					});
				}
			});
		});
	})(jQuery_1_8_2);
	<?php endif; ?>
	</script>
	<?php
}
?>