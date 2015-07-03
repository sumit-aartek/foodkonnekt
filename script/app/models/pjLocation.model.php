<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjLocationModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'locations';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'lat', 'type' => 'float', 'default' => ':NULL'),
		array('name' => 'lng', 'type' => 'float', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public $i18n = array('name', 'address');
	
	public static function factory($attr=array())
	{
		return new pjLocationModel($attr);
	}
}
?>