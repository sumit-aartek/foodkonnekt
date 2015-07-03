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
}else{
	?>
	<div class="dashboard_header">
		<div class="item">
			<div class="stat orders">
				<div class="info">
					<abbr><?php echo $tpl['cnt_delivery_orders'];?> / <?php echo pjUtil::formatCurrencySign(number_format($tpl['amount_delivery_orders'], 0), $tpl['option_arr']['o_currency'])?></abbr>
					<?php echo $tpl['cnt_delivery_orders'] != 1 ? __('lblDeliveryOrdersToday', true, false) : __('lblDeliveryOrderToday', true, false); ?>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat orders">
				<div class="info">
					<abbr><?php echo $tpl['cnt_pickup_orders'];?> / <?php echo pjUtil::formatCurrencySign(number_format($tpl['amount_pickup_orders'], 0), $tpl['option_arr']['o_currency'])?></abbr>
					<?php echo $tpl['cnt_pickup_orders'] != 1 ? __('lblPickupOrdersToday', true, false) : __('lblPickupOrderToday', true, false); ?>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat orders">
				<div class="info">
					<abbr><?php echo $tpl['cnt_orders'];?> / <?php echo pjUtil::formatCurrencySign(number_format($tpl['amount_orders'], 0), $tpl['option_arr']['o_currency'])?></abbr>
					<?php echo $tpl['cnt_orders'] != 1 ? __('lblTotalOrders', true, false) : __('lblOneOrder', true, false); ?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="dashboard_box">
		<div class="dashboard_top">
			<div class="dashboard_column_top"><?php __('lblLatestDeliveryOrders');?> (<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionIndex&amp;type=delivery"><?php __('lblViewAll');?></a>)</div>
			<div class="dashboard_column_top"><?php __('lblLatestPickupOrders');?> (<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionIndex&amp;type=pickup"><?php __('lblViewAll');?></a>)</div>
			<div class="dashboard_column_top"><?php count($tpl['location_arr']) == 1 ? __('lblLocationWorkingStatus') : __('lblLocationsWorkingStatus') ;?></div>
		</div>
		<div class="dashboard_middle">
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					$order_statuses = __('order_statuses', true, false);
					
					if(!empty($tpl['latest_delivery']))
					{
						foreach($tpl['latest_delivery'] as $v)
						{
							?>
							<div class="dashboard_row">
								<label><span><?php __('lblOrderID')?>:</span> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']?>"><?php echo stripslashes($v['uuid']);?></a></label>
								<label><span><?php __('lblClient')?>:</span> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionUpdate&amp;id=<?php echo $v['client_id']?>"><?php echo pjSanitize::html($v['client_name']);?></a></label>
								<label><span><?php __('lblLocation')?>:</span> <?php echo pjSanitize::html($v['location']);?></label>
								<label><span><?php __('lblStatus')?>:</span> <?php echo $order_statuses[$v['status']];?></label>
								<label><span><?php __('lblDateTime')?>:</span> <?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['d_dt'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['d_dt']));?></label>
								<label><span><?php __('lblTotal')?>:</span> <?php echo pjUtil::formatCurrencySign($v['total'], $tpl['option_arr']['o_currency']);?></label>
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row">
							<label><span><?php __('lblDashNoOrder');?></span></label>
						</div>
						<?php
					} 
					?>
				</div>
			</div>
			
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					if(!empty($tpl['latest_pickup']))
					{
						foreach($tpl['latest_pickup'] as $v)
						{
							?>
							<div class="dashboard_row">
								<label><span><?php __('lblOrderID')?>:</span> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOrders&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']?>"><?php echo stripslashes($v['uuid']);?></a></label>
								<label><span><?php __('lblClient')?>:</span> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionUpdate&amp;id=<?php echo $v['client_id']?>"><?php echo pjSanitize::html($v['client_name']);?></a></label>
								<label><span><?php __('lblLocation')?>:</span> <?php echo pjSanitize::html($v['location']);?></label>
								<label><span><?php __('lblStatus')?>:</span> <?php echo $order_statuses[$v['status']];?></label>
								<label><span><?php __('lblDateTime')?>:</span> <?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['p_dt'])) . ', ' . date($tpl['option_arr']['o_time_format'], strtotime($v['p_dt']));?></label>
								<label><span><?php __('lblTotal')?>:</span> <?php echo pjUtil::formatCurrencySign($v['total'], $tpl['option_arr']['o_currency']);?></label>
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row">
							<label><span><?php __('lblDashNoOrder');?></span></label>
						</div>
						<?php
					} 
					?>
				</div>
			</div>
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list quick_links">
					<?php
					foreach($tpl['location_arr'] as $v)
					{
						?>
						<label><?php echo pjSanitize::clean($v['location_title']);?></label>
						<label><span><?php __('lblDelivery')?>: <abbr><?php echo $v['delivery']; ?></abbr></span></label>
						<label class="space"><span><?php __('lblPickup')?>: <abbr><?php echo $v['pickup']; ?></abbr></span></label>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<div class="dashboard_bottom"></div>
	</div>
	
	<div class="clear_left t20 overflow">
		<div class="float_left black t30 t20"><span class="gray"><?php echo ucfirst(__('lblDashLastLogin', true)); ?>:</span> <?php echo pjUtil::formatDate(date('Y-m-d', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'Y-m-d', $tpl['option_arr']['o_date_format']) . ', ' . pjUtil::formatTime(date('H:i:s', strtotime($_SESSION[$controller->defaultUser]['last_login'])), 'H:i:s', $tpl['option_arr']['o_time_format']); ?></div>
		<div class="float_right overflow">
		<?php
		list($hour, $day, $other) = explode("_", date("H:i_l_F d, Y"));
		$days = __('days', true, false);
		?>
			<div class="dashboard_date">
				<abbr><?php echo $days[date('w')]; ?></abbr>
				<?php echo pjUtil::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>
			</div>
			<div class="dashboard_hour"><?php echo $hour; ?></div>
		</div>
	</div>
	<?php
}
?>