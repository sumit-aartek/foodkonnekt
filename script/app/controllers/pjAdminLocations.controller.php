<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminLocations extends pjAdmin
{
	public function pjActionCreate()
	{		
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['location_create']))
			{
				$data = array();
				if (empty($_POST['lat']) && empty($_POST['lng']))
				{
					$data = pjAppController::getCoords($_POST['i18n'][$this->getLocaleId()]['address']);
				}

				$id = pjLocationModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$pjWorkingTimeModel = pjWorkingTimeModel::factory();
					$pjWorkingTimeModel->init($id);
					
					if (isset($_POST['data']))
					{
						$pjLocationCoordModel = pjLocationCoordModel::factory();
						foreach ($_POST['data'] as $type => $coords)
						{
							foreach ($coords as $hash => $d)
							{
								$pjLocationCoordModel->reset()->setAttributes(array(
									'location_id' => $id,
									'type' => $type,
									'hash' => md5($hash),
									'data' => $d
								))->insert();
							}
						}
					}
					
					$err = 'AL03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjLocation', 'data');
					}
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminLocations&action=pjActionUpdate&id=$id&err=$err");
				} else {
					$err = 'AL04';
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminLocations&action=pjActionIndex&err=$err");
				}
			} else {
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
						
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file']; 
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
		
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('js?sensor=false&libraries=drawing', 'https://maps.googleapis.com/maps/api/', TRUE);
				$this->appendJs('pjAdminLocations.js');
			}
		} else {
			$this->set('status', 2);
		}
	}//End of pjActionCreate().
	
	public function getCloverAddress()
	{
		$this->setAjax(true);
		
		if($this->isXHR())
		{
			$merchant_id = $_POST['merchant_id'];
			$access_token = $_POST['merchant_access_token'];
			
			$response_name = file_get_contents("https://api.clover.com/v3/merchants/". $merchant_id ."?access_token=". $access_token);
			$response_name = json_decode($response_name);
			$location_name = $response_name->name;		
			
			$response_address = file_get_contents($response_name->address->href.'?access_token='. $access_token);
			$response_address = json_decode($response_address);
			$location_address = $response_address->address1.' '. $response_address->address2.' '. $response_address->city.' '. $response_address->country. ' '. $response_address->state. ' '. $response_address->zip;
			
			$arr = array(
				'name' => $location_name,
				'address' => $location_address
			);			
			pjAppController::jsonResponse($arr);
		}
		exit;
	}
		
	public function pjActionDeleteLocation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjLocationModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjLocation')->where('foreign_id', $_GET['id'])->eraseAll();
				pjLocationCoordModel::factory()->where('location_id', $_GET['id'])->eraseAll();
				pjWorkingTimeModel::factory()->where('location_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteLocationBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjLocationModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjLocation')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				pjLocationCoordModel::factory()->whereIn('location_id', $_POST['record'])->eraseAll();
				pjWorkingTimeModel::factory()->whereIn('location_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetLocation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{			
			$pjLocationModel = pjLocationModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjLocation' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'address'", 'left')
				->where('user_id', $_SESSION['admin_user']['id']);
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjLocationModel->where('t2.content LIKE', "%$q%");
				$pjLocationModel->orWhere('t3.content LIKE', "%$q%");
			}

			$column = 'name';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjLocationModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 20;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjLocationModel->select('t1.*, t2.content AS name, t3.content AS address')
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
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminLocations.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveLocation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjLocationModel = pjLocationModel::factory();
			if (!in_array($_POST['column'], $pjLocationModel->getI18n()))
			{
				$pjLocationModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjLocation', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['location_update']))
			{
				$data = array();
				if (empty($_POST['lat']) && empty($_POST['lng']))
				{
					$data = pjAppController::getCoords($_POST['i18n'][$this->getLocaleId()]['address']);
				}
				
				pjLocationModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjLocation', 'data');
				}
				$pjLocationCoordModel = pjLocationCoordModel::factory();
				$pjLocationCoordModel->where('location_id', $_POST['id'])->eraseAll();
				if (isset($_POST['data']))
				{
					foreach ($_POST['data'] as $type => $coords)
					{
						foreach ($coords as $hash => $d)
						{
							$pjLocationCoordModel->reset()->setAttributes(array(
								'location_id' => $_POST['id'],
								'type' => $type,
								'hash' => md5($hash),
								'data' => $d
							))->insert();
						}
					}
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminLocations&action=pjActionUpdate&id=".$_POST['id']."&err=AL01");
				
			} else {
				$arr = pjLocationModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminLocations&action=pjActionIndex&err=AL08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjLocation');
				$this->set('arr', $arr);
				$this->set('coord_arr', pjLocationCoordModel::factory()->where('location_id', $_GET['id'])->findAll()->getData());
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file']; 
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('js?sensor=false&libraries=drawing', 'https://maps.googleapis.com/maps/api/', TRUE);
				$this->appendJs('pjAdminLocations.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionPrice()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['price_update']))
			{
				$pjPriceModel = pjPriceModel::factory();
				$pjPriceModel->where('location_id', $_POST['location_id'])->eraseAll();

				if (isset($_POST['price']) && count($_POST['price']) > 0)
				{
					foreach ($_POST['price'] as $k => $price)
					{
						if ((float) $_POST['total_from'][$k] >= 0 && (float) $_POST['total_to'][$k] > 0 && (float) $_POST['total_from'][$k] <= (float) $_POST['total_to'][$k])
						{
							$pjPriceModel->reset()->setAttributes(array(
								'location_id' => $_POST['location_id'],
								'total_from' => $_POST['total_from'][$k],
								'total_to' => $_POST['total_to'][$k],
								'price' => $_POST['price'][$k]
							))->insert();
						}
					}
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminLocations&action=pjActionPrice&id=".$_POST['location_id']."&err=AL09");
				
			} else {
				$this->set('arr', pjPriceModel::factory()->where('location_id', $_GET['id'])->orderBy("t1.total_from ASC, t1.total_to ASC")->findAll()->getData());
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminLocations.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetCoords()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$data = pjAppController::getCoords($_POST['i18n'][$this->getLocaleId()]['address']);
			if (is_array($data['lat']) && $data['lat'][0] == 'NULL' && is_array($data['lng']) && $data['lng'][0] == 'NULL')
			{
				$data = array();
			}
			pjAppController::jsonResponse($data);
		}
		exit;
	}
	
	public function pjActionGetCloverData()
	{
		$this->setAjax();
		
		if($this->isXHR())
		{
			$user_id = $_SESSION['admin_user']['id'];
			$merchant_id = $_POST['merchant_id'];
			$access_token = $_POST['merchant_access_token'];
			include PJ_VIEWS_PATH.'pjAdminLocations/elements/cloverApi.php';			
		}
		exit;
	}
	
	public function pjActionCloverUpdate()
	{
		$this->setAjax();
		
		if($this->isXHR())
		{
			$location_id = $_GET['id'];
			$location_arr = pjLocationMapModel::factory()
				->where('PHPJABBER_LOCATION_ID', $location_id)
				->findAll()->getData();
			$user_id = $_SESSION['admin_user']['id'];
			$merchant_id = $location_arr[0]['CLOVER_MID'];
			$access_token = $location_arr[0]['CLOVER_ACCESS_TOKEN_ID'];
			include PJ_VIEWS_PATH.'pjAdminLocations/elements/cloverApi.php';
		}
		exit;
	}
}
?>