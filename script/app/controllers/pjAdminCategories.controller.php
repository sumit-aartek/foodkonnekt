<?php

if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminCategories extends pjAdmin
{
	
	public function pjActionCheckCategory()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() && isset($_POST['locale']))
		{
			$locale = $_POST['locale'];
			
			$value = $_POST['i18n'][$locale]['name'];
			
			$pjCategoryModel = pjCategoryModel::factory();
			
			if (isset($_POST['id']) && (int) $_POST['id'] > 0)
			{
				$pjCategoryModel->where('t1.id !=', $_POST['id']);
			}
			$pjCategoryModel->where("t1.id IN(SELECT TL.foreign_id FROM `".pjMultiLangModel::factory()->getTable()."` AS TL WHERE TL.model='pjCategory' AND TL.field='name' AND TL.content = '".$value."' AND TL.locale='$locale')");
			echo $pjCategoryModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
	
		if ($this->isAdmin() || $this->isEditor())
		{
			
			if (isset($_POST['category_create']))
			{
				$pjCategoryModel = pjCategoryModel::factory();
				$data = array();
				$data['order'] = $pjCategoryModel->getLastOrder();
				$data['location_id'] = $_POST['mlocaiton_id'];
				$id = $pjCategoryModel->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'ACT03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjCategory', 'data');
					}
				} else {
					$err = 'ACT04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCategories&action=pjActionIndex&err=$err");
			} else {
				$lp_arr = array();
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();						
				$locationModel = pjLocationModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.field = 'name'")
				->select("t1.*, t2.content as name")
				->where('t1.user_id', $_SESSION['admin_user']['id'])
				->where('t2.locale', $this->getLocaleId())
				->findAll()
				->getData();
				
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				$this->set('location_arr', $locationModel);
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
		
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminCategories.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
		
	public function pjActionDeleteCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjCategoryModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjCategory')->where('foreign_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteCategoryBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjMultiLangModel::factory()->where('model', 'pjCategory')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				pjCategoryModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCategoryModel = pjCategoryModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->where('user_id', $_SESSION['admin_user']['id']);
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjCategoryModel->where('t2.content LIKE', "%$q%");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjCategoryModel->where('t1.status', $_GET['status']);
			}

			$column = 'order';
			$direction = 'ASC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjCategoryModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 20;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			
			$data = $pjCategoryModel
				->select('t1.*, t2.content AS name, 
						  (SELECT COUNT(TPC.product_id) FROM `'.pjProductCategoryModel::factory()->getTable().'` AS TPC WHERE TPC.category_id=t1.id) AS cnt_products')
				->orderBy("`$column` $direction")
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
			$this->appendJs('pjAdminCategories.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSortCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{			
			$pjCategoryModel = pjCategoryModel::factory();
			
			foreach($_POST['sort'] as $k => $v)
			{
				$pjCategoryModel->reset()->set('id', $v)->modify(array('order' => $k+1));
			}
		}
		exit;
	}
	
	public function pjActionSaveCategory()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCategoryModel = pjCategoryModel::factory();
			if (!in_array($_POST['column'], $pjCategoryModel->getI18n()))
			{
				$pjCategoryModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjCategory', 'data');
			}
		}
		exit;
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['category_update']))
			{
				
				pjCategoryModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll($_POST);
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjCategory', 'data');
				}
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCategories&action=pjActionIndex&err=ACT01");
				
			} else {
				$arr = pjCategoryModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCategories&action=pjActionIndex&err=ACT08");
				}
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjCategory');
				$this->set('arr', $arr);
				
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
				$this->appendJs('pjAdminCategories.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>