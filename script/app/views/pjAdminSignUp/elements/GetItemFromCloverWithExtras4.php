<?php


$servername = "gator4131.hostgator.com";
$username = "desisaud_foodDB";
$password = "Sum!t123";
$dbname = "desisaud_onlineorder";

$response1 = file_get_contents("https://api.clover.com:443/v3/merchants/".$CLOVER_MID."/items?expand=modifierGroups&access_token=".$ACCESS_TOKEN);
//echo "<br> response1".$response1;
$PHPJABBER_PRODUCT_ID='';
$modifireId='';
$result=json_decode($response1);
//echo "<br/><br/><br/> print href:";
//echo $result->href;


foreach($result->elements as $data) {
	
	echo'<br>==>';
		////echo "{$data->id} <br/>";
    echo "{$data->name} <br/>";
	//	//echo "{$data->price} <br/>";
		
	 $price=$data->price;
	  $price= $price/100;
		//echo  '<br>Item Id: '.$data->id.'<br>';
	
		
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT PHPJABBER_PRODUCT_ID FROM clover_phpjabber_lineitem_product_map WHERE CLOVER_LINE_ITEM_ID='".$data->id."' AND CLOVER_MID='".$CLOVER_MID."' LIMIT 1";
$result = $conn->query($sql);

$size=$result->num_rows;
//echo "size=".$size;
if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
     //    //echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
   $PHPJABBER_PRODUCT_ID=$row["PHPJABBER_PRODUCT_ID"];
   //echo "if :".$PHPJABBER_PRODUCT_ID;
     }
} else {
   //  echo "0 results";
}

