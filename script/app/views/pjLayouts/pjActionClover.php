<!doctype html>
<html>
	<head>
		<title>Get Data From Clover</title>		
        <?php		
		foreach ($controller->getJs() as $js)
		{
			echo '<script src="'.(isset($js['remote']) && $js['remote'] ? NULL : PJ_INSTALL_URL).$js['path'].htmlspecialchars($js['file']).'"></script>';
		}
		?>        
	</head>
	<body>
		<?php require $content_tpl; ?>
	</body>
</html>