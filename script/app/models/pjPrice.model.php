<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPriceModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'prices';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'location_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'total_from', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'total_to', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjPriceModel($attr);
	}
}
?>