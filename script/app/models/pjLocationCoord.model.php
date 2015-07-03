<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjLocationCoordModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'locations_coords';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'location_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'data', 'type' => 'text', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjLocationCoordModel($attr);
	}
}
?>