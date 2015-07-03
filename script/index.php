<?php
if (!headers_sent())
{
	session_name('FoodDelivery');
	@session_start();
}
if (in_array($_SERVER['SERVER_ADDR'], array('127.0.0.1', '::1')))
{
	ini_set("display_errors", "On");
	error_reporting(E_ALL|E_STRICT);
} else {
	error_reporting(0);
}
header("Content-type: text/html; charset=utf-8");
if (!defined("ROOT_PATH"))
{
	define("ROOT_PATH", dirname(__FILE__) . '/');
}

require ROOT_PATH . 'app/config/options.inc.php';

require_once PJ_FRAMEWORK_PATH . 'pjAutoloader.class.php';
pjAutoloader::register();

if (!isset($_GET['controller']) || empty($_GET['controller']))
{
	header("HTTP/1.1 301 Moved Permanently");
	pjUtil::redirect(PJ_INSTALL_URL . basename($_SERVER['PHP_SELF'])."?controller=pjAdmin&action=pjActionIndex");
}

if (isset($_GET['controller']))
{
	$pjObserver = pjObserver::factory();
	$pjObserver->init();
}
?>