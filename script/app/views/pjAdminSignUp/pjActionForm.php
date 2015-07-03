<?php
//Get value from clover form.
$merchant_id = $_POST['merchant_id'];
$employee_id = $_POST['employee_id'];
$client_id = $_POST['client_id'];
$access_token = $_POST['access_token_key'];

//Get anchor url.		
/*$anchoururl = $_COOKIE['anchor'];
$anchoururlSplit = explode("=", $anchoururl);
$access_token = $anchoururlSplit[1]; // piece2*/

$response1 = file_get_contents("https://www.clover.com/v3/merchants/$merchant_id/address?access_token=$access_token");
$data=json_decode($response1);
$address1=$data->address1;
$address2=$data->address2;
$address3=$data->address3;
$city=$data->city;
$country=$data->country;
$state=$data->state;
$zip=$data->zip;

$response2 = file_get_contents("https://api.clover.com/v3/merchants/$merchant_id?access_token=$access_token");
$data2=json_decode($response2);
$name=$data2->name;

$cloverData = array(
	'merchant_id' => $merchant_id,
	'employee_id' => $employee_id,
	'client_id' => $client_id,
	'access_token' => $access_token,
	'name' => $name,
	'address1' => $address1,
	'address2' => $address2,
	'address3' => $address3,
	'city' => $city,
	'state' => $state,
	'country' => $country,
	'zip' => $zip
);

//Set clover data for globally.		
$_SESSION['cloverData'] = $cloverData;

//Let's check merchant id exists into db.
$pjLocationMapModel = pjLocationMapModel::factory()
	->where('CLOVER_MID', $merchant_id);
$count = $pjLocationMapModel->findCount()->getData();

if ($count == 0){
	echo "<script>
		window.location = '".PJ_INSTALL_URL."index.php?controller=pjAdminSignUp&action=pjActionCreate';
	</script>";
} else {
	echo "<script>
		window.location = '".PJ_INSTALL_URL."index.php?controller=pjAdmin&action=pjActionLogin';
	</script>";
}

//include PJ_VIEWS_PATH . 'pjAdminSignUp/elements/manage.php';
?>