<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjTenderModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'tenders';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'tender_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'tender_type', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'location_id', 'type' => 'int', 'default' => ':NULL'),
	);
	
	public static function factory($attr=array())
	{
		return new pjTenderModel($attr);
	}
}
?>