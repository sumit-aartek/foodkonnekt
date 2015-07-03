<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminMerchant extends pjAdmin
{
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			    $merchantId=$_POST['merchant_id'];
				$accessTokenId=$_POST['merchant_access_token_id'];
				$response1 = file_get_contents("https://api.clover.com/v3/merchants/".$merchantId."?access_token=".$accessTokenId);
				$responseAddress = file_get_contents("https://www.clover.com/v3/merchants/".$merchantId."/address?access_token=".$accessTokenId);
                $_SESSION["response1"]=$response1; 
			    $_SESSION["merchantId"]=$merchantId; 
				$_SESSION["responseAddress"]=$responseAddress; 
			if (isset($_POST['merchant_create']))
			{
				$data = array();				
				$id = pjMerchantModel::factory($_POST)->insert()->getInsertId();
				
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AU03';
				} else {
					$err = 'AU04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminMerchant&action=pjActionIndex&err=$err");
			} else {
				
				$this->set('role_arr', pjRoleModel::factory()->orderBy('t1.id ASC')->findAll()->getData());
		
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminMerchant.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteMerchant()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if ($_GET['id'] != $this->getUserId() && $_GET['id'] != 1)
			{
				if (pjMerchantModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
				{
					$response['code'] = 200;
				} else {
					$response['code'] = 100;
				}
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionGetMerchant()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{			
			$pjMerchantModel = pjMerchantModel::factory()
				->where('user_id', $_SESSION['admin_user']['id']);
			
			$column = 'merchant_name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjMerchantModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjMerchantModel->select('t1.merchant_id, t1.merchant_name, t1.merchant_address')
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();

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
			$this->appendJs('pjAdminMerchant.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSetActive()
	{
		$this->setAjax(true);

		if ($this->isXHR())
		{
			$pjMerchantModel = pjMerchantModel::factory();
			
			$arr = $pjMerchantModel->find($_POST['id'])->getData();
			
			if (count($arr) > 0)
			{
				switch ($arr['is_active'])
				{
					case 'T':
						$sql_status = 'F';
						break;
					case 'F':
						$sql_status = 'T';
						break;
					default:
						return;
				}
				$pjMerchantModel->reset()->setAttributes(array('id' => $_POST['id']))->modify(array('is_active' => $sql_status));
			}
		}
		exit;
	}
	
	public function pjActionSaveMerchant()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjMerchantModel = pjMerchantModel::factory();
			
			$pass = true;
			if ((int) $_GET['id'] === 1)
			{
				if(in_array($_POST['column'], array('role_id', 'status', 'is_active')))
				{
					$pass = false;
				}else if(in_array($_POST['column'], array('name', 'email')) && $_POST['value'] == ''){
					$pass = false;
				}else if($_POST['column'] == 'email' && $_POST['value'] != '' && !filter_var($_POST['value'], FILTER_VALIDATE_EMAIL)){
					$pass = false;
				}
			}
			if ($pass)
			{
				$pjMerchantModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			}
		}
		exit;
	}
	
	public function pjActionStatusMerchant()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjMerchantModel::factory()->whereIn('id', $_POST['record'])->where('id <>', 1)->modifyAll(array(
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
				
			if (isset($_POST['merchant_update']))
			{
				pjMerchantModel::factory()->where('merchant_id', $_POST['id'])->limit(1)->modifyAll($_POST);
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminMerchant&action=pjActionIndex&err=AU01");
				
			} else {
				$arr = pjMerchantModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminMerchant&action=pjActionIndex&err=AU08");
				}
				$this->set('arr', $arr);
				
				$this->set('role_arr', pjRoleModel::factory()->orderBy('t1.id ASC')->findAll()->getData());
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminMerchant.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>