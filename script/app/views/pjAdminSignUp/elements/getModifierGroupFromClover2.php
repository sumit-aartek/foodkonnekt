<?php
$servername = "gator4131.hostgator.com";
$username = "desisaud_foodDB";
$password = "Sum!t123";
$dbname = "desisaud_onlineorder";
/*
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
} 
	$sqlDelete = "DELETE FROM myshoppingkartfood_delivery_modifier_group";
	$result = $conn->query($sqlDelete);
	
$conn->close();

*/
$response1 = file_get_contents("https://api.clover.com:443/v3/merchants/".$CLOVER_MID."/modifier_groups?access_token=".$ACCESS_TOKEN);
echo "<br> for modifier group $response1".$response1;
$result=json_decode($response1);
foreach($result->elements as $data) {
	echo'<br>==>';
		echo "{$data->id} <br/>";
        echo "{$data->name} <br/>";
	

		
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT ModifierGroup_CloverId FROM myshoppingkartfood_delivery_modifier_group WHERE ModifierGroup_CloverId='".$data->id."' AND Merchant_Id='".$CLOVER_MID."' LIMIT 1";
$result = $conn->query($sql);
//$PHPJABBER_CATEGORY_ID='';
$size=$result->num_rows;
echo "size=".$size;
if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
     //    echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
  // $PHPJABBER_CATEGORY_ID=$row["PHPJABBER_CATEGORY_ID"];
     }
} else {
     echo "0 results";
}

$conn->close();
		// echo "<br>PHPJABBER_PRODUCT_ID=".$PHPJABBER_CATEGORY_ID;
		if($size==0){
echo '<br>creating new===>';
		
			// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
		$sql = "INSERT INTO myshoppingkartfood_delivery_modifier_group (ModifierGroup_Name, ModifierGroup_CloverId, Merchant_Id,user_id)VALUES('".$data->name."', '".$data->id."', '".$CLOVER_MID."','".$user_id."')";

		if ($conn->query($sql) === TRUE) {
		echo "<br>New records created successfully in clover_phpjabber_lineitem_product_map";
		} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
		}

		$conn->close();
		
		
		}
		else{
		echo '<br>Updating===>';

	

		
		
					// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
			$sql = "UPDATE myshoppingkartfood_delivery_modifier_group SET ModifierGroup_Name = '".$data->name."' WHERE ModifierGroup_CloverId = '".$data->id."' and Merchant_Id='".$CLOVER_MID."'";

if ($conn->query($sql) === TRUE) {
    echo "content Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}
		$conn->close();	

		
		}
		
		
		
		
}

echo "<br><br><<<<<<<<<<   ******  Adding Extras>>>>>>>>>>>>><br><br>";
include 'getExtraFromClover3.php';

?>