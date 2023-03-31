<?php 
include_once "ses_set_as.php"; 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/dbconst.class.php";
include "classes/include/fetch_data.php";
include_once("classes/utility.class.php");
include_once("classes/home_s.class.php");
include_once("classes/voucher.class.php");

$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$obj_voucher = new voucher($dbConn);
$obj_Utility = new utility($dbConn);

if(isset($_REQUEST['direct']))
{
	$voucherNo = $_GET['vno'];
	$voucherType = $_GET['type'];
}
else
{
	$voucherNo = base64_decode($_GET['vno']);
	$voucherType = base64_decode($_GET['type']);
}

if( $voucherType <> VOUCHER_SALES && ($voucherType == VOUCHER_RECEIPT || $voucherType == VOUCHER_CONTRA ||  $voucherType == VOUCHER_PAYMENT))
{
	$VoucherArray = $obj_voucher->GetVoucherDetails($voucherNo , $voucherType,true);
}
else if( $voucherType <> VOUCHER_SALES && ($voucherType == VOUCHER_JOURNAL))
{
		$VoucherArray = $obj_voucher->GetVoucherDetails($voucherNo , $voucherType,false);
}

if($VoucherArray[0]['RefTableID'] == TABLE_PAYMENT_DETAILS)
{
	 $rowspan = 2;
	  $name = "Voucher No";
	  $date = "Voucher Date";
}
else if($VoucherArray[0]['RefTableID'] == TABLE_CHEQUE_DETAILS)
{
	 $rowspan = 1;
	 $name = "Receipt No";
	 $date = "Receipt Date";
}
else
{
	$name = "Voucher No";
	 $date = "Voucher Date";
}


$Amount = (int) $VoucherArray[0]['Debit']; 
$totalTDS = 0;
if(isset($VoucherArray[0]['TDSAmount']))
{
	//$total_payable = $Amount;
	for($i = 1;$i <= sizeof($VoucherArray) -1;$i++)
	{
		if($VoucherArray[$i]['InvoiceAmount'] > 0)
		{
			$total_payable += (int)$VoucherArray[$i]['InvoiceAmount'] - (int) $VoucherArray[$i]['TDSAmount'];
			$total_invoiceamount += $VoucherArray[$i]['InvoiceAmount'];			
		}
		else
		{
			$total_payable += (int)$Amount - (int) $VoucherArray[$i]['TDSAmount'];
			$total_invoiceamount += $Amount;
		}
		$totalTDS += $VoucherArray[$i]['TDSAmount'];
	}
}
else
{
	$total_payable = $Amount;
	$total_invoiceamount = $Amount;		
}

$BankID = 0;

for($j = 0; $j < count($VoucherArray); $j++)
{
	if($BankID == 0)
	{
		if($VoucherArray[$j]['VoucherTypeID'] == VOUCHER_RECEIPT)
		{
			$BankID = $VoucherArray[$j]['ToLedgerID'];
		}
		else if($VoucherArray[$j]['VoucherTypeID'] == VOUCHER_PAYMENT || $VoucherArray[$j]['VoucherTypeID'] == VOUCHER_CONTRA)
		{
			$BankID = $VoucherArray[$j]['ByLedgerID'];	
		} 
	}
}

$prefix = $obj_Utility->GetPreFix($VoucherArray[0]['VoucherTypeID'],$BankID);

if(!empty($prefix))
{
	$prefix = $prefix.'-';
}
$prefix .= $VoucherArray[0]['ExternalCounter'];
?>

<html>
<head>
<meta charset="utf-8">

<title><?php echo $VoucherArray[0]['Type']; ?></title>

<style>
	body{
		width: 90%;
		margin: auto;
		font-size: 18px;
	}
	table {
    	border-collapse: collapse;
		border: 1px solid #cccccc;
		text-align:left;
		padding-top:0px;
		padding-bottom:0px;
	}
	th, td {
   		text-align:left;
		padding-top:0px;
		padding-bottom:0	px;
	}	
</style>

<style type="text/css" media="print">
  @page { size: landscape; }
</style>

