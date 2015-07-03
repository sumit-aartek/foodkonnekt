<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminTime extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['working_time']))
			{
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				
				$arr = $pjWorkingTimeModel->find($_POST['location_id'])->getData();
				
				$data = array();
				$data['location_id'] = $_POST['location_id'];
				
				$types = array('p_' => 'pickup', 'd_' => 'delivery');
				$weekDays = pjUtil::getWeekdays();
				foreach ($types as $prefix => $type)
				{
					foreach ($weekDays as $day)
					{
						if (!isset($_POST[$prefix . $day . '_dayoff']))
						{
							$data[$prefix . $day . '_from'] = $_POST[$prefix . $day . '_hour_from'] . ":" . $_POST[$prefix . $day . '_minute_from'];
							$data[$prefix . $day . '_to'] = $_POST[$prefix . $day . '_hour_to'] . ":" . $_POST[$prefix . $day . '_minute_to'];
						}
					}
				}
				if (count($arr) > 0)
				{
					$pjWorkingTimeModel->reset()->setAttributes(array('location_id' => $_POST['location_id']))->erase();
				}
				$pjWorkingTimeModel->reset()->setAttributes($data)->insert();
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTime&action=pjActionIndex&id=".$_POST['location_id']."&err=AT01");
			}
			
			if (isset($_POST['custom_time']))
			{
				$date = pjUtil::formatDate($_POST['date'], $this->option_arr['o_date_format']);
				
				$pjDateModel = pjDateModel::factory();
				$pjDateModel->where('type', $_POST['type'])->where('date', $date)->eraseAll();
				
				$data = array();
				$data['location_id'] = $_POST['location_id'];
				$data['start_time'] = join(":", array($_POST['start_hour'], $_POST['start_minute']));
				$data['end_time'] = join(":", array($_POST['end_hour'], $_POST['end_minute']));
				$data['date'] = $date;
				$data['type'] = $_POST['type'];
				$data['is_dayoff'] = $_POST['is_dayoff'];
				
				$pjDateModel->reset()->setAttributes($data)->insert();
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTime&action=pjActionIndex&id=".$_POST['location_id']."&err=AT02&tab_id=tabs-2");
			}
			
			$arr = pjLocationModel::factory()->find($_GET['id'])->getData();
			if (count($arr) === 0)
			{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminLocations&action=pjActionIndex&err=AL08");
			}
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjLocation');
			$this->set('arr', $arr);
			$this->set('wt_arr', pjWorkingTimeModel::factory()->find($_GET['id'])->getData());
			
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminTime.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetDate()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjDateModel = pjDateModel::factory();
			
			$column = 'date';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			if (isset($_GET['location_id']) && (int) $_GET['location_id'] > 0)
			{
				$pjDateModel->where('location_id', $_GET['location_id']);
			}

			$total = $pjDateModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjDateModel->select('t1.*')
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
				
			$yesno = __('_yesno', true, false);
			$types = __('types', true, false);
			foreach($data as $k => $v){
				$v['start_time'] = date($this->option_arr['o_time_format'], strtotime($v['start_time']));
				$v['end_time'] = date($this->option_arr['o_time_format'], strtotime($v['end_time']));
				$v['is_dayoff'] = $yesno[$v['is_dayoff']];
				$v['type'] = $types[$v['type']];
				$data[$k] = $v;
			}
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionDeleteDate()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjDateModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteDateBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjDateModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$arr = pjDateModel::factory()->find($_GET['id'])->getData();
			if (count($arr) === 0)
			{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminTime&action=pjActionIndex&err=AT09&tab_id=tabs-2");
			}
			
			$this->set('arr', $arr);
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminTime.js');
			
		} else {
			$this->set('status', 2);
		}
	}
}
?>