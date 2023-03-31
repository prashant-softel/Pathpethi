<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/dbconst.class.php";

include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);

include_once "classes/income_register_report.class.php";
$obj_tax_report=new income_report($dbConn);
$show_tax_details=$obj_tax_report->getGSTDetails(getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));
$Invoice_gst_details=$obj_tax_report->get_InvoiceGSTDetails(getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));
//print_r($show_tax_details);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Tax Register</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border:1px solid #cccccc;
		text-align:left;
	}	
</style>
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
</head>

<body>
<center>
<div id="mainDiv" style="width:80%;">
		<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>
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
            <div style="font-weight:bold; font-size:16px;">GST Worksheet</div>
                        <div style="font-weight; font-size:16px;">FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></div>
           <br>
           <center><p><b>GST Collected</b></p></center>
            
        </div>
        <table  style="width:100%;font-size:14px;">
                <tr>
                <th style="text-align:center;  border:1px solid #cccccc;width: 29%;"colspan="2">Month</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">IGST</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">CGST</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">SGST</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">CESS</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">Total</th>
               </tr>
               
               <?php
			   $count=0;
			   $totalAmount=0;
			  
			    if($show_tax_details <> '')
			   {
				   $tempLedgerID=0;
				  $tempDate=0;
				  $BalanceAmount=0;
				  $Credit=0;
				  //$Debit=0;
				  $FinalBalanceAmount=0;
				  $FinalCreditIGST=0;
				  $FinalCreditCGST=0;
				  $FinalCreditSGST=0;
				  $FinalCreditCESS=0;
				  //$FinalDebit=0;
				  $Total=0;

			   $AmountToGST=array();
				
				//print_r($show_tax_details);
				for($i=0;$i<sizeof($show_tax_details);$i++)
				{
					$AmountToGST[$show_tax_details[$i]['MonthYear']]['Collected'] = $show_tax_details[$i];
					// array_push($AmountToGST, $show_tax_details[$i++]); 
					//print_r($AmountToGST);
					
					if($show_tax_details[$i]['LedgerID'] <> $tempLedgerID)
					{
						
						if($tempLedgerID <> 0)
						{
							/*echo "<tr style='border:1px solid #cccccc;'><td style='text-align:center;' colspan='2'></td><td style='text-align:center;' colspan=3><b>Total</b></td><td style='text-align:right;' colspan=3><b>".number_format($Credit,2)."</b></td><td style='text-align:right;' colspan=3><b>".number_format($Credit,1)."</b></td><td style='text-align:right;'colspan=3><b>".number_format($BalanceAmount,2)."</b></td></tr>";*/
						}
						if($i == sizeof($show_tax_details))
						{
							break;
						}
						/*echo "<tr><td style='text-align:center;' colspan='2'></td><td  style='border-left:none;text-align:center;font-size:16px;' colspan='3'><b>".$show_tax_details[$i]['TO']."</b></td><td colspan=3 style='border-left:none;text-align:right;'></td><td colspan=3 style='border-left:none;text-align:right;'></td><td colspan=3 style='border-left:none;text-align:right;'></td></tr>";*/
						$tempLedgerID=$show_tax_details[$i]['LedgerID'];
						$BalanceAmount=0;
						$Credit=0;
				  		//$Debit=0;
					}
					
					if($tempLedgerID==$show_tax_details[$i]['LedgerID'])
					{
						$month = strtotime(getDisplayFormatDate($show_tax_details[$i]['MonthYear']));
						
						$Total = (float)$show_tax_details[$i]['IGST']+(float)$show_tax_details[$i]['CGST']+(float)$show_tax_details[$i]['SGST']+(float)$show_tax_details[$i]['CESS'];
						//$Debit += (float)$show_tax_details[$i]['Debit'];
						//$BalanceAmount += $Total;
						$FinalBalanceAmount +=$Total;
						$FinalCreditIGST +=(float)$show_tax_details[$i]['IGST'];
						$FinalCreditCGST +=(float)$show_tax_details[$i]['CGST'];
						$FinalCreditSGST +=(float)$show_tax_details[$i]['SGST'];
						$FinalCreditCESS +=(float)$show_tax_details[$i]['CESS'];
						//$FinalDebit +=$Debit;
						
					}
					echo "<tr><td style='text-align:left;' colspan='2'>&nbsp;".date('F', $month)."</td><td  style='border-left:none;text-align:right;' colspan='3'><a style='text-decoration: none;' href='view_ledger_details.php?lid=".IGST_SERVICE_TAX."&gid=".INCOME."&dt'>".number_format($show_tax_details[$i]['IGST'],2)."</a></td>
					
					<td colspan=3 style='border-left:none;text-align:right;'><a style='text-decoration: none;' href='view_ledger_details.php?lid=".CGST_SERVICE_TAX."&gid=".INCOME."&dt'>".number_format($show_tax_details[$i]['CGST'],2)."</a></td>
					
					<td colspan=3 style='border-left:none;text-align:right;'><a style='text-decoration: none;' href='view_ledger_details.php?lid=".SGST_SERVICE_TAX."&gid=".INCOME."&dt'>".number_format($show_tax_details[$i]['SGST'],2)."</a></td>
					
					<td colspan=3 style='border-left:none;text-align:right;'><a style='text-decoration: none;' href='view_ledger_details.php?lid=".CESS_SERVICE_TAX."&gid=".INCOME."&dt'>".number_format($show_tax_details[$i]['CESS'],2)."</td>
<td colspan=3 style='border-left:none;text-align:right;'>".number_format($Total,2)."</td></tr>";
				}   
				   echo "<tr><td  style='text-align:center;background-color:#D2D2D2;' colspan=2><b>***GST Collected Total***</b></td><td style='text-align:right;background-color:#D2D2D2;'  colspan=3><b>".number_format($FinalCreditIGST,2)."</b></td><td style='text-align:right;background-color:#D2D2D2;' colspan=3><b>".number_format($FinalCreditCGST,2)."</b></td><td style='text-align:right;background-color:#D2D2D2;' colspan=3><b>".number_format($FinalCreditSGST,2)."</b></td> <td style='text-align:right;background-color:#D2D2D2;' colspan=3><b>".number_format($FinalCreditCESS,2)."</b></td>
				   <td style='text-align:right;background-color:#D2D2D2;' colspan=3><b>".number_format($FinalBalanceAmount,2)."</b></td>
				   </tr>";
				 
				}?>
