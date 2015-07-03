<?php
require_once '../app/config/config.inc.php';

$amount = base64_decode($_GET['amount']);
$merchant_name = base64_decode($_GET['mname']);
$o_user_id = $_GET['uid'];
$c_order_id = $_GET['oid'];
$c_merchant_id = $_GET['mid'];
$c_access_token = $_GET['at'];

$servername = PJ_HOST;
$username = PJ_USER;
$password = PJ_PASS;
$dbname = PJ_DB;
		
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT id FROM foodkonnektfood_delivery_orders ORDER BY id DESC LIMIT 1";
$result1 = $conn->query($sql);
$orderId='';

if ($result1->num_rows > 0) {
    while($row = $result1->fetch_assoc()) {
		$orderId=$row["id"];
    }
}
$sql = "SELECT id FROM foodkonnektfood_delivery_clients ORDER BY id DESC LIMIT 1";
$result1 = $conn->query($sql);
$clientId='';

if ($result1->num_rows > 0) {
    while($row = $result1->fetch_assoc()) {
		$clientId=$row["id"];
    }
}
// echo "orderId: " .$orderId. "clientId :" .$clientId;
?>

<html>
<head>
<title>Sample PHP Code to Post Payments to TGATE's Payment Gateway</title>
</head>
<body>

    <form target="_blank" action="submit_txn.php" method="POST">
		<input type="hidden" name="c_user_id" value="<?=$o_user_id?>" />
		<input type="hidden" name="c_merchant_name" value="<?=$merchant_name?>" />
		<input type="hidden" name="c_order_id" value="<?=$c_order_id?>" />
		<input type="hidden" name="c_merchant_id" value="<?=$c_merchant_id?>" />
		<input type="hidden" name="c_access_token" value="<?=$c_access_token?>" />
	
		<table cellspacing="0" cellpadding="4" frame="box" bordercolor="#dcdcdc" rules="none" style="border-collapse: collapse;">
		<tr>
		  <td class="frmText" type="hidden" style="color: #000000; font-weight: normal;"></td>
		  <td><input class="frmInput" type = "hidden" size="25" name="clientId" value="<?php echo $clientId; ?>"></td>
		</tr>
		<tr>
		  <td class="frmText" type="hidden" style="color: #000000; font-weight: normal;"></td>
		  <td><input class="frmInput" type = "hidden" size="25" name="orderId" value="<?php echo $orderId; ?>"></td>
		</tr>
		<tr>
		  <td class="frmText" type="hidden" style="color: #000000; font-weight: normal;"></td>
		  <td><input class="frmInput" type = "hidden" size="25" name="UserName" ></td>
		</tr>
		<tr>
		  <td class="frmText" type="hidden" style="color: #000000; font-weight: normal;"></td>
		  <td><input class="frmInput" type = "hidden" size="8" name="Password" ></td>
		</tr>
		<tr>
		  <td class="frmText" style="color: #000000; font-weight: normal;"></td>
		  <td><input class="frmInput" type="hidden" size="25" name="TransType" value="Sale"></td>
		</tr>
		<tr>
		  <td class="frmText" style="color: #000000; font-weight: normal;"></td>
		  <td><input class="frmInput" type="hidden" size="4" name="MagData" ></td>
		</tr>
		<tr>
		  <td class="frmText" style="color: #000000; font-weight: normal;"></td>
		  <td><input class="frmInput" type="hidden" size="4" name="PNRef" ></td>
		</tr>
		<tr>
		  <td class="frmText" style="color: #000000; font-weight: normal;"></td>
		  <td><input class="frmInput" type="hidden" size="4" name="ExtData" ></td>
		</tr>
		<tr>
		  <td class="frmText" style="color: #000000; font-weight: normal;">Name On Card:</td>
		  <td><input class="frmInput" type="text" size="50" name="NameOnCard" ></td>
		</tr>
		<tr>
		  <td class="frmText" style="color: #000000; font-weight: normal;">Billing Street:</td>
		  <td><input class="frmInput" type="text" size="50" name="Street" ></td>
		</tr>
		<tr>
		  <td class="frmText" style="color: #000000; font-weight: normal;">Billing Zip:</td>
		  <td><input class="frmInput" type="text" size="10" name="Zip" ></td>
		</tr>
		<tr>
		  <td class="frmText" style="color: #000000; font-weight: normal;">CardNum:</td>
		  <td><input class="frmInput" type="text" size="16" name="CardNum" ></td>
		</tr>
		<tr>
		  <td class="frmText" style="color: #000000; font-weight: normal;">ExpDate (MM/YY):</td>
		  <td>
			<input class="frmInput" type="text" size="4" name="ExpMonth" >/
			<input class="frmInput" type="text" size="4" name="ExpYear" >
		  </td>
		</tr>
		<tr>
		  <td class="frmText" style="color: #000000; font-weight: normal;">Amount:</td>
		  <td><input class="frmInput" type="text" size="12" name="Amount" readonly="readonly" value="<?php echo $amount; ?>"></td>
		</tr>
		<tr>
		  <td class="frmText" style="color: #000000; font-weight: normal;">CVNum:</td>
		  <td><input class="frmInput" type="text" size="4" name="CVNum" ></td>
		</tr>
		<tr>
		  <td class="frmText" style="color: #000000; font-weight: normal;"></td>
		  <td><input class="frmInput" type="hidden" size="20" name="InvNum"></td>
		</tr>
		<tr>
		  <td></td>
		  <td align="right"> <input type="submit" value="Process Payment" class="button"> <input type="reset" value="Reset!"></td>
		</tr>
		</table>
	</form>
</body>
</html>