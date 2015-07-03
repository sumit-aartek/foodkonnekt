<?php
echo '<pre>';
print_r($_SESSION['cloverData']);
echo '</pre>';

//Define DB Details-----------------------------
$servername   = "localhost";
$username     = "desisaud_foodkon";
$password     = "PK5LeRWz;(9W";
$dbname       = "desisaud_foodkonnekt";

//Define user id
$user_id = '';
if(isset($_SESSION['user_id'])) {
	echo 'u='.$user_id = $_SESSION['user_id'];
} else {
	echo 'u='.$user_id = $_SESSION['admin_user']['id'];
}
  
//Set globally clover data----------------------
$case         = $_GET['case'];
$merchant_id  = $_SESSION['cloverData']['merchant_id'];
$employee_id  = $_SESSION['cloverData']['employee_id'];
$client_id    = $_SESSION['cloverData']['client_id'];
$access_token = $_SESSION['cloverData']['access_token'];
$name         = $_SESSION['cloverData']['name'];
$address1     = $_SESSION['cloverData']['address1'];
$address2     = $_SESSION['cloverData']['address2'];
$address3     = $_SESSION['cloverData']['address3'];
$city         = $_SESSION['cloverData']['city'];
$country      = $_SESSION['cloverData']['country'];
$state        = $_SESSION['cloverData']['state'];
$zip          = $_SESSION['cloverData']['zip'];

//Create DB connection----------------------------
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM foodkonnektfood_delivery_locations_map WHERE CLOVER_MID='" . $merchant_id . "'";
$result = $conn->query($sql);
$size   = $result->num_rows;
$location_id = '';

