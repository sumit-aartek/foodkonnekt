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
		$bodies_text = str_replace("{SIZE}", ini_get('post_max_size'), @$bodies[$_GET['err']]);
		pjUtil::printNotice(@$titles[$_GET['err']], $bodies_text);
	}
	$_yesno = __('_yesno', true);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionIndex"><?php __('menuProducts'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionCreate"><?php __('lblAddProduct'); ?></a></li>
		</ul>
	</div>
	
	<?php pjUtil::printNotice(__('infoProductsTitle', true, false), __('infoProductsDesc', true, false)); ?>
	
	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		<br class="clear_both" />
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['category_id']) && (int) $_GET['category_id'] > 0)
	{
		?>pjGrid.queryString += "&category_id=<?php echo (int) $_GET['category_id']; ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.image = "<?php __('lblImage'); ?>";
	myLabel.name = "<?php __('lblName'); ?>";
	myLabel.price = "<?php __('lblPrice'); ?>";
	myLabel.is_featured = "<?php __('lblFeaturedProduct'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.yes = "<?php echo $_yesno['T']; ?>";
	myLabel.no = "<?php echo $_yesno['F']; ?>";
	myLabel.delete_selected = "<?php __('delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation'); ?>";
	</script>
	<?php
}
?>