<script>

	window.onbeforeprint = function(){
  		
		//Get the print button and put it into a variable
		var btnPrint = document.getElementById("Print");
		
		btnPrint.style.visibility = 'hidden';
		
	}
	window.onafterprint = function(){
		
		//Get the print button and put it into a variable
		var btnPrint = document.getElementById("Print");
		
  		//Set the print button to 'visible' again 
        btnPrint.style.visibility = 'visible';
		
	}


	function PrintPage() 
	{
		//Get the print button and put it into a variable
		var btnPrint = document.getElementById("Print");
		
		btnPrint.style.visibility = 'hidden';
		
		//Print the page content
        window.print();
        
		//Set the print button to 'visible' again 
        btnPrint.style.visibility = 'visible';
	}
</script>
</head>
<body>
<br>
<div align="center">
 <INPUT TYPE="button" id="Print" onClick="PrintPage()" name="Print" value="Print" width="300" style="width:60px;height:30px; font-size:20px" />
</div>
 <br>
 <br>
 <center>
<div style="border: 1px solid #cccccc;width:95%;" id="voucher">
	<div id="bill_header" style="text-align:center;padding: 10px;">
    	<div id="society_name"><b><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></b></div>
        <div id="society_reg" style="font-size:14px;">
			<?php 
			if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
			?>
        </div>
		<div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
		<div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:17px; padding-top:5px;"><?php echo $VoucherArray[0]['Type']; ?></div>
        </div>
    </div>
