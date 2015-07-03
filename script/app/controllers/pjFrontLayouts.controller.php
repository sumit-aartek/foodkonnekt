<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontLayouts extends pjAppController
{
	public function __construct()
	{
		$this->setLayout('pjFrontLayoutsIndex');
	}
	
	public function pjActionIndex()
	{	
		$user_id = base64_decode($_GET['restaurants']);
		$name = urldecode($_GET['name']);
		if(is_numeric($user_id) == TRUE)
		{
			//Get all location from location table.			
			$pjLocationModel = pjLocationModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'")
				->where('t1.user_id', $user_id)
				->select("t1.*, t2.content as name")
				->findAll()
				->getData();
			$this->set('location', $pjLocationModel);
			$this->set('user', $user_id);
			$this->set('name', $name);
			$this->appendJs('pjFrontIndex.js');
		} else {
			pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdmin&action=pjActionLogin");
		}
	}
	
	public function pjActionGetWTime()
	{		
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			
			if (isset($_POST['date']) && !empty($_POST['date']))
			{
				$date = pjUtil::formatDate($_POST['date'], $this->option_arr['o_date_format']);
				$wt_arr = pjAppController::getWorkingTime($date, $_POST['location_id'], $_POST['type']);
			} else {
				$date = '1981-02-01';
				$wt_arr = array('start_hour' => 0, 'end_hour' => 23);
				$this->_set('p_hour', 0);
				$this->_set('p_minute', 0);
				$this->_set('d_hour', 0);
				$this->_set('d_minute', 0);
			}			
			$this->tpl['date'] = $date;
			$this->tpl['wt_arr'] = $wt_arr;			
		}		
	}
	
	public function pjActionLoadCss()
	{
		$arr = array(
			array('file' => 'front_lib.css', 'path' => PJ_CSS_PATH),
			array('file' => 'calendar.css', 'path' => PJ_LIBS_PATH . 'calendarJS/themes/light/'),
			array('file' => 'FoodDelivery.css', 'path' => PJ_CSS_PATH)
		);
		header("Content-Type: text/css; charset=utf-8");
		foreach ($arr as $item)
		{
			ob_start();
			@readfile($item['path'] . $item['file']);
			$string = ob_get_contents();
			ob_end_clean();
			
			if ($string !== FALSE)
			{
				echo str_replace(
					array('../img/', '[URL]', 'images/'),
					array(
						PJ_INSTALL_URL . PJ_IMG_PATH,
						PJ_INSTALL_URL,
						PJ_INSTALL_URL . PJ_LIBS_PATH . 'pjQ/css/images/'),
					$string
				) . "\n";
			}
		}
		exit;
	}
}
?>