if ($size == 0)
{
    $sql = "INSERT INTO foodkonnektfood_delivery_locations(lat, lng,user_id) VALUES ('32.811043','-96.770760','" . $user_id . "')";
    if ($conn->query($sql) === TRUE) {
		$location_id = $conn->insert_id;
		$_SESSION['location_id'] = $location_id;
        echo "<br />New records created successfully in foodkonnektfood_delivery_locations = ". $location_id.'<br />';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    
    
    $sql = "INSERT INTO foodkonnektfood_delivery_multi_lang( foreign_id, model, locale, field, content, source) VALUES ('" . $location_id . "','pjLocation','1','address','" . $address1 . " " . $address2 . " " . $address3 . " " . $city . " " . $state . " " . $country . " " . $zip . "','script')";
    if ($conn->query($sql) === TRUE) {
        echo "<br>New records created successfully in foodkonnektfood_delivery_multi_lang";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $sql = "INSERT INTO foodkonnektfood_delivery_merchant(merchant_name, merchant_address,user_id) VALUES ('" . $name . "','" . $address1 . " " . $address2 . " " . $address3 . " " . $city . " " . $state . " " . $country . " " . $zip . "','" . $user_id . "')";
    if ($conn->query($sql) === TRUE) {
        echo "<br>New records created successfully in foodkonnektfood_delivery_merchant";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $sql = "INSERT INTO foodkonnektfood_delivery_multi_lang( foreign_id, model, locale, field, content, source) VALUES ('" . $location_id . "','pjLocation','1','name','" . $name . "','script')";
    if ($conn->query($sql) === TRUE) {
        echo "<br>New records created successfully in foodkonnektfood_delivery_multi_lang";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $sql = "INSERT INTO foodkonnektfood_delivery_locations_map(PHPJABBER_LOCATION_ID,CLOVER_MID,CLOVER_ACCESS_TOKEN_ID,USER_ID) VALUES ('" . $location_id . "','" . $merchant_id . "','" . $access_token . "', '". $user_id ."')";
    if ($conn->query($sql) === TRUE) {
        echo "<br>New records created successfully in foodkonnektfood_delivery_locations_map";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
} else {
	
}
//---------------------------------------------------------------------------//
//include 'getCategoryFromClover1.php';
echo '</br>------------------- getCategoryFromClover1.php---------------</br>';
// MID and AccessTokenID for TacosYMas Ross Avenue
$CLOVER_MID   = '';
$ACCESS_TOKEN = '';
if ($merchant_id == '') {
    $CLOVER_MID   = "RJV9QTEG55DC6";
    $ACCESS_TOKEN = "1547138e-978d-ecb0-77d0-3fb5bc342eda";
} else {
    echo '<<<<<<<<<<<<<<<<<<<<<....THROUGH SESSION......>>>>>>>>>>>>>>>>>>>>>>>';
    $CLOVER_MID   = $merchant_id;
    $ACCESS_TOKEN = $access_token;
    $location_id  = $_SESSION['location_id'];
}
$response1 = file_get_contents("https://api.clover.com:443/v3/merchants/" . $CLOVER_MID . "/categories?access_token=" . $ACCESS_TOKEN);
//echo $response1;
$result    = json_decode($response1);
//echo "<br/><br/><br/> print href:";
//echo $result->href;
echo "<br/><br/><br/> print elements:<br/>";
foreach ($result->elements as $data) {
    echo '<br>==>';
    echo "{$data->id} <br/>";
    echo "{$data->name} <br/>";
    
    echo "now getting jabber category id";
    $sql                   = "SELECT PHPJABBER_CATEGORY_ID FROM clover_phpjabber_category_map WHERE CLOVER_CATEGORY_ID='" . $data->id . "' AND CLOVER_MID='" . $CLOVER_MID . "' LIMIT 1";
    //echo "<br>sql query".$sql;
    $result                = $conn->query($sql);
    $PHPJABBER_CATEGORY_ID = '';
    $size                  = $result->num_rows;
    $PHPJABBER_CATEGORY_ID = '';
    echo "size=" . $size;
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<br>CLOVER_MID: " . $row["CLOVER_MID"] . " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
            $PHPJABBER_CATEGORY_ID = $row["PHPJABBER_CATEGORY_ID"];
        }
    }
    
    echo "<br>PHPJABBER_PRODUCT_ID=" . $PHPJABBER_CATEGORY_ID;
    if ($size == 0) {
        echo '<br>creating new===>';
        echo "###########".$location_id;
        $sql = "INSERT INTO foodkonnektfood_delivery_categories(STATUS,location_id,user_id) VALUES( 'T','" . $location_id . "','" . $user_id . "')";
        if ($conn->multi_query($sql) === TRUE) {
            echo "New records created successfully";
			
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        } 
        
        $sql           = "SELECT 	id FROM foodkonnektfood_delivery_categories ORDER BY id DESC LIMIT 1";
        $result        = $conn->query($sql);
        $maxcategoryid = '';
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                //    echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
                $maxcategoryid = $row["id"];
                echo $maxcategoryid;
            }
        } else {
            echo "0 results";
        }
        
        $sql = "INSERT INTO foodkonnektfood_delivery_multi_lang(foreign_id,model,locale,FIELD,content,source)VALUES('" . $maxcategoryid . "','pjCategory','1','name','" . $data->name . "','data');";
        if ($conn->query($sql) === TRUE) {
            echo "New records created successfully in multy lang";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        
        $sql = "INSERT INTO clover_phpjabber_category_map (CLOVER_CATEGORY_ID, PHPJABBER_CATEGORY_ID,CLOVER_MID,user_id) VALUES('" . $data->id . "','" . $maxcategoryid . "','" . $CLOVER_MID . "','" . $user_id . "')";
        if ($conn->multi_query($sql) === TRUE) {
            echo "<br>New records created successfully in clover_phpjabber_category_map";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        
    } else {
        echo '<br>Updating===>';        
        $sql = "UPDATE foodkonnektfood_delivery_multi_lang SET content = '" . $data->name . "' WHERE foreign_id = '" . $PHPJABBER_CATEGORY_ID . "' AND model='pjCategory'";
        if ($conn->query($sql) === TRUE) {
            echo "content Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }        
    }
    echo "<br><br><br><br><br><br>";
}
echo ">>>>>>>>>>>>Adding modifier group php1>>>>>>>>>>>>>>>>";
//include 'getModifierGroupFromClover2.php';
echo '</br>------------------- getModifierGroupFromClover2.php---------------</br>';

$response1 = file_get_contents("https://api.clover.com:443/v3/merchants/" . $CLOVER_MID . "/modifier_groups?access_token=" . $ACCESS_TOKEN);
echo "<br> for modifier group $response1" . $response1;
$result = json_decode($response1);
foreach ($result->elements as $data) {
    echo '<br>==>';
    echo "{$data->id} <br/>";
    echo "{$data->name} <br/>";
    
    $sql    = "SELECT ModifierGroup_CloverId FROM foodkonnektfood_delivery_modifier_group WHERE ModifierGroup_CloverId='" . $data->id . "' AND Merchant_Id='" . $CLOVER_MID . "' LIMIT 1";
    $result = $conn->query($sql);
    //$PHPJABBER_CATEGORY_ID='';
    $size   = $result->num_rows;
    echo "size=" . $size;
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            //    echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
            // $PHPJABBER_CATEGORY_ID=$row["PHPJABBER_CATEGORY_ID"];
        }
    } else {
        echo "0 results";
    }
    
    // echo "<br>PHPJABBER_PRODUCT_ID=".$PHPJABBER_CATEGORY_ID;
    if ($size == 0) {
        echo '<br>creating new===>';
        
        $sql = "INSERT INTO foodkonnektfood_delivery_modifier_group (ModifierGroup_Name, ModifierGroup_CloverId, Merchant_Id,user_id)VALUES('" . $data->name . "', '" . $data->id . "', '" . $CLOVER_MID . "','" . $user_id . "')";
        if ($conn->query($sql) === TRUE) {
            echo "<br>New records created successfully in foodkonnektfood_delivery_modifier_group";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }        
    } else {
        echo '<br>Updating===>';        
        $sql = "UPDATE foodkonnektfood_delivery_modifier_group SET ModifierGroup_Name = '" . $data->name . "',user_id = '" . $user_id . "' WHERE ModifierGroup_CloverId = '" . $data->id . "' and Merchant_Id='" . $CLOVER_MID . "'";
        if ($conn->query($sql) === TRUE) {
            echo "content Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }        
    }
}
echo "<br><br><<<<<<<<<<   ******  Adding Extras>>>>>>>>>>>>><br><br>";
//include 'getExtraFromClover3.php';
echo '</br>------------------- getExtraFromClover3.php---------------</br>';

$response1 = file_get_contents("https://api.clover.com:443/v3/merchants/" . $CLOVER_MID . "/modifiers?access_token=" . $ACCESS_TOKEN);
$result    = json_decode($response1);
echo "<br> for modifiers url" . $response1;
foreach ($result->elements as $data) {
    $price = $data->price;
    $price = $price / 100;
    echo $price . '<br>';
    $modifierGroupId = $data->modifierGroup->id;
    echo "<br>=Modifier Group id from clover==" . $modifierGroupId;
    //$modifierGroupId='J7ZC4KECB9Z3T';
    $PhpJabberModifierGroup_Id = '';    
    $sql                = "SELECT PHPJABBER_EXTRA_ID FROM clover_phpjabber_modifire_extra_map WHERE CLOVER_MODIFIRE_ID='" . $data->id . "' AND CLOVER_MID='" . $CLOVER_MID . "' LIMIT 1";
    $result             = $conn->query($sql);
    $PHPJABBER_EXTRA_ID = '';
    $size               = $result->num_rows;
    echo "<br>size=########################" . $size;
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            //    //echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
            $PHPJABBER_EXTRA_ID = $row["PHPJABBER_EXTRA_ID"];
        }
    } else {
        //echo "0 results";
    }
    
    $sql    = "SELECT ModifierGroup_Id FROM `foodkonnektfood_delivery_modifier_group` WHERE ModifierGroup_CloverId ='" . $modifierGroupId . "'";
    $result = $conn->query($sql);
    //$size=$result->num_rows;
    //echo "<br>number of rows from php jabbers==".$size;
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $PhpJabberModifierGroup_Id = $row["ModifierGroup_Id"];
            echo "<br>phpModifierGroupId==" . $PhpJabberModifierGroup_Id;
        }
    } else {
        //echo "0 results";
    }
    // End It is return by sandeep for select phpjabber id
    echo "<br>PHPJABBER_EXTRA_ID=" . $PHPJABBER_EXTRA_ID;
    echo "########*****************#######" . $PhpJabberModifierGroup_Id;
    if ($size == 0) {        
        echo "###############" . $PhpJabberModifierGroup_Id;
        $sql = "INSERT INTO foodkonnektfood_delivery_extras (price,ModifierGroup_Id,user_id)VALUES('" . $price . "','" . $PhpJabberModifierGroup_Id . "','" . $user_id . "')";
        if ($conn->query($sql) === TRUE) {
            echo "<br><br>New records created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        
        $sql        = "SELECT id FROM foodkonnektfood_delivery_extras ORDER BY id DESC LIMIT 1";
        $result     = $conn->query($sql);
        $maxExtraId = '';
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                //    //echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
                $maxExtraId = $row["id"];
                //echo $maxExtraId;
            }
        } else {
            //echo "0 results";
        }
        
        $sql = "INSERT INTO foodkonnektfood_delivery_multi_lang(foreign_id,model,locale,FIELD,content,source)VALUES('" . $maxExtraId . "','pjExtra','1','name','" . $data->name . "','data');";
        if ($conn->query($sql) === TRUE) {
            echo "New records created successfully in multy lang";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        
        $sql = "INSERT INTO clover_phpjabber_modifire_extra_map (PHPJABBER_EXTRA_ID, CLOVER_MODIFIRE_ID, CLOVER_MID, CLOVER_MODIFIRE_GROUP_ID,user_id)VALUES('" . $maxExtraId . "','" . $data->id . "','" . $CLOVER_MID . "','" . $data->modifierGroup->id . "','" . $user_id . "');";
        if ($conn->query($sql) === TRUE) {
            echo "<br>New records created successfully in clover_phpjabber_modifire_extra_map";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }        
    } else {        
        $sql = "UPDATE foodkonnektfood_delivery_extras SET price = '" . $price . "',ModifierGroup_Id='" . $PhpJabberModifierGroup_Id . "',user_id = '" . $user_id . "' WHERE id = '" . $PHPJABBER_EXTRA_ID . "'";
        if ($conn->query($sql) === TRUE) {
            echo "price Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }
        
        $sql = "UPDATE foodkonnektfood_delivery_multi_lang SET content = '" . $data->name . "' WHERE foreign_id = '" . $PHPJABBER_EXTRA_ID . "' AND model='pjExtra'";
        if ($conn->query($sql) === TRUE) {
            echo "content Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }        
    }
}
echo "<br><br><<<<<<<<<<<==Adding php 4 >>>>>>>>>>>>>><br><br>";
//include 'GetItemFromCloverWithExtras4.php';
echo '</br>------------------- GetItemFromCloverWithExtras4.php---------------</br>';
$response1            = file_get_contents("https://api.clover.com:443/v3/merchants/" . $CLOVER_MID . "/items?expand=modifierGroups&access_token=" . $ACCESS_TOKEN);
//echo "<br> response1".$response1;
$PHPJABBER_PRODUCT_ID = '';
$modifireId           = '';
$result               = json_decode($response1);
//echo "<br/><br/><br/> print href:";
//echo $result->href;
foreach ($result->elements as $data)
{
    echo '<br>==>';
    ////echo "{$data->id} <br/>";
    echo "{$data->name} <br/>";
    //	//echo "{$data->price} <br/>";
    $price = $data->price;
    $price = $price / 100;
    //echo  '<br>Item Id: '.$data->id.'<br>';
    
    $sql    = "SELECT PHPJABBER_PRODUCT_ID FROM clover_phpjabber_lineitem_product_map WHERE CLOVER_LINE_ITEM_ID='" . $data->id . "' AND CLOVER_MID='" . $CLOVER_MID . "' LIMIT 1";
    $result = $conn->query($sql);
    $size   = $result->num_rows;
    //echo "size=".$size;
    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            //    //echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
            $PHPJABBER_PRODUCT_ID = $row["PHPJABBER_PRODUCT_ID"];
            //echo "if :".$PHPJABBER_PRODUCT_ID;
        }
    } else {
        //  echo "0 results";
    }
    
    echo "<br>PHPJABBER_PRODUCT_ID=" . $PHPJABBER_PRODUCT_ID;
    if ($size == 0) {        
        $sql = "INSERT INTO foodkonnektfood_delivery_products (set_different_sizes, price,is_featured,user_id)VALUES('F','" . $price . "','0','" . $user_id . "')";
        if ($conn->multi_query($sql) === TRUE) {
            echo "<br>New records created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        
        $sql          = "SELECT id FROM foodkonnektfood_delivery_products ORDER BY id DESC LIMIT 1";
        $result       = $conn->query($sql);
        $maxproductid = '';
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                //    //echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
                $maxproductid         = $row["id"];
                $PHPJABBER_PRODUCT_ID = $maxproductid;
                //echo $maxproductid;
            }
        } else {
            //echo "0 results";
        }
        
        $sql = "INSERT INTO foodkonnektfood_delivery_multi_lang(foreign_id,model,locale,FIELD,content,source)VALUES('" . $maxproductid . "','pjProduct','1','name','" . $data->name . "','data');";
        if ($conn->multi_query($sql) === TRUE) {
            echo "<br>New records created successfully in multy lang";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        
        $sql = "INSERT INTO clover_phpjabber_lineitem_product_map (CLOVER_LINE_ITEM_ID, PHPJABBER_PRODUCT_ID,CLOVER_MID,user_id)VALUES('" . $data->id . "','" . $maxproductid . "','" . $CLOVER_MID . "','" . $user_id . "');";
        if ($conn->multi_query($sql) === TRUE) {
            echo "<br>New records created successfully in clover_phpjabber_lineitem_product_map";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        
        $sql    = "SELECT PHPJABBER_PRODUCT_ID FROM clover_phpjabber_lineitem_product_map WHERE CLOVER_LINE_ITEM_ID='" . $data->id . "' AND CLOVER_MID='" . $CLOVER_MID . "' LIMIT 1";
        $result = $conn->query($sql);
        $size   = $result->num_rows;
        //echo "size=".$size;
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                //    //echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
                $PHPJABBER_PRODUCT_ID = $row["PHPJABBER_PRODUCT_ID"];
            }
        } else {
            //echo "0 results";
        }
        
        $response2 = file_get_contents("https://api.clover.com:443/v3/merchants/" . $CLOVER_MID . "/items/" . $data->id . "/categories/?access_token=" . $ACCESS_TOKEN);
        //echo "<br>response2".$response2;
        $result2   = json_decode($response2);
        foreach ($result2->elements as $data2) {
            //echo'<br>==>';
            //echo "{$data2->id} <br/>";
            echo '<br>data2->name===>';
            //echo "{$data2->name} <br/>";            
            $sql                   = "SELECT PHPJABBER_CATEGORY_ID FROM clover_phpjabber_category_map WHERE CLOVER_CATEGORY_ID='" . $data2->id . "' LIMIT 1";
            $result                = $conn->query($sql);
            $PHPJABBER_CATEGORY_ID = '';
            $size2                 = $result->num_rows;
            //echo "size=".$size2;
            if ($result->num_rows > 0) {
                // output data of each row
                while ($row = $result->fetch_assoc()) {
                    //    //echo "<br>CLOVER_MID: ". $row["CLOVER_MID"]. " ACCESS_TOKEN:- " . $row["CLOVER_ACCESS_TOKEN_ID"] . "<br>";
                    $PHPJABBER_CATEGORY_ID = $row["PHPJABBER_CATEGORY_ID"];
                }
            } else {
                //echo "0 results";
            }
            
            $sql = "INSERT INTO foodkonnektfood_delivery_products_categories (product_id, category_id,user_id) VALUES('" . $PHPJABBER_PRODUCT_ID . "','" . $PHPJABBER_CATEGORY_ID . "','" . $user_id . "')";
            if ($conn->multi_query($sql) === TRUE) {
                echo "<br>New records created successfully in foodkonnektfood_delivery_products_categories";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }            
        }
    } else {
        echo "<br> PHPJABBER_PRODUCT_ID-->" . $PHPJABBER_PRODUCT_ID;        
        $sql = "UPDATE foodkonnektfood_delivery_products SET price = '" . $price . "',user_id = '" . $user_id . "' WHERE id = '" . $PHPJABBER_PRODUCT_ID . "'";
        if ($conn->query($sql) === TRUE) {
            echo "<br>price Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }        
        $sql = "UPDATE foodkonnektfood_delivery_multi_lang SET content = '" . $data->name . "',locale='1',source='data' WHERE foreign_id = '" . $PHPJABBER_PRODUCT_ID . "' AND model='pjProduct'";
        if ($conn->query($sql) === TRUE) {
            echo "content Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }        
    }
    
    foreach ($data->modifierGroups->elements as $modifireData) {
        $modifire = $modifireData->modifierIds;
        $array    = explode(",", $modifire);
        $i        = 0;
        foreach ($array as $modifireId) {
            echo "\$array[$i]  => $modifireId.\n";
            echo $PHPJABBER_PRODUCT_ID;
            $sql                = "SELECT PHPJABBER_EXTRA_ID FROM clover_phpjabber_modifire_extra_map WHERE CLOVER_MODIFIRE_ID='" . $modifireId . "' LIMIT 1";
            $result             = $conn->query($sql);
            $PHPJABBER_EXTRA_ID = '';
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $PHPJABBER_EXTRA_ID = $row["PHPJABBER_EXTRA_ID"];
                }
                $sql    = "SELECT * FROM foodkonnektfood_delivery_products_extras WHERE extra_id='" . $PHPJABBER_EXTRA_ID . "' AND product_id= '" . $PHPJABBER_PRODUCT_ID . "' LIMIT 1";
                $result = $conn->query($sql);
                if ($result->num_rows == 0) {
                    $sql = "INSERT INTO foodkonnektfood_delivery_products_extras (product_id, extra_id,user_id) VALUES('" . $PHPJABBER_PRODUCT_ID . "','" . $PHPJABBER_EXTRA_ID . "','" . $user_id . "')";
                    $conn->query($sql);
                }
                $i++;
            }
            // echo "<br> PHPJABBER_EXTRA_ID -->". $PHPJABBER_EXTRA_ID;
            // echo "<br> PHPJABBER_PRODUCT_ID-->".$PHPJABBER_PRODUCT_ID;
        }
    }    
}

