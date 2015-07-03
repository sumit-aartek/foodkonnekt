<?php
$active = " ui-tabs-active ui-state-active";
?>
<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminLocations' && $_GET['action'] == 'pjActionUpdate'  ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionUpdate&amp;id=<?php echo $_GET['id'];?>"><?php __('menuDetails'); ?></a></li>
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminTime' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTime&amp;action=pjActionIndex&amp;id=<?php echo $_GET['id'];?>"><?php __('menuWorkingTime'); ?></a></li>
		<li class="ui-state-default ui-corner-top<?php echo $_GET['action'] == 'pjActionPrice'  ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionPrice&amp;id=<?php echo $_GET['id'];?>"><?php __('menuDeliveryPrices'); ?></a></li>
	</ul>
</div>