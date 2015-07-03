<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>FoodKonnekt</title>
	<link href="<?php echo PJ_INSTALL_URL . PJ_THIRD_PARTY_PATH; ?>front/bootstrap3/css/bootstrap.css" rel="stylesheet" />
	<link href="<?php echo PJ_INSTALL_URL . PJ_THIRD_PARTY_PATH; ?>front/assets/css/get-shit-done.css" rel="stylesheet" />  
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link href="<?php echo PJ_INSTALL_URL . PJ_THIRD_PARTY_PATH; ?>front/assets/css/demo.css" rel="stylesheet" /> 
    
    <!--     Font Awesome     -->
    <link href="<?php echo PJ_INSTALL_URL . PJ_THIRD_PARTY_PATH; ?>front/bootstrap3/css/font-awesome.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Grand+Hotel' rel='stylesheet' type='text/css'>
	
	<script src="<?php echo PJ_INSTALL_URL . PJ_THIRD_PARTY_PATH; ?>front/jquery/jquery-1.10.2.js" type="text/javascript"></script>
	<script src="<?php echo PJ_INSTALL_URL . PJ_THIRD_PARTY_PATH; ?>front/jquery/jquery.validate.min.js" type="text/javascript"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="<?php echo PJ_INSTALL_URL . PJ_THIRD_PARTY_PATH; ?>front/bootstrap3/js/bootstrap.js" type="text/javascript"></script>
  
</head>
<body>
	<div id="page">
		<?php require $content_tpl; ?>
	</div><!--#page-->
	<?php
	foreach ($controller->getJs() as $js)
	{
		echo '<script src="'.(isset($js['remote']) && $js['remote'] ? NULL : PJ_INSTALL_URL).$js['path'].$js['file'].'"></script>';
	}
	?>	
	<script src="<?php echo PJ_INSTALL_URL . PJ_THIRD_PARTY_PATH; ?>front/assets/js/gsdk-radio.js"></script>
</body>
</html>