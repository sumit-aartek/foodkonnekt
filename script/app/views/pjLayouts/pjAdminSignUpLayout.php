<!doctype html>
<html>
	<head>
		<title>Food Konnekt</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<?php
		foreach ($controller->getCss() as $css)
		{
			echo '<link type="text/css" rel="stylesheet" href="'.(isset($css['remote']) && $css['remote'] ? NULL : PJ_INSTALL_URL).$css['path'].$css['file'].'" />';
		}
		
		foreach ($controller->getJs() as $js)
		{
			echo '<script src="'.(isset($js['remote']) && $js['remote'] ? NULL : PJ_INSTALL_URL).$js['path'].$js['file'].'"></script>';
		}
		?>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<a href="#" id="logo" target="_blank"><img src="<?php echo PJ_INSTALL_URL . PJ_IMG_PATH; ?>backend/logo.png" alt="" /></a>
			</div>
			<div id="middle">
				<div id="login-content">
				<?php require $content_tpl; ?>
				</div>
			</div> <!-- middle -->
		</div> <!-- container -->
		<div id="footer-wrap">
			<div id="footer">
			   	<!--<p><a href="http://www.foodkonnekt.com" target="_blank">Foodkonnect</a> Copyright &copy; <?php echo date("Y"); ?>. All rights reserved.</p>-->
	        </div>
        </div>
	</body>
</html>