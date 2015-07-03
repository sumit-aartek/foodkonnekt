<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjProductPriceModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'products_prices';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'product_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL')
	);
	
	public $i18n = array('price_name');
	
	public static function factory($attr=array())
	{
		return new pjProductPriceModel($attr);
	}
}
?>