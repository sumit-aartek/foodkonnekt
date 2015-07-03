<?php
if(isset($_SESSION[$controller->defaultLangMenu]) && $_SESSION[$controller->defaultLangMenu] == 'show')
{
	?>
	<div class="fdLocale">
	<?php
	if (isset($tpl['locale_arr']) && is_array($tpl['locale_arr']) && !empty($tpl['locale_arr']))
	{
		?>
		
		
		<?php
	}
	?>
	</div>
	<?php
} 
?>