/* ---------------------------------------------------------------- */
/*		GET EMPLOYEE DETAILS FROM CLOVER AND INSERT INTO DATABASE 	*/
/* ---------------------------------------------------------------- */
$emp_response = file_get_contents('https://api.clover.com/v3/merchants/'. $merchant_id .'/employees/'. $employee_id .'?access_token='. $access_token .'&return_null_fields=true');
$emp_data = json_decode($emp_response);
echo "Employee id==".$employee_id;
//Let's check employee records already exists or not.
$emp_select = "SELECT * FROM foodkonnektfood_delivery_employees WHERE employee_id = '". $employee_id ."'";
$emp_result = $conn->query($emp_select);

if($emp_result->num_rows == 0 )
{
	$emp_query = "INSERT INTO foodkonnektfood_delivery_employees (employee_id, name, nickname, email, role) VALUES ('". $employee_id ."', '". $emp_data->name ."', '". $emp_data->nickname ."', '". $emp_data->email ."', '". $emp_data->role ."')";
	
	 
	if($conn->query($emp_query) == TRUE)
	{
		echo '<br />Employee record inserted!.<br />';
	} else {
		echo '<br />Error : '. $conn->error() .'<br />';
	}
	
} else {
	
	echo '<br />This employee already exists.<br />';
} 
$emp_query1 ="UPDATE foodkonnektfood_delivery_locations_map SET employee_id= '". $employee_id ."' WHERE CLOVER_MID ='".$merchant_id."'";
	$conn->query($emp_query1);


