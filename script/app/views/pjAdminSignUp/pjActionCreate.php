<h3>Sign Up</h3>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSignUp&amp;action=pjActionCreate" method="post" id="frmSignupUser" class="form pj-form" autocomplete="off">
	<input type="hidden" name="user_singup" value="1" />
	<input type="hidden" name="role_id" value="1" />
	<p>
		<label class="title"><?php __('email'); ?></label>
		<span class="pj-form-field-custom pj-form-field-custom-before">
			<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
			<input type="text" name="email" id="email" class="pj-form-field required email w200" />
		</span>
	</p>
	<p>
		<label class="title"><?php __('pass'); ?></label>
		<span class="pj-form-field-custom pj-form-field-custom-before">
			<span class="pj-form-field-before"><abbr class="pj-form-field-icon-password"></abbr></span>
			<input type="password" name="password" id="password" class="pj-form-field required w200" />
		</span>
	</p>
	<p>
		<label class="title">Confirm Password</label>
		<span class="pj-form-field-custom pj-form-field-custom-before">
			<span class="pj-form-field-before"><abbr class="pj-form-field-icon-password"></abbr></span>
			<input type="password" name="confirm_password" id="confirm_password" class="pj-form-field required w200" />
		</span>
	</p>
	<p>
		<label class="title"><?php __('lblName'); ?></label>
		<span class="inline_block">
			<input type="text" name="name" id="name" class="pj-form-field w250 required" />
		</span>
	</p>
	<p>
		<label class="title"><?php __('lblPhone'); ?></label>
		<span class="pj-form-field-custom pj-form-field-custom-before">
			<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
			<input type="text" name="phone" id="phone" class="pj-form-field w200" placeholder="(123) 456-7890"/>
		</span>
	</p>
	
	<p>
		<label class="title">&nbsp;</label>
		<input type="submit" value="<?php __('btnSave', false, true); ?>" class="pj-button" />
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionLogin" class="no-decor l10"><?php __('lnkBack'); ?></a>
	</p>
</form>

<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.email_taken = "<?php __('pj_email_taken', false, true); ?>";
</script>