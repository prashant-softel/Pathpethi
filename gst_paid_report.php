<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/dbconst.class.php";

include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);

include_once "classes/income_register_report.class.php";
$obj_tax_report=new income_report($dbConn);
$paid_Invoice_details=$obj_tax_report->paid_InvoiceGSTDetails(getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));
//print_r($paid_Invoice_details);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Outgoing GST Report</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border:1px solid #cccccc;
		text-align:left;
	}	
@media print {

    @page {size: A4 landscape; }
}
</style>
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
</head>

<body>
<center>
<div id="mainDiv" style="width:80%;">
		<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>
        <script>
		document.getElementById('landscape').value='1';
		</script>
<div style="border:1px solid #cccccc;">
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?>
            </div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">Outgoing GST Reports</div>
                        <div style="font-weight; font-size:16px;">FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></div>
           
            
        </div>
        <table  style="width:100%;font-size:14px;">
                <tr>
                <th style="text-align:center;  border:1px solid #cccccc; width:8%;"colspan="">Invoice Date</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:18%;"colspan="">Vendor</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:11%;" colspan="">GSTIN No.</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:9%;" colspan="">PAN No.</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:8%;"colspan="">Invoice No</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:7%;" colspan="">Invoice Gross Amount</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;" colspan="">IGST</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;" colspan="">CGST</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;" colspan="">SGST</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;" colspan="">CESS</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:7%;" colspan="">Invoice Amount</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;" colspan="">TDS</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:10%;" colspan="">Net Payable Amount</th>
               <!-- <th style="text-align:center;  border:1px solid #cccccc"" colspan="3">Total</th>-->
               </tr>
               
               <?php
			   $count=0;
			   $totalAmount=0;
			  
			    if($paid_Invoice_details <> '')
			   {
				   $tempLedgerID=0;
				  $tempDate=0;
				  $BalanceAmount=0;
				  $Credit=0;
				  //$Debit=0;
				  $FinalBalanceAmount=0;
				  $FinalGrossTotal=0;
				  $FinalCreditIGST=0;
				  $FinalCreditCGST=0;
				  $FinalCreditSGST=0;
				  $FinalCreditCESS=0;
				  $FinalInvoiceAmount=0;
				  $FinalTDSAmount=0;
				  $FinalNetPayableAmount=0;
				  //$FinalDebit=0;
				  $Total=0;
				  $GrossTotal=0;
				  $TotalPayableAmount=0;
	
				for($i=0;$i<sizeof($paid_Invoice_details);$i++)
				{
					if($paid_Invoice_details[$i]['LedgerID'] <> $tempLedgerID)
					{
						$BalanceAmount=0;
						$Credit=0;
				  		$Debit=0;
					}
					
					if($tempLedgerID==$paid_Invoice_details[$i]['LedgerID'])
					{
						$month = getDisplayFormatDate($paid_Invoice_details[$i]['MonthYear']);
						
						$Total = (float)$paid_Invoice_details[$i]['TotalIGST']+(float)$paid_Invoice_details[$i]['TotalCGST']+(float)$paid_Invoice_details[$i]['TotalSGST']+(float)$paid_Invoice_details[$i]['TotalCESS'];
						$Debit += (float)$paid_Invoice_details[$i]['Debit'];
						$BalanceAmount += $Total;
						$FinalBalanceAmount +=$Total;
						$FinalCreditIGST +=(float)$paid_Invoice_details[$i]['TotalIGST'];
						$FinalCreditCGST +=(float)$paid_Invoice_details[$i]['TotalCGST'];
						$FinalCreditSGST +=(float)$paid_Invoice_details[$i]['TotalSGST'];
						$FinalCreditCESS +=(float)$paid_Invoice_details[$i]['TotalCESS'];
						$FinalInvoiceAmount+=(float)$paid_Invoice_details[$i]['TotalInvoiceAmount'];
						$FinalTDSAmount+=(float)$paid_Invoice_details[$i]['TDSAmounts'];
						$FinalDebit +=$Debit;
						
						$GrossTotal=(float)$paid_Invoice_details[$i]['TotalInvoiceAmount']-(float)$paid_Invoice_details[$i]['TotalIGST']-(float)$paid_Invoice_details[$i]['TotalCGST']-(float)$paid_Invoice_details[$i]['TotalSGST']-(float)$paid_Invoice_details[$i]['TotalCESS'];
						
						$TotalPayableAmount=(float)$paid_Invoice_details[$i]['TotalInvoiceAmount']-(float)$paid_Invoice_details[$i]['TDSAmounts'];
						$FinalGrossTotal +=$GrossTotal;
						$FinalNetPayableAmount+=$TotalPayableAmount;
					}
					
					echo "<tr>
					<td style='text-align:left;' colspan=''>&nbsp;".$month."</td>
					
					<td style='text-align:left;' colspan=''>".$paid_Invoice_details[$i]['LegerName']."</td>

					<td  style='border-left:none;text-align:left;' colspan=''>&nbsp;".$paid_Invoice_details[$i]['GSTIN_No']."</td>
					
					<td  style='border-left:none;text-align:left;' colspan=''>&nbsp;".$paid_Invoice_details[$i]['PAN_No']."</td>
					<td style='text-align:left;' colspan=''>&nbsp;&nbsp;".$paid_Invoice_details[$i]['InvoiceRaisedVoucherNo']."</td>
					
					<td  style='border-left:none;text-align:right;' colspan=''>".number_format($GrossTotal,2)."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($paid_Invoice_details[$i]['TotalIGST'],2)."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($paid_Invoice_details[$i]['TotalCGST'],2)."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($paid_Invoice_details[$i]['TotalSGST'],2)."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($paid_Invoice_details[$i]['TotalCESS'],2)."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($paid_Invoice_details[$i]['TotalInvoiceAmount'],2)."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($paid_Invoice_details[$i]['TDSAmounts'],2)."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($TotalPayableAmount,2)."&nbsp;</td>
					</tr>";
				}   
				   echo "<tr><td  style='text-align:center;background-color:#D2D2D2;' colspan=5><b>***Grand Total***</b></td>
				  <td style='text-align:right;background-color:#D2D2D2;'  ><b>".number_format($FinalGrossTotal,2)."</b>&nbsp;</td>
				 <td style='text-align:right;background-color:#D2D2D2;'  ><b>".number_format($FinalCreditIGST,2)."</b>&nbsp;</td>
				   <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalCreditCGST,2)."</b>&nbsp;</td>
				   <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalCreditSGST,2)."</b>&nbsp;</td> 
				   <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalCreditCESS,2)."</b>&nbsp;</td>
				  <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalInvoiceAmount,2)."</b>&nbsp;</td>
				   <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalTDSAmount,2)."</b>&nbsp;</td>
				   <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalNetPayableAmount,2)."</b>&nbsp;</td>
				   </tr>";
				   
				}?>
</table>
</div>
</div>
</center>
</body>
</html>