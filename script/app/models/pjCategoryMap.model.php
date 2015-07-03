<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCategoryMapModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'categories_map';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'clover_category_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'category_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'clover_mid', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjCategoryMapModel($attr);
	}
	
	public function getLastRecord()
	{
		$order = 1;
		$arr = $this
			->reset()
			->orderBy("`id` DESC")
			->limit(1)
			->findAll()
			->getData();
		if(!empty($arr))
		{
			$order = $arr[0]['id'] + 1;
		}
		return $order;
	}
}
?>