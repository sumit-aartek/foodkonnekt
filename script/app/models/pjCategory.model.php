<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCategoryModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'categories';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'order', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'location_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
		return new pjCategoryModel($attr);
	}
	
	public function getLastOrder()
	{
		$order = 1;
		$arr = $this
			->reset()
			->orderBy("`order` DESC")
			->limit(1)
			->findAll()
			->getData();
		if(!empty($arr))
		{
			$order = $arr[0]['order'] + 1;
		}
		return $order;
	}
}
?>