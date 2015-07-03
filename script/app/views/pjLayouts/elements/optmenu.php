<?php
$active = " ui-tabs-active ui-state-active";
?>
<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] != 'pjAdminOptions' || $_GET['action'] != 'pjActionIndex' ? NULL : $active; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionIndex"><?php __('menuGeneral'); ?></a></li>
		<li class="ui-state-default ui-corner-top<?php echo ($_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionOrders'))) ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionOrders"><?php __('menuOrders'); ?></a></li>
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionTerm') ) ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionTerm"><?php __('menuTerms'); ?></a></li>
		
		<!-- <li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionOrderForm') ) ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionOrderForm"><?php __('menuOrderForm'); ?></a></li>
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionDeliveryForm') ) ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionDeliveryForm"><?php __('menuDeliveryForm'); ?></a></li> -->
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionClientDetails') ) ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionClientDetails"><?php __('menuClientDetails'); ?></a></li>
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionNotification') ) ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionNotification"><?php __('menuNotifications'); ?></a></li>
		<?php
		if ($controller->isAdmin() && pjObject::getPlugin('pjSms') !== NULL)
		{
			?><li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] != 'pjSms' ? NULL : $active; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjSms&amp;action=pjActionIndex"><?php __('plugin_sms_menu_sms'); ?></a></li><?php
		} 
		?>
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] != 'pjLocale' ? NULL : $active; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjLocale&amp;action=pjActionIndex&amp;tab=1"><?php __('menuLocales'); ?></a></li>
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] != 'pjBackup' ? NULL : $active; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjBackup&amp;action=pjActionIndex"><?php __('menuBackup'); ?></a></li>
	</ul>
</div>