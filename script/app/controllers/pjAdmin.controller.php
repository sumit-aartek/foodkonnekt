<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdmin extends pjAppController
{
	public $defaultUser = 'admin_user';
	
	public $requireLogin = true;
	
	public function __construct($requireLogin=null)
	{
		$this->setLayout('pjActionAdmin');
		
		if (!is_null($requireLogin) && is_bool($requireLogin))
		{
			$this->requireLogin = $requireLogin;
		}
		
		if ($this->requireLogin)
		{
			if (!$this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin', 'pjActionForgot', 'pjActionPreview')))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
			}
		}
	}
	
	public function afterFilter()
	{
		parent::afterFilter();
		if ($this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin')))
		{
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		}
	}
	
	public function beforeRender()
	{
		
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$user_id = $_SESSION['admin_user']['id'];
			$pjOrderModel = pjOrderModel::factory();
			
			$cnt_delivery_orders = $pjOrderModel
				->where('user_id', $user_id)
				->where("type", "delivery")
				->where("DATE(t1.d_dt) = CURDATE()")
				->findCount()
				->getData();
			$amount_delivery_orders = $pjOrderModel
				->reset()
				->select("SUM(total) AS amount")
				->where("type", "delivery")
				->where('user_id', $user_id)
				->where("DATE(t1.d_dt) = CURDATE()")
				->findAll()
				->getData();
			$cnt_pickup_orders = $pjOrderModel
				->reset()
				->where('user_id', $user_id)
				->where("type", "pickup")
				->where("DATE(t1.p_dt) = CURDATE()")
				->findCount()
				->getData();
			$amount_pickup_orders = $pjOrderModel
				->reset()
				->select("SUM(total) AS amount")
				->where('user_id', $user_id)
				->where("type", "pickup")
				->where("DATE(t1.p_dt) = CURDATE()")
				->findAll()
				->getData();	
			
			$cnt_orders = $pjOrderModel
				->reset()
				->where('user_id', $user_id)
				->findCount()
				->getData();
			$amount_orders = $pjOrderModel
				->reset()
				->select("SUM(total) AS amount")
				->where('user_id', $user_id)
				->findAll()
				->getData();
			
			$this->set('cnt_delivery_orders', $cnt_delivery_orders);
			$this->set('amount_delivery_orders', !empty($amount_delivery_orders) ? $amount_delivery_orders[0]['amount'] : 0);
			$this->set('cnt_pickup_orders', $cnt_pickup_orders);
			$this->set('amount_pickup_orders', !empty($amount_pickup_orders) ? $amount_pickup_orders[0]['amount'] : 0);
			$this->set('cnt_orders', $cnt_orders);
			$this->set('amount_orders', !empty($amount_orders) ? $amount_orders[0]['amount'] : 0);
			
			$latest_delivery = $pjOrderModel
				->reset()
				->join('pjClient', "t2.id=t1.client_id", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->select('t1.*, t2.c_name as client_name, t3.content as location')
				->where("type", "delivery")
				->where('t1.user_id', $user_id)
				->orderBy("d_dt DESC")
				->limit(6)
				->findAll()
				->getData();
			
			$latest_pickup = $pjOrderModel
				->reset()
				->join('pjClient', "t2.id=t1.client_id", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->select('t1.*, t2.c_name as client_name, t3.content as location')
				->where('t1.user_id', $user_id)
				->where("type", "pickup")
				->orderBy("p_dt DESC")
				->limit(6)
				->findAll()
				->getData();
				
			$this->set('latest_delivery', $latest_delivery);
			$this->set('latest_pickup', $latest_pickup);
			
			$location_arr = pjWorkingTimeModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.location_id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->select('t1.*, t2.content as location_title')
				//->where('t1.user_id', $user_id)
				->findAll()
				->getData();
			$week_day = strtolower(date("l"));
			$current_time = date('H:i:s');
			foreach($location_arr as $k => $v)
			{
				if($v['p_' . $week_day . '_from'] <= $current_time && $current_time <= $v['p_' . $week_day . '_to'])
				{
					$v['pickup'] = __('lblOpened', true);
				}else{
					$v['pickup'] = __('lblClosed', true);
				}
				if($v['d_' . $week_day . '_from'] <= $current_time && $current_time <= $v['d_' . $week_day . '_to'])
				{
					$v['delivery'] = __('lblOpened', true);
				}else{
					$v['delivery'] = __('lblClosed', true);
				}
				$location_arr[$k] = $v;
			}
			
			$this->set('location_arr', $location_arr);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionForgot()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['forgot_user']))
		{
			if (!isset($_POST['forgot_email']) || !pjValidation::pjActionNotEmpty($_POST['forgot_email']) || !pjValidation::pjActionEmail($_POST['forgot_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			}
			$pjUserModel = pjUserModel::factory();
			$user = $pjUserModel
				->where('t1.email', $_POST['forgot_email'])
				->limit(1)
				->findAll()
				->getData();
				
			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			} else {
				$user = $user[0];
				
				$Email = new pjEmail();
				$Email
					->setTo($user['email'])
					->setFrom($user['email'])
					->setSubject(__('emailForgotSubject', true));
				
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
				
				$body = str_replace(
					array('{Name}', '{Password}'),
					array($user['name'], $user['password']),
					__('emailForgotBody', true)
				);

				if ($Email->send($body))
				{
					$err = "AA11";
				} else {
					$err = "AA12";
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=$err");
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionMessages()
	{
		$this->setAjax(true);
		header("Content-Type: text/javascript; charset=utf-8");
	}
	
	public function pjActionLogin()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['login_user']))
		{
			if (!isset($_POST['login_email']) || !isset($_POST['login_password']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_email']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_password']) ||
				!pjValidation::pjActionEmail($_POST['login_email']))
			{				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=4");
			}
			$pjUserModel = pjUserModel::factory();

			$user = $pjUserModel
				->where('t1.email', $_POST['login_email'])
				->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", pjObject::escapeString($_POST['login_password']), PJ_SALT))
				->limit(1)
				->findAll()
				->getData();			
			
			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=1");
			} else {
				$user = $user[0];
				unset($user['password']);
				if (!in_array($user['role_id'], array(1,2,3)))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['role_id'] == 3 && $user['is_active'] == 'F')
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['status'] != 'T')
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=3");
				}
				
				$last_login = date("Y-m-d H:i:s");
    			$_SESSION[$this->defaultUser] = $user;    			
    			$data = array();
    			$data['last_login'] = $last_login;
    			$pjUserModel->reset()->setAttributes(array('id' => $user['id']))->modify($data);

    			if ($this->isAdmin() || $this->isEditor())
    			{
					//Let's check get data from clover.
					if (array_key_exists('cloverData', $_SESSION)) {
						pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminSignUp&action=pjActionMain&case=login");
					}
	    			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");
    			}
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionLogout()
	{
		if ($this->isLoged())
        {
        	unset($_SESSION[$this->defaultUser]);
        }
       	pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
	}
	
	public function pjActionProfile()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin())
		{
			if (isset($_POST['profile_update']))
			{
				$pjUserModel = pjUserModel::factory();
				$arr = $pjUserModel->find($this->getUserId())->getData();
				$data = array();
				$data['role_id'] = $arr['role_id'];
				$data['status'] = $arr['status'];
				$post = array_merge($_POST, $data);
				if (!$pjUserModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA14");
				}
				$pjUserModel->set('id', $this->getUserId())->modify($post);
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA13");
			} else {
				$this->set('arr', pjUserModel::factory()->find($this->getUserId())->getData());
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdmin.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
    
    public function pjActionToken()
    {
        print_r($_GET);
    }
	
}
?>