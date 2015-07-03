<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminVouchers extends pjAdmin
{
	public function pjActionCheckCode()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (!isset($_GET['code']) || empty($_GET['code']))
			{
				echo 'false';
				exit;
			}
			$pjVoucherModel = pjVoucherModel::factory()->where('t1.code', $_GET['code']);
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjVoucherModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjVoucherModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCheckDate()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$dt_from = sprintf("%s %s:%s:00", pjUtil::formatDate($_POST['p_date_from'], $this->option_arr['o_date_format']), $_POST['p_hour_from'], $_POST['p_minute_from']);
			$dt_to = sprintf("%s %s:%s:00", pjUtil::formatDate($_POST['p_date_to'], $this->option_arr['o_date_format']), $_POST['p_hour_to'], $_POST['p_minute_to']);
			$dt_from = strtotime($dt_from);
			$dt_to = strtotime($dt_to);
			echo $dt_to > $dt_from ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['voucher_create']))
			{
				$data = array();
			
				$data['code'] = $_POST['code'];
				$data['discount'] = $_POST['discount'];
				$data['type'] = $_POST['type'];
				$data['valid'] = $_POST['valid'];
				switch ($_POST['valid'])
				{
					case 'fixed':
						$data['date_from'] = pjUtil::formatDate($_POST['f_date'], $this->option_arr['o_date_format']);
						$data['date_to'] = $data['date_from'];
						$data['time_from'] = $_POST['f_hour_from'] . ":" . $_POST['f_minute_from'] . ":00";
						$data['time_to'] = $_POST['f_hour_to'] . ":" . $_POST['f_minute_to'] . ":00";
						break;
					case 'period':
						$data['date_from'] = pjUtil::formatDate($_POST['p_date_from'], $this->option_arr['o_date_format']);
						$data['date_to'] = pjUtil::formatDate($_POST['p_date_to'], $this->option_arr['o_date_format']);
						$data['time_from'] = $_POST['p_hour_from'] . ":" . $_POST['p_minute_from'] . ":00";
						$data['time_to'] = $_POST['p_hour_to'] . ":" . $_POST['p_minute_to'] . ":00";
						break;
					case 'recurring':
						$data['every'] = $_POST['r_every'];
						$data['time_from'] = $_POST['r_hour_from'] . ":" . $_POST['r_minute_from'] . ":00";
						$data['time_to'] = $_POST['r_hour_to'] . ":" . $_POST['r_minute_to'] . ":00";
						break;
				}
				$id = pjVoucherModel::factory($data)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AV03';
				} else {
					$err = 'AV04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminVouchers&action=pjActionIndex&err=$err");
			} else {
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminVouchers.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteVoucher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			
			if (pjVoucherModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteVoucherBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjVoucherModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionExportVoucher()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjVoucherModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Vouchers-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetVoucher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjVoucherModel = pjVoucherModel::factory();
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjVoucherModel->where('t1.code LIKE', "%$q%");
			}
	
			$column = 'code';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjVoucherModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjVoucherModel->select('t1.*')
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
			foreach($data as $k => $v)
			{
				if($v['type'] == 'percent')
				{
					$v['discount'] = $v['discount'] . '%';
				}else{
					$v['discount'] = pjUtil::formatCurrencySign($v['discount'], $this->option_arr['o_currency']);
				}
				$v['datetime_valid'] = '';
				switch ($v['valid'])
				{
					case 'fixed':
						$v['datetime_valid'] = date($this->option_arr['o_date_format'], strtotime($v['date_from'])) . ' ' . __('lblFrom', true) . ' ' . date($this->option_arr['o_time_format'], strtotime($v['time_from'])) . ' ' . __('lblTo', true) . ' ' . date($this->option_arr['o_time_format'], strtotime($v['time_to']));
						break;
					case 'period':
						$v['datetime_valid'] = __('lblFrom', true) . ' ' . date($this->option_arr['o_date_format'], strtotime($v['date_from'])) . ' ' . __('lblTo', true) . ' ' . date($this->option_arr['o_date_format'], strtotime($v['date_to']));
						break;
					case 'recurring':
						$days = __('voucher_days', true, false);
						$v['datetime_valid'] = __('lblEvery', true) . ' ' . $days[$v['every']] . ' ' . __('lblFrom', true) . ' ' . date($this->option_arr['o_time_format'], strtotime($v['time_from'])) . ' ' . __('lblTo', true) . ' ' . date($this->option_arr['o_time_format'], strtotime($v['time_to']));
						break;
				}
				$data[$k] = $v;
			}	
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminVouchers.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveVoucher()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjVoucherModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['voucher_update']))
			{
				$data = array();
				$data['code'] = $_POST['code'];
				$data['discount'] = $_POST['discount'];
				$data['type'] = $_POST['type'];
				$data['valid'] = $_POST['valid'];
				switch ($_POST['valid'])
				{
					case 'fixed':
						$data['date_from'] = pjUtil::formatDate($_POST['f_date'], $this->option_arr['o_date_format']);
						$data['date_to'] = $data['date_from'];
						$data['time_from'] = $_POST['f_hour_from'] . ":" . $_POST['f_minute_from'] . ":00";
						$data['time_to'] = $_POST['f_hour_to'] . ":" . $_POST['f_minute_to'] . ":00";
						$data['every'] = array('NULL');
						break;
					case 'period':
						$data['date_from'] = pjUtil::formatDate($_POST['p_date_from'], $this->option_arr['o_date_format']);
						$data['date_to'] = pjUtil::formatDate($_POST['p_date_to'], $this->option_arr['o_date_format']);
						$data['time_from'] = $_POST['p_hour_from'] . ":" . $_POST['p_minute_from'] . ":00";
						$data['time_to'] = $_POST['p_hour_to'] . ":" . $_POST['p_minute_to'] . ":00";
						$data['every'] = array('NULL');
						break;
					case 'recurring':
						$data['date_from'] = array('NULL');
						$data['date_to'] = array('NULL');
						$data['every'] = $_POST['r_every'];
						$data['time_from'] = $_POST['r_hour_from'] . ":" . $_POST['r_minute_from'] . ":00";
						$data['time_to'] = $_POST['r_hour_to'] . ":" . $_POST['r_minute_to'] . ":00";
						break;
				}
				
				pjVoucherModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll($data);
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminVouchers&action=pjActionIndex&err=AV01");
				
			} else {
				$arr = pjVoucherModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminVouchers&action=pjActionIndex&err=AV08");
				}
				$this->set('arr', $arr);
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminVouchers.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>