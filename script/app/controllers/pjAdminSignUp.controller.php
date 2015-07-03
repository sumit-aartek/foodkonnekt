<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}

class pjAdminSignUp extends pjAppController
{
	public function pjActionCreate()
	{
		$this->setLayout('pjAdminSignUpLayout');
		
		if (isset($_POST['user_singup']))
		{
			$data = array();
			$data['is_active'] = 'T';
			$data['ip'] = $_SERVER['REMOTE_ADDR'];			
			$id = pjUserModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
			if ($id !== false && (int) $id > 0)
			{
				$err = 'AU03';
			} else {
				$err = 'AU04';
			}
			
			//Let's check when or not coming from clover.
			if (empty($_SESSION['cloverData'])) {
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
			} else {
				$_SESSION['user_id'] = $id;
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminSignUp&action=pjActionMain&case=signup");
			}
		} else {
			
			$this->set('role_arr', pjRoleModel::factory()->orderBy('t1.id ASC')->findAll()->getData());

			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminUsers.js');
		}
	}
	
	public function pjActionClover()		
	{
		$this->setLayout('pjAdminSingUpLayout');
		$data = array(
			'merchant_id' => $_GET['merchant_id'],
			'employee_id' => $_GET['employee_id'],
			'client_id' => $_GET['client_id']
		);
		$this->set('arr', $data);
        
        $this->appendJs('pjActionAccessToken.js');		
	}
	
	public function pjActionForm()
	{		
		$this->setLayout('pjAdminSingUpLayout');
	}
	
	public function pjActionMain()
	{
		$this->setLayout('pjAdminSingUpLayout');
	}	
	/*-------------------- This time not use below function -----------------------*/
	public function pjActionCloverApiData()
	{
		//Get clover API Data
		/*$user_id = $_SESSION['user_id'];
		$merchant_id = $_SESSION['cloverData']['merchant_id'];
		$employee_id = $_SESSION['cloverData']['employee_id'];
		$client_id = $_SESSION['cloverData']['client_id'];
		$access_token = $_SESSION['cloverData']['access_token'];
		$name = $_SESSION['cloverData']['name'];
		$address1 = $_SESSION['cloverData']['address1'];
		$address2 = $_SESSION['cloverData']['address2'];	
		$address3 = $_SESSION['cloverData']['address3'];
		$city = $_SESSION['cloverData']['city'];
		$country = $_SESSION['cloverData']['country'];
		$state = $_SESSION['cloverData']['state'];
		$zip = $_SESSION['cloverData']['zip'];*/
		
		$user_id = 9;
		$merchant_id = 'HKADRSA0HKDYC';
		$employee_id = 'T03TVG9M6C7PG';
		$client_id = 'XZRJ9TRX6DSV6';
		$access_token = '1d0fb9a1-c3ef-43de-d512-8eb13b802611';
		$name = 'mammohan';
		$address1 = '178';
		$address2 = 'Vijay nagar';
		$address3 = '';
		$city = 'Indore';
		$country = 'India';
		$state = 'MP';
		$zip = '75012';
		
		$address = $address1.' '.$address2.' '.$address3.' '.$city.' '.$state.' '.$country.'-'.$zip;
		
		//Check merchant_id/clover_mid exists or not.
		/*$pjLocationMapModel = pjLocationMapModel::factory()
			->where('CLOVER_MID', $merchant_id);
		$size = $pjLocationMapModel->findCount()->getData();*/
		$size = 0;
		if($size == 0)
		{
			/*//Insert data into location_map table.
			$data = array();
			$data['lat'] = '32.811043';
			$data['lng'] = '-96.770760';
			$data['user_id'] = $user_id;
			$lmm_id = pjLocationMapModel::factory($data)->insert()->getInsertId();
			
			//Get locations max(id)/last id.
			$pjLocationModel = pjLocationModel::factory()
				->select('id')
				->orderBy('id DESC')
				->limit(1)->findAll()->getData();
			$location_id = $pjLocationModel[0]['id'];
			
			//Insert data into multi lang table.
			$multiLangDataName = array(
				'foreign_id' => $location_id,
				'model' => 'pjLocation',
				'locale' => '4',
				'field' => 'name',
				'content' => $name,
				'source' => 'script'
			);
			$ml_name = pjMultiLangModel::factory($multiLangDataName)->insert()->getInsertId();
			$multiLangDataAdd = array(
				'foreign_id' => $location_id,
				'model' => 'pjLocation',
				'locale' => '4',
				'field' => 'address',
				'content' => $address,
				'source' => 'script'
			);
			$ml_add = pjMultiLangModel::factory($multiLangDataAdd)->insert()->getInsertId();
			
			//Insert data into merchant table.
			$merchantData = array(
				'merchant_name' => $name,
				'merchant_address' => $address,
				'user_id' => $user_id
			);
			$mer_id = pjMerchantModel::factory($merchantData)->insert()->getInsertId();
			
			//Insert data into location_map table.
			$lmapData = array(
				'PHPJABBER_LOCATION_ID' => $location_id,
				'CLOVER_MID' => $merchant_id,
				'CLOVER_ACCESS_TOKEN_ID' => $access_token
			);
			$lmap_id = pjLocationMapModel::factory($lmapData)->insert()->getInsertId();*/
			
			//Get all categories from clover.
			$clover_cate_response = file_get_contents("https://api.clover.com:443/v3/merchants/".$merchant_id."/categories?access_token=".$access_token);
			$clover_cate_arr = json_decode($clover_cate_response);
			$clover_cate_arr = $clover_cate_arr->elements;
			foreach($clover_cate_arr as $clover_category) {
				//Check clover category exists or not in db.
				$pjCategoryMapModel = pjCategoryMapModel::factory()
					->where('clover_category_id', $clover_category->id)
					->where('clover_mid', $merchant_id);
				$category_count = $pjCategoryMapModel->findCount()->getData();
				echo 'count:'.$category_count;
			}
		}
	}
}
?>