<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminClients extends pjAdmin
{
	public function pjActionCheckEmail()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (!isset($_GET['c_email']) || empty($_GET['c_email']))
			{
				echo 'false';
				exit;
			}
			$pjClientModel = pjClientModel::factory()->where('t1.c_email', $_GET['c_email']);
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjClientModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjClientModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionDeleteClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjClientModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				$pjOrderModel = pjOrderModel::factory();
				$order_id_arr = $pjOrderModel->where('t1.client_id', $_GET['id'])->findAll()->getDataPair("id", "id");
				if(!empty($order_id_arr))
				{
					$pjOrderModel->reset()->whereIn('id', $order_id_arr)->eraseAll();
					pjOrderItemModel::factory()->whereIn('order_id', $order_id_arr)->eraseAll();
					pjOrderPaymentModel::factory()->whereIn('order_id', $order_id_arr)->eraseAll();
				}
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteClientBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjClientModel::factory()
					->whereIn('id', $_POST['record'])
					->eraseAll();
					
				$pjOrderModel = pjOrderModel::factory();
				$order_id_arr = $pjOrderModel->whereIn('t1.client_id', $_POST['record'])->findAll()->getDataPair("id", "id");
				if(!empty($order_id_arr))
				{
					$pjOrderModel->reset()->whereIn('id', $order_id_arr)->eraseAll();
					pjOrderItemModel::factory()->whereIn('order_id', $order_id_arr)->eraseAll();
					pjOrderPaymentModel::factory()->whereIn('order_id', $order_id_arr)->eraseAll();
				}
			}
		}
		exit;
	}
	
	public function pjActionExportClient()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjClientModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Clients-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionGetClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjClientModel = pjClientModel::factory()
				->where('user_id', $_SESSION['admin_user']['id']);
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjClientModel->where('t1.c_email LIKE', "%$q%");
				$pjClientModel->orWhere('t1.c_name LIKE', "%$q%");
			}

			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjClientModel->where('t1.status', $_GET['status']);
			}
				
			$column = 'c_name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjClientModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjClientModel
				->select("t1.id, t1.c_email, t1.c_name, t1.status, (SELECT COUNT(TO.client_id) FROM `".pjOrderModel::factory()->getTable()."` AS `TO` WHERE `TO`.client_id=t1.id) AS cnt_orders")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
				
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
			$this->appendJs('pjAdminClients.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjClientModel = pjClientModel::factory();
			
			$pjClientModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
		}
		exit;
	}
	
	public function pjActionStatusClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjClientModel::factory()->whereIn('id', $_POST['record'])->modifyAll(array(
					'status' => ":IF(`status`='F','T','F')"
				));
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['client_update']))
			{
				pjClientModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll($_POST);
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminClients&action=pjActionIndex&err=AC01");
				
			} else {
				$arr = pjClientModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminClients&action=pjActionIndex&err=AC08");
				}
				$this->set('arr', $arr);
				
				$this->set('role_arr', pjRoleModel::factory()->orderBy('t1.id ASC')->findAll()->getData());
				
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
						
				$this->set('country_arr', $country_arr);
				
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminClients.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>