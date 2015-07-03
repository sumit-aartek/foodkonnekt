<?php
$front_messages = __('front_messages', true, false);

//print_r($tpl['arr']['cart_info']);

switch ($tpl['arr']['payment_method'])
{
	case 'paypal':
?>
             //   <?php include PJ_VIEWS_PATH . 'pjFront/elements/api_test.php'; ?>
		<div class="fdSystemMessage"><?php echo $front_messages[1]; ?></div><?php
		if (pjObject::getPlugin('pjPaypal') !== NULL)
		{
			$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
		}
		break;
	case 'authorize':
		?>
               // <?php include PJ_VIEWS_PATH . 'pjFront/elements/api_test.php'; ?>
                <div class="fdSystemMessage"><?php echo $front_messages[2]; ?></div><?php
		if (pjObject::getPlugin('pjAuthorize') !== NULL)
		{
			$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
		}
		break;
	case 'bank':
		?>
		//<?php include PJ_VIEWS_PATH . 'pjFront/elements/api_test.php'; ?>
                <div class="fdSystemMessage">
			<?php
			$system_msg = str_replace("[STAG]", "<a href='#' class='fdStartOver'>", $front_messages[3]);
			$system_msg = str_replace("[ETAG]", "</a>", $system_msg); 
			echo $system_msg; 
			?>
			<br /><br />
			<?php echo pjSanitize::html(nl2br($tpl['option_arr']['o_bank_account'])); ?>
		</div>
		<?php
		break;
	case 'creditcard':
	case 'cash':
	default:		
		$user_name = urlencode($_SESSION['order_data']['o_user_name']);
		?>		
		<div class="fdSystemMessage">
			<?php
			$system_msg = str_replace("[STAG]", "<a href='". PJ_INSTALL_URL. $user_name.'/restaurants/'.base64_encode($_SESSION['order_data']['o_user_id']) ."'>", $front_messages[3]);
			$system_msg = str_replace("[ETAG]", "</a>", $system_msg); 
			echo $system_msg; 
			?>
		</div>
		<?php
}

?>