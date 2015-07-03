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
	$sqlDelete = "DELETE FROM myshoppingkartfood_delivery_extras";
	$result = $conn->query($sqlDelete);
	$sqlDelete = "DELETE FROM myshoppingkartfood_delivery_multi_lang WHERE model = pjExtra";
	$result = $conn->query($sqlDelete);
	$sqlDelete = "DELETE FROM clover_phpjabber_modifire_extra_map";
	$result = $conn->query($sqlDelete);
	
$conn->close();
*/


$response1 = file_get_contents("https://api.clover.com:443/v3/merchants/".$CLOVER_MID."/modifiers?access_token=".$ACCESS_TOKEN);
$result=json_decode($response1);
echo "<br> for modifiers url".$response1;
foreach($result->elements as $data) {
	
 $price=$data->price;
	  $price= $price/100;
		echo  $price.'<br>';
$modifierGroupId=$data->modifierGroup->id;		
echo "<br>=Modifier Group id from clover==".$modifierGroupId;
//$modifierGroupId='J7ZC4KECB9Z3T';
 $PhpJabberModifierGroup_Id='';		
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT PHPJABBER_EXTRA_ID FROM clover_phpjabber_modifire_extra_map WHERE CLOVER_MODIFIRE_ID='".$data->id."' AND CLOVER_MID='".$CLOVER_MID."' LIMIT 1";
$result = $conn->query($sql);
$PHPJABBER_EXTRA_ID='';

$size=$result->num_rows;
echo "<br>size=########################".$size;
if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
     //    //echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
   $PHPJABBER_EXTRA_ID=$row["PHPJABBER_EXTRA_ID"];
 
     }
} else {
     //echo "0 results";
}

$conn->close();
// Start It is return by sandeep for select phpjabber id

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT ModifierGroup_Id FROM `myshoppingkartfood_delivery_modifier_group` WHERE ModifierGroup_CloverId ='".$modifierGroupId."'";
$result = $conn->query($sql);
//$size=$result->num_rows;
//echo "<br>number of rows from php jabbers==".$size;
if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
     
   $PhpJabberModifierGroup_Id =$row["ModifierGroup_Id"];
 echo "<br>phpModifierGroupId==".$PhpJabberModifierGroup_Id;
     }
} else {
     //echo "0 results";
}
// End It is return by sandeep for select phpjabber id





		 echo "<br>PHPJABBER_EXTRA_ID=".$PHPJABBER_EXTRA_ID;
		 echo "########*****************#######".$PhpJabberModifierGroup_Id;
		if($size==0){

		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
		
		die("Connection failed: " . $conn->connect_error);
		}
		echo "###############".$PhpJabberModifierGroup_Id;
		$sql = "INSERT INTO myshoppingkartfood_delivery_extras (price,ModifierGroup_Id,user_id)VALUES('".$price."','".$PhpJabberModifierGroup_Id."','".$user_id."')";

		if ($conn->query($sql) === TRUE) {
		echo "<br><br>New records created successfully";
		} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
		}

		$conn->close();
		
		
		

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT id FROM myshoppingkartfood_delivery_extras ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$maxExtraId='';

if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
     //    //echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
   $maxExtraId=$row["id"];
  //echo $maxExtraId;
     }
} else {
     //echo "0 results";
}

$conn->close();
		
	


		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
		$sql = "INSERT INTO myshoppingkartfood_delivery_multi_lang(foreign_id,model,locale,FIELD,content,source)VALUES('".$maxExtraId."','pjExtra','4','name','".$data->name."','data');";

		if ($conn->query($sql) === TRUE) {
		echo "New records created successfully in multy lang";
		} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
		}

		$conn->close();

	
		
			// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
		$sql = "INSERT INTO clover_phpjabber_modifire_extra_map (PHPJABBER_EXTRA_ID, CLOVER_MODIFIRE_ID, CLOVER_MID, CLOVER_MODIFIRE_GROUP_ID,user_id)VALUES('".$maxExtraId."','".$data->id."','".$CLOVER_MID."','".$data->modifierGroup->id."','".$user_id."');";
		if ($conn->query($sql) === TRUE) {
		echo "<br>New records created successfully in clover_phpjabber_modifire_extra_map";
		} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
		}

		$conn->close();
		
		
		
	
		}
		else{
				// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
			$sql = "UPDATE myshoppingkartfood_delivery_extras SET price = '".$price."',ModifierGroup_Id='".$PhpJabberModifierGroup_Id."'  WHERE id = '".$PHPJABBER_EXTRA_ID."'";

if ($conn->query($sql) === TRUE) {
    echo "price Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}
		$conn->close();	

		
		
					// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
			$sql = "UPDATE myshoppingkartfood_delivery_multi_lang SET content = '".$data->name."' WHERE foreign_id = '".$PHPJABBER_EXTRA_ID."' AND model='pjExtra'";

if ($conn->query($sql) === TRUE) {
    echo "content Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}
		$conn->close();	
			
		}
		
}
echo "<br><br><<<<<<<<<<<==Adding php 4 >>>>>>>>>>>>>><br><br>";
		include 'GetItemFromCloverWithExtras4.php';
?>