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
	?>
	<div class="pj-loader-2"></div>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionIndex"><?php __('menuLocations'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionCreate"><?php __('lblAddLocation'); ?></a></li>
		</ul>
	</div>
	
	<?php pjUtil::printNotice(__('infoAddLocationTitle', true, false), __('infoAddLocationDesc', true, false)); ?>
	
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
	<div class="multilang"></div>
	<?php endif; ?>
	<div class="pj-loader-outer">
		<div class="pj-loader-1"></div>
		<form action="" method="post" id="frmGetDetails" class="form pj-form frmLocation" autocomplete="off">	
			<p>
				<label class="title">Merchant Id</label>
				<span class="inline_block">
					<input type="text" name="merchant_id" id="merchant_id" class="pj-form-field w250 required" />
				</span>
			</p>		
			<p>
				<label class="title">Access Token Id</label>
				<span class="inline_block">
					<textarea name="merchant_access_token" id="access_token" class="pj-form-field w250 required"></textarea>
				</span>
			</p>		
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="Get Details"  class="pj-button btnGetDetails" />
			</p>	
		</form>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionCreate" method="post" id="frmCreateLocation" class="form pj-form frmLocation" autocomplete="off">
		<input type="hidden" name="location_create" value="1" />
		<input type="hidden" name="user_id" value="<?php echo $_SESSION['admin_user']['id']; ?>" />
		<input type="hidden" name="lat" id="lat" value="" />
		<input type="hidden" name="lng" id="lng" value="" />		
		<?php		
		foreach ($tpl['lp_arr'] as $v)
		{
		?>
			<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
				<label class="title"><?php __('lblLocationName'); ?></label>
				<span class="inline_block">
					<input type="text" id="i18n_name_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" value="<?php echo $name?>" />
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
				<label class="title"><?php __('lblAddress'); ?></label>
				<span class="inline_block">
					<input type="text" id="i18n_address_<?php echo $v['id'];?>" name="i18n[<?php echo $v['id']; ?>][address]" class="pj-form-field w400<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" lang="<?php echo $v['id']; ?>" value="<?php echo $adressLocation?>" />
					<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
					<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
					<?php endif; ?>
				</span>
			</p>
			<?php
		}
		?>		
		<p>
			<label class="title">&nbsp;</label>
			<input type="button" value="<?php __('btnFindCoord'); ?>" class="pj-button btnGetCoords" />
		</p>
		<p id="fd_get_coords_error" style="display:none">
			<label class="title">&nbsp;</label>
			<label class="content red"><?php __('lblAddressNotFound');?></label>
		</p>
		<div class="fd-map-holder pj-loader-outer">
			<div class="pj-loader"></div>
			<div id="fd_map_canvas" class="fd-map-canvas"></div>
		</div>
		<p>
			<label class="title">&nbsp;</label>
			<input type="button" value="<?php __('btnSave'); ?>" class="pj-button pjCloverApi" />
			<input type="button" value="<?php __('btnDeleteShape'); ?>" style="display:none" class="pj-button btnDeleteShape" />
		</p>
	</form>
	
	<script type="text/javascript">
	<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
	var pjLocale = pjLocale || {};
	var myLabel = myLabel || {};
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
					
				}
			});
		});
	})(jQuery_1_8_2);
	<?php endif; ?>
	</script>
	<?php
}
?>