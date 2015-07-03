<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjVoucherModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'vouchers';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'code', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'discount', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'valid', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'date_from', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'date_to', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'time_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'time_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'every', 'type' => 'enum', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjVoucherModel($attr);
	}
}
?>