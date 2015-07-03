<div class="fdLoader"></div>
<?php
include PJ_VIEWS_PATH . 'pjFront/elements/locale.php'; 
$index = $_GET['index'];
?>
<div class="fdContainerInner">	
	<div id="fdMain_<?php echo $index; ?>" class="fdMain"><?php include PJ_VIEWS_PATH . 'pjFront/elements/categories.php'; ?></div>
	<div id="fdCart_<?php echo $index; ?>" class="fdCart"><?php include PJ_VIEWS_PATH . 'pjFront/elements/cart.php'; ?></div>
</div>