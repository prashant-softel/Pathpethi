<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/dbconst.class.php";
include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
include_once "classes/list_member.class.php";
include_once "classes/income_register_report.class.php";
$obj_tax_report=new income_report($dbConn);

$obj_unit=new list_member($dbConn);
$unitList=$obj_unit->getAllUnits();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Incoming GST Report</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border:1px solid #cccccc;
		text-align:left;
	}	
/*@media print {

    @page {size: A4 landscape; }
}*/
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
            <div style="font-weight:bold; font-size:16px;">Incoming GST Reports</div>
                        <div style="font-weight; font-size:16px;">FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></div>
           
            
        </div>
        <table  style="width:100%;font-size:14px;">
                <tr>
                <th style="text-align:center;  border:1px solid #cccccc; width:8%;"colspan="">Unit No</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:18%;"colspan="">Member Name</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:11%;" colspan="">GSTIN No.</th>
                <!--<th style="text-align:center;  border:1px solid #cccccc; width:9%;" colspan="">PAN No.</th>-->
              <!--  <th style="text-align:center;  border:1px solid #cccccc; width:8%;"colspan="">Invoice No</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:7%;" colspan="">Invoice Gross Amount</th>-->
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;" colspan="">IGST</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;" colspan="">CGST</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;" colspan="">SGST</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;" colspan="">CESS</th>
               <!-- <th style="text-align:center;  border:1px solid #cccccc; width:7%;" colspan="">Invoice Amount</th>
                <th style="text-align:center;  border:1px solid #cccccc; width:6%;" colspan="">TDS</th>-->
                <th style="text-align:center;  border:1px solid #cccccc; width:10%;" colspan="">Total</th>
               <!-- <th style="text-align:center;  border:1px solid #cccccc"" colspan="3">Total</th>-->
               </tr>
               
               <?php
			   $count=0;
			   $totalAmount=0;
				
					for($iCount=0;$iCount<sizeof($unitList);$iCount++)
							{
								$UnitID=$unitList[$iCount];
								
								$objFetchData->GetMemberDetails($UnitID);
								$memberName=$objFetchData->objMemeberDetails->sMemberName;
								$unitNumber=$objFetchData->objMemeberDetails->sUnitNumber;
								$OwnerGSTIN=$objFetchData->objMemeberDetails->sMemberGstinNo;
								
							$GST_Incoming_Reoprt=$obj_tax_report->getGSTIncomingDetails(getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']),$UnitID);

					if($GST_Incoming_Reoprt <> '' )
					{
						for($k=0;$k<sizeof($GST_Incoming_Reoprt);$k++)
						{
						//$month = getDisplayFormatDate($GST_Incoming_Reoprt[$i]['MonthYear']);
						$IGSTTotal=$GST_Incoming_Reoprt[$k]['TotalIGST'];
						$CGSTTotal=$GST_Incoming_Reoprt[$k]['TotalCGST'];
						$SGSTTotal=$GST_Incoming_Reoprt[$k]['TotalSGST'];
						$CESSTotal=$GST_Incoming_Reoprt[$k]['TotalCESS'];
						$TotalAmount = (float)$GST_Incoming_Reoprt[$k]['TotalIGST']+(float)$GST_Incoming_Reoprt[$k]['TotalCGST']+(float)$GST_Incoming_Reoprt[$k]['TotalSGST']+(float)$GST_Incoming_Reoprt[$k]['TotalCESS'];
						//$Debit += (float)$paid_Invoice_details[$i]['Debit'];
						$BalanceAmount += $TotalAmount;
						$FinalBalanceAmount +=$TotalAmount;
						$FinalCreditIGST +=(float)$GST_Incoming_Reoprt[$k]['TotalIGST'];
						$FinalCreditCGST +=(float)$GST_Incoming_Reoprt[$k]['TotalCGST'];
						$FinalCreditSGST +=(float)$GST_Incoming_Reoprt[$k]['TotalSGST'];
						$FinalCreditCESS +=(float)$GST_Incoming_Reoprt[$k]['TotalCESS'];
						 
					}
						
						
					echo "<tr>
					
					<td style='text-align:left;'>&nbsp;&nbsp; ".$unitNumber."</td>
					
					<td style='text-align:left;' colspan=''>".$memberName."</td>

					<td  style='border-left:none;text-align:left;' colspan=''>&nbsp;&nbsp;".$OwnerGSTIN."</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($IGSTTotal,2)."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($CGSTTotal,2)."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($SGSTTotal,2)."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($CESSTotal,2)."&nbsp;</td>
					
					<td colspan='' style='border-left:none;text-align:right;'>".number_format($TotalAmount ,2)."&nbsp;</td>
					</tr>";
				  
				}
			}
				   echo "<tr><td  style='text-align:center;background-color:#D2D2D2;' colspan=3><b>***Grand Total***</b></td>
				 <td style='text-align:right;background-color:#D2D2D2;'  ><b>".number_format($FinalCreditIGST,2)."</b>&nbsp;</td>
				 <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalCreditCGST,2)."</b>&nbsp;</td>
				 <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalCreditSGST,2)."</b>&nbsp;</td> 							
				 <td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalCreditCESS,2)."</b>&nbsp;</td>
				<td style='text-align:right;background-color:#D2D2D2;' ><b>".number_format($FinalBalanceAmount,2)."</b>&nbsp;</td>
				  
				   </tr>";
				   
					?>
</table>
</div>
</div>
</center>
</body>
</html>