<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{	
	public $defaultCaptcha = 'pjFoodDelivery_Captcha';
	
	public $defaultLocale = 'pjFoodDelivery_LocaleId';
	
	public $defaultClient = 'pjFoodDelivery_Client';
	
	public $defaultLangMenu = 'pjFoodDelivery_LangMenu';
	
	public $defaultStore = 'pjFoodDelivery_Store';
	
	public $defaultForm = 'pjFoodDelivery_Form';
	
	public function __construct()
	{
		$this->setLayout('pjActionFront');
	}
	
	public function _get($key)
	{
		if ($this->_is($key))
		{
			return $_SESSION[$this->defaultStore][$key];
		}
		return false;
	}
	
	public function _is($key)
	{
		return isset($_SESSION[$this->defaultStore]) && isset($_SESSION[$this->defaultStore][$key]);
	}
	
	public function _set($key, $value)
	{
		$_SESSION[$this->defaultStore][$key] = $value;
		return $this;
	}
	
	public function _unset($key)
	{
		if ($this->_is($key))
		{
			unset($_SESSION[$this->defaultStore][$key]);
		}
	}

	public function isFrontLogged()
    {
        if (isset($_SESSION[$this->defaultClient]) && count($_SESSION[$this->defaultClient]) > 0)
        {
            return true;
	    }
	    return false;
    }
    
    public function getClientId()
    {
    	return isset($_SESSION[$this->defaultClient]) && array_key_exists('id', $_SESSION[$this->defaultClient]) ? $_SESSION[$this->defaultClient]['id'] : FALSE;
    }
   


	
	public function afterFilter()
	{		
		if (!isset($_GET['hide']) || (isset($_GET['hide']) && (int) $_GET['hide'] !== 1) &&
			in_array($_GET['action'], array('pjActionMain', 'pjActionTypes', 'pjActionLogin', 'pjActionCheckout', 'pjActionPreview')))
		{
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			
			$this->set('locale_arr', $locale_arr);
		}
	}
	
	public function beforeFilter()
	{
		$OptionModel = pjOptionModel::factory();
		$this->option_arr = $OptionModel->getPairs($this->getForeignId());
		$this->set('option_arr', $this->option_arr);
		$this->setTime();

		if (!isset($_SESSION[$this->defaultLocale]))
		{
			$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
			if (count($locale_arr) === 1)
			{
				$this->setLocaleId($locale_arr[0]['id']);
			}
		}
		if (!in_array($_GET['action'], array('pjActionLoadCss')))
		{
			$this->loadSetFields();
		}
	}
	
	public function beforeRender()
	{
		if (isset($_GET['iframe']))
		{
			$this->setLayout('pjActionIframe');
		}
	}
	
	public function pjActionLocale()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['locale_id']))
			{
				$this->pjActionSetLocale($_GET['locale_id']);
				
				$this->loadSetFields(true);
				
				$day_names = __('day_names', true);
				ksort($day_names, SORT_NUMERIC);
				
				$months = __('months', true);
				ksort($months, SORT_NUMERIC);
				
				pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Locale have been changed.', 'opts' => array(
					'day_names' => array_values($day_names),
					'month_names' => array_values($months)
				)));
			}
		}
		exit;
	}
	private function pjActionSetLocale($locale)
	{
		if ((int) $locale > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $locale;
		}
		return $this;
	}
	
	public function pjActionGetLocale()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}
	
	public function pjActionCaptcha()
	{
		$this->setAjax(true);
		$Captcha = new pjCaptcha('app/web/obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$Captcha->setImage('app/web/img/button.png')->init(isset($_GET['rand']) ? $_GET['rand'] : null);
	}

	public function pjActionCheckCaptcha()
	{
		$this->setAjax(true);
		if (!isset($_GET['captcha']) || empty($_GET['captcha']) || strtoupper($_GET['captcha']) != $_SESSION[$this->defaultCaptcha]){
			echo 'false';
		}else{
			echo 'true';
		}
	}
	
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
			$pjClientModel = pjClientModel::factory()
				->where('t1.c_email', $_GET['c_email'])
				->where('t1.user_id', $_SESSION['order_data']['o_user_id']);
			if ($this->isFrontLogged())
			{
				$pjClientModel->where('t1.id !=', $this->getClientId());
			}
			echo $pjClientModel->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionAddProduct()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if (isset($_POST['product_id']))
			{
				$extras = array();
				if(isset($_POST['extra_id']))
				{
					foreach ($_POST['extra_id'] as $extra_id => $qty)
					{
						if(intval($qty) > 0)
						{
							$extras[$extra_id] = $qty;
						}
					}
				}
				ksort($extras);
				if(isset($_POST['price_id']))
				{
					$hash = md5($_POST['product_id'] . $_POST['price_id'] . serialize($extras));
				}else{
					$hash = md5($_POST['product_id'] . serialize($extras));
				}
				
				$cart = $this->_get('cart');
				if ($cart === false)
				{
					$cart = array();
				}
				
				if (!array_key_exists($hash, $cart))
				{
					$cart[$hash] = array(
						'product_id' => $_POST['product_id'],
						'price_id' => isset($_POST['price_id']) ? $_POST['price_id'] : null,
						'cnt' => 0,
						'extras' => $extras
					);
				}
				
				$cart[$hash]['cnt'] += 1;
				$this->_set('cart', $cart);
			}
		}
	}
	
	function pjActionAddPromo()
	{
		$this->setAjax(true);
			
		if ($this->isXHR())
		{
			$pre = array(
				'type' => $this->_get('type'),
				'd_date' => $this->_get('d_date'),
				'd_hour' => $this->_get('d_hour'),
				'd_minute' => $this->_get('d_minute'),
				'p_date' => $this->_get('p_date'),				
				'p_hour' => $this->_get('p_hour'),
				'p_minute' => $this->_get('p_minute')
			);			
			$resp = pjAppController::getDiscount(array_merge($_POST, $pre), $this->option_arr);
			$promo_statuses = __('promo_statuses', true, false);
			$resp['text'] = $promo_statuses[$resp['code']];
			if($resp['code'] == 200)
			{
				$this->_set('voucher_code', $resp['voucher_code']);
				
				/* Manmohan code here */
				$this->_set('voucher_code_for', $resp['voucher_code_for']);
				$this->_set('voucher_product_id', $resp['voucher_product_id']);
				$this->_set('voucher_category_id', $resp['voucher_category_id']);
				/* Manmohan end of code here */
				
				
				$this->_set('voucher_type', $resp['voucher_type']);
				$this->_set('voucher_discount', $resp['voucher_discount']);
				
				$voucher = array(
					'voucher_code' => $resp['voucher_code'],
					'voucher_code_for' => $resp['voucher_code_for'],
					'voucher_product_id' => $resp['voucher_product_id'],
					'voucher_category_id' => $resp['voucher_category_id'],
					'voucher_type' => $resp['voucher_type'],
					'voucher_discount' => $resp['voucher_discount']
				);
				$_SESSION['voucher'] = $voucher;
			}
			pjAppController::jsonResponse($resp);
		}
	}
	
	public function pjActionRemove()
	{
		$this->isAjax = true;
	
		if ($this->isXHR())
		{
			if (isset($_POST['hash']) && isset($_POST['extra_id']))
			{
				$cart = $this->_get('cart');
				if ($cart !== false)
				{
					if (array_key_exists($_POST['hash'], $cart))
					{
						if((int) $_POST['extra_id'] > 0)
						{
							unset($cart[$_POST['hash']]['extras'][$_POST['extra_id']]);
						}else{
							unset($cart[$_POST['hash']]);
						}
						$this->_set('cart', $cart);
					}
				}
			}
		}
	}
	
	private function getCartInfo()
	{
		$product_arr = array();
		$items_in_cart = 0;
		$cart = $this->_get('cart');
		if ($cart !== false)
		{
			$ids = array();
			foreach ($cart as $item)
			{
				$ids[] = $item['product_id'];
				$items_in_cart += $item['cnt'];
			}
			if (count($ids) > 0)
			{
				$pjProductModel = pjProductModel::factory();
				$pjProductPriceModel = pjProductPriceModel::factory();
				$pjExtraModel = pjExtraModel::factory();	
				
				$product_arr = $pjProductModel
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProduct' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->select('t1.*, t2.content as name')
					->whereIn('t1.id', $ids)
					->findAll()
					->getData();
					
				foreach ($cart as $k => $item)
				{
					$price_arr = array();
					if(!empty($item['price_id']))
					{
						$price_arr = $pjProductPriceModel
							->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProductPrice' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'price_name'", 'left')
							->select('t1.*, t2.content as size')
							->find($item['price_id'])
							->getData();
						if($price_arr)	
						{		
							$cart[$k]['price'] = $price_arr['price'];
							$cart[$k]['size'] = $price_arr['size'];
						}else{
							$cart[$k]['price'] = 0;
							$cart[$k]['size'] = '';
						}
					}else{
						$price_arr = $pjProductModel->reset()->find($item['product_id'])->getData();
						if(!empty($price_arr))	
						{
							$cart[$k]['price'] = $price_arr['price'];
							$cart[$k]['size'] = '';
						}else{
							$cart[$k]['price'] = 0;
							$cart[$k]['size'] = '';
						}
					}
					
					$extra_arr = array();
					if(isset($item['extras']) && $item['extras'])
					{
						foreach ($item['extras'] as $extra_id => $qty)
						{
							$_arr = $pjExtraModel
								->reset()
								->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
								->select('t1.*, t2.content as name')
								->whereIn('t1.id', $ids)
								->find($extra_id)
								->getData();
							$_arr['qty'] = $qty;
							$extra_arr[$extra_id] = $_arr;
						}
					}
					$cart[$k]['extra_arr'] = $extra_arr;
				}
			}
		}
		
		return compact('cart', 'items_in_cart', 'product_arr');
	}
	
	private function getCategories($get)
	{
		$user_id = '';
		//check front layout order data set or not.		
		if(isset($_SESSION['order_data']))
		{
			$user_id = $_SESSION['order_data']['o_user_id'];
		} else {
			$user_id = $_SESSION['admin_user']['id'];
		}		
		$category_arr = '';
		
		//Get categories from DB.
		$category_arr = pjCategoryModel::factory()
			->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCategory' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
			->select("t1.*, t2.content as name")				
			->where('t1.status', 'T')
			->where('t1.location_id', $_SESSION['order_data']['o_location_id'])
			->where('t1.user_id', $user_id)
			->orderBy("`order` ASC")
			->findAll()
			->getData();
			
		$open_id = null;
		$product_arr = array();
		if(isset($get['category_id']))
		{
			$open_id = $get['category_id'];
			$this->_set('open_id', $open_id);
		} elseif(!empty($lc))
		{
			$open_id = $category_arr[0]['id'];
			$this->_set('open_id', $open_id);
		}else{
			if(!$this->_is('open_id'))
			{
				foreach($category_arr as $k => $v)
				{
					if($k == 0)
					{
						$open_id = $v['id'];
					}
				}
			}else{
				$open_id = $this->_get('open_id');
			}
		}
		if($open_id != null)
		{
			$pjExtraModel = pjExtraModel::factory();
			$pjProductExtraTable = pjProductExtraModel::factory()->getTable();
			$pjProductPriceModel = pjProductPriceModel::factory();
			
			$product_arr = pjProductModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProduct' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjProduct' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'description'", 'left')
				->select("t1.*, t2.content AS name, t3.content AS description")
				->where("(t1.id IN (SELECT TPC.product_id FROM `".pjProductCategoryModel::factory()->getTable()."` AS TPC WHERE TPC.category_id='$open_id'))")
				->orderBy("t1.is_featured DESC, t2.content ASC")
				->findAll()
				->getData();
			foreach($product_arr as $k => $product)
			{
				$product['price_arr'] = array();
				$product['extra_arr'] = array();
				$product['extra_arr'] = $pjExtraModel
					->reset()
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->where("(t1.id IN (SELECT TPE.extra_id FROM `".$pjProductExtraTable."` AS TPE WHERE TPE.product_id='".$product['id']."'))")
					->select("t1.*, t2.content AS name")
					->orderBy("name ASC")
					->findAll()
					->getData();
				if($product['set_different_sizes'] == 'T')
				{
					$product['price_arr'] = $pjProductPriceModel
						->reset()
						->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProductPrice' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'price_name'", 'left')
						->where('t1.product_id', $product['id'])
						->select("t1.*, t2.content AS price_name")
						->findAll()
						->getData();
				}
				$product_arr[$k] = $product;
			}
		}		
		return compact('category_arr', 'product_arr', 'open_id', 'mlocation_arr', 'lc');
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
	
	public function pjActionLoad()
	{
		ob_start();
		header("Content-Type: text/javascript; charset=utf-8");
		
		if(isset($_GET['locale']) && $_GET['locale'] > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $_GET['locale'];
			$_SESSION[$this->defaultLangMenu] = 'hide';
		}else{
			$_SESSION[$this->defaultLangMenu] = 'show';
		}
		
		$days_off = array();
		
		$w_arr = pjWorkingTimeModel::factory()
			->orderBy("t1.location_id ASC")
			->findAll()
			->getData();
		foreach ($w_arr as $w)
		{
			if ($w['p_monday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['pickup'][] = 1;
			}
			if ($w['p_tuesday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['pickup'][] = 2;
			}
			if ($w['p_wednesday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['pickup'][] = 3;
			}
			if ($w['p_thursday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['pickup'][] = 4;
			}
			if ($w['p_friday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['pickup'][] = 5;
			}
			if ($w['p_saturday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['pickup'][] = 6;
			}
			if ($w['p_sunday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['pickup'][] = 0;
			}
		
			if ($w['d_monday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['delivery'][] = 1;
			}
			if ($w['d_tuesday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['delivery'][] = 2;
			}
			if ($w['d_wednesday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['delivery'][] = 3;
			}
			if ($w['d_thursday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['delivery'][] = 4;
			}
			if ($w['d_friday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['delivery'][] = 5;
			}
			if ($w['d_saturday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['delivery'][] = 6;
			}
			if ($w['d_sunday_dayoff'] == 'T')
			{
				$days_off[$w['location_id']]['delivery'][] = 0;
			}
		}
		$this->set('days_off', $days_off);
		
		$dates_off = $dates_on = array();
		$d_arr = pjDateModel::factory()
			->where("t1.date >= CURDATE()")
			->findAll()
			->getData();
			
		foreach ($d_arr as $date)
		{
			if ($date['is_dayoff'] == 'T')
			{
				$dates_off[$date['location_id']][$date['type']][] = $date['date'];
			} else {
				$dates_on[$date['location_id']][$date['type']][] = $date['date'];
			}
		}
		$this->set('dates_on', $dates_on);
		$this->set('dates_off', $dates_off);
	}
	
	public function pjActionMain()
	{
		$this->setAjax(true);		
		
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			$this->set('main', $this->getCategories($_GET));
			$this->set('cart_box', $this->getCartInfo());
		}
	}
	
	public function pjActionCategories()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			$this->set('main', $this->getCategories($_GET));
		}
	}
	
	public function pjActionCart()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			$this->set('cart_box', $this->getCartInfo());
		}
	}
	
	public function pjActionTypes()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_SESSION[$this->defaultStore]) && count($_SESSION[$this->defaultStore]) > 0)
			{
				$location_arr = pjLocationModel::factory()
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->select('t1.*, t2.content AS name')
					->where('user_id', $_SESSION['order_data']['o_user_id'])
					->orderBy("name ASC")
					->findAll()
					->getData();
				$type = $this->_get('type');
				if ($type !== false)
				{
					switch ($type)
					{
						case 'pickup':
							$p_location_id = $this->_get('p_location_id');
							$p_date = $this->_get('p_date');
							if ($p_location_id !== false && $p_date !== false)
							{
								$wt_arr = pjAppController::getWorkingTime(pjUtil::formatDate($p_date, $this->option_arr['o_date_format']), $p_location_id, 'pickup');
								$this->set('wt_arr', $wt_arr);	
							}
							break;
						case 'delivery':
							
							break;
					}
				}
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();

				if($this->isFrontLogged())
				{
					$order_arr = pjOrderModel::factory()
						->select("t1.*")
						->where('client_id', $this->getClientId())
						->where('type', 'delivery')
						->orderBy("t1.created DESC")
						->findAll()
						->getData();
					
					$this->set('order_arr', $order_arr);
				}	
				
				$this->set('country_arr', $country_arr);
				$this->set('location_arr', $location_arr);
				$this->set('status', 'OK');
			}else{
				$this->set('status', 'ERR');
			}	
			$this->set('cart_box', $this->getCartInfo());
		}
	}
	
	public function pjActionLogin()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			$this->set('cart_box', $this->getCartInfo());	
		}
	}
	
	public function pjActionForgot()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			$this->set('cart_box', $this->getCartInfo());	
		}
	}
	
	public function pjActionCheckout()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{
			if (isset($_SESSION[$this->defaultStore]) && count($_SESSION[$this->defaultStore]) > 0)
			{
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
				$this->set('country_arr', $country_arr);
				
				$terms_conditions = pjMultiLangModel::factory()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_terms')
					->limit(0, 1)
					->findAll()
					->getData();
				
				$this->set('terms_conditions', $terms_conditions[0]['content']);
				$this->set('status', 'OK');
			}else{
				$this->set('status', 'ERR');
			}	
			$this->set('cart_box', $this->getCartInfo());
		}
	}
	
	public function pjActionPreview()
	{
		$this->setAjax(true);
		
		if ($this->isXHR() || isset($_GET['_escaped_fragment_']))
		{			
			if (isset($_SESSION[$this->defaultStore]) && count($_SESSION[$this->defaultStore]) > 0)
			{
				$country_arr = array();
				if(isset($_SESSION[$this->defaultForm]['c_country']) && !empty($_SESSION[$this->defaultForm]['c_country']))
				{
					$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->find($_SESSION[$this->defaultForm]['c_country'])->getData();
				}
				
				$this->set('country_arr', $country_arr);
				$this->set('status', 'OK');
			}else{
				$this->set('status', 'ERR');
			}
			$this->set('ldata', $this->_get('p_location_id'));
			$this->set('cart_box', $this->getCartInfo());
		}
	}
	public function pjActionGetPickupLocations()
	{
		$this->setAjax(true);		
	
		if ($this->isXHR())
		{
			$arr = pjLocationModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjLocation' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'address'", 'left')
				->select('t1.*, t2.content AS name, t3.content AS address')
				->where('user_id', $_SESSION['order_data']['o_user_id'])
				->findAll()
				->getData();
			pjAppController::jsonResponse($arr);
		}
		exit;
	}
	public function pjActionGetLocation()
	{	
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$arr = pjLocationModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjLocation' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'address'", 'left')
				->select('t1.*, t2.content AS name, t3.content AS address')
				->find($_GET['id'])
				->getData();
			pjAppController::jsonResponse($arr);
		}
		exit;
	}
	
	public function pjActionGetLocations()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjLocationCoordModel = pjLocationCoordModel::factory();
			$arr = pjLocationModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjLocation' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'address'", 'left')
				->select('t1.*, t2.content AS name, t3.content AS address')				
				->findAll()
				->getData();
			foreach ($arr as $k => $v)
			{
				$arr[$k]['coords'] = $pjLocationCoordModel->reset()->where('t1.location_id', $v['id'])->findAll()->getData();
			}
			pjAppController::jsonResponse($arr);
		}
		exit;
	}
	
	public function pjActionGetWTime()
	{		
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['date']) && !empty($_GET['date']))
			{
				$date = pjUtil::formatDate($_GET['date'], $this->option_arr['o_date_format']);
				$wt_arr = pjAppController::getWorkingTime($date, $_GET['location_id'], $_GET['type']);
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
	
	public function pjActionSetTypes()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['loadTypes']))
			{
				$this->_set('type', $_POST['type']);
				$this->_set('user_id', $_POST['user_id']);
				if($_POST['type'] == 'delivery')
				{
					$this->_set('d_date', $_POST['d_date']);
					$this->_set('d_hour', $_POST['d_hour']);
					$this->_set('d_minute', $_POST['d_minute']);
					$this->_set('d_location_id', $_POST['d_location_id']);
					
					$this->_set('d_address_1', isset($_POST['d_address_1']) ? $_POST['d_address_1'] : NULL);
					$this->_set('d_address_2', isset($_POST['d_address_2']) ? $_POST['d_address_2'] : NULL);
					$this->_set('d_country_id', isset($_POST['d_country_id']) ? $_POST['d_country_id'] : NULL);
					$this->_set('d_state', isset($_POST['d_state']) ? $_POST['d_state'] : NULL);
					$this->_set('d_city', isset($_POST['d_city']) ? $_POST['d_city'] : NULL);
					$this->_set('d_zip', isset($_POST['d_zip']) ? $_POST['d_zip'] : NULL);
					$this->_set('d_notes', isset($_POST['d_notes']) ? $_POST['d_notes'] : NULL);
					
					$arr = pjPriceModel::factory()
						->where('t1.location_id', $this->_get('d_location_id'))
						->where('t1.total_from <= ' . $this->_get('price'))
						->where('t1.total_to >= ' . $this->_get('price'))
						->limit(1)
						->findAll()
						->getData();
						
					$delivery = 0;
					if (count($arr) === 1)
					{
						$delivery = $arr[0]['price'];
					}
					$this->_set('delivery', $delivery);
				}else{
					$this->_set('p_location_id', $_POST['p_location_id']);					
					$this->_set('p_date', $_POST['p_date']);
					$this->_set('p_hour', $_POST['p_hour']);
					$this->_set('p_minute', $_POST['p_minute']);
				}
			}
			$this->_unset('voucher_code');
			$this->_unset('voucher_discount');
			$this->_unset('voucher_type');
		}
		exit;
	}
	
	public function pjActionCheckLogin()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjClientModel = pjClientModel::factory();
			
			$client = $pjClientModel
				->where('t1.c_email', $_POST['login_email'])
				->where(sprintf("t1.c_password = AES_ENCRYPT('%s', '%s')", pjObject::escapeString($_POST['login_password']), PJ_SALT))
				->limit(1)
				->findAll()
				->getData();

			$resp = array();	
			if (count($client) != 1)
			{
				$resp['code'] = 100;
			}else{
				if ($client[0]['status'] != 'T')
				{
					$resp['code'] = 101;
				}else{
					$last_login = date("Y-m-d H:i:s");
	    			$_SESSION[$this->defaultClient] = $client[0];
	    			
	    			$data = array();
	    			$data['last_login'] = $last_login;
	    			$pjClientModel->reset()->setAttributes(array('id' => $client[0]['id']))->modify($data);
					$resp['code'] = 200;
				}
			}
			pjAppController::jsonResponse($resp);
		}
		exit;
	}
	
	public function pjActionSendPassword()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjClientModel = pjClientModel::factory();
			
			$client = $pjClientModel
				->where('t1.c_email', $_POST['email'])
				->limit(1)
				->findAll()
				->getData();

			$resp = array();	
			if (count($client) != 1)
			{
				$resp['code'] = 100;
			}else{
				if ($client[0]['status'] != 'T')
				{
					$resp['code'] = 101;
				}else{
					pjFront::pjActionConfirmSend($this->option_arr, $client[0], PJ_SALT, 'forgot');
					$resp['code'] = 200;
				}
			}
			pjAppController::jsonResponse($resp);
		}
		exit;
	}
	
	public function pjActionSaveForm()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (!isset($_SESSION[$this->defaultForm]) || count($_SESSION[$this->defaultForm]) === 0)
			{
				$_SESSION[$this->defaultForm] = array();
			}
			$_SESSION[$this->defaultForm] = $_POST;
			$_SESSION['form'] = $_POST;
			
			$resp = array('code' => 200);
			pjAppController::jsonResponse($resp);
		}
	}
	
	public function pjActionPostCloverData()
	{
		$cart_box = $this->getCartInfo();		
		$payment = $_GET['payment'];
		$location_id = $_GET['location_id'];
		$amount = $_GET['price'];
		$tax = $_GET['tax'];
		
		$lc_arr = array(
			'location_id' => $location_id,
			'payment' => $payment,
			'cart_box' => $cart_box,
			'price' => $amount,
			'tax' => $tax
		);
		$this->set('lc_arr', $lc_arr);
	}
	
	public function pjActionCreditCard()
	{
		$this->setLayout('pjActionEmpty');
	}
	
	public function pjActionSaveOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$cart = $this->_get('cart');
			
			$pjOrderModel = pjOrderModel::factory();
			
			$STORAGE = $_SESSION[$this->defaultStore];
			$FORM = $_SESSION[$this->defaultForm];
			
			$data = array();
			$data['status'] = $this->option_arr['o_booking_status'];
			$data['price'] = $this->_get('price');
			$data['price_delivery'] = $this->_get('delivery');
			$data['discount'] = $this->_get('discount');
			$data['subtotal'] = $this->_get('subtotal');
			$data['tax'] = $this->_get('tax');
			$data['total'] = $this->_get('total');
			$data['uuid'] = time();
			$data['ip'] = $_SERVER['REMOTE_ADDR'];
			$data['user_id'] = $this->_get('user_id');
			$data['location_id'] = $this->_get('p_location_id');			
			
			switch ($this->_get('type'))
			{
				case 'pickup':
					$data['p_dt'] = pjUtil::formatDate($this->_get('p_date'), $this->option_arr['o_date_format']) . " " . $this->_get('p_hour') . ":" . $this->_get('p_minute') . ":00";
					unset($STORAGE['d_address_1']);
					unset($STORAGE['d_address_2']);
					unset($STORAGE['d_country_id']);
					unset($STORAGE['d_state']);
					unset($STORAGE['d_city']);
					unset($STORAGE['d_zip']);
					unset($STORAGE['d_notes']);
					unset($STORAGE['d_date']);
					unset($STORAGE['d_hour']);
					unset($STORAGE['d_minute']);
					break;
				case 'delivery':
					$data['d_dt'] = pjUtil::formatDate($this->_get('d_date'), $this->option_arr['o_date_format']) . " " . $this->_get('d_hour') . ":" . $this->_get('d_minute') . ":00";					
					unset($STORAGE['p_date']);
					unset($STORAGE['p_hour']);
					unset($STORAGE['p_minute']);
					break;
			}
			unset($STORAGE['cart']);
			unset($STORAGE['subtotal']);
			unset($STORAGE['total']);
			unset($STORAGE['delivery']);
			
			$payment = 'none';
			if(isset($FORM['payment_method']))
			{
				if ($FORM['payment_method'] == 'creditcard')
				{
					$data['cc_exp'] = $FORM['cc_exp_month'] . "/" . $FORM['cc_exp_year'];
				} else {
					unset($FORM['cc_type']);
					unset($FORM['cc_num']);
					unset($FORM['cc_exp_month']);
					unset($FORM['cc_exp_year']);
					unset($FORM['cc_code']);
				}
				$payment = $FORM['payment_method'];
			}
			
			$is_new_client = false;
			$update_client = false;
			$pjClientModel = pjClientModel::factory();
			$data['client_id'] = ':NULL';
			if($this->isFrontLogged())
			{
				$cnt = $pjClientModel
					->where('t1.id', $this->getClientId())
					->findCount()
					->getData();
				if($cnt == 0)
				{
					$is_new_client = true;
				}else{
					$update_client = true;
				}
			}else{
				$is_new_client = true;
			}
			if($is_new_client == true)
			{
				$c_data = array();
				$c_data['status'] = 'T';
				$c_data['user_id'] = $this->_get('user_id');
				$c_data['c_password'] = pjUtil::getRandomPassword(6);
				$c_data = array_merge($FORM, $c_data);				
				$client_id = $pjClientModel
					->reset()
					->setAttributes($c_data)
					->insert()
					->getInsertId();
				if ($client_id !== false && (int) $client_id > 0)
				{
					$data['client_id'] = $client_id;
					if($this->isFrontLogged())
					{
						$client = $pjClientModel
							->reset()
							->find($client_id)
							->getData();
						unset($_SESSION[$this->defaultClient]);
						$_SESSION[$this->defaultClient] = $client;
					}
					pjFront::pjActionConfirmSend($this->option_arr, $c_data, PJ_SALT, 'account');
				}
			}
			if($update_client == true)
			{
				if(isset($FORM['update_address']))
				{
					$c_data = array();
					if(isset($FORM['c_address_1']))
					{
						$c_data['c_address_1'] = $FORM['c_address_1'];
					}
					if(isset($FORM['c_address_2']))
					{
						$c_data['c_address_1'] = $FORM['c_address_1'];
					}
					if(isset($FORM['c_country']))
					{
						$c_data['c_country'] = $FORM['c_country'];
					}
					if(isset($FORM['c_state']))
					{
						$c_data['c_state'] = $FORM['c_state'];
					}
					if(isset($FORM['c_city']))
					{
						$c_data['c_city'] = $FORM['c_city'];
					}
					if(isset($FORM['c_zip']))
					{
						$c_data['c_zip'] = $FORM['c_zip'];
					}
					$pjClientModel
						->reset()
						->where('id', $this->getClientId())
						->limit(1)
						->modifyAll($c_data);
				}
				if(isset($FORM['update_details']))
				{
					$c_data = array();
					if(isset($FORM['c_title']))
					{
						$c_data['c_title'] = $FORM['c_title'];
					}
					if(isset($FORM['c_name']))
					{
						$c_data['c_name'] = $FORM['c_name'];
					}
					if(isset($FORM['c_email']))
					{
						$c_data['c_email'] = $FORM['c_email'];
					}
					if(isset($FORM['c_phone']))
					{
						$c_data['c_phone'] = $FORM['c_phone'];
					}
					if(isset($FORM['c_company']))
					{
						$c_data['c_company'] = $FORM['c_company'];
					}
					if(isset($FORM['c_notes']))
					{
						$c_data['c_notes'] = $FORM['c_notes'];
					}
					$pjClientModel
						->reset()
						->where('id', $this->getClientId())
						->limit(1)
						->modifyAll($c_data);
				}
									
				$client = $pjClientModel
					->reset()
					->find($this->getClientId())
					->getData();
				unset($_SESSION[$this->defaultClient]);
				$_SESSION[$this->defaultClient] = $client;
				
				$data['client_id'] = $this->getClientId();
			}
			
			$data = array_merge($STORAGE, $FORM, $data);
			$order_id = $pjOrderModel
				->setAttributes($data)
				->insert()
				->getInsertId();
			if ($order_id !== false && (int) $order_id > 0)
			{
				$pjOrderItemModel = pjOrderItemModel::factory();
				$pjProductPriceModel = pjProductPriceModel::factory();
				$pjProductModel = pjProductModel::factory();
				$pjExtraModel = pjExtraModel::factory();
				
				foreach ($cart as $item)
				{
					$price_id = ':NULL';
					$price = 0;
					if(!empty($item['price_id']))
					{
						$price_arr = $pjProductPriceModel->find($item['price_id'])->getData();
						if($price_arr)	
						{		
							$price_id = $price_arr['id'];
							$price = $price_arr['price'];
						}
					}else{
						$price_arr = $pjProductModel->reset()->find($item['product_id'])->getData();
						if(!empty($price_arr))	
						{
							$price = $price_arr['price'];
						}
					}
					
					$hash = md5(uniqid(rand(), true));
					$oid = $pjOrderItemModel
						->reset()
						->setAttributes(array(
							'order_id' => $order_id,
							'foreign_id' => $item['product_id'],
							'type' => 'product',
							'price_id' => $price_id,
							'price' => $price,
							'hash' => $hash,
							'cnt' => $item['cnt']))
						->insert();
					foreach ($item['extras'] as $extra_id => $extra_cnt)
					{
						if ($extra_cnt > 0)
						{
							$extra_price = 0;
							$extra_arr = $pjExtraModel
								->reset()
								->find($extra_id)
								->getData();
							if(!empty($extra_arr) && !empty($extra_arr['price']))
							{
								$extra_price = $extra_arr['price'];
							}
							$pjOrderItemModel
								->reset()
								->setAttributes(array(
									'order_id' => $order_id,
									'foreign_id' => $extra_id,
									'type' => 'extra',
									'price_id' => ':NULL',
									'price' => $extra_price,
									'hash' => $hash,
									'cnt' => $extra_cnt))
								->insert();
						}
					}
				}				
				
				$order_arr = $pjOrderModel
					->reset()
					->join('pjClient', "t2.id=t1.client_id", 'left outer')
					->select('t1.*, t2.c_title, t2.c_email, t2.c_name, t2.c_phone, t2.c_company, t2.c_address_1, t2.c_address_2, t2.c_country, t2.c_state, t2.c_city, t2.c_zip, t2.c_notes')
					->find($order_id)
					->getData();
				
				$pdata = array();
				$pdata['order_id'] = $order_id;
				$pdata['payment_method'] = $payment;
				$pdata['payment_type'] = 'online';
				$pdata['amount'] = $order_arr['total'];
				$pdata['status'] = 'notpaid'; 
				pjOrderPaymentModel::factory()->setAttributes($pdata)->insert();
				
				pjAppController::addOrderDetails($order_arr, $this->getLocaleId());
				
				pjFront::pjActionConfirmSend($this->option_arr, $order_arr, PJ_SALT, 'confirm');
				
				unset($_SESSION[$this->defaultStore]);
				unset($_SESSION[$this->defaultForm]);
				unset($_SESSION[$this->defaultClient]);
				
				//Redirect to Credit card payment url.
				if($payment == 'creditcard')
				{
					$cardData = $_SESSION['cardData'];
					$params = 'amount='. base64_encode($cardData['total']) .'&oid='. $cardData['clover_order_id'] .'&mid='. $cardData['clover_mid'] .'&at='. $cardData['clover_access_token'] .'&uid='. base64_encode($cardData['o_user_id']).'&mname='. base64_encode($cardData['o_m_name']);
					$url = PJ_INSTALL_URL .'payment/creditcard.php?'. $params;
					$json = array('code' => 200, 'text' => '', 'order_id' => $order_id, 'payment' => $payment, 'path' => $url);
				} else {				
					$json = array('code' => 200, 'text' => '', 'order_id' => $order_id, 'payment' => $payment, 'path' => 'cash');
				}
			}else{
				$json = array('code' => 100, 'text' => '');
			}		
			
			pjAppController::jsonResponse($json);
		}
	}
	
	public function pjActionGetPaymentForm()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$arr = pjOrderModel::factory()
				->select('t1.*')
				->find($_GET['order_id'])
				->getData();
			
			$item_arr = pjOrderItemModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.foreign_id AND t2.model = 'pjProduct' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->select('t1.*, t2.content as name')
				->where('t1.order_id', $_GET['order_id'])
				->where('t1.type', 'product')
				->findAll()
				->getData();
			$_arr = array();
			foreach($item_arr as $v)
			{
				$_arr[] = stripslashes($v['name']);
			}
			$arr['product_name'] = !empty($_arr) ? join("; ", $_arr) : null;
			$arr['cart_info'] = $item_arr;
				
			switch ($arr['payment_method'])
			{
				case 'paypal':
					$this->set('params', array(
						'name' => 'fdPaypal',
						'id' => 'fdPaypal',
						'business' => $this->option_arr['o_paypal_address'],
						'item_name' => $arr['product_name'],
						'custom' => $arr['id'],
						'amount' => number_format($arr['total'], 2, '.', ''),
						'currency_code' => $this->option_arr['o_currency'],
						'return' => $this->option_arr['o_thankyou_page'],
						'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmPaypal',
						'target' => '_self'
					));
					break;
				case 'authorize':
					$this->set('params', array(
						'name' => 'fdAuthorize',
						'id' => 'fdAuthorize',
						'target' => '_self',
						'timezone' => $this->option_arr['o_timezone'],
						'transkey' => $this->option_arr['o_authorize_transkey'],
						'x_login' => $this->option_arr['o_authorize_merchant_id'],
						'x_description' => $arr['product_name'],
						'x_amount' => number_format($arr['total'], 2, '.', ''),
						'x_invoice_num' => $arr['id'],
						'x_receipt_link_url' => $this->option_arr['o_thankyou_page'],
						'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmAuthorize'
					));
					break;
			}
			
			$this->set('arr', $arr);
			$this->set('get', $_GET);
		}
	}
	
	public function pjActionConfirmAuthorize()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		$pjOrderModel = pjOrderModel::factory();
		
		$order_arr = $pjOrderModel
			->join('pjClient', "t2.id=t1.client_id", 'left outer')
			->select('t1.*, t2.c_title, t2.c_email, t2.c_name, t2.c_phone, t2.c_company, t2.c_address_1, t2.c_address_2, t2.c_country, t2.c_state, t2.c_city, t2.c_zip, t2.c_notes')
			->find($_POST['x_invoice_num'])
			->getData();							
		if (count($order_arr) == 0)
		{
			$this->log('No such booking');
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}					
		
		if (count($order_arr) > 0)
		{
			$params = array(
				'transkey' => $this->option_arr['o_authorize_transkey'],
				'x_login' => $this->option_arr['o_authorize_merchant_id'],
				'md5_setting' => $this->option_arr['o_authorize_md5_hash'],
				'key' => md5($this->option_arr['private_key'] . PJ_SALT)
			);
			
			$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
			if ($response !== FALSE && $response['status'] === 'OK')
			{
				$pjOrderModel->reset()
					->setAttributes(array('id' => $response['transaction_id']))
					->modify(array('status' => $this->option_arr['o_payment_status'], 'processed_on' => ':NOW()'));

				pjOrderPaymentModel::factory()->setAttributes(array('order_id' => $response['transaction_id'], 'payment_type' => 'online'))
												->modify(array('status' => 'paid'));
					
				pjAppController::addOrderDetails($order_arr, $this->getLocaleId());
				pjFront::pjActionConfirmSend($this->option_arr, $order_arr, PJ_SALT, 'payment');
				
			} elseif (!$response) {
				$this->log('Authorization failed');
			} else {
				$this->log('Booking not confirmed. ' . $response['response_reason_text']);
			}
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}
	}

	public function pjActionConfirmPaypal()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		$pjOrderModel = pjOrderModel::factory();
		
		$order_arr = $pjOrderModel
			->join('pjClient', "t2.id=t1.client_id", 'left outer')
			->select('t1.*, t2.c_title, t2.c_email, t2.c_name, t2.c_phone, t2.c_company, t2.c_address_1, t2.c_address_2, t2.c_country, t2.c_state, t2.c_city, t2.c_zip, t2.c_notes')
			->find($_POST['custom'])
			->getData();
		if (count($order_arr) == 0)
		{
			$this->log('No such booking');
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}					
		
		$params = array(
			'txn_id' => @$order_arr['txn_id'],
			'paypal_address' => $this->option_arr['o_paypal_address'],
			'deposit' => @$order_arr['total'],
			'currency' => $this->option_arr['o_currency'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);
		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Booking confirmed');
			$pjOrderModel->reset()->setAttributes(array('id' => $pjOrderModel['id']))->modify(array(
				'status' => $this->option_arr['o_payment_status'],
				'txn_id' => $response['transaction_id'],
				'processed_on' => ':NOW()'
			));
			pjOrderPaymentModel::factory()->setAttributes(array('order_id' => $order_arr['id'], 'payment_type' => 'online'))
											->modify(array('status' => 'paid'));
			
			pjAppController::addOrderDetails($order_arr, $this->getLocaleId());
			pjFront::pjActionConfirmSend($this->option_arr, $order_arr, PJ_SALT, 'payment');
			
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Booking not confirmed');
		}
		pjUtil::redirect($this->option_arr['o_thankyou_page']);
	}	

	public function pjActionConfirmSend($option_arr, $data, $salt, $opt)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
				->setTransport('smtp')
				->setSmtpHost($option_arr['o_smtp_host'])
				->setSmtpPort($option_arr['o_smtp_port'])
				->setSmtpUser($option_arr['o_smtp_user'])
				->setSmtpPass($option_arr['o_smtp_pass'])
			;
		}
		$Email->setContentType('text/html');
		
		$pjMultiLangModel = pjMultiLangModel::factory();
		
		$admin_email = $this->getAdminEmail();
		$admin_phone = $this->getAdminPhone();
		$from_email = $admin_email;
		if(!empty($option_arr['o_sender_email']))
		{
			$from_email = $option_arr['o_sender_email'];
		}
		
		$locale_id = isset($booking_arr['locale_id']) && (int) $booking_arr['locale_id'] > 0 ? (int) $booking_arr['locale_id'] : $this->getLocaleId();
		
		if($opt == 'account' || $opt == 'forgot')
		{
			$tokens = pjAppController::getClientTokens($option_arr, $data, PJ_SALT, $this->getLocaleId());
			
			$lang_message = $pjMultiLangModel
				->reset()
				->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_'.$opt.'_message')
				->limit(0, 1)
				->findAll()
				->getData();
			$lang_subject = $pjMultiLangModel
				->reset()
				->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $locale_id)
				->where('t1.field', 'o_email_'.$opt.'_subject')
				->limit(0, 1)
				->findAll()
				->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				
				$Email
					->setTo($data['c_email'])
					->setFrom($from_email)
					->setSubject($lang_subject[0]['content'])
					->send(pjUtil::textToHtml($message));
			}
		}else{
			$tokens = pjAppController::getTokens($option_arr, $data, PJ_SALT, $this->getLocaleId());
			
			if ($option_arr['o_email_confirmation'] == 1 && $opt == 'confirm')
			{
				$lang_message = $pjMultiLangModel
					->reset()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $locale_id)
					->where('t1.field', 'o_email_confirmation_message')
					->limit(0, 1)
					->findAll()
					->getData();
				$lang_subject = $pjMultiLangModel
					->reset()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $locale_id)
					->where('t1.field', 'o_email_confirmation_subject')
					->limit(0, 1)
					->findAll()
					->getData();
							   
				if (count($lang_message) === 1 && count($lang_subject) === 1)
				{
					if ($data['type'] == 'delivery')
					{
						$message = str_replace(array('[Delivery]', '[/Delivery]'), array('', ''), $lang_message[0]['content']);
					} else {
						$message = preg_replace('/\[Delivery\].*\[\/Delivery\]/s', '', $lang_message[0]['content']);
					}
					
					$message = str_replace($tokens['search'], $tokens['replace'], $message);
					
					$Email
						->setTo($data['c_email'])
						->setFrom($from_email)
						->setSubject($lang_subject[0]['content'])
						->send(pjUtil::textToHtml($message));
				}
			}
			
			if ($option_arr['o_admin_email_confirmation'] == 1 && $opt == 'confirm')
			{	
				$lang_message = $pjMultiLangModel
					->reset()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $locale_id)
					->where('t1.field', 'o_admin_email_confirmation_message')
					->limit(0, 1)
					->findAll()
					->getData();
				$lang_subject = $pjMultiLangModel
					->reset()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $locale_id)
					->where('t1.field', 'o_admin_email_confirmation_subject')
					->limit(0, 1)
					->findAll()
					->getData();
							   
				if (count($lang_message) === 1 && count($lang_subject) === 1)
				{
					if ($data['type'] == 'delivery')
					{
						$message = str_replace(array('[Delivery]', '[/Delivery]'), array('', ''), $lang_message[0]['content']);
					} else {
						$message = preg_replace('/\[Delivery\].*\[\/Delivery\]/s', '', $lang_message[0]['content']);
					}
					$message = str_replace($tokens['search'], $tokens['replace'], $message);
					
					$Email
						->setTo($admin_email)
						->setFrom($from_email)
						->setSubject($lang_subject[0]['content'])
						->send(pjUtil::textToHtml($message));
				}
				
				if(!empty($admin_phone))
				{
					$lang_message = $pjMultiLangModel
						->reset()
						->select('t1.*')
						->where('t1.model','pjOption')
						->where('t1.locale', $locale_id)
						->where('t1.field', 'o_admin_sms_confirmation_message')
						->limit(0, 1)
						->findAll()
						->getData();
					if (count($lang_message) === 1)
					{
						$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
						$params = array(
							'text' => $message,
							'key' => md5($option_arr['private_key'] . PJ_SALT)
						);
						$params['number'] = $admin_phone;
						$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
					}
				}
			}
			
			if ($option_arr['o_email_payment'] == 1 && $opt == 'payment')
			{
				$lang_message = $pjMultiLangModel
					->reset()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $locale_id)
					->where('t1.field', 'o_email_payment_message')
					->limit(0, 1)
					->findAll()
					->getData();
				$lang_subject = $pjMultiLangModel
					->reset()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $locale_id)
					->where('t1.field', 'o_email_payment_subject')
					->limit(0, 1)
					->findAll()
					->getData();
					
				if (count($lang_message) === 1 && count($lang_subject) === 1)
				{
					if ($data['type'] == 'delivery')
					{
						$message = str_replace(array('[Delivery]', '[/Delivery]'), array('', ''), $lang_message[0]['content']);
					} else {
						$message = preg_replace('/\[Delivery\].*\[\/Delivery\]/s', '', $lang_message[0]['content']);
					}
					$message = str_replace($tokens['search'], $tokens['replace'], $message);
					
					$Email
						->setTo($data['c_email'])
						->setFrom($from_email)
						->setSubject($lang_subject[0]['content'])
						->send(pjUtil::textToHtml($message));
				}
			}
			if ($option_arr['o_admin_email_payment'] == 1 && $opt == 'payment')
			{	
				$lang_message = $pjMultiLangModel
					->reset()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $locale_id)
					->where('t1.field', 'o_admin_email_payment_message')
					->limit(0, 1)
					->findAll()
					->getData();
				$lang_subject = $pjMultiLangModel
					->reset()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $locale_id)
					->where('t1.field', 'o_admin_email_payment_subject')
					->limit(0, 1)
					->findAll()
					->getData();
					
				if (count($lang_message) === 1 && count($lang_subject) === 1)
				{
					if ($data['type'] == 'delivery')
					{
						$message = str_replace(array('[Delivery]', '[/Delivery]'), array('', ''), $lang_message[0]['content']);
					} else {
						$message = preg_replace('/\[Delivery\].*\[\/Delivery\]/s', '', $lang_message[0]['content']);
					}
					$message = str_replace($tokens['search'], $tokens['replace'], $message);
					
					$Email
						->setTo($admin_email)
						->setFrom($from_email)
						->setSubject($lang_subject[0]['content'])
						->send(pjUtil::textToHtml($message));
				}
				if(!empty($admin_phone))
				{
					$lang_message = $pjMultiLangModel
						->reset()
						->select('t1.*')
						->where('t1.model','pjOption')
						->where('t1.locale', $locale_id)
						->where('t1.field', 'o_admin_sms_payment_message')
						->limit(0, 1)
						->findAll()
						->getData();
					if (count($lang_message) === 1)
					{
						$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
						$params = array(
							'text' => $message,
							'key' => md5($option_arr['private_key'] . PJ_SALT)
						);
						$params['number'] = $admin_phone;
						$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
					}
				}
			}
			
			if ($option_arr['o_email_cancel'] == 1 && $opt == 'cancel')
			{
				$lang_message = $pjMultiLangModel
					->reset()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $locale_id)
					->where('t1.field', 'o_email_cancel_message')
					->limit(0, 1)
					->findAll()
					->getData();
				$lang_subject = $pjMultiLangModel
					->reset()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $locale_id)
					->where('t1.field', 'o_email_cancel_subject')
					->limit(0, 1)
					->findAll()
					->getData();
							   
				if (count($lang_message) === 1 && count($lang_subject) === 1)
				{
					if ($data['type'] == 'delivery')
					{
						$message = str_replace(array('[Delivery]', '[/Delivery]'), array('', ''), $lang_message[0]['content']);
					} else {
						$message = preg_replace('/\[Delivery\].*\[\/Delivery\]/s', '', $lang_message[0]['content']);
					}
					
					$message = str_replace($tokens['search'], $tokens['replace'], $message);
					
					$Email
						->setTo($data['c_email'])
						->setFrom($from_email)
						->setSubject($lang_subject[0]['content'])
						->send(pjUtil::textToHtml($message));
				}
			}
			if ($option_arr['o_admin_email_cancel'] == 1 && $opt == 'cancel')
			{	
				$lang_message = $pjMultiLangModel
					->reset()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $locale_id)
					->where('t1.field', 'o_admin_email_cancel_message')
					->limit(0, 1)
					->findAll()
					->getData();
				$lang_subject = $pjMultiLangModel
					->reset()
					->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $locale_id)
					->where('t1.field', 'o_admin_email_cancel_subject')
					->limit(0, 1)
					->findAll()
					->getData();
							   
				if (count($lang_message) === 1 && count($lang_subject) === 1)
				{
					if ($data['type'] == 'delivery')
					{
						$message = str_replace(array('[Delivery]', '[/Delivery]'), array('', ''), $lang_message[0]['content']);
					} else {
						$message = preg_replace('/\[Delivery\].*\[\/Delivery\]/s', '', $lang_message[0]['content']);
					}
					$message = str_replace($tokens['search'], $tokens['replace'], $message);
					
					$Email
						->setTo($admin_email)
						->setFrom($from_email)
						->setSubject($lang_subject[0]['content'])
						->send(pjUtil::textToHtml($message));
				}
			}
		}
	}
	
	public function pjActionCancel()
	{
		$this->setLayout('pjActionCancel');
		
		$pjOrderModel = pjOrderModel::factory();
		
		if (isset($_POST['order_cancel']))
		{
			$order_arr = $pjOrderModel
				->reset()
				->join('pjClient', "t2.id=t1.client_id", 'left outer')
				->select('t1.*, t2.c_title, t2.c_email, t2.c_name, t2.c_phone, t2.c_company, t2.c_address_1, t2.c_address_2, t2.c_country, t2.c_state, t2.c_city, t2.c_zip, t2.c_notes')
				->find($_POST['id'])
				->getData();
			if (count($order_arr) > 0)
			{
				$sql = "UPDATE `".$pjOrderModel->getTable()."` SET status = 'cancelled' WHERE SHA1(CONCAT(`id`, `created`, '".PJ_SALT."')) = '" . $_POST['hash'] . "'";
				
				$pjOrderModel->reset()->execute($sql);

				pjAppController::addOrderDetails($order_arr, $this->getLocaleId());
				
				pjFront::pjActionConfirmSend($this->option_arr, $order_arr, PJ_SALT, 'cancel');
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . '?controller=pjFront&action=pjActionCancel&err=200');
			}
		}else{
			if (isset($_GET['hash']) && isset($_GET['id']))
			{
				$arr = $pjOrderModel
					->reset()
					->join('pjClient', "t2.id=t1.client_id", 'left outer')
					->join('pjMultiLang', "t3.model='pjCountry' AND t3.foreign_id=t1.d_country_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t4.model='pjCountry' AND t4.foreign_id=t1.location_id AND t4.field='name' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjClient', "t2.id=t1.client_id", 'left outer')
					->select('t1.*, t3.content as d_country, t4.content as location, t2.c_title, t2.c_email, t2.c_name, t2.c_phone, t2.c_company, t2.c_address_1, t2.c_address_2, t2.c_country, t2.c_state, t2.c_city, t2.c_zip, t2.c_notes')
					->find($_GET['id'])
					->getData();
				if (count($arr) == 0)
				{
					$this->set('status', 2);
				}else{
					if ($arr['status'] == 'cancelled')
					{
						$this->set('status', 4);
					}else{
						$hash = sha1($arr['id'] . $arr['created'] . PJ_SALT);
						if ($_GET['hash'] != $hash)
						{
							$this->set('status', 3);
						}else{
							pjAppController::addOrderDetails($arr, $this->getLocaleId());
							$this->set('arr', $arr);
						}
					}
				}
			}elseif (!isset($_GET['err'])) {
				$this->set('status', 1);
			}
		}
	}	
	
}
?>