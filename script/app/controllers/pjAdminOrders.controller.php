<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOrders extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminOrders.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjOrderModel = pjOrderModel::factory()
				->join('pjClient', "t2.id=t1.client_id", 'left outer')
				->where('t1.user_id', $_SESSION['admin_user']['id']);
				
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjOrderModel->where("(t1.id = '$q' OR t1.uuid = '$q' OR t1.c_name LIKE '%$q%' OR t1.c_email LIKE '%$q%')");
			}
			
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('confirmed','cancelled','pending')))
			{
				$pjOrderModel->where('t1.status', $_GET['status']);
			}
			if (isset($_GET['client_id']) && (int) $_GET['client_id'] > 0)
			{
				$pjOrderModel->where('t1.client_id', $_GET['client_id']);
			}
			if (isset($_GET['type']) && !empty($_GET['type']))
			{
				$pjOrderModel->where('t1.type', $_GET['type']);
			}	
			$column = 'created';
			$direction = 'DESC';
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjOrderModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = array();
			
			$data = $pjOrderModel
				->select('t1.*, t2.c_name as client_name')
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			foreach($data as $k => $v)
			{
				$data[$k]['total'] = pjUtil::formatCurrencySign($v['total'], $this->option_arr['o_currency']);
				if($v['type'] == 'delivery')
				{
					$data[$k]['datetime'] = pjUtil::formatDate(date("Y-m-d", strtotime($v['d_dt'])), "Y-m-d", $this->option_arr['o_date_format']) . ', ' . pjUtil::formatTime(date("H:i:s", strtotime($v['d_dt'])), "H:i:s", $this->option_arr['o_time_format']);
				}else if($v['type'] == 'pickup'){
					$data[$k]['datetime'] = pjUtil::formatDate(date("Y-m-d", strtotime($v['p_dt'])), "Y-m-d", $this->option_arr['o_date_format']) . ', ' . pjUtil::formatTime(date("H:i:s", strtotime($v['p_dt'])), "H:i:s", $this->option_arr['o_time_format']);
				}
			}
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionSaveOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjOrderModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			
		}
		exit;
	}
	
	public function pjActionExportOrder()
	{
		$this->checkLogin();
		
		if (isset($_POST['record']) && is_array($_POST['record']))
		{
			$arr = pjOrderModel::factory()->whereIn('id', $_POST['record'])->findAll()->getData();
			$csv = new pjCSV();
			$csv
				->setHeader(true)
				->setName("Orders-".time().".csv")
				->process($arr)
				->download();
		}
		exit;
	}
	
	public function pjActionDeleteOrder()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjOrderModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjOrderItemModel::factory()->where('order_id', $_GET['id'])->eraseAll();
				
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteOrderBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjOrderModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjOrderItemModel::factory()->whereIn('order_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['order_create']))
			{
				$pjOrderModel = pjOrderModel::factory();
				
				$data = array();
				$data['uuid'] = time();
				$data['ip'] = pjUtil::getClientIp();
				
				if(!isset($_POST['client_id']) || (isset($_POST['client_id']) && $_POST['client_id'] == ''))
				{
					$c_data = array();
					$c_data['c_title'] = isset($_POST['c_title']) ? $_POST['c_title'] : ':NULL';
					$c_data['c_name'] = isset($_POST['c_name']) ? $_POST['c_name'] : ':NULL';
					$c_data['c_email'] = isset($_POST['c_email']) ? $_POST['c_email'] : ':NULL';
					$c_data['c_password'] = pjUtil::getRandomPassword(6);
					$c_data['c_phone'] = isset($_POST['c_phone']) ? $_POST['c_phone'] : ':NULL';
					$c_data['c_address_1'] = isset($_POST['c_address_1']) ? $_POST['c_address_1'] : ':NULL';
					$c_data['c_address_2'] = isset($_POST['c_address_2']) ? $_POST['c_address_2'] : ':NULL';
					$c_data['c_city'] = isset($_POST['c_city']) ? $_POST['c_city'] : ':NULL';
					$c_data['c_state'] = isset($_POST['c_state']) ? $_POST['c_state'] : ':NULL';
					$c_data['c_zip'] = isset($_POST['c_zip']) ? $_POST['c_zip'] : ':NULL';
					$c_data['c_country'] = isset($_POST['c_country']) ? $_POST['c_country'] : ':NULL';
					$c_data['status'] = 'T';
					
					if($c_data['c_email'] != ':NULL')
					{
						$pjClientModel = pjClientModel::factory();
						$client_id = $pjClientModel->setAttributes($c_data)->insert()->getInsertId();
						if ($client_id !== false && (int) $client_id > 0)
						{
							$data['client_id'] = $client_id;
							
							$client_arr = $pjClientModel->reset()->find($client_id)->getData();
							$tokens = pjAppController::getClientTokens($this->option_arr, $client_arr, PJ_SALT, $this->getLocaleId());
							
							$pjMultiLangModel = pjMultiLangModel::factory();
							$lang_message = $pjMultiLangModel
								->reset()
								->select('t1.*')
								->where('t1.model','pjOption')
								->where('t1.locale', $this->getLocaleId())
								->where('t1.field', 'o_email_account_message')
								->limit(0, 1)
								->findAll()
								->getData();
							$lang_subject = $pjMultiLangModel
								->reset()
								->select('t1.*')
								->where('t1.model','pjOption')
								->where('t1.locale', $this->getLocaleId())
								->where('t1.field', 'o_email_account_subject')
								->limit(0, 1)
								->findAll()
								->getData();
								
							if (count($lang_message) === 1 && count($lang_subject) === 1)
							{
								$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
								
								$Email = new pjEmail();
								if ($this->option_arr['o_send_email'] == 'smtp')
								{
									$Email
										->setTransport('smtp')
										->setSmtpHost($this->option_arr['o_smtp_host'])
										->setSmtpPort($this->option_arr['o_smtp_port'])
										->setSmtpUser($this->option_arr['o_smtp_user'])
										->setSmtpPass($this->option_arr['o_smtp_pass'])
									;
								}
								$Email->setContentType('text/html');
								$Email
									->setTo($c_data['c_email'])
									->setFrom($this->getAdminEmail())
									->setSubject($lang_subject[0]['content'])
									->send(pjUtil::textToHtml($message));
							}
						}
					}
				}
				
				switch ($_POST['type'])
				{
					case 'pickup':
						if (!empty($_POST['p_dt']))
						{
							$date_time = $_POST['p_dt'];
							if(count(explode(" ", $date_time)) == 3)
							{
								list($_date, $_time, $_period) = explode(" ", $date_time);
								$time = pjUtil::formatTime($_time . ' ' . $_period, $this->option_arr['o_time_format']);
							}else{
								list($_date, $_time) = explode(" ", $date_time);
								$time = pjUtil::formatTime($_time, $this->option_arr['o_time_format']);
							}
							unset($_POST['p_dt']);
							$data['p_dt'] = pjUtil::formatDate($_date, $this->option_arr['o_date_format']) . ' ' . $time;
						}
						if (isset($_POST['p_location_id']) && (int) $_POST['p_location_id'] > 0)
						{
							$data['location_id'] = $_POST['p_location_id'];
						}
						break;
					case 'delivery':
						if (!empty($_POST['d_dt']))
						{
							$date_time = $_POST['d_dt'];
							if(count(explode(" ", $date_time)) == 3)
							{
								list($_date, $_time, $_period) = explode(" ", $date_time);
								$time = pjUtil::formatTime($_time . ' ' . $_period, $this->option_arr['o_time_format']);
							}else{
								list($_date, $_time) = explode(" ", $date_time);
								$time = pjUtil::formatTime($_time, $this->option_arr['o_time_format']);
							}
							unset($_POST['d_dt']);
							$data['d_dt'] = pjUtil::formatDate($_date, $this->option_arr['o_date_format']) . ' ' . $time;
						}
						if (isset($_POST['d_location_id']) && (int) $_POST['d_location_id'] > 0)
						{
							$data['location_id'] = $_POST['d_location_id'];
						}
						break;
				}
				if ($_POST['payment_method'] == 'creditcard')
				{
					$data['cc_exp'] = $_POST['cc_exp_month'] . "/" . $_POST['cc_exp_year'];
				}
				
				$id = pjOrderModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
				
				if ($id !== false && (int) $id > 0)
				{
					if (isset($_POST['product_id']) && count($_POST['product_id']) > 0)
					{
						$pjOrderItemModel = pjOrderItemModel::factory();
						$pjProductPriceModel = pjProductPriceModel::factory();
						$pjProductModel = pjProductModel::factory();
						$pjExtraModel = pjExtraModel::factory();
						
						foreach ($_POST['product_id'] as $k => $pid)
						{
							$product = $pjProductModel
								->reset()
								->find($pid)
								->getData();
							if (strpos($k, 'new_') === 0)
							{
								$price = 0;
								$price_id = ":NULL";
								
								if($product['set_different_sizes'] == 'T')
								{
									$price_id = $_POST['price_id'][$k];
									$price_arr = $pjProductPriceModel
										->reset()
										->find($price_id)
										->getData();
									if($price_arr)
									{
										$price = $price_arr['price'];
									}
								}else{
									$price = $product['price'];
								}
								
								$hash = md5(uniqid(rand(), true));
								$oid = $pjOrderItemModel
									->reset()
									->setAttributes(array(
										'order_id' => $id,
										'foreign_id' => $pid,
										'type' => 'product',
										'hash' => $hash,
										'price_id' => $price_id,
										'price' => $price,
										'cnt' => $_POST['cnt'][$k]
									))
									->insert()
									->getInsertId();
								if ($oid !== false && (int) $oid > 0)
								{
									if (isset($_POST['extra_id']) && isset($_POST['extra_id'][$k]))
									{
										foreach ($_POST['extra_id'][$k] as $i => $eid)
										{
											$extra_price = 0;
											$extra_arr = $pjExtraModel
												->reset()
												->find($eid)
												->getData();
											if(!empty($extra_arr) && !empty($extra_arr['price']))
											{
												$extra_price = $extra_arr['price'];
											}
											$pjOrderItemModel
												->reset()
												->setAttributes(array(
													'order_id' => $id,
													'foreign_id' => $eid,
													'type' => 'extra',
													'hash' => $hash,
													'price_id' => ':NULL',
													'price' => $extra_price,
													'cnt' => $_POST['extra_cnt'][$k][$i]
												))
												->insert();
										}
									}
								}
							}
						}
					}
					
					$err = 'AR03';
				}else{
					$err = 'AR04';
				}
				
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminOrders&action=pjActionIndex&err=$err");
			}else{
				
				$country_arr = pjCountryModel::factory()
							->select('t1.id, t2.content AS country_title')
							->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
							->orderBy('`country_title` ASC')->findAll()->getData();
						
				$this->set('country_arr', $country_arr);
				
				$product_arr = pjProductModel::factory()
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProduct' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->select("t1.*, t2.content AS name")
					->orderBy("name ASC")
					->findAll()
					->getData();
				$this->set('product_arr', $product_arr);
				
				$location_arr = pjLocationModel::factory()
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->select("t1.*, t2.content AS name")
					->orderBy("name ASC")
					->findAll()
					->getData();
				$this->set('location_arr', $location_arr);
				
				$client_arr = pjClientModel::factory()
					->where('t1.status', 'T')
					->orderBy('t1.c_name ASC')
					->findAll()
					->getData();
				$this->set('client_arr', $client_arr);

				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				$this->appendJs('jquery-ui-sliderAccess.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminOrders.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['order_update']))
			{
				$pjOrderModel = pjOrderModel::factory();
				$pjOrderItemModel = pjOrderItemModel::factory();
				$pjProductPriceModel = pjProductPriceModel::factory();
				$pjExtraModel = pjExtraModel::factory();
				$pjProductModel = pjProductModel::factory();
				
				$arr = $pjOrderModel->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOrders&action=pjActionIndex&err=AR08");
				}
				if (isset($_POST['product_id']) && count($_POST['product_id']) > 0)
				{
					$keys = array_keys($_POST['product_id']);
					$pjOrderItemModel
						->reset()
						->where('order_id', $_POST['id'])
						->whereNotIn('hash', $keys)
						->eraseAll();
						
					$pjOrderItemModel
						->reset()
						->where('order_id', $_POST['id'])
						->where('type', 'extra')
						->eraseAll();

					foreach ($_POST['product_id'] as $k => $pid)
					{
						$product = $pjProductModel
							->reset()
							->find($pid)
							->getData();
						$price = 0;
						$price_id = ":NULL";
						
						if($product['set_different_sizes'] == 'T')
						{
							$price_id = $_POST['price_id'][$k];
							$price_arr = $pjProductPriceModel
								->reset()
								->find($price_id)
								->getData();
							if($price_arr)
							{
								$price = $price_arr['price'];
							}
						}else{
							$price = $product['price'];
						}
						if (strpos($k, 'new_') === 0)
						{
							$hash = md5(uniqid(rand(), true));
							$oid = $pjOrderItemModel
								->reset()
								->setAttributes(array(
									'order_id' => $_POST['id'],
									'foreign_id' => $pid,
									'type' => 'product',
									'hash' => $hash,
									'price_id' => $price_id,
									'price' => $price,
									'cnt' => $_POST['cnt'][$k]
								))
								->insert()
								->getInsertId();
								
							if ($oid !== false && (int) $oid > 0)
							{
								if (isset($_POST['extra_id']) && isset($_POST['extra_id'][$k]))
								{
									foreach ($_POST['extra_id'][$k] as $i => $eid)
									{
										$extra_price = 0;
										$extra_arr = $pjExtraModel
											->reset()
											->find($eid)
											->getData();
										if(!empty($extra_arr) && !empty($extra_arr['price']))
										{
											$extra_price = $extra_arr['price'];
										}
										$pjOrderItemModel
											->reset()
											->setAttributes(array(
												'order_id' => $_POST['id'],
												'foreign_id' => $eid,
												'type' => 'extra',
												'hash' => $hash,
												'price_id' => ':NULL',
												'price' => $extra_price,
												'cnt' => $_POST['extra_cnt'][$k][$i]
											))
											->insert();
									}
								}
							}
						
						} else {
							$pjOrderItemModel
								->reset()
								->where('hash', $k)
								->where('type', 'product')
								->limit(1)
								->modifyAll(array(
									'foreign_id' => $pid,
									'cnt' => $_POST['cnt'][$k],
									'price_id' => $price_id,
									'price' => $price,
								));
							if (isset($_POST['extra_id']) && isset($_POST['extra_id'][$k]))
							{
								foreach ($_POST['extra_id'][$k] as $i => $eid)
								{
									$extra_price = 0;
									$extra_arr = $pjExtraModel
										->reset()
										->find($eid)
										->getData();
									if(!empty($extra_arr) && !empty($extra_arr['price']))
									{
										$extra_price = $extra_arr['price'];
									}
									
									$pjOrderItemModel
										->reset()
										->setAttributes(array(
											'order_id' => $_POST['id'],
											'foreign_id' => $eid,
											'type' => 'extra',
											'hash' => $k,
											'price_id' => ':NULL',
											'price' => $extra_price,
											'cnt' => $_POST['extra_cnt'][$k][$i]
										))
										->insert();
									
								}
							} 
						}
					}
				} 
								
				$data = array();
				$data['ip'] = pjUtil::getClientIp();
				switch ($_POST['type'])
				{
					case 'pickup':
						if (!empty($_POST['p_dt']))
						{
							$date_time = $_POST['p_dt'];
							if(count(explode(" ", $date_time)) == 3)
							{
								list($_date, $_time, $_period) = explode(" ", $date_time);
								$time = pjUtil::formatTime($_time . ' ' . $_period, $this->option_arr['o_time_format']);
							}else{
								list($_date, $_time) = explode(" ", $date_time);
								$time = pjUtil::formatTime($_time, $this->option_arr['o_time_format']);
							}
							unset($_POST['p_dt']);
							unset($_POST['d_dt']);
							$data['p_dt'] = pjUtil::formatDate($_date, $this->option_arr['o_date_format']) . ' ' . $time;
						}
						if (isset($_POST['p_location_id']) && (int) $_POST['p_location_id'] > 0)
						{
							$data['location_id'] = $_POST['p_location_id'];
						}
						break;
					case 'delivery':
						if (!empty($_POST['d_dt']))
						{
							$date_time = $_POST['d_dt'];
							if(count(explode(" ", $date_time)) == 3)
							{
								list($_date, $_time, $_period) = explode(" ", $date_time);
								$time = pjUtil::formatTime($_time . ' ' . $_period, $this->option_arr['o_time_format']);
							}else{
								list($_date, $_time) = explode(" ", $date_time);
								$time = pjUtil::formatTime($_time, $this->option_arr['o_time_format']);
							}
							unset($_POST['p_dt']);
							unset($_POST['d_dt']);
							$data['d_dt'] = pjUtil::formatDate($_date, $this->option_arr['o_date_format']) . ' ' . $time;
						}
						if (isset($_POST['d_location_id']) && (int) $_POST['d_location_id'] > 0)
						{
							$data['location_id'] = $_POST['d_location_id'];
						}
						break;
				}
				if ($_POST['payment_method'] == 'creditcard')
				{
					$data['cc_exp'] = $_POST['cc_exp_month'] . "/" . $_POST['cc_exp_year'];
				}
				
				$pjOrderModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				$err = 'AR01';
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminOrders&action=pjActionIndex&err=$err");
			}else{
				
				$arr = pjOrderModel::factory()
					->join('pjClient', "t2.id=t1.client_id", 'left outer')
					->select('t1.*,t2.c_name as client_name')
					->find($_GET['id'])
					->getData();

				if(count($arr) <= 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminOrders&action=pjActionIndex&err=AR08");
				}
				$this->set('arr', $arr);
				
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
						
				$this->set('country_arr', $country_arr);
				
				$product_arr = pjProductModel::factory()
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProduct' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->select("t1.*, t2.content AS name, (SELECT GROUP_CONCAT(extra_id SEPARATOR '~:~') FROM `".pjProductExtraModel::factory()->getTable()."` WHERE product_id = t1.id GROUP BY product_id LIMIT 1) AS allowed_extras ")
					->orderBy("name ASC")
					->findAll()
					->toArray('allowed_extras', '~:~')
					->getData();
				$this->set('product_arr', $product_arr);
				
				$location_arr = pjLocationModel::factory()
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->select("t1.*, t2.content AS name")
					->orderBy("name ASC")
					->findAll()
					->getData();
				$this->set('location_arr', $location_arr);
				
				$extra_arr = pjExtraModel::factory()
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->select("t1.*, t2.content AS name")
					->orderBy("name ASC")
					->findAll()
					->getData();
				$this->set('extra_arr', $extra_arr);
				
				$pjProductPriceModel = pjProductPriceModel::factory();
				$oi_arr = array();
				$_oi_arr = pjOrderItemModel::factory()
					->where('t1.order_id', $arr['id'])
					->findAll()
					->getData();
				foreach ($_oi_arr as $item)
				{
					if($item['type'] == 'product')
					{
						$item['price_arr'] = $pjProductPriceModel
							->reset()
							->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProductPrice' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'price_name'", 'left')
							->select("t1.*, t2.content AS price_name")
							->where('product_id', $item['foreign_id'])
							->findAll()
							->getData();
					}
					$oi_arr[] = $item;
				}
				$this->set('oi_arr', $oi_arr);
				
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'harvest/chosen/');
				$this->appendJs('jquery-ui-sliderAccess.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'timepicker/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.noty.packaged.min.js', PJ_THIRD_PARTY_PATH . 'noty/packaged/');
				$this->appendJs('pjAdminOrders.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionPrintOrder()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$this->setLayout('pjActionPrint');
			
			$pjOrderModel = pjOrderModel::factory();
							
			$arr = $pjOrderModel
				->join('pjClient', "t2.id=t1.client_id", 'left outer')
				->select('t1.*, t2.c_title, t2.c_email, t2.c_name, t2.c_phone, t2.c_company, t2.c_address_1, t2.c_address_2, t2.c_country, t2.c_state, t2.c_city, t2.c_zip, t2.c_notes')
				->find($_GET['id'])
				->getData();
			if (empty($arr))
			{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminOrders&action=pjActionIndex&err=AR08");
			}
			
			$hash = sha1($arr['id'].$arr['created'].PJ_SALT);
			if($hash != $_GET['hash'])
			{
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminOrders&action=pjActionIndex&err=AR08");
			}
			
			
			pjAppController::addOrderDetails($arr, $this->getLocaleId());
			
			$pjMultiLangModel = pjMultiLangModel::factory();
			$lang_template = $pjMultiLangModel
				->reset()->select('t1.*')
				->where('t1.model','pjOption')
			 	->where('t1.locale', $this->getLocaleId())
			 	->where('t1.field', 'o_print_order')
			 	->limit(0, 1)
			 	->findAll()->getData();
			$template = '';											 
			if (count($lang_template) === 1)
			{
				$template = $lang_template[0]['content'];
			}									 

			$template_arr = '';
			$data = pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $this->getLocaleId());
			$template_arr = str_replace($data['search'], $data['replace'], $template);
			$this->set('template_arr', $template_arr);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionReminderEmail()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['send_email']) && isset($_POST['to']) && !empty($_POST['to']) && !empty($_POST['from']) &&
				!empty($_POST['subject']) && !empty($_POST['message']) && !empty($_POST['id']))
			{
				$Email = new pjEmail();
				$Email->setContentType('text/html');
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass']);
				}
				$r = $Email
					->setTo($_POST['to'])
					->setFrom($_POST['from'])
					->setSubject($_POST['subject'])
					->send(pjUtil::textToHtml($_POST['message']));
					
				if (isset($r) && $r)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => __('lblEmailSent', true, false)));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => __('lblFailedToSend', true, false)));
			}
			
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjOrderModel = pjOrderModel::factory();
								
				$arr = $pjOrderModel
					->join('pjClient', "t2.id=t1.client_id", 'left outer')
					->select('t1.*, t2.c_title, t2.c_email, t2.c_name, t2.c_phone, t2.c_company, t2.c_address_1, t2.c_address_2, t2.c_country, t2.c_state, t2.c_city, t2.c_zip, t2.c_notes')
					->find($_GET['id'])
					->getData();
						
				if (!empty($arr))
				{
					pjAppController::addOrderDetails($arr, $this->getLocaleId());
					
					$tokens = pjAppController::getTokens($this->option_arr, $arr, PJ_SALT, $this->getLocaleId());
					
					$pjMultiLangModel = pjMultiLangModel::factory();
					$lang_message = $pjMultiLangModel
						->reset()
						->select('t1.*')
						->where('t1.model','pjOption')
						->where('t1.locale', $this->getLocaleId())
						->where('t1.field', 'o_email_confirmation_message')
						->limit(0, 1)
						->findAll()
						->getData();
					$lang_subject = $pjMultiLangModel
						->reset()
						->select('t1.*')
						->where('t1.model','pjOption')
						->where('t1.locale', $this->getLocaleId())
						->where('t1.field', 'o_email_confirmation_subject')
						->limit(0, 1)
						->findAll()
						->getData();
								   
					if (count($lang_message) === 1 && count($lang_subject) === 1)
					{
						if ($arr['type'] == 'delivery')
						{
							$message = str_replace(array('[Delivery]', '[/Delivery]'), array('', ''), $lang_message[0]['content']);
						} else {
							$message = preg_replace('/\[Delivery\].*\[\/Delivery\]/s', '', $lang_message[0]['content']);
						}
						
						$subject_client = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
						$message_client = str_replace($tokens['search'], $tokens['replace'], $message);
						$from = !empty($this->option_arr['o_sender_email']) ? $this->option_arr['o_sender_email'] : $this->getAdminEmail();
						
						$this->set('arr', array(
							'id' => $_GET['id'],
							'client_email' => $arr['c_email'],
							'from' => $from,
							'message' => $message_client,
							'subject' => $subject_client
						));
					}
				}else{
					exit;
				}
			} else {
				exit;
			}
		}
	}
	
	public function pjActionGetExtras()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['product_id']) && (int) $_GET['product_id'] > 0)
			{
				$extra_arr = pjExtraModel::factory()
					->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
					->select("t1.*, t2.content AS name")
					->where("t1.id IN (SELECT TPE.extra_id FROM `".pjProductExtraModel::factory()->getTable()."` AS TPE WHERE TPE.product_id=".$_GET['product_id'].")")
					->orderBy("name ASC")
					->findAll()
					->getData();
				$this->set('extra_arr', $extra_arr);
			}
		}
	}
	public function pjActionGetPrices()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_GET['product_id']) && (int) $_GET['product_id'] > 0)
			{
				$arr = pjProductModel::factory()
					->find($_GET['product_id'])
					->getData();
				if (!empty($arr))
				{
					if($arr['set_different_sizes'] == 'T')
					{
						$price_arr = pjProductPriceModel::factory()
							->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjProductPrice' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'price_name'", 'left')
							->select("t1.*, t2.content AS price_name")
							->where("product_id", $_GET['product_id'])
							->orderBy("price_name ASC")
							->findAll()
							->getData();
						$this->set('price_arr', $price_arr);
					}
				}
				$this->set('arr', $arr);
			}
		}
	}
	
	public function pjActionGetTotal()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$price = 0;
			$subtotal = 0;
			$delivery = 0;
			$tax = 0;
			$total = 0;
			
			$pjProductModel = pjProductModel::factory();
			$pjProductPriceModel = pjProductPriceModel::factory();
			$pjExtraModel = pjExtraModel::factory();
			 
			$product_arr = $pjProductModel
				->whereIn("t1.id", $_POST['product_id'])
				->findAll()
				->getData();
			$extra_arr = $pjExtraModel
				->findAll()
				->getData();
				
			foreach ($_POST['product_id'] as $hash => $product_id)
			{
				foreach ($product_arr as $product)
				{
					if ($product['id'] == $product_id)
					{
						$_price = 0;
						$extra_price = 0;
						
						if($product['set_different_sizes'] == 'T')
						{
							$price_arr = $pjProductPriceModel
								->reset()
								->find($_POST['price_id'][$hash])
								->getData();
							if($price_arr)
							{
								$_price = $price_arr['price'];
							}
						}else{
							$_price = $product['price'];
						}
						
						$product_price = $_price * $_POST['cnt'][$hash];
						if (isset($_POST['extra_id']) && isset($_POST['extra_id'][$hash]))
						{
							foreach ($_POST['extra_id'][$hash] as $oi_id => $extra_id)
							{
								if (isset($_POST['extra_cnt'][$hash][$oi_id]) && (int) $_POST['extra_cnt'][$hash][$oi_id] > 0)
								{
									foreach ($extra_arr as $extra)
									{
										if ($extra['id'] == $extra_id)
										{
											$extra_price += $extra['price'] * $_POST['extra_cnt'][$hash][$oi_id];
											break;
										}
									}
								}
							}
						}
						$_price = $product_price + $extra_price;
						$price += $_price;
						break;
					}
				}
			}

			if ($_POST['type'] == 'delivery' && isset($_POST['d_location_id']) && (int) $_POST['d_location_id'] > 0)
			{
				$arr = pjPriceModel::factory()
					->where("t1.location_id", $_POST['d_location_id'])
					->where("(t1.total_from <= $price)")
					->where("(t1.total_to >= $price)")
					->findAll()
					->limit(1)
					->getData();
					
				if (count($arr) === 1)
				{
					$delivery = $arr[0]['price'];
				}
			}

			$discount = 0;
			
			if ($_POST['voucher_code'] !== false)
			{
				if ($_POST['type'] == 'delivery')
				{
					$resp = pjAppController::getDiscount($_POST, $this->option_arr);
					if ($resp['code'] == 200)
					{
						$voucher_discount = $resp['voucher_discount'];
						switch ($resp['voucher_type'])
						{
							case 'percent':
								$discount = (($subtotal + $delivery) * $voucher_discount) / 100;
								break;
							case 'amount':
								$discount = $voucher_discount;
								break;
						}
					}
				}
			}
			$subtotal = $price + $delivery - $discount;
			if(!empty($this->option_arr['o_tax_payment']))
			{
				$tax = ($subtotal * $this->option_arr['o_tax_payment']) / 100;
			}
			$total = $subtotal + $tax;
			
			$price = number_format($price, 2);
			$discount = number_format($discount, 2);
			$delivery = number_format($delivery, 2);
			$subtotal = number_format($subtotal, 2);
			$tax = number_format($tax, 2);
			$total = number_format($total, 2);
			
			pjAppController::jsonResponse(compact('price', 'discount', 'delivery', 'subtotal', 'tax', 'total'));
		}
		exit;
	}
	
	public function pjActionGetClient()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$client_arr = pjClientModel::factory()->find($_GET['id'])->getData();
			pjAppController::jsonResponse($client_arr);
		}
		exit;
	}
}
?>