$conn->close();

		echo "<br>PHPJABBER_PRODUCT_ID=".$PHPJABBER_PRODUCT_ID;
		if($size==0){

		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
		$sql = "INSERT INTO myshoppingkartfood_delivery_products (set_different_sizes, price,is_featured,user_id)VALUES('F','".$price."','0','".$user_id."')";

		if ($conn->multi_query($sql) === TRUE) {
		echo "<br>New records created successfully";
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

$sql = "SELECT id FROM myshoppingkartfood_delivery_products ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$maxproductid='';

if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
     //    //echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
   $maxproductid=$row["id"];
    $PHPJABBER_PRODUCT_ID=$maxproductid;
  //echo $maxproductid;
  
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
		$sql = "INSERT INTO myshoppingkartfood_delivery_multi_lang(foreign_id,model,locale,FIELD,content,source)VALUES('".$maxproductid."','pjProduct','4','name','".$data->name."','data');";

		if ($conn->multi_query($sql) === TRUE) {
		echo "<br>New records created successfully in multy lang";
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
		$sql = "INSERT INTO clover_phpjabber_lineitem_product_map (CLOVER_LINE_ITEM_ID, PHPJABBER_PRODUCT_ID,CLOVER_MID,user_id)VALUES('".$data->id."','".$maxproductid."','".$CLOVER_MID."','".$user_id."');";

		if ($conn->multi_query($sql) === TRUE) {
		echo "<br>New records created successfully in clover_phpjabber_lineitem_product_map";
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

$sql = "SELECT PHPJABBER_PRODUCT_ID FROM clover_phpjabber_lineitem_product_map WHERE CLOVER_LINE_ITEM_ID='".$data->id."' AND CLOVER_MID='".$CLOVER_MID."' LIMIT 1";
$result = $conn->query($sql);

$size=$result->num_rows;
//echo "size=".$size;
if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
     //    //echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
   $PHPJABBER_PRODUCT_ID=$row["PHPJABBER_PRODUCT_ID"];
  
 
     }
} else {
     //echo "0 results";
}

$conn->close();
		
		
		
		
		
			$response2 = file_get_contents("https://api.clover.com:443/v3/merchants/".$CLOVER_MID."/items/".$data->id."/categories/?access_token=".$ACCESS_TOKEN);
//echo "<br>response2".$response2;

$result2=json_decode($response2);
		
		foreach($result2->elements as $data2) {
	
	//echo'<br>==>';
		//echo "{$data2->id} <br/>";
		echo '<br>data2->name===>';
        //echo "{$data2->name} <br/>";
	
				// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
} 
		$sql = "SELECT PHPJABBER_CATEGORY_ID FROM clover_phpjabber_category_map WHERE CLOVER_CATEGORY_ID='".$data2->id."' LIMIT 1";
$result = $conn->query($sql);
$PHPJABBER_CATEGORY_ID='';
$size2=$result->num_rows;
//echo "size=".$size2;
if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
     //    //echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
   $PHPJABBER_CATEGORY_ID=$row["PHPJABBER_CATEGORY_ID"];
 
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
		$sql = "INSERT INTO myshoppingkartfood_delivery_products_categories (product_id, category_id,user_id) VALUES('".$PHPJABBER_PRODUCT_ID."','".$PHPJABBER_CATEGORY_ID."','".$user_id."')";

		if ($conn->multi_query($sql) === TRUE) {
		echo "<br>New records created successfully in myshoppingkartfood_delivery_products_categories";
		} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
		}
	

		$conn->close();
		
		
		}
		
		
		}
		
		
		else{ 


         
			echo "<br> PHPJABBER_PRODUCT_ID-->".$PHPJABBER_PRODUCT_ID;
				// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
			$sql = "UPDATE myshoppingkartfood_delivery_products SET price = '".$price."' WHERE id = '".$PHPJABBER_PRODUCT_ID."'";

if ($conn->query($sql) === TRUE) {
    echo "<br>price Record updated successfully";
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
			$sql = "UPDATE myshoppingkartfood_delivery_multi_lang SET content = '".$data->name."',locale='4',source='data' WHERE foreign_id = '".$PHPJABBER_PRODUCT_ID."' AND model='pjProduct'";

if ($conn->query($sql) === TRUE) {
    echo "content Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}
		$conn->close();	
		
			
		}
		
	
	
	$conn = new mysqli($servername, $username, $password, $dbname);
		
		
			foreach($data->modifierGroups->elements as $modifireData) {
		        $modifire=$modifireData->modifierIds;
		        $array = explode(",", $modifire);
		        $i = 0;
		       foreach($array as $modifireId)
		       {
		       		echo "\$array[$i]  => $modifireId.\n";
		       		echo $PHPJABBER_PRODUCT_ID;
    				
		       $sql = "SELECT PHPJABBER_EXTRA_ID FROM clover_phpjabber_modifire_extra_map WHERE CLOVER_MODIFIRE_ID='".$modifireId."' LIMIT 1";
                       
                       $result = $conn->query($sql);
$PHPJABBER_EXTRA_ID='';

if ($result->num_rows > 0) {
    
     while($row = $result->fetch_assoc()) {
     
    $PHPJABBER_EXTRA_ID=$row["PHPJABBER_EXTRA_ID"];
 
     }
     
      $sql = "SELECT * FROM myshoppingkartfood_delivery_products_extras WHERE extra_id='".$PHPJABBER_EXTRA_ID."' AND product_id= '".$PHPJABBER_PRODUCT_ID."' LIMIT 1";
                       
                       $result = $conn->query($sql);

if ($result->num_rows == 0) {
$sql = "INSERT INTO myshoppingkartfood_delivery_products_extras (product_id, extra_id,user_id) VALUES('".$PHPJABBER_PRODUCT_ID."','".$PHPJABBER_EXTRA_ID."','".$user_id."')";
$conn->query($sql);
}

   $i++;
		       }
                        
		
                           // echo "<br> PHPJABBER_EXTRA_ID -->". $PHPJABBER_EXTRA_ID;
                            // echo "<br> PHPJABBER_PRODUCT_ID-->".$PHPJABBER_PRODUCT_ID;
                             
		                       }
		        
		               
                          
			
			}
	
	
	$conn->close();	
	
	
		
		
		
}


//close connection


?>