<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppController extends pjController
{
	public $models = array();
	
	public $defaultLocale = 'admin_locale_id';
  
	public $defaultFields = 'fields';
	
	public $defaultFieldsIndex = 'fields_index';
  
	protected function loadSetFields($force=FALSE)
	{
		$registry = pjRegistry::getInstance();
		if ($force
			|| !isset($_SESSION[$this->defaultFieldsIndex])
			|| $_SESSION[$this->defaultFieldsIndex] != $this->option_arr['o_fields_index']
			|| !isset($_SESSION[$this->defaultFields])
			|| empty($_SESSION[$this->defaultFields]))
		{
			pjAppController::setFields($this->getLocaleId());
			 
			# Update session
			if ($registry->is('fields'))
			{
				$_SESSION[$this->defaultFields] = $registry->get('fields');
			}
			$_SESSION[$this->defaultFieldsIndex] = $this->option_arr['o_fields_index'];
		}
	
		if (isset($_SESSION[$this->defaultFields]) && !empty($_SESSION[$this->defaultFields]))
		{
			# Load fields from session
			$registry->set('fields', $_SESSION[$this->defaultFields]);
		}
		
		return TRUE;
	}
	
	public function isCountryReady()
    {
    	return $this->isAdmin();
    }
    
	public function isOneAdminReady()
    {
    	return $this->isAdmin();
    }
	
	public static function setTimezone($timezone="UTC")
    {
    	if (in_array(version_compare(phpversion(), '5.1.0'), array(0,1)))
		{
			date_default_timezone_set($timezone);
		} else {
			$safe_mode = ini_get('safe_mode');
			if ($safe_mode)
			{
				putenv("TZ=".$timezone);
			}
		}
    }

	public static function setMySQLServerTime($offset="-0:00")
    {
		pjAppModel::factory()->prepare("SET SESSION time_zone = :offset;")->exec(compact('offset'));
    }
    
	public function setTime()
	{
		if (isset($this->option_arr['o_timezone']))
		{
			$offset = $this->option_arr['o_timezone'] / 3600;
			if ($offset > 0)
			{
				$offset = "-".$offset;
			} elseif ($offset < 0) {
				$offset = "+".abs($offset);
			} elseif ($offset === 0) {
				$offset = "+0";
			}
	
			pjAppController::setTimezone('Etc/GMT' . $offset);
			if (strpos($offset, '-') !== false)
			{
				$offset = str_replace('-', '+', $offset);
			} elseif (strpos($offset, '+') !== false) {
				$offset = str_replace('+', '-', $offset);
			}
			pjAppController::setMySQLServerTime($offset . ":00");
		}
	}
    
    public function beforeFilter()
    {
    	$this->appendJs('jquery-1.8.2.min.js', PJ_THIRD_PARTY_PATH . 'jquery/');
    	$this->appendJs('pjAdminCore.js');
    	$this->appendCss('reset.css');
    	
    	$this->appendJs('jquery-ui.custom.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/js/');
		$this->appendCss('jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/css/smoothness/');
				
		$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
		$this->appendCss('admin.css');
		
    	if ($_GET['controller'] != 'pjInstaller')
		{
			$this->models['Option'] = pjOptionModel::factory();
			$this->option_arr = $this->models['Option']->getPairs($this->getForeignId());
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
			if (!in_array($_GET['action'], array('pjActionPreview')))
			{
				$this->loadSetFields();
			}
		}
    }
    
	public function isEditor()
    {
    	return $this->getRoleId() == 2;
    }
    
    public function getForeignId()
    {
    	return 1;
    }
    
    public static function setFields($locale)
    {
		$fields = pjMultiLangModel::factory()
			->select('t1.content, t2.key')
			->join('pjField', "t2.id=t1.foreign_id", 'inner')
			->where('t1.locale', $locale)
			->where('t1.model', 'pjField')
			->where('t1.field', 'title')
			->findAll()
			->getDataPair('key', 'content');
		$registry = pjRegistry::getInstance();
		$tmp = array();
		if ($registry->is('fields'))
		{
			$tmp = $registry->get('fields');
		}
		$arrays = array();
		foreach ($fields as $key => $value)
		{
			if (strpos($key, '_ARRAY_') !== false)
			{
				list($prefix, $suffix) = explode("_ARRAY_", $key);
				if (!isset($arrays[$prefix]))
				{
					$arrays[$prefix] = array();
				}
				$arrays[$prefix][$suffix] = $value;
			}
		}
		require PJ_CONFIG_PATH . 'settings.inc.php';
		$fields = array_merge($tmp, $fields, $settings, $arrays);
		$registry->set('fields', $fields);
    }

    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}
	
	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}
	
	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : false;
	}
	
	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}
	
	public function pjActionCheckInstall()
	{
		$this->setLayout('pjActionEmpty');
		
		$result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
		$folders = array(
							'app/web/backup', 
							'app/web/upload',
							'app/web/upload/products'
						);
		foreach ($folders as $dir)
		{
			if (!is_writable($dir))
			{
				$result['status'] = 'ERR';
				$result['code'] = 101;
				$result['text'] = 'Permission requirement';
				$result['info'][] = sprintf('Folder \'<span class="bold">%1$s</span>\' is not writable. You need to set write permissions (chmod 777) to directory located at \'<span class="bold">%1$s</span>\'', $dir);
			}
		}
		
		return $result;
	}
	
	public function friendlyURL($str, $divider='-')
	{
		$str = mb_strtolower($str, mb_detect_encoding($str));
		$str = trim($str);
		$str = preg_replace('/[_|\s]+/', $divider, $str);
		$str = preg_replace('/\x{00C5}/u', 'AA', $str);
		$str = preg_replace('/\x{00C6}/u', 'AE', $str);
		$str = preg_replace('/\x{00D8}/u', 'OE', $str);
		$str = preg_replace('/\x{00E5}/u', 'aa', $str);
		$str = preg_replace('/\x{00E6}/u', 'ae', $str);
		$str = preg_replace('/\x{00F8}/u', 'oe', $str);
		$str = preg_replace('/[^a-z\x{0400}-\x{04FF}0-9-]+/u', '', $str);
		$str = preg_replace('/[-]+/', $divider, $str);
		$str = preg_replace('/^-+|-+$/', '', $str);
		return $str;
	}
	
	public function getCoords($str)
	{
		if (!is_array($str))
		{
			$_address = preg_replace('/\s+/', '+', $str);
			$_address = urlencode($_address);
		} else {
			$address = array();
			$address[] = $str['d_zip'];
			$address[] = $str['d_address_1'];
			$address[] = $str['d_city'];
			$address[] = $str['d_state'];
	
			foreach ($address as $k => $v)
			{
				$tmp = preg_replace('/\s+/', '+', $v);
				$address[$k] = $tmp;
			}
			$_address = join(",+", $address);
		}
		
		$api = sprintf("https://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false", $_address);
		
		$pjHttp = new pjHttp();
		$pjHttp->request($api);
		$response = $pjHttp->getResponse();
		
		$geoObj = pjAppController::jsonDecode($response);
		
		$data = array();
		if ($geoObj->status == 'OK')
		{
			$data['lat'] = $geoObj->results[0]->geometry->location->lat;
			$data['lng'] = $geoObj->results[0]->geometry->location->lng;
		} else {
			$data['lat'] = array('NULL');
			$data['lng'] = array('NULL');
		}
		return $data;
	}
	
	public function getDiscount($data, $option_arr)
	{
		$resp = array();
		
		if (isset($data['voucher_code']) && !empty($data['voucher_code']))
		{
			$_arr = pjVoucherModel::factory()
				->where('code', $data['voucher_code'])
				->findAll()
				->getData();
			if(count($_arr) > 0)
			{
				$arr = $_arr[0];
				
				if ($data['type'] == 'delivery')
				{
					$date = null;
					$time = "00:00:00";
					if (isset($data['d_dt']))
					{
						$date_time = $data['d_dt'];
						if(count(explode(" ", $date_time)) == 3)
						{
							list($_date, $_time, $_period) = explode(" ", $date_time);
							$time = pjUtil::formatTime($_time . ' ' . $_period, $option_arr['o_time_format']);
						}else{
							list($_date, $_time) = explode(" ", $date_time);
							$time = pjUtil::formatTime($_time, $option_arr['o_time_format']);
						}
						$date = $_date;
					}
					if(isset($data['p_date']))
					{
						$date = $data['d_date'];
						if (isset($data['d_hour']) && isset($data['d_minute']))
						{
							$time = $data['d_hour'] . ":" . $data['d_minute'] . ":00";
						}
					}
				}else{
					$date = null;
					$time = "00:00:00";
					if (isset($data['p_dt']))
					{
						$date_time = $data['p_dt'];
						if(count(explode(" ", $date_time)) == 3)
						{
							list($_date, $_time, $_period) = explode(" ", $date_time);
							$time = pjUtil::formatTime($_time . ' ' . $_period, $option_arr['o_time_format']);
						}else{
							list($_date, $_time) = explode(" ", $date_time);
							$time = pjUtil::formatTime($_time, $option_arr['o_time_format']);
						}
						$date = $_date;
					}
					if(isset($data['d_date']))
					{
						$date = $data['p_date'];
						if (isset($data['p_hour']) && isset($data['p_minute']))
						{
							$time = $data['p_hour'] . ":" . $data['p_minute'] . ":00";
						}
					}
				}
				
				if (!empty($date))
				{
					$date= pjUtil::formatDate($date, $option_arr['o_date_format']);
					$d = strtotime($date);
					$dt = strtotime($date . " ". $time);
					
					$valid = false;
					
					switch ($arr['valid'])
					{
						case 'fixed':
							$time_from = strtotime($arr['date_from'] . " " . $arr['time_from']);
							$time_to = strtotime($arr['date_to'] . " " . $arr['time_to']);							
							if ($time_from <= $dt && $time_to >= $dt)
							{
								$valid = true;
							}
							break;
						case 'period':
							$d_from = strtotime($arr['date_from']);
							$d_to = strtotime($arr['date_to']);
							$t_from = strtotime($arr['date_from'] . " " . $arr['time_from']);
							$t_to = strtotime($arr['date_to'] . " " . $arr['time_to']);
							if ($d_from <= $d && $d_to >= $d && $t_from <= $dt && $t_to >= $dt)
							{
								$valid = true;
							}
							break;
						case 'recurring':
							$t_from = strtotime($date . " " . $arr['time_from']);
							$t_to = strtotime($date . " " . $arr['time_to']);
							if ($arr['every'] == strtolower(date("l", $dt)) && $t_from <= $dt && $t_to >= $dt)
							{
								$valid = true;
							}
							break;
					}
					if ($valid)
					{
						$resp['voucher_code'] = $arr['code'];
						$resp['voucher_type'] = $arr['type'];
						
						 /* Manmohan code here */
                                                $resp['voucher_code_for'] = $arr['code_for'];
                                                $resp['voucher_product_id'] = $arr['product_id'];
                                                $resp['voucher_category_id'] = $arr['category_id'];
                                                /* Manmohan end of code here */
						
						$resp['voucher_discount'] = $arr['discount'];
						$resp['code'] = 200;
					}else{
						$resp['code'] = 102;
					}
				}else{
					$resp['code'] = 103;
				}
			}else{
				$resp['code'] = 101;
			}
		}else {
			$resp['code'] = 100;
		}
		return $resp;
	}
	
	public function getWorkingTime($date, $location_id, $type)
	{
		$date_arr = pjDateModel::factory()->getWorkingTime($date, $location_id, $type);
		if ($date_arr === false)
		{
			$wt_arr = pjWorkingTimeModel::factory()->getWorkingTime($location_id, $type, $date);
			if (count($wt_arr) == 0)
			{
				return false;
			}
			$t_arr = $wt_arr;
		} else {
			if (count($date_arr) == 0)
			{
				return false;
			}
			$t_arr = $date_arr;
		}
		return $t_arr;
	}
	
	public function getAdminEmail()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? $arr[0]['email'] : null;	
	}
	
	public function getAdminPhone()
	{
		$arr = pjUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? (!empty($arr[0]['phone']) ? $arr[0]['phone'] : null) : null;	
	}
	
	public function getClientTokens($option_arr, $data, $salt, $locale_id)
	{
		$country = NULL;
		if (isset($data['c_country']) && !empty($data['c_country']))
		{
			if(isset($data['c_country']) && (int) $data['c_country'] > 0)
			{
				$country_arr = pjCountryModel::factory()
							->select('t1.id, t2.content AS country_title')
							->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
							->find($data['c_country'])->getData();
				if (!empty($country_arr))
				{
					$country = $country_arr['country_title'];
				}
			}
		}
		$search = array(
			'{Title}', '{Name}', '{Email}', '{Password}', '{Phone}',
			'{Address1}', '{Address2}', '{Country}', '{State}',
			'{City}', '{Zip}');
		$replace = array(
			@$data['c_title'], @$data['c_name'], @$data['c_email'], @$data['c_password'], @$data['c_phone'],
			@$data['c_address_1'], @$data['c_address_2'], $country, @$data['c_state'],
			@$data['c_city'], @$data['c_zip']);

		return compact('search', 'replace');
		
	}
	
	public function getTokens($option_arr, $data, $salt, $locale_id)
	{
		$c_country = NULL;
		$d_country = NULL;
		
		if (isset($data['c_country']) && !empty($data['c_country']))
		{
			$pjCountryModel = pjCountryModel::factory();
			
			$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
						->find($data['c_country'])->getData();
			if (!empty($country_arr))
			{
				$c_country = $country_arr['country_title'];
			}
			$country_arr = pjCountryModel::factory()
				->reset()
				->select('t1.id, t2.content AS country_title')
				->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$locale_id."'", 'left outer')
				->find($data['d_country_id'])
				->getData();
			if (!empty($country_arr))
			{
				$d_country = $country_arr['country_title'];
			}
		}
		$row = array();
		if (isset($data['product_arr']))
		{
			foreach ($data['product_arr'] as $v)
			{
				$extra = array();
				foreach ($v['extra_arr'] as $e)
				{
					$extra[] = stripslashes(sprintf("%u x %s", $e['cnt'], $e['name']));
				}
				$row[] = stripslashes(sprintf("%u x %s", $v['cnt'], $v['name'])) . (count($extra) > 0 ? sprintf(" (%s)", join("; ", $extra)) : NULL);
			}
		}
		$order_data = count($row) > 0 ? join("\n", $row) : NULL;
		$discount = NULL;
		if (!empty($data['voucher_code']))
		{
			$voucher_arr = pjVoucherModel::factory()
				->where('t1.code', $data['voucher_code'])
				->limit(1)
				->findAll()
				->getData();
			if (!empty($voucher_arr))
			{
				$voucher_arr = $voucher_arr[0];
				switch ($voucher_arr['type'])
				{
					case "amount":
						$discount = pjUtil::formatCurrencySign($voucher_arr['discount'], $option_arr['o_currency']);
						break;
					case "percent":
						$discount = $voucher_arr['discount'] . '%';
						break;
				}
			}
		}
		$subtotal = pjUtil::formatCurrencySign($data['subtotal'], $option_arr['o_currency']);
		$price_delivery = pjUtil::formatCurrencySign($data['price_delivery'], $option_arr['o_currency']);
		$total = pjUtil::formatCurrencySign($data['total'], $option_arr['o_currency']);
		
		$cancelURL = PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionCancel&id='.@$data['id'].'&hash='.sha1(@$data['id'].@$data['created'].$salt);

		$search = array(
			'{Country}', '{City}', '{State}', '{Notes}',
			'{Zip}', '{Address1}', '{Address2}',
			'{Name}', '{Email}', '{Phone}', '{dCountry}',
			'{dCity}', '{dState}', '{dZip}', '{dAddress1}', '{dAddress2}',
			'{CCType}', '{CCNum}', '{CCExp}',
			'{CCSec}', '{PaymentMethod}', '{DateTime}',
			'{Subtotal}', '{Delivery}', '{Discount}',
			'{Total}', '{dNotes}', '{Location}',
			'{OrderID}', '{CancelURL}', '{OrderDetails}');
		$replace = array(
			$c_country, @$data['c_city'], @$data['c_state'], @$data['c_notes'],
			@$data['c_zip'], @$data['c_address_1'], @$data['c_address_2'],
			@$data['c_name'], @$data['c_email'], @$data['c_phone'], $d_country,
			@$data['d_city'], $data['d_state'], @$data['d_zip'], @$data['d_address_1'], @$data['d_address_2'],
			@$data['cc_type'], @$data['cc_num'], (@$data['payment_method'] == 'creditcard' ? @$data['cc_exp'] : NULL),
			@$data['cc_code'], @$data['payment_method'], date($option_arr['o_datetime_format'], strtotime(@$data['type'] == 'pickup' ? @$data['p_dt'] : @$data['d_dt'])),
			$subtotal, $price_delivery, @$discount,
			$total, @$data['d_notes'], @$data['location'],
			@$data['uuid'], $cancelURL, $order_data);
			
		return compact('search', 'replace');
	}
	
	public function addOrderDetails(&$arr, $locale_id)
	{
		$l_arr = pjLocationModel::factory()
			->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$locale_id."' AND t2.field = 'name'", 'left')
			->select('t1.*, t2.content as name')
			->find($arr['location_id'])
			->getData();
		if (count($l_arr) > 0)
		{
			$arr['location'] = $l_arr['name'];
		}

		$pjOrderItemModel = pjOrderItemModel::factory();
		
		$arr['product_arr'] = $pjOrderItemModel
			->reset()
			->join('pjMultiLang', "t2.foreign_id = t1.foreign_id AND t2.model = 'pjProduct' AND t2.locale = '".$locale_id."' AND t2.field = 'name'", 'left')
			->select('t1.*, t2.content as name')
			->where('t1.order_id', $arr['id'])
			->where('type', 'product')
			->findAll()
			->getData();
			
		foreach ($arr['product_arr'] as $k => $product)
		{
			$arr['product_arr'][$k]['extra_arr'] = $pjOrderItemModel
				->reset()
				->join('pjMultiLang', "t2.foreign_id = t1.foreign_id AND t2.model = 'pjExtra' AND t2.locale = '".$locale_id."' AND t2.field = 'name'", 'left')
				->select('t1.*, t2.content as name')
				->where('t1.order_id', $arr['id'])
				->where('type', 'extra')
				->where('hash', $product['hash'])
				->findAll()
				->getData();
		}
	}
}
?>