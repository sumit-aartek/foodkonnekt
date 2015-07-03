mohan
<?php
$servername = "gator4131.hostgator.com";
$username = "desisaud_foodDB";
$password = "Sum!t123";
$dbname = "desisaud_onlineorder";
// MID and AccessTokenID for TacosYMas Ross Avenue

$CLOVER_MID='';
$ACCESS_TOKEN='';
echo '###########'.$merchant_id;
exit;
if($merchant_id==''){
$CLOVER_MID="RJV9QTEG55DC6";
$ACCESS_TOKEN="1547138e-978d-ecb0-77d0-3fb5bc342eda";
}else{
echo '<<<<<<<<<<<<<<<<<<<<<....THROUGH SESSION......>>>>>>>>>>>>>>>>>>>>>>>';
$CLOVER_MID=$merchant_id;
$ACCESS_TOKEN=$access_token;
$location_id= $_SESSION['location_id'];
}







$response1 = file_get_contents("https://api.clover.com:443/v3/merchants/".$CLOVER_MID."/categories?access_token=".$ACCESS_TOKEN);
//echo $response1;

$result=json_decode($response1);
//echo "<br/><br/><br/> print href:";
//echo $result->href;

echo "<br/><br/><br/> print elements:<br/>";
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

echo "now getting jabber cate id";
$sql = "SELECT PHPJABBER_CATEGORY_ID FROM clover_phpjabber_category_map WHERE CLOVER_CATEGORY_ID='".$data->id."' AND CLOVER_MID='".$CLOVER_MID."' LIMIT 1";
//echo "<br>sql query".$sql;
$result = $conn->query($sql);
$PHPJABBER_CATEGORY_ID='';
$size=$result->num_rows;
$PHPJABBER_CATEGORY_ID='';
echo "size=".$size;
if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
       echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
   $PHPJABBER_CATEGORY_ID=$row["PHPJABBER_CATEGORY_ID"];
}
}
$conn->close();
		 echo "<br>PHPJABBER_PRODUCT_ID=".$PHPJABBER_CATEGORY_ID;
		if($size==0){
echo '<br>creating new===>';
		
			// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
		$sql = "INSERT INTO myshoppingkartfood_delivery_categories(STATUS,location_id,user_id) VALUES( 'T','".$location_id."','".$user_id."')";

		if ($conn->multi_query($sql) === TRUE) {
		echo "New records created successfully";
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

$sql = "SELECT 	id FROM myshoppingkartfood_delivery_categories ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$maxcategoryid='';

if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
     //    echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
   $maxcategoryid=$row["id"];
  echo $maxcategoryid;
     }
} else {
     echo "0 results";
}

$conn->close();
		
		
		
			// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
		$sql = "INSERT INTO myshoppingkartfood_delivery_multi_lang(foreign_id,model,locale,FIELD,content,source)VALUES('".$maxcategoryid."','pjCategory','4','name','".$data->name."','data');";

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
		$sql = "INSERT INTO clover_phpjabber_category_map (CLOVER_CATEGORY_ID, PHPJABBER_CATEGORY_ID,CLOVER_MID,user_id) VALUES('".$data->id."','".$maxcategoryid."','".$CLOVER_MID."','".$user_id."')";

		if ($conn->multi_query($sql) === TRUE) {
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
			$sql = "UPDATE myshoppingkartfood_delivery_multi_lang SET content = '".$data->name."' WHERE foreign_id = '".$PHPJABBER_CATEGORY_ID."' AND model='pjCategory'";

if ($conn->query($sql) === TRUE) {
    echo "content Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}
		$conn->close();	

}
echo "<br><br><br><br><br><br>";		
}
echo ">>>>>>>>>>>>Adding modifier group php1>>>>>>>>>>>>>>>>";
include 'getModifierGroupFromClover2.php';		
?>
		
	