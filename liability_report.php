<?php 

include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/dbconst.class.php";
include "classes/include/fetch_data.php";

include_once("classes/home_s.class.php");
$obj_AdminPanel = new CAdminPanel($dbConn);
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);

class CAssetSummary
{
	function __construct()
	{
		
	}
}
?>
<html>
<title>Liability Register</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border: 0px solid black;
		text-align:left;
	}
	tr:hover {background-color: #f5f5f5}		
</style>
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
<body>
<div id="mainDiv">
<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>
<div style="border:1px solid #cccccc; border-bottom:none;">
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <!--<div id="society_type" style="font-weight:bold; font-size:20px;">PREMISES CO-OPERATIVE SOCIETY LTD.</div>-->
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?>
            </div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">Liability Register</div>
            
            <div style="font-weight; font-size:16px;">As on Date:<?php echo date("d.m.Y");?></div>
           
            
        </div>
</div>
<table  style="width:100%;font-size:14px;">

<tr>
                <th style="text-align:center; width:5%; border:1px solid #cccccc;">Sr. No.</th>
                <th style="text-align:center;  width:20%; border:1px solid #cccccc;"" >SubCategory</th>
                <th style="text-align:center;  width:20%; border:1px solid #cccccc;"" >Liability</th>
                <th style="text-align:center;  width:15%;border:1px solid #cccccc;"" colspan="3">Debits (In Rs.)</th>
                <th style="text-align:center;  width:15%;border:1px solid #cccccc;"" colspan="3">Credits (In Rs.)</th>
                <th style="text-align:center;  width:15%; border:1px solid #cccccc;"" colspan="3">Balance (In Rs.)</th>
                
</tr>
<?php
    $arBankDetails = $obj_AdminPanel->GetSummary(LIABILITY);
	//$arBankDetails = $obj_AdminPanel->GetLiabilitySummary(4);
   //print_r($arBankDetails);
   $count=0;
   $totalDebit=0;
   $totalCredit=0;
   $totalBalance=0;
   
   if($arBankDetails <> '')
   {
		   foreach($arBankDetails as $arData=>$arvalue)
		   {
			   
					$count++;
					$category =$arvalue["CategoryID"];
					$subcategory =$obj_AdminPanel->GetCategoryNameFromID($arvalue["SubCategoryID"]);
					$subcategoryid =$arvalue["SubCategoryID"];
					$BankName = $obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]);
					$receipts =$arvalue["debit"];
					$payments = $arvalue["credit"]; 
					$BalAmount =  $payments - $receipts;
		
					$totalDebit= $totalDebit + $receipts;
					$totalCredit=$totalCredit + $payments;
					$totalBalance=$totalBalance + $BalAmount;
			   
			   echo "<tr><td style='border:1px solid #cccccc;text-align:center;'>".$count."</td><td colspan=1 style='border:1px solid #cccccc;border-left:none;text-align:left;'><a href='ledger_details.php?gid=".$category."&cid=".$subcategoryid."'>".$subcategory."</a></td><td  style='border:1px solid #cccccc;border-left:none;text-align:left;'>".$BankName."</td><td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:right;'>".number_format($receipts,2)."</td><td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:right;'>".number_format($payments,2)."</td><td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:right;'>".number_format($BalAmount,2)."</td>";
			   
		   }
		   
		   echo "<tr><td colspan=3 style='border:1px solid #cccccc;text-align:center;background-color: #D3D3D3;'>***Total Liabilities***</td><td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:right;background-color: #D3D3D3;'>".number_format($totalDebit,2)."</td><td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:right;background-color: #D3D3D3;'>".number_format($totalCredit,2)."</td><td colspan=3 style='border:1px solid #cccccc;border-left:none;text-align:right;background-color: #D3D3D3;'>".number_format($totalBalance,2)."</td>";
   }
   else
   {
	   
	   echo "<tr><td colspan=13 style='border:1px solid #cccccc;text-align:center;'>No Records Found..</td></tr>";
	   
	   
	}
   
   ?>
   
   </table>
  </div> 
</body>