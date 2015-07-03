<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjMerchantModel extends pjAppModel
{
	protected $primaryKey = 'merchant_id';
	
	protected $table = 'merchant';
	
	protected $schema = array(
		array('name' => 'merchant_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'merchant_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'merchant_address', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	protected $validate = array(
		'rules' => array(
			'merchant_id' => array(
				'pjActionNumeric' => true,
				'pjActionRequired' => true
			),
			'merchant_name' => array(
				'pjActionRequired' => true,
				'pjActionNotEmpty' => true
			)
		)
	);

	public static function factory($attr=array())
	{
		return new pjMerchantModel($attr);
	}
}
?>