/* ---------------------------------------------------------------- */
/*		GET EMPLOYEE DETAILS FROM CLOVER AND INSERT INTO DATABASE 	*/
/* ---------------------------------------------------------------- */
$tender_response = file_get_contents('https://api.clover.com:443/v3/merchants/'. $merchant_id .'/tenders?access_token='. $access_token);
$tender_data = json_decode($tender_response);
$t = 1;
$tender_select = "SELECT * FROM foodkonnektfood_delivery_tenders WHERE location_id=". $location_id;
$tender_result = $conn->query($tender_select);
if($tender_result->num_rows == 0)
{
	foreach($tender_data->elements as $data)
	{
		$tender_query = "INSERT INTO foodkonnektfood_delivery_tenders (tender_id, tender_type, location_id) VALUES ('". $data->id ."', '". $data->label ."', '". $location_id ."');";
		if($conn->query($tender_query) == TRUE)
		{	
			echo '<br />'. $t .'Tenders record inserted!.<br />';
		} else {
			echo '<br />Error : '. $conn->error() .'<br />';
		}
		$t++;
	}
}

//close connection----------------------------

$conn->close(); 
?>
<?php
if ($case == 'login') {
?>
<script>
	window.location = '<?= PJ_INSTALL_URL ?>index.php?controller=pjAdmin&action=pjActionIndex';
</script>
<?php
} else {
?>  
<script>
	window.location = '<?= PJ_INSTALL_URL ?>index.php?controller=pjAdmin&action=pjActionLogin&msg=AU05';
</script>
<?php
}
?>