</table>
<br>
<!---------------------------------------------paid Gst Tax Details---------------------------------------->

<?php  if($Invoice_gst_details <> '')
{?>
	<center><p><b> GST Paid</b></p></center>
 <table  style="width:100%;font-size:14px;">
                <tr>
                <th style="text-align:center;  border:1px solid #cccccc; width: 29%;" colspan="2">Month</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">IGST</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">CGST</th>
                <th style="text-align:center;  border:1px solid #cccccc; width: 14%;" colspan="3">SGST</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">CESS</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">Total</th>
               </tr>
<?php }?>            
               <?php
			   $countPaid=0;
			   $totalPaidAmount=0;
			  
			    if($Invoice_gst_details <> '')
			   {
				  $tempPaidLedgerID=0;
				  $tempPaidDate=0;
				  $BalanceAmount=0;
				  $Credit=0;
				  //$Debit=0;
				  $FinalPaidBalanceAmount=0;
				  $FinalCreditPaidIGST=0;
				  $FinalCreditPaidCGST=0;
				  $FinalCreditPaidSGST=0;
				  $FinalCreditPaidCESS=0;
				  //$FinalDebit=0;
				  $PaidTotal=0;
				  $PaidToGST= array();
				
				//print_r($Invoice_gst_details);
				for($iCount=0;$iCount<sizeof($Invoice_gst_details);$iCount++)
				{
					$AmountToGST[$Invoice_gst_details[$iCount]['MonthYear']]['Paid'] = $Invoice_gst_details[$iCount];
					//array_push($PaidToGST, $Invoice_gst_details[$iCount++]);
					//print_r($PaidToGST);
					if($Invoice_gst_details[$iCount]['LedgerID'] <> $tempPaidLedgerID)
					{
						
						if($tempPaidLedgerID <> 0)
						{
							/*echo "<tr style='border:1px solid #cccccc;'><td style='text-align:center;' colspan='2'></td><td style='text-align:center;' colspan=3><b>Total</b></td><td style='text-align:right;' colspan=3><b>".number_format($Credit,2)."</b></td><td style='text-align:right;' colspan=3><b>".number_format($Credit,1)."</b></td><td style='text-align:right;'colspan=3><b>".number_format($BalanceAmount,2)."</b></td></tr>";*/
						}
						if($iCount == sizeof($Invoice_gst_details))
						{
							break;
						}
						/*echo "<tr><td style='text-align:center;' colspan='2'></td><td  style='border-left:none;text-align:center;font-size:16px;' colspan='3'><b>".$show_tax_details[$i]['TO']."</b></td><td colspan=3 style='border-left:none;text-align:right;'></td><td colspan=3 style='border-left:none;text-align:right;'></td><td colspan=3 style='border-left:none;text-align:right;'></td></tr>";*/
						$tempPaidLedgerID=$Invoice_gst_details[$iCount]['LedgerID'];
						$BalanceAmount=0;
						$Credit=0;
				  		//$Debit=0;
					}
					
					if($tempPaidLedgerID==$Invoice_gst_details[$iCount]['LedgerID'])
					{
						$Paidmonth = strtotime(getDisplayFormatDate($Invoice_gst_details[$iCount]['MonthYear']));
						
						$PaidTotal = (float)$Invoice_gst_details[$iCount]['TotalIGST']+(float)$Invoice_gst_details[$iCount]['TotalCGST']+(float)$Invoice_gst_details[$iCount]['TotalSGST']+(float)$Invoice_gst_details[$iCount]['TotalCESS'];
						//$Debit += (float)$show_tax_details[$i]['Debit'];
						//$BalanceAmount += $Total;
						$FinalPaidBalanceAmount +=$PaidTotal;
						$FinalCreditPaidIGST +=(float)$Invoice_gst_details[$iCount]['TotalIGST'];
						$FinalCreditPaidCGST +=(float)$Invoice_gst_details[$iCount]['TotalCGST'];
						$FinalCreditPaidSGST +=(float)$Invoice_gst_details[$iCount]['TotalSGST'];
						$FinalCreditPaidCESS +=(float)$Invoice_gst_details[$iCount]['TotalCESS'];
						//$FinalDebit +=$Debit;
						
					}
					
					echo "<tr><td style='text-align:left;' colspan='2'>&nbsp;".date('F', $Paidmonth)."</td><td  style='border-left:none;text-align:right;' colspan='3'>".number_format($Invoice_gst_details[$iCount]['TotalIGST'],2)."</td>
					
					<td colspan=3 style='border-left:none;text-align:right;'>".number_format($Invoice_gst_details[$iCount]['TotalCGST'],2)."</td>
					
					<td colspan=3 style='border-left:none;text-align:right;'>".number_format($Invoice_gst_details[$iCount]['TotalSGST'],2)."</td>
					
					<td colspan=3 style='border-left:none;text-align:right;'>".number_format($Invoice_gst_details[$iCount]['TotalCESS'],2)."</td>
					
					<td colspan=3 style='border-left:none;text-align:right;'>".number_format($PaidTotal,2)."</td></tr>";
				}   
				   echo "<tr><td  style='text-align:center;background-color:#D2D2D2;' colspan=2><b>***GST Paid Total***</b></td><td style='text-align:right;background-color:#D2D2D2;'  colspan=3><b>".number_format($FinalCreditPaidIGST,2)."</b></td><td style='text-align:right;background-color:#D2D2D2;' colspan=3><b>".number_format($FinalCreditPaidCGST,2)."</b></td><td style='text-align:right;background-color:#D2D2D2;' colspan=3><b>".number_format($FinalCreditPaidSGST,2)."</b></td> <td style='text-align:right;background-color:#D2D2D2;' colspan=3><b>".number_format($FinalCreditPaidCESS,2)."</b></td>
				   <td style='text-align:right;background-color:#D2D2D2;' colspan=3><b>".number_format($FinalPaidBalanceAmount,2)."</b></td>
				   </tr>";
				   
				}?>
