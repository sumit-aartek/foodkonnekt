<?php
if (pjObject::getPlugin('pjOneAdmin') !== NULL)
{
	$controller->requestAction(array('controller' => 'pjOneAdmin', 'action' => 'pjActionMenu'));
}
?>

<div class="leftmenu-top"></div>
<div class="leftmenu-middle">
	<ul class="menu">
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionIndex' ? 'menu-focus' : NULL; ?>"><span class="menu-dashboard">&nbsp;</span><?php __('menuDashboard'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminOrders' ? 'menu-focus' : NULL; ?>"><span class="menu-orders">&nbsp;</span><?php __('menuOrders'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProducts&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminProducts' ? 'menu-focus' : NULL; ?>"><span class="menu-products">&nbsp;</span><?php __('menuProducts'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCategories&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminCategories' ? 'menu-focus' : NULL; ?>"><span class="menu-categories">&nbsp;</span><?php __('menuCategories'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminExtras' ? 'menu-focus' : NULL; ?>"><span class="menu-extras">&nbsp;</span><?php __('menuExtras'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminLocations' || $_GET['controller'] == 'pjAdminTime' ? 'menu-focus' : NULL; ?>"><span class="menu-locations">&nbsp;</span><?php __('menuLocations'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVouchers&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminVouchers' ? 'menu-focus' : NULL; ?>"><span class="menu-vouchers">&nbsp;</span><?php __('menuVouchers'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminClients' ? 'menu-focus' : NULL; ?>"><span class="menu-clients">&nbsp;</span><?php __('menuClients'); ?></a></li>		
		<?php
		if ($controller->isAdmin())
		{
			?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionIndex" class="<?php echo ($_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionIndex', 'pjActionOrders', 'pjActionClientDetails', 'pjActionNotification', 'pjActionTerm'))) || in_array($_GET['controller'], array('pjAdminLocales', 'pjBackup', 'pjLocale', 'pjSms')) ? 'menu-focus' : NULL; ?>"><span class="menu-options">&nbsp;</span><?php __('menuOptions'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminUsers' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span><?php __('menuUsers'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminMerchant&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminMerchant' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span>Merchant</a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionInstall" class="<?php echo $_GET['controller'] == 'pjAdminOptions' && $_GET['action'] == 'pjActionInstall' ? 'menu-focus' : NULL; ?>"><span class="menu-install">&nbsp;</span><?php __('menuInstall'); ?></a></li>
			<?php
		}
		if ($controller->isEditor())
		{
			?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionProfile" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionProfile' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span><?php __('menuProfile'); ?></a></li><?php
		}
		?>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionLogout"><span class="menu-logout">&nbsp;</span><?php __('menuLogout'); ?></a></li>
	</ul>
</div>
<div class="leftmenu-bottom"></div>