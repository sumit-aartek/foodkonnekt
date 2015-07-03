<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjProductModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'products';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'set_different_sizes', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'image', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'is_featured', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'order', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public $i18n = array('name', 'description');
	
	public static function factory($attr=array())
	{
		return new pjProductModel($attr);
	}
}
?>