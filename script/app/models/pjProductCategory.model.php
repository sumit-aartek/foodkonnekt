<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjProductCategoryModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'products_categories';
	
	protected $schema = array(
		array('name' => 'product_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'category_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjProductCategoryModel($attr);
	}
}
?>