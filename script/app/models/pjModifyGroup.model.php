<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjModifyGroupModel extends pjAppModel
{
	protected $primaryKey = 'ModifierGroup_Id';
	
	protected $table = 'modifier_group';
	
	protected $schema = array(
		array('name' => 'ModifierGroup_Id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'ModifierGroup_Name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'ModifierGroup_CloverId', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'Merchant_Id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'group_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'group_name', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'group_description', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	//public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjModifyGroupModel($attr);
	}
}
?>