<?php
include_once "classes/include/dbop.class.php"; 
 include_once("classes/dbconst.class.php");
 include_once "classes/utility.class.php";
 include "classes/include/fetch_data.php";

$m_dbConn = new dbop();
 ?>
<?php
include_once("classes/view_ledger_details.class.php");
$objFetchData = new FetchData($m_dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$obj_ledger_details = new view_ledger_details($m_dbConn);
$m_objUtility = new utility($m_dbConn);


if(isset($_SESSION['from_date']) && isset($_SESSION['to_date']) && isset($_REQUEST['dt']))
{
	
	$get_details = $obj_ledger_details->details($_REQUEST["gid"],$_REQUEST['lid'], getDBFormatDate($_SESSION['from_date']) , getDBFormatDate($_SESSION['to_date']));
	
}
else
{
	$get_details = $obj_ledger_details->details($_REQUEST["gid"],$_REQUEST['lid'], "", "");

}

if($_REQUEST["gid"] == 1 || $_REQUEST["gid"] == 2)
{
	$date = ""; 
	if(isset($_SESSION['from_date']) && isset($_SESSION['to_date']) && isset($_REQUEST['dt']))
	{	
		$date = $_SESSION['from_date'];
	}
	else
	{
		$date = $m_objUtility->getCurrentYearBeginingDate($_SESSION['default_year']);		
	}
	if($date <> "")
	{
		$res = $m_objUtility->getOpeningBalance($_REQUEST['lid'],$date);
		$arLedgerParentDetails = $m_objUtility->getParentOfLedger($_REQUEST['lid']);
		if(!(empty($arLedgerParentDetails)))
		{
			$LedgerGroupID = $arLedgerParentDetails['group'];
			$LedgerCategoryID = $arLedgerParentDetails['category'];
		}
		if($res <> "")
		{
			if($LedgerCategoryID == BANK_ACCOUNT || $LedgerCategoryID == CASH_ACCOUNT)
			{
				$data[0] = array("id" => $_REQUEST['lid'] , "Date" => $res['OpeningDate'] , "Particular" => $res['LedgerName'] , "Debit" => ($res['OpeningType'] == TRANSACTION_CREDIT) ? $res['Total'] : 0 , "Credit" => ($res['OpeningType'] == TRANSACTION_DEBIT) ? $res['Total'] : 0  , "VoucherID" => 0 , "VoucherTypeID" => 0 , "Is_Opening_Balance" => 1,"owner_name" =>"");
			}
			else
			{
				$data[0] = array("id" => $_REQUEST['lid'] , "Date" => $res['OpeningDate'] , "Particular" => $res['LedgerName'] , "Debit" => ($res['OpeningType'] == TRANSACTION_DEBIT) ? $res['Total'] : 0 , "Credit" => ($res['OpeningType'] == TRANSACTION_CREDIT) ? $res['Total'] : 0  , "VoucherID" => 0 , "VoucherTypeID" => 0 , "Is_Opening_Balance" => 1,"owner_name" =>"");		
			}
			if($get_details <> "")
			{
				for($i = 0 ; $i < sizeof($get_details); $i++)
				{
					$data[$i + 1] = $get_details[$i];
				}
			}
		}
		else
		{
			$data = $get_details;	
		}
	}
}
else
{
	$data = $get_details;		
}
$IsCreditor=$obj_ledger_details->IsCreditor($_REQUEST['lid']);
?>
<html>




<style>
	body{
		width: 90%;
		margin: auto;
		margin-bottom:30px;
		font-size: 18px;
		/*color:#5A5959;*/
	}
	table {
    	border: 1px solid #cccccc;
		border-collapse: collapse;
		text-align:left;
	}
	th, td {
   		text-align:left;
		border: 1px solid #cccccc;
		border-collapse: collapse;
	}	
	
	@media print
	{    
		.no-print, .no-print *
		{
			display: none !important;
		}
	}
</style>

<style type="text/css" media="print">
  @page { size: landscape; }
</style>

<script>
function PrintPage() 
	{
		
		//Get the print button and put it into a variable
		var btnPrint = document.getElementById("Print");
		var btnExport = document.getElementById("btnExport");
		
		 //Set the print button visibility to 'hidden' 
		btnPrint.style.visibility = 'hidden';
		btnExport.style.visibility = 'hidden';
		
		//Print the page content
        window.print();
		
		//Set the print button to 'visible' again 
       btnPrint.style.visibility = 'visible';
	   btnExport.style.visibility = 'visible';
}
</script>
<script type="text/javascript" language="javascript" src="js/jquery-2.0.3.min.js"></script>
<title><?php echo $data[0]['Particular'];?></title>
</head>

<body>
 <br>
 <div align="center" style="alignment-adjust:middle; left:80px;">
		<INPUT TYPE="button" id="Print" onClick="PrintPage()" name="Print!" value="Print!"  width="300" style="height:30px; font-size:14px;">
        <INPUT TYPE="button" id="btnExport"  name="btnExport" value="Export To Excel"  width="300" style="height:30px; font-size:14px;">
</div>
 <br>
<div  id="originalDiv" style="border: 1px solid #cccccc;border-bottom:none;">
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
           	 	<br/><div style="font-weight:bold; font-size:16px;"><?php echo $data[0]['Particular']; if($data[1]['owner_name'] <> ""){echo ' - ' .$data[1]['owner_name'] ;}?></div><br/>
        	 </div>
               
<table  width="100%"  style="border-left:none; border-right:none; ">
	<thead>
        <tr style="height:30px; text-align:center;border:0px solid #cccccc;">
        	 <th  style="text-align:center;border:1px solid #cccccc; width:80px; border-left:none;">Date</th>
            <th style="text-align:center;border:1px solid #cccccc; ">Particular</th>
            <th style="text-align:center;border:1px solid #cccccc; ">Cheque/Bill Number</th>
            <th style="text-align:center;border:1px solid #cccccc; ">Debit (Rs.)</th>
            <th style="text-align:center;border:1px solid #cccccc;">Credit (Rs.)</th>
            <th style="text-align:center;border:1px solid #cccccc; border-right:none;">Balance (Rs.)</th>
        </tr>
    </thead>
   	<tbody>  
        <?php
		$BalanceAmt=0;
		$DebitTotal = 0;
		$CreditTotal = 0;
		
		if($data<>"")
		{			
				foreach($data as $k => $v)
				{
					
					//$categoryid=$obj_utility->getParentOfLedger($data[$k]['id']);
					$categoryid=$obj_ledger_details->obj_utility->getParentOfLedger($data[$k]['id']);
					
					$Is_Opening_Balance=$data[$k]['Is_Opening_Balance'];
					//echo 'CATEGORYID'.$categoryid['category'];
					if($categoryid['category']==BANK_ACCOUNT || $categoryid['category']== CASH_ACCOUNT )
					{ 
							$CreditAmt = $data[$k]['Debit'];
							$DebitAmt = $data[$k]['Credit'];	
							
							if($Is_Opening_Balance==1 && $CreditAmt <> 0)
							{
								$BalanceAmt -= $CreditAmt;
							}
							else if($Is_Opening_Balance==1 && $DebitAmt <> 0)
							{
								$BalanceAmt += $DebitAmt;
							}
							else
							{
								if($DebitAmt <> 0 )
								{
									$BalanceAmt += $DebitAmt;
								}
								
								if($CreditAmt <> 0)
								{
									$BalanceAmt -= $CreditAmt;
								}																	
							}
					
					}
					else
					{
						$DebitAmt = $data[$k]['Debit'];
						$CreditAmt = $data[$k]['Credit'];
						//$BalanceAmt = $BalanceAmt + $CreditAmt - $DebitAmt;
						$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
					}
					$DebitTotal = $DebitTotal + $DebitAmt;
					$CreditTotal = $CreditTotal + $CreditAmt;
				?>
				<tr id="tr_<?php echo $data[$k]['VoucherID']; ?>">
                <?php $voucher_details=$obj_ledger_details->get_voucher_details('',$data[$k]['VoucherID']);
						  $voucher_type =$obj_ledger_details->get_voucher_details($data[$k]['VoucherTypeID']);
						  // echo  $voucher_details[0]['VoucherNo'];
				?>
         
					<td  style="text-align:center;border:1px solid #cccccc;width:80px;border-left:none;"><?php echo getDisplayFormatDate($data[$k]['Date']);?></td>
					<td style="border:1px solid #cccccc;">
					<?php
						if(($_REQUEST['gid']==1 || $_REQUEST['gid']==2) && $data[$k]['Is_Opening_Balance'] ==1)
						{
								echo 'Opening Balance';
						}
						else
						{
								//echo "<br>".$voucher_details[0]['Note']; 	
								echo "[".$voucher_type." - ".$voucher_details[0]['VoucherNo']."]<br>"; 
						}
						
						if($data[$k]['VoucherTypeID']==VOUCHER_JOURNAL || $data[$k]['VoucherTypeID']==VOUCHER_SALES)
						{ 
							if($DebitAmt <> 0)
							{
								$finalData = $obj_ledger_details->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$_REQUEST['lid'],$_REQUEST["gid"], "To");
								
								echo "<br>".$finalData[0]['ledger_name'];
							}
							else
							{
								$finalData = $obj_ledger_details->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$_REQUEST['lid'],$_REQUEST["gid"], "By");
								echo "<br>".$finalData[0]['ledger_name'];
							}
						}
						else
						{
							$finalData = $obj_ledger_details->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$_REQUEST['lid'],$_REQUEST["gid"]);
							echo "<br>".$finalData[0]['ledger_name'];
						}
						
						if(($_REQUEST['gid']==1 || $_REQUEST['gid']==2) && $data[$k]['Is_Opening_Balance'] ==1)
						{
								//echo 'Opening Balance';
						}
						else
						{
								echo "<br>".$voucher_details[0]['Note']; 	
								//echo "<br> [".$voucher_type." - ".$voucher_details[0]['VoucherNo']."]"; 
						}
						
						
						
						
					?>
                    </td>
					
                     <td style="text-align:center;border:1px solid #cccccc;">
                    <?php echo $obj_ledger_details->getChequeNumber($data[$k]['VoucherID']);?></td>
                    <td style="text-align:right;border:1px solid #cccccc;"><?php if($DebitAmt <> 0){echo number_format($DebitAmt, 2);}else{echo '0.00';} ?></td>
					<td  style="text-align:right;border:1px solid #cccccc;"><?php if($CreditAmt <> 0){echo number_format($CreditAmt, 2);}else{echo '0.00';} ?></td>
                    <td style="text-align:right;border:1px solid #cccccc;  border-right:none;"><?php if($IsCreditor==true){echo number_format(abs($BalanceAmt), 2);} else{echo number_format($BalanceAmt, 2);}?></td>
                
                </tr>
				 <?php
				
				}
				?>
                <tr><td colspan="3" style="text-align:center;border:1px solid #cccccc;  border-left:none; border-bottom:none;"> <b>Total (Rs.)</b></td><td style="text-align:right;border:1px solid #cccccc; border-bottom:none;"><b><?php echo number_format($DebitTotal, 2);?></b></td><td style="text-align:right;border:1px solid #cccccc; border-bottom:none;"><b><?php echo number_format($CreditTotal, 2);?></b></td><td style="text-align:right;border:1px solid #cccccc; border-right:none; border-bottom:none;"><b><?php echo number_format($BalanceAmt, 2);?></b></td></tr>
				
		<?php }
		
		else
		{
			?>
            </tbody><tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records Not Found....<!--  by admin --></b></font></td></tr>
            <?php	
		}
		?>
            
</table>

</div>

</body>

<script>
$("#btnExport").click(function(e) {
  window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#originalDiv").html()));
  e.preventDefault();
         
});

</script>
</html>