<?php if( $voucherType <> VOUCHER_SALES && ($voucherType == VOUCHER_CONTRA || $voucherType == VOUCHER_PAYMENT))
{?>
<table id="table1" border="1">
<table style="font-size:17px;border: 1px solid #cccccc; border-collapse:collapse;width:100%; border-left:none; border-right:none; ">
	<tr>
    		<td style="text-align:left;width:10%; padding-left:10px;" >Bank/Cash Name :</td>
            <td>:</td>
            <td ><?php echo $VoucherArray[0]['By']; ?></td>
            
    	
            <td style="text-align:left; width:13%;" ><?php echo $name;?></td>
		<td>:</td>
		<td style="text-align:left;width:10%;"><?php echo $prefix; ?></td>
   </tr>
     <!--<tr>
    	<td style="text-align:left;width:15%;padding-left:10px;" ><?php if($VoucherArray[0]['ExpenseBy'] <> ""  && strtolower($VoucherArray[0]['ExpenseBy']) <> 'cash' ){ echo 'Expense Head';}else if(strtolower($VoucherArray[0]['ExpenseBy']) <> 'cash'  && strtolower($VoucherArray[0]['By']) <> 'cash'){ echo 'Expense Head';}?></td>
            <td><?php if($VoucherArray[0]['ExpenseBy'] <> "" && strtolower($VoucherArray[0]['ExpenseBy']) <> 'cash' ){echo ':'; }else if(strtolower($VoucherArray[0]['ExpenseBy']) <> 'cash'  && strtolower($VoucherArray[0]['By']) <> 'cash'){ echo ':';}?></td>
            <td ><?php if($VoucherArray[0]['ExpenseBy'] <> "" && strtolower($VoucherArray[0]['ExpenseBy']) <> 'cash' ){echo $VoucherArray[0]['ExpenseBy'];}else if(strtolower($VoucherArray[0]['By']) <> 'cash'){echo $VoucherArray[0]['By'];} ?></td>
 	 </tr>-->   	
	<tr>
		<td style="text-align:left;width:13%;padding-left:10px;"><?php  if($VoucherArray[0]['RefTableID'] == TABLE_PAYMENT_DETAILS || $VoucherArray[0]['RefTableID'] == TABLE_CHEQUE_DETAILS){ echo 'Cheque No';}?></td>
		<td><?php  if($VoucherArray[0]['RefTableID'] == TABLE_PAYMENT_DETAILS || $VoucherArray[0]['RefTableID'] == TABLE_CHEQUE_DETAILS){ echo ':'; }?></td>
		<td ><?php  if($VoucherArray[0]['RefTableID'] == TABLE_PAYMENT_DETAILS || $VoucherArray[0]['RefTableID'] == TABLE_CHEQUE_DETAILS){ if($VoucherArray[0]['ChequeNumber'] == -1){echo 'Cash';}else{echo $VoucherArray[0]['ChequeNumber']; }}?></td>
        <td style="text-align:left;" ><?php echo $date;?></td>
		<td>:</td>
		<td style="text-align:left;width:15%;"><?php echo $VoucherArray[0]['Date']; ?></td>
	</tr>
     
    <tr>
		    <td style="text-align:left;width:10%; padding-left:10px;" >Paid To</td>
            <td>:</td>
            <td ><?php echo $VoucherArray[1]['To']; ?></td>
    </tr> 
                
    <tr><td colspan="6" style="padding:10px;text-align:left;width:13%;" ></td></tr>
</table>
<table style="font-size:17px;width:100%; border:none;">
	<tr>
    	<th style="text-align:center;border: 1px solid #cccccc;border-left:none;border-top:none;border-collapse:collapse;">Sr. No.</th>
        <th style="text-align:center;border: 1px solid #cccccc;border-left:none;border-top:none;border-collapse:collapse;">Expense Head</th>
        <th style="text-align:center;border: 1px solid #cccccc;border-left:none;border-top:none;border-collapse:collapse;">Invoice Amount</th>
		<td style="text-align:center; border: 1px solid #cccccc;font-weight:bold;border-left:none;border-top:none;border-collapse:collapse;" >TDS Amount</td>
		<td  style="text-align:center; border: 1px solid #cccccc;font-weight:bold; border-right:none;border-top:none;border-collapse:collapse;">Amount(Rs.)</td>
	</tr>
    <?php for($i = 1;$i <= sizeof($VoucherArray) -1;$i++)
	{	
		if(!empty($VoucherArray[$i]['ExpenseBy']))
		{
			
		
		//echo 'Number Of Time Loop '.$i;		
		if($VoucherArray[$i]['InvoiceAmount'] > 0) 
		{ 
			$invoiceAmt = $VoucherArray[$i]['InvoiceAmount']; 			
		} 
		else 
		{ 
			$invoiceAmt = $VoucherArray[$i]['Credit']; 
		}		
		?>
	<tr>
    	<td style="text-align:center;border:1px solid #cccccc;border-left:none;border-collapse:collapse;width:10%;" ><?php echo $i; ?></td>
        
        
        
        <?php //echo 'Head : '.$invoiceAmt ;?>
        <td style="border:1px solid #cccccc;border-left:none;border-collapse:collapse;width:30%;" ><?php if($VoucherArray[$i]['ExpenseBy'] <>"" ){ echo $VoucherArray[$i]['ExpenseBy'];}?></td>
		<td style="border: 1px solid #cccccc; text-align:right;border-right:none; border-collapse:collapse;width:20%;"><?php echo number_format($invoiceAmt,2); ?></td>
        <td style="border: 1px solid #cccccc; text-align:right; border-collapse:collapse;width:20%;"><?php echo number_format($VoucherArray[$i]['TDSAmount'],2); ?></td>
		<td  style="border: 1px solid #cccccc; text-align:right;border-right:none; border-left:none; border-collapse:collapse;width:20%;"><?php echo number_format($invoiceAmt-$VoucherArray[$i]['TDSAmount'],2); ?></td>        
	</tr>
    
	<?php 
		}
	}
	if(sizeof($VoucherArray) < 5)
	{
		//adding emty tr to maintain standard size for voucher print
		for($i = 0;$i <= (5 - sizeof($VoucherArray)) ;$i++)
		{			
			?>
		<tr style="empty-cells:hide; border-collapse: separate;">
			<td style="text-align:center;border:1px solid #cccccc;border-left:none;width:10%;" >&nbsp;</td>
			<td style="border:1px solid #cccccc;border-left:none;width:30%;" >&nbsp;</td>
			<td style="border: 1px solid #cccccc; text-align:right;border-right:none; width:20%;">&nbsp;</td>
			<td style="border: 1px solid #cccccc; text-align:right; width:20%;">&nbsp;</td>
			<td  style="border: 1px solid #cccccc; text-align:right;border-right:none; border-left:none; width:20%;">&nbsp;</td>        
		</tr>
			<?php 	
		}
	}
	//if($VoucherArray[0]['RefTableID'] == TABLE_PAYMENT_DETAILS && $VoucherArray[0]['TDSAmount'] > 0)
	if($VoucherArray[0]['RefTableID'] == TABLE_PAYMENT_DETAILS)
	{?>
	<!--<tr>
		<td style="border: 1px solid #cccccc; border-bottom:none; border-collapse:collapse;" >LESS - TDS(Rs.)</td>
		<td style="text-align:right;border-right:none; border-collapse:collapse;"><?php //echo number_format($VoucherArray[0]['TDSAmount'],2); ?></td>
	</tr>-->
    <?php }?>
	<tr>
    	<th colspan="2" style="border: 1px solid #cccccc; border-collapse:collapse;text-align:right;">Total Payable (Rs.)</th>        
		<td style="border: 1px solid #cccccc; border-collapse:collapse;text-align:right;"><?php echo number_format($total_invoiceamount,2); ?></td>
        <td style="border: 1px solid #cccccc; border-collapse:collapse;text-align:right;"><?php echo number_format($totalTDS,2); ?></td>
		<td style="border: 1px solid #cccccc; text-align:right;border-right:none; border-left:none; border-collapse:collapse;"><?php echo number_format($total_payable,2); ?></td>
	</tr>
</table>
<table style="font-size:17px;width:100%; border:none;">
	<tr>
		<th style="width:23%;">Narration</th>      
		<td colspan="9"> : <?php   if($VoucherArray[0]['Note'] <> ''){echo $VoucherArray[0]['Note'] ; }else{echo '-';}?></td>
	</tr>
    <tr style=" border:none;"><td><br></td></tr>
    <tr>
		<th style="border: 1px solid #cccccc; ;border-right: 0; border-left:none; border-top:none; border-collapse:collapse; width:23%;">Amount (In Words) </th>        
		<td style="border: 1px solid #cccccc; ;border-left: 0; border-collapse:collapse;border-top:none; border-right:none; " colspan="9"> : <?php   if($total_payable <> ''){ echo "Rupees ".  $obj_Utility->convert_number_to_words($total_payable)." Only"; }?></td>
	</tr>
    
    <tr style=" border:none;"><td><br></td></tr>
    <tr style=" border:none;"><td><br></td></tr>
    </table>

</table>
<?php }
else if($voucherType == VOUCHER_RECEIPT)
{?>
<table id="table1" border="1">
<table style="font-size:17px;border: 1px solid #cccccc; border-collapse:collapse;width:100%; border-left:none; border-right:none;border-bottom:none; ">
 <tr>
    	<td  style="text-align:left;padding-left:10px;" ><?php echo $name;?>:<?php echo $prefix; ?></td>
        <td   style="text-align:right; width:30%;padding-right:10px; " ><?php echo $date;?> : <?php echo $VoucherArray[0]['Date']; ?></td>
    	
</tr>
<tr><td><br/></td></tr>
	<?php if( strtolower($VoucherArray[1]['To']) <> 'cash')
	{?>
    
    <?php  if($VoucherArray[0]['IsMember'] == 1 )
	{?>
     <tr>
    	<td style="text-align:left;width:20%;padding-left:10px;padding-bottom:20px;"  colspan="2">Received With Thanks From : <?php echo $VoucherArray[0]['owner_name'];?></td>
     </tr>
     <?php } ?>
     
     <tr>
    	<td style="text-align:left;width:15%;padding-left:10px;padding-bottom:20px;"  colspan="2"><?php if($VoucherArray[0]['IsMember'] == 1 ){echo 'Unit No';}else{echo 'Expense Head';} ?>:<?php if($VoucherArray[0]['ExpenseBy'] <> "" ){echo $VoucherArray[0]['ExpenseBy'];}else{echo $VoucherArray[0]['By'];} ?></td>
    	<!--<td style="text-align:left; width:13%;" ><?php //echo $name;?>:<?php //echo $VoucherArray[0]['VoucherNo']; ?></td>-->
   </tr>
    
    
   
   <?php } 
   else 
   {
	   ?>
  
     <?php  if($VoucherArray[0]['IsMember'] == 1 )
	{?>
     <tr>
    	<td style="text-align:left;width:20%;padding-left:10px;padding-bottom:20px;"  colspan="2">Received With Thanks From:<?php echo $VoucherArray[0]['owner_name'];?></td>
     </tr>
     <?php } ?>
  
    <tr>
    	<td style="text-align:left;width:15%;padding-left:10px;padding-bottom:20px;" ><?php if($VoucherArray[0]['IsMember'] == 1 ){echo 'Unit No';}else{echo 'Expense Head';} ?>:<?php if($VoucherArray[0]['ExpenseBy'] <> "" ){echo $VoucherArray[0]['ExpenseBy'];}else{echo $VoucherArray[0]['By'];} ?></td>
        <!--<td style="text-align:left; width:13%;" ><?php // echo $name;?>:<?php //echo $VoucherArray[0]['VoucherNo']; ?></td>-->
         
	</tr>
 
    <?php } ?>
	<tr><td  style="padding:10px;" ></td></tr>
	
   
   </table>
   <table style="font-size:17px;width:100%; border:none;">
   
   <tr>
		<td style="border:none;padding-left:10px;padding-bottom:20px;"   colspan="2">Rupees : <?php echo number_format($total_payable,2);  if($Amount <> ''){echo " ( Rupees ". $obj_Utility->convert_number_to_words($Amount)." Only )"; }?></td>
	</tr>
    
    <tr>
		 <td style="text-align:left;width:25%;padding-left:10px;padding-bottom:20px;"> 
         <?php if ($VoucherArray[0]['DepositID']== -2)
		 {?>
         	By NEFT :
			
		  <?php }
		  else
		  {?>
			  By Cheque No :
		   <?php }?>
          <?php  if($VoucherArray[0]['ChequeNumber'] == -1){echo 'Cash';}else{echo $VoucherArray[0]['ChequeNumber']; }?> </td>
		<td style="text-align:left;padding-bottom:20px;" >&nbsp;&nbsp;
        <?php if ($VoucherArray[0]['DepositID']== -2)
		{ ?>
			Transaction Date :
		<?php }
		else
		{?>
			 Cheque Date :
		<?php }?>
         <?php echo $VoucherArray[0]['ChequeDate']; ?></td>
	 </tr>
    <tr>
		<td style="border:none;padding-left:10px;padding-bottom:20px;"  colspan="2">Drawn On : <?php echo $VoucherArray[0]['PayerBank'];echo ",".$VoucherArray[0]['PayerChequeBranch'];?></td>
	</tr>
     <?php  if($VoucherArray[0]['IsMember'] == 1 )
	{?>
    <tr>
		<td style="border:none;padding-left:10px;padding-bottom:20px;"  colspan="2">Towards: <?php echo $VoucherArray[0]['BillDetails'];?></td>
	</tr>
    <?php }?>
    <tr>
		<td style="border:none;padding-left:10px;padding-bottom:20px;"  colspan="2"><?php   if($VoucherArray[0]['Note'] <> ''){echo $VoucherArray[0]['Note'] ; }else{echo '-';}?></td>
	</tr>
    
	
</table>
<table style="font-size:17px;width:100%; border:none;">
 	<tr style=" border:none;"><td><br></td></tr>
    <tr style=" border:none;"><td><br></td></tr>
    </table>

</table>
<?php }?>
<?php if($voucherType == VOUCHER_JOURNAL)
{  
$sqlQuery = "SELECT `InvoiceDate` FROM `paymentdetails` WHERE `VoucherID` = '".$VoucherArray[0]['id']."'";
$invoiceDate = $obj_voucher->m_dbConn->select($sqlQuery);
?>
<table id="table2" border="1">
	
    <table style="font-size:17px;border: 1px solid #cccccc; border-collapse:collapse;width:100%; border-left:none; border-right:none; ">
	<tr >
		<td style="text-align:left;padding:10px;" ><?php echo $name;?>: <?php echo $prefix; ?></td>
		 <td style="text-align:left; float:right;padding:10px;" >Invoice Date : <?php if(sizeof($invoiceDate) > 0){ echo getDisplayFormatDate($invoiceDate[0]['InvoiceDate']); } else { echo $VoucherArray[0]['Date']; } ?></td>
	</tr>
	 </table>
   
   <table style="font-size:17px;width:100%; border:none;">
	<tr>
		<td style="text-align:center; border: 1px solid #cccccc;font-weight:bold;border-left:none;border-top:none;border-collapse:collapse; width:80%;"  colspan="2" >Particulars</td>
		<td  style="text-align:center; border: 1px solid #cccccc;font-weight:bold; border-right:none;border-top:none;border-collapse:collapse;">Debit(Rs.)</td>
        <td  style="text-align:center; border: 1px solid #cccccc;font-weight:bold; border-right:none;border-top:none;border-collapse:collapse;">Credit(Rs.)</td>
	</tr>
    <?php 
    $totalJVDebit = 0;
		$totalJVCredit = 0;
    for($i = 0;$i <= sizeof($VoucherArray) -1;$i++)
	{
		if($VoucherArray[$i]['By'] <> "")
			{
				$totalJVDebit += $VoucherArray[$i]['Debit'];
		?>
	<tr>
		<td style="border: 1px solid #cccccc; border-left:none;border-collapse:collapse; border-right:none;" ><?php echo $VoucherArray[$i]['By']; ?></td>
        <td  style="border: 1px solid #cccccc; border-left:none;border-collapse:collapse; text-align:right;">A/c...</td>
		<td style="border: 1px solid #cccccc; text-align:right;border-right:none; border-collapse:collapse;"><?php echo number_format($VoucherArray[$i]['Debit'],2); ?></td>
         <td  style="text-align:center; border: 1px solid #cccccc;font-weight:bold; border-right:none;border-top:none;border-collapse:collapse;"></td>
	</tr>
	<?php }
	else
	{
		$totalJVCredit += $VoucherArray[$i]['Credit'];
		?>
		<tr>
			<td style="border: 1px solid #cccccc; border-left:none;border-collapse:collapse;border-right:none;" ><?php echo $VoucherArray[$i]['To']; ?></td>
            <td   style="border: 1px solid #cccccc; border-left:none;border-collapse:collapse; text-align:right;">A/c...</td>
             <td  style="text-align:center; border: 1px solid #cccccc;font-weight:bold; border-right:none;border-top:none;border-collapse:collapse;"></td>
			<td style="border: 1px solid #cccccc; text-align:right;border-right:none; border-collapse:collapse;"><?php echo number_format($VoucherArray[$i]['Credit'],2); ?></td>
		</tr>
	<?php  } }?>
	
	<tr>
		<td style="border: 1px solid #cccccc; border-collapse:collapse;"  colspan="2" >Total (Rs.)</td>
		<td  style="border: 1px solid #cccccc; text-align:right;border-left:none; border-collapse:collapse;"><?php echo number_format($totalJVDebit ,2); ?></td>
        <td  style="border: 1px solid #cccccc; text-align:right;border-right:none; border-left:none; border-collapse:collapse;"><?php echo number_format($totalJVCredit, 2); ?></td>
	</tr>
	</table>
	<table style="font-size:17px;width:100%; border:none;">
    <tr>
		<td style="border-right: 0; border-left:none; border-collapse:collapse; width:20%;">Amount (In Words) :</td>
		<td style="border-left: 0; border-collapse:collapse;" colspan="9"><?php   if($totalJVDebit <> ''){echo "Rupees ". $obj_Utility->convert_number_to_words($totalJVDebit)." Only"; }?></td>
	</tr>
  
    <tr>
		<td style="border: 1px solid #cccccc;border-right: 0; border-left:none; border-collapse:collapse; width:20%;">Comments :</td>
		<td style="border: 1px solid #cccccc;border-left: 0;border-right:none; border-collapse:collapse;" colspan="9"><?php   if($VoucherArray[0]['Note'] <> ''){echo $VoucherArray[0]['Note'] ; }else{echo '-';}?></td>
    </tr>
    <tr style=" border:none;"><td><br></td></tr>
    <tr style=" border:none;"><td><br></td></tr>
    </table>

</table>
</table>
<?php }?>
<table width="100%"  style="border:none;">
	<tr>
		<td  style="width:24.33%; text-align:center;">PREPARED BY</td>
		<td style="width:47.33%; text-align:center;" >CHAIRMAN/SECRETARY/TREASURER/MC MEMBER</td>
		<td  style="width:24.33%;text-align:center;">RECEIVER SIGN</td>
	</tr>
    
    <?php if($voucherType == VOUCHER_RECEIPT)
	{?>
    <tr>
		<td  style="text-align:left;" colspan="3"><br/></td>
	</tr>
    
    <tr>
		<td  style="text-align:left;padding-left:10px;" colspan="2">This Receipt is Valid Subject to realisation of cheque..</td>
	</tr>
    <?php } ?>
</table>

</div>
<br>
</center>
</body>
</html>
