<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjClientModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'clients';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'c_title', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_password', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'c_phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_company', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_address_1', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_address_2', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_country', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'c_state', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_city', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_zip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_notes', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'last_login', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'F')
	);
	
	public static function factory($attr=array())
	{
		return new pjClientModel($attr);
	}
}
?>