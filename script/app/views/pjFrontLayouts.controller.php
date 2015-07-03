<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontLayouts extends pjAppController
{
	public function pjActionIndex()
	{
		$this->setLayout('pjFrontLayoutsIndex');
		
		$user_id = base64_decode($_GET['restaurants']);
		//$name = urldecode($_GET['name']);
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
			//$this->set('name', $name);
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
}
?>