<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjLocationMapModel extends pjAppModel
{
	protected $primaryKey = 'PHPJABBER_LOCATION_ID';
	
	protected $table = 'locations_map';
	
	protected $schema = array(
		array('name' => 'PHPJABBER_LOCATION_ID', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'CLOVER_MID', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'CLOVER_ACCESS_TOKEN_ID', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'USER_ID', 'type' => 'int', 'default' => ':NULL')
	);
	
	public $i18n = array('name', 'address');
	
	public static function factory($attr=array())
	{
		return new pjLocationMapModel($attr);
	}
}
?>