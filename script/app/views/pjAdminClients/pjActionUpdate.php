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
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionIndex"><?php __('lblClients'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionUpdate&amp;id=<?php echo $tpl['arr']['id']; ?>"><?php __('lblUpdateClient'); ?></a></li>
		</ul>
	</div>
	<?php pjUtil::printNotice(__('infoUpdateClientTitle', true, false), __('infoUpdateClientDesc', true, false)); ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminClients&amp;action=pjActionUpdate" method="post" id="frmUpdateClient" class="form pj-form">
		<input type="hidden" name="client_update" value="1" />
		<input type="hidden" name="id" value="<?php echo (int) $tpl['arr']['id']; ?>" />
		<p>
			<label class="title"><?php __('lblTitle'); ?></label>
			<span class="inline-block">
				<select name="c_title" id="c_title" class="pj-form-field w150">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					$name_titles = __('personal_titles', true, false);
					foreach ($name_titles as $k => $v)
					{
						?><option value="<?php echo $k; ?>"<?php echo $tpl['arr']['c_title'] == $k ? ' selected="selected"' : NULL;?>><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblName'); ?></label>
			<span class="inline-block">
				<input type="text" name="c_name" id="c_name" class="pj-form-field w300 required" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_name'])); ?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblEmail'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
				<input type="text" name="c_email" id="email" class="pj-form-field w300 email required" placeholder="info@domain.com" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_email'])); ?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('pass'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-password"></abbr></span>
				<input type="text" name="c_password" id="c_password" class="pj-form-field required w200" value="<?php echo pjSanitize::html($tpl['arr']['c_password']); ?>" />
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblPhone'); ?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
				<input type="text" name="c_phone" id="phone" class="pj-form-field w150" placeholder="(123) 456-7890" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_phone'])); ?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblCompany'); ?></label>
			<span class="inline-block">
				<input type="text" name="c_company" id="c_company" class="pj-form-field w300" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_company'])); ?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblNotes'); ?></label>
			<span class="inline-block">
				<textarea name="c_notes" id="c_notes" class="pj-form-field w500 h120"><?php echo htmlspecialchars(stripslashes($tpl['arr']['c_notes'])); ?></textarea>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblAddressLine1'); ?></label>
			<span class="inline-block">
				<input type="text" name="c_address_1" id="c_address_1" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_address_1'])); ?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblAddressLine2'); ?></label>
			<span class="inline-block">
				<input type="text" name="c_address_2" id="c_address_2" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_address_2'])); ?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblCity'); ?></label>
			<span class="inline-block">
				<input type="text" name="c_city" id="c_city" class="pj-form-field w300" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_city'])); ?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblState'); ?></label>
			<span class="inline-block">
				<input type="text" name="c_state" id="c_state" class="pj-form-field w300" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_state'])); ?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblZip'); ?></label>
			<span class="inline-block">
				<input type="text" name="c_zip" id="c_zip" class="pj-form-field w300" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_zip'])); ?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblCountry'); ?></label>
			<span class="inline-block">
				<select name="c_country" id="c_country" class="pj-form-field w300">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach ($tpl['country_arr'] as $v)
					{
						?><option value="<?php echo $v['id']; ?>"<?php echo $tpl['arr']['c_country'] == $v['id'] ? ' selected="selected"' : NULL;?>><?php echo stripslashes($v['country_title']); ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblStatus'); ?></label>
			<span class="inline_block">
				<select name="status" id="status" class="pj-form-field required">
					<option value="">-- <?php __('lblChoose'); ?>--</option>
					<?php
					foreach (__('u_statarr', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['status'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		
		<p>
			<label class="title"><?php __('lblClientCreated'); ?></label>
			<span class="left"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['created'])); ?>, <?php echo date("H:i", strtotime($tpl['arr']['created'])); ?></span>
		</p>		
		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
		</p>
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.email_exists = "<?php __('email_taken', false, true); ?>";
	</script>
	<?php
}
?>