</table>
<br>
<!----------------------------------------------GST TOTAL DIFFRENCE ---------------------------------->
<?php 
 if($Invoice_gst_details <> '')
{?>
<center><p><b>GST Payable</b></p></center>
<table style="width:100%;font-size:14px;">
                <tr>
                <th style="text-align:center;  border:1px solid #cccccc; width: 29%;" colspan="2">Month</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">IGST</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">CGST</th>
                <th style="text-align:center;  border:1px solid #cccccc; width: 14%;" colspan="3">SGST</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">CESS</th>
                <th style="text-align:center;  border:1px solid #cccccc;width: 14%;" colspan="3">Total</th>
               </tr>
              
               <?php
			   
			  $paidTotalGST=0;
			 // print_r($AmountToGST);
			  if(sizeof($AmountToGST > 0))
			  {
			  foreach($AmountToGST as $k => $v)
			  {
				
			    $Paidmonth = strtotime(getDisplayFormatDate($k));
				$paidTotalGST=(float)$v['Collected']['IGST'] - $v['Paid']['TotalIGST']+(float)$v['Collected']['CGST'] - $v['Paid']['TotalCGST']+(float)$v['Collected']['SGST'] - $v['Paid']['TotalSGST']+(float)$v['Collected']['CESS'] - $v['Paid']['TotalCESS'];
				$FinalPendingBalanceAmount +=$paidTotalGST;
				$pendingIGST+=(float)$v['Collected']['IGST'] - $v['Paid']['TotalIGST'];
				$pendingCGST+=(float)$v['Collected']['CGST'] - $v['Paid']['TotalCGST'];
				$pendingSGST+=(float)$v['Collected']['SGST'] - $v['Paid']['TotalSGST'];
				$pendingCESS+=(float)$v['Collected']['CESS'] - $v['Paid']['TotalCESS'];
				
					
					echo "<tr><td style='text-align:left;' colspan='2'>&nbsp;".date('F', $Paidmonth)."</td><td  style='border-left:none;text-align:right;' colspan='3'>".number_format($v['Collected']['IGST'] - $v['Paid']['TotalIGST'],2)."</td>
					
					<td colspan=3 style='border-left:none;text-align:right;'>".number_format($v['Collected']['CGST'] - $v['Paid']['TotalCGST'],2)."</td>
					
					<td colspan=3 style='border-left:none;text-align:right;'>".number_format($v['Collected']['SGST'] - $v['Paid']['TotalSGST'],2)."</td>
					
					<td colspan=3 style='border-left:none;text-align:right;'>".number_format($v['Collected']['CESS'] - $v['Paid']['TotalCESS'],2)."</td>
					
					<td colspan=3 style='border-left:none;text-align:right;'>".number_format($paidTotalGST,2)."</td></tr>";
			  }
				   echo "<tr><td  style='text-align:center;background-color:#D2D2D2;' colspan=2><b>***GST Payable Total***</b></td>
				   <td style='text-align:right;background-color:#D2D2D2;'  colspan=3><b>".number_format($pendingIGST,2)."</b></td>
				   <td style='text-align:right;background-color:#D2D2D2;' colspan=3><b>".number_format($pendingCGST,2)."</b></td>
				   <td style='text-align:right;background-color:#D2D2D2;' colspan=3><b>".number_format($pendingSGST,2)."</b></td> 
				   <td style='text-align:right;background-color:#D2D2D2;' colspan=3><b>".number_format($pendingCESS,2)."</b></td>
				   <td style='text-align:right;background-color:#D2D2D2;' colspan=3><b>".number_format($FinalPendingBalanceAmount,2)."</b></td>
				   </tr>";
				   
			  }?>
</table>
<?php }?>
</div>
</div>
</center>
</body>
</html>