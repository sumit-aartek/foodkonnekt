<html xmlns="http://www.w3.org/1999/xhtml">  
	<head>
		<title></title>
		<link href="style/receipt.css" rel="stylesheet" type="text/css" />
	</head> 
	<body> 
		<table width="100%" height="100%">   
			<tr>       
				<td valign="middle" align="center">    
					<table class="receipt">    
						<tr>
							<td align="left" valign="bottom"><img src="images/tg_logo.png" /></td>
							<td align="right" valign="bottom"><h2><!--[MerchantName]--></h2></td>
						</tr>  
						<tr>   
							<td class="instructions" colspan="2">
								<hr />
								Your payment was successfully processed. Please print this receipt for your records.  
								<hr />
							</td>  
						</tr> 
						<tr>   
							<td align="left" colspan="2">
								<table class="details">
									<tr><td colspan="2"><h3>Payment Details</h3></td></tr>
									<tr><td class="spacerRow"></td></tr>
									<tr>
										<th>Total Amount:</th>
										<td><?php $_POST['Amount'] ?></td>  
									</tr>
									<tr>
										<th>Description:</th>
										<td><!--[Description]--></td>
									</tr> 
									<tr>   
										<th>Invoice No.:</th>
										<td><!--[InvoiceNum]--></td>
									</tr>
									<tr>
										<th>Auth code:</th>
										<td><!--[r_AuthCode]--></td>
									</tr> 
								</table>
							</td> 
						</tr>
						<tr>  
							<td align="center" class="instructions" colspan="2">
								Click <a href="<!--[ReturnReceiptUrl]-->">here</a> to return to <!--[MerchantName]-->
							</td>  
						</tr> 
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>