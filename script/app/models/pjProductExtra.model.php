<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjProductExtraModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'products_extras';
	
	protected $schema = array(
		array('name' => 'product_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'extra_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjProductExtraModel($attr);
	}
}
?>