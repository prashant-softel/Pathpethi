<?php 
include_once "ses_set_as.php"; 
include_once("classes/dbconst.class.php");
include "classes/include/fetch_data.php";
include_once "classes/utility.class.php";
$objFetchData = new FetchData($m_dbConn);
$m_objUtility = new utility($m_dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
if(isset($_REQUEST['Cluster']))
{
	$DecodeUrl = urldecode($_REQUEST['Cluster']);
	$DecodeJSON = json_decode($DecodeUrl);
	$UnitArray = $DecodeJSON;
	$UnitArraystring = implode(",", $UnitArray);
}

?>

<?php
include_once "classes/unit_report.class.php";
$obj_view_unit_report = new unit_report($m_dbConn);

//$show_owner_details=$obj_view_unit_report->show_owner_name($_REQUEST["uid"]);
//$show_due_details=$obj_view_unit_report->show_due_details($_REQUEST["uid"]);

$AryUnitToDisplay = array();
if($_REQUEST["uid"] <> 0)
{
	array_push($AryUnitToDisplay, $_REQUEST["uid"]);
}
else
{
	if(!isset($_REQUEST['Cluster']))
	{
		$UnitArray = $obj_view_unit_report->getAllUnits();
	}
	$AryUnitToDisplay = $UnitArray;
}
?>

<html>
<head>

<style>
	table {
    	border-collapse: collapse;
		text-align:center;
	}
	table, th, td {
   		border: 0px solid black;
		text-align:center;
		padding-top:0px;
		padding-bottom:0px;
	}	
	
	@media print
	{    
		.no-print, .no-print *
		{
			display: none !important;
		}
	}
</style>
	<!--<link rel="stylesheet" type="text/css" href="css/pagination.css" >-->
    <script type="text/javascript" src="js/jsReportPdfNEmail.js"></script>
    <!--<script type="text/javascript" src="js/ajax.js"></script>-->
	<script type="text/javascript" language="javascript" src="js/jquery-2.0.3.min.js"></script>
    <script language="javascript" type="application/javascript">
	//alert("test");
	var unitstring;
	var jUnitArray;
	var jUnitIDNoArray = [];
	var CurrentUnit;
	var a; 
	var currentLocation = window.location.href;
	unitstring = "<?php echo "".$UnitArraystring.""; ?>";
	if (unitstring != null || unitstring != '')
	{
		jUnitArray = unitstring.split(',');
		CurrentUnit = "<?php echo $_REQUEST['uid']?>";
		a = jUnitArray.indexOf(CurrentUnit); 
		//alert("indexofval:" + a); 
	}
	
	
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	
	function ButtonStatus()
	{
		if(a == '0')
		{
			document.getElementById('prev').style.visibility = 'hidden';
		}
		else if(jUnitArray.length > 1)
		{
			document.getElementById('prev').style.visibility = 'visible';
		}
		
		if(a == jUnitArray.length - 1)
		{
			document.getElementById('next').style.visibility = 'hidden';	
		}
		else  if(jUnitArray.length > 1)
		{
			document.getElementById('next').style.visibility = 'visible';	
		}		
		
	}
	function Prev()
	{
		//window.location.href = 'unit_report.php?&uid=' + jUnitArray[a - 1] ;
		if(CurrentUnit != 0)
		{
			if (unitstring != null || unitstring != '')
			{
				currentLocation = currentLocation.replace('uid='+jUnitArray[a], 'uid='+jUnitArray[a - 1]);
				window.location.href = currentLocation;
			}
		}
		else
		{
			ShowSingle();
		}
		//ButtonStatus();
	}
	
	function Next()
	{
		if(CurrentUnit != 0)
		{
			if (unitstring != null || unitstring != '')
			{
				currentLocation = currentLocation.replace('uid='+jUnitArray[a], 'uid='+jUnitArray[a + 1]);
				window.location.href = currentLocation;		
			}
		}
		else
		{
			ShowSingle();
		}
	}
	
	function ShowAll()
	{
		currentLocation = currentLocation.replace('uid='+jUnitArray[a], 'uid='+0);
		window.location.href = currentLocation;		
	}
	
	function ShowSingle()
	{
		currentLocation = currentLocation.replace('uid='+0, 'uid='+jUnitArray[0]);
		window.location.href = currentLocation;		
	}
		
	function PrintPage() 
	{
		var btnPrev;
		var btnNext;
		//Get the print button and put it into a variable
		if (unitstring != '')
		{
			btnPrev = document.getElementById("prev");
			btnNext = document.getElementById("next");
		}
		var btnPrint = document.getElementById("Print");
		var btnExportToPDf = document.getElementById("btnExportPdf");
		var btnSendMail = document.getElementById("btnSendMail");
		var btnShowAll = document.getElementById('ShowAll');
		if (unitstring != '')
		{
			btnPrev.style.visibility = 'hidden';
			btnNext.style.visibility = 'hidden';
		}
		btnPrint.style.visibility = 'hidden';
		btnExportToPDf.style.visibility = 'hidden';
		btnSendMail.style.visibility = 'hidden';
		btnShowAll.style.visibility = 'hidden';
		
		//if(CurrentUnit != 0)
		{
			var btnExport = document.getElementById("btnExport");
			btnExport.style.visibility = 'hidden';
		}
		//Print the page content
        window.print();
        //Set the print button to 'visible' again 
		if (unitstring != '')
		{
			btnPrev.style.visibility = 'visible';
			btnNext.style.visibility = 'visible';
		}
		btnPrint.style.visibility = 'visible';
		//if(CurrentUnit != 0)
		{
			var btnExport = document.getElementById("btnExport");
			btnExport.style.visibility = 'visible';
		}
		btnExportToPDf.style.visibility = 'visible';
		btnSendMail.style.visibility = 'visible';
		btnShowAll.style.visibility = 'visible';
    }
				
	window.onfocus = function() {
		var result = localStorage.getItem('refreshPage');	
		if(result != null && result > 0 )
		{	
			localStorage.setItem('refreshPage', "0");
			location.reload();
		}
	};
		</script>
    
</head>
<body>
<?php
$star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
{
	$msg = "Sorry !!! You can't delete it. ( Dependency )";
}
else if(isset($_REQUEST['msg1']))
{
	$msg = "Deleted Successfully.";
}
else{}
?>
<br>
<?php if(sizeof($UnitArray) > 0 && $_REQUEST['uid'] <> 0)
{
	?>
		<center>
			<input type="button" name="prev" value="&laquo;" size="24px" id="prev"  onClick="Prev();" style="width:54px;font-size:36px;" align="middle">
			<input type="button" name="next" value="&raquo;" size="24px"  id="next"  onClick="Next();"  style="width:54px;font-size:36px" >
		</center>
		<br>
	<?php 
}
?>
<div id="Exportdiv" style="border: 1px solid #cccccc;;">
<?php 
			for($iCnt = 0; $iCnt < sizeof($AryUnitToDisplay); $iCnt++)
			{
				$show_due_details = array();
				if($iCnt == 2)
				{
					//break;
				}
				
				if(isset($_GET['from']) && isset($_GET['to']) && $_GET['from'] <> "" && $_GET['to'] <> "")
				{
					$show_owner_details = $obj_view_unit_report->show_owner_name($AryUnitToDisplay[$iCnt],$_GET['to']);
					$get_details = $obj_view_unit_report->show_due_details($AryUnitToDisplay[$iCnt],$_GET['from'],$_GET['to']);
					
				}
				else
				{
					$show_owner_details = $obj_view_unit_report->show_owner_name($AryUnitToDisplay[$iCnt]);
					$get_details = $obj_view_unit_report->show_due_details($AryUnitToDisplay[$iCnt]);	
				}
				//$show_due_details = $obj_view_unit_report->show_due_details($AryUnitToDisplay[$iCnt]);
				$date = $m_objUtility->getCurrentYearBeginingDate($_SESSION['default_year']);	
				$res = $m_objUtility->getOpeningBalance($AryUnitToDisplay[$iCnt],$date);
				if($res <> "")
				{
					$show_due_details[0] = array("Date" => $res['OpeningDate'] , "Particular" => $res['LedgerName'],"Debit" => ($res['OpeningType'] == TRANSACTION_DEBIT) ? $res['Total'] : 0 , "Credit" => ($res['OpeningType'] == TRANSACTION_CREDIT) ? $res['Total'] : 0 ,"VoucherID" => 0 ,"VoucherID" => 0 , "VoucherTypeID" => 0);
					if($get_details <> "")
					{
						for($i = 0 ; $i < sizeof($get_details); $i++)
						{
							$show_due_details[$i + 1] = $get_details[$i];
						}
					}
				}
			?>  
				
<div style="border: 1px solid #cccccc;;width:100%;" id="unit_<?php echo $AryUnitToDisplay[$iCnt] . '_' . $show_owner_details[0]['unit_no']; ?>">
		<script>
			jUnitIDNoArray.push("unit_<?php echo $AryUnitToDisplay[$iCnt] . '_' . $show_owner_details[0]['unit_no']; ?>");
		</script>
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <!--<div id="society_type" style="font-weight:bold; font-size:20px;">PREMISES CO-OPERATIVE SOCIETY LTD.</div>-->
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?></div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">Member Ledger Report</div>
            <div style="font-weight; font-size:16px;">As on Date:<?php echo date("d.m.Y");?></div>
         </div>
  		
		<table style="width:100%;font-size:14px; border: 1px solid #cccccc;"  id="tableMemberDetils">
					<tr align="center"><td colspan="5"><font  size="+1"><b><?php echo $show_owner_details[0]['owner_name'];?></b></font></td></tr>
					<tr>
						<td width="50"><b>Wing:</b><?php echo $show_owner_details[0]['wing'];?></td>
						<!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
						<td width="100"><b>Unit No:</b><?php echo $show_owner_details[0]['unit_no'];?></td>
						<td><b>Residence No:</b><?php echo $show_owner_details[0]['resd_no'];?></td>
						<!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
						<td><b>Mobile No:</b><?php echo $show_owner_details[0]['mob'];?></td>
						
						<!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
						
						<td><b>Email ID:</b><?php echo $show_owner_details[0]['email'];?></td>
						
					</tr>
				</table>
 
				<table id="tableID" style="font-size:14px;width:100%;" >
               		<tr >
            			<th style="text-align:center;  border: 1px solid #cccccc;width:10%;">Date</th>
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:5%;">Voucher</th>
						<!--<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;">Voucher No</th>-->
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:10%;">ChequeNo/Bill Number</th>
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:20%;">Particular</th>
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:10%;">Debit (Rs.)</th>
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:10%;">Credit (Rs.)</th>
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;width:15%;">Balance (Rs.)</th>
						<!--<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;">Status</th>
						<th style="text-align:center;  border: 1px solid #cccccc;border-left:none;">Remark</th>-->
        		</tr>
       
				<?php
				if($show_due_details<>"")
				{
					$BalanceAmt = 0;
					$ChequeNumber=0;
						
				foreach($show_due_details as $k => $v)
				{
		
					//$show_particulars=$obj_view_unit_report->getParticularDetails($_REQUEST["uid"],$show_due_details[$k]['Date']);
					if($show_due_details[$k]['Credit'] <> 0)
					{							
						$show_particulars=$obj_view_unit_report->details2($_REQUEST["uid"],$show_due_details[$k]['VoucherID'],$show_due_details[$k]['VoucherTypeID'], $show_due_details[$k]['Debit'], $show_due_details[$k]['Credit'], "By");
					}
					else
					{
						$show_particulars=$obj_view_unit_report->details2($_REQUEST["uid"],$show_due_details[$k]['VoucherID'],$show_due_details[$k]['VoucherTypeID'], $show_due_details[$k]['Debit'], $show_due_details[$k]['Credit'], "To");
					}
							
					if($show_due_details[$k]['VoucherID']==0)
					{				
						$show_particulars[0]['ledger_name']='Opening Balance';
					}
					//if($show_due_details[$k]['Debit'] > 0 || $show_due_details[$k]['Credit'] > 0)
					{
					//$BillNumber = $show_due_details[$k]['Debit'];															
					$DebitAmt = $show_due_details[$k]['Debit'];
					$CreditAmt = $show_due_details[$k]['Credit'];
					$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
					$ParticularName=$show_particulars[0]['ledger_name'];
					if($show_particulars[0]['DepositGrp']==-2 && $show_due_details[$k]['VoucherTypeID'] <> 1)
					{
						$ChequeNumber = "NEFT[TRXN:".$show_particulars[0]['ChequeNumber']."]";
					}
					else if($show_due_details[$k]['VoucherTypeID'] <> 1)
					{
						$ChequeNumber = $show_particulars[0]['ChequeNumber'];
					}
					if($show_particulars[0]['voucher_name']=="Sales Voucher")
					{
						$strBillType = "";
						if($show_particulars[0]["BillType"] == 0)
						{
							$strBillType = "Maintenance";
						}
						else
						{
							$strBillType = "Supplementary";
						}
						$ParticularName= $strBillType." Bill [". $show_particulars[0]['billFor']. "]";
						$ChequeNumber = $show_particulars[0]['BillNumber'];
					}
					$filtered_Voucher_name = str_replace('Voucher', '', $show_particulars[0]['voucher_name']);
				?>
			   
				<tr>
					<td style="border: 1px solid #cccccc;text-align:center;"><?php echo getDisplayFormatDate($show_due_details[$k]['Date']);?></td>
					<td style="border: 1px solid #cccccc;border-left:none;text-align:center;"><?php echo $filtered_Voucher_name;?></td>
					<!--<td style="border: 1px solid #cccccc;border-left:none;text-align:center;width:10%; "><?php //echo $show_particulars[0]['VoucherNo'];?></td>-->
					<td style="border: 1px solid #cccccc;border-left:none;text-align:center; "><?php echo $ChequeNumber;
							if($show_particulars[0]['PayerBank'] <> "")
							{
								$bankdetails = "[". $show_particulars[0]['PayerBank'];
								if($show_particulars[0]['PayerChequeBranch'] <> "") 
								{ 
									$bankdetails .= ", ".$show_particulars[0]['PayerChequeBranch'] ;
								} 
								$bankdetails .= "]" ;	
							}
							//echo $bankdetails;?></td>
					<td style="border: 1px solid #cccccc;border-left:none;text-align:left;"><?php echo $ParticularName;//$show_particulars[0]['ledger_name'];?></td>
					<td style="border: 1px solid #cccccc;text-align:right;">
					<?php			
						if($show_particulars[0]['voucher_name'] == 'Sales Voucher')
						{					
							echo "<a  href='Maintenance_bill.php?UnitID=".$AryUnitToDisplay[$iCnt]."&PeriodID=". $show_particulars[0]['PeriodID']."&BT=".$show_particulars[0]["BillType"]."' target='_blank'>".number_format($DebitAmt,2)."</a>";
						}
						else if($show_particulars[0]['voucher_name'] == 'Journal Voucher' && $DebitAmt <> 0)
						{				
							echo "<a href='VoucherEdit.php?Vno=". $show_particulars[0]['VoucherNo']."' target='_blank'>".number_format($DebitAmt,2). "</a>";
						}
						else
						{
							echo number_format($DebitAmt,2);
						}
					?></td>          
					<td style="border: 1px solid #cccccc;text-align:right;">
				   <?php if(number_format($CreditAmt,2) > 0 && ($show_particulars[0]['DepositGrp'] == -2 || $show_particulars[0]['DepositGrp'] == -3 || $show_particulars[0]['DepositGrp'] > 0) ) //for pay cash no need of edit link[pay cash = depositID:-1]
						{
							
							if($show_particulars[0]['DepositGrp'] > 0 || $show_particulars[0]['DepositGrp'] == -3) //for receive cash and deposit cheque edit
							{
								$chqDetailsUrl = "ChequeDetails.php?depositid=".$show_particulars[0]['DepositGrp']."&bankid=".$show_particulars[0]['id']."&edt=".$show_particulars[0]['RefNo'];						
							?>
								<a href="#" onClick="window.open('<?php echo $chqDetailsUrl; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');"> <?php echo number_format($CreditAmt,2) ?> </a>
							<?php	
							}
							else if($show_particulars[0]['DepositGrp'] == -2)// For Neft Transaction edit
							{
								$url = "NeftDetails.php?bankid=".$show_particulars[0]['id']."&edt=".$show_particulars[0]['RefNo'];
								?> 
								
								<a href="#" onClick="window.open('<?php echo $url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "> <?php echo number_format($CreditAmt,2) ?> </a> 						                                                                           
							<?php																				
							}
						}
						else
						{
							if($show_particulars[0]['voucher_name'] == 'Journal Voucher' && $CreditAmt <> 0)
							{
								echo "<a href='VoucherEdit.php?Vno=". $show_particulars[0]['VoucherNo']."' target='_blank'>".number_format($CreditAmt,2). "</a>";
							}
							else
							{
								echo number_format($CreditAmt,2);
							}
						}
					?></td>
					<td style="border: 1px solid #cccccc;border-left:none;text-align:right;"><?php echo number_format($BalanceAmt,2);?></td>
					<!--<td style="border: 1px solid #cccccc;text-align:center;border-left:none;"><?php //echo $show_particulars[0]['Status']; ?></td>
					<td style="border: 1px solid #cccccc;border-left:none;text-align:center;"><?php //echo $show_particulars[0]['Note'];?></td>-->
				</tr>
				 <?php
				
					}
				}
					if($BalanceAmt>0){?>
					<tr>
					<td style="border: 1px solid #cccccc;text-align:center;background-color:#F5F3F3;" colspan="6">Total  (Rs.)</td>
					<td style="border: 1px solid #cccccc;border-left:none;text-align:center;background-color:#F5F3F3;"><?php echo number_format(abs($BalanceAmt),2).'(Dr)';?></td>
					 <!--<td colspan="2" style="border: 1px solid #cccccc;text-align:center;background-color:#D2D2D2; "></td> -->            
					</tr>
					<?php 
					}
					else
					{?>
						<tr>
					<td style="border: 1px solid #cccccc;text-align:center;background-color:#F5F3F3;" colspan="6">Total  (Rs.)</td>
					<td style="border: 1px solid #cccccc;text-align:center;background-color:#F5F3F3;"><?php echo number_format(abs($BalanceAmt),2).'(Cr)';?></td>
					<!-- <td style="border: 1px solid #cccccc;text-align:center; "></td>-->
					</tr>
						
				<?php		
					}
				}
				else
				{
					?>
					<tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records Not Found....<!--  by admin --></b></font></td></tr>
					<?php	
				}
				?>
				   
		</table>
		
</div>
<div style='page-break-after:always;'>&nbsp;</div>
		<?php
			}
			?>
			
</div>
<!--<a href="#" onClick ="$('#tableID').tableExport({type:'excel',escape:'false'});">XLS</a>
<a href="#" onClick ="$('#tableID').tableExport({type:'csv',escape:'false'});">CSV</a>-->
<!--<input type="button" id="btnExport" value=" Export"  onClick ="$('#tableID').tableExport({type:'excel',escape:'false',tableName:'unitreport'});"/> -->
<br>
<!--<input  type="button" id="btnSendMail" value="Email"  style="width:80px; height:30px; float:right;" onClick="sendEmail(<?php echo $_REQUEST["uid"];?>,'<?php echo $show_owner_details[0]['email'];?>');"/> -->
<input  type="button" id="btnSendMail" value="Email"  style="width:80px; height:30px; float:right;" onClick="ShowTable()" />
<?php
 if($_SESSION['feature']['CLIENT_FEATURE_EXPORT_MODULE'] == 1)
 {
	 if($_REQUEST["uid"] <> 0)
	{
		?>
			<input  type="button" id="btnExport" value=" Export To Excel"  style="width:150px; height:30px; float:right;" />
			<input  type="button" id="btnExportPdf" value=" Export To Pdf"   onClick="ViewPDF('<?php echo $objFetchData->objSocietyDetails->sSocietyCode;?>' , '<?php echo $show_owner_details[0]['unit_no'];?>')" style="width:150px; height:30px; float:right;"/> 
		<?php
	}
	else
	{
		?>
			<input  type="button" id="btnExport" value=" Export To Excel" disabled style="width:150px; height:30px; float:right;"/>
			<input  type="button" id="btnExportPdf" value=" Export To Pdf"   onClick="ViewAllPDF('<?php echo $objFetchData->objSocietyDetails->sSocietyCode;?>' , '<?php echo $show_owner_details[0]['unit_no'];?>')" style="width:150px; height:30px; float:right;"/> 
		<?php
	}
	?>
	<INPUT TYPE="button" id="Print" onClick="PrintPage()" name="Print!" value="Print" style="width:100px; height:30px;float:right; "/>
<?php    	
}
 if($_REQUEST["uid"] <> 0)
{
	?>
	<input type="button" id="ShowAll" onClick="ShowAll()" name="Show_All_Unit" value="Show All Units" style="width:100px; height:30px;float:right; display:none; "/>	
	<?php
}
else
{
	?>
	<input type="button" id="ShowAll" onClick="ShowSingle()" name="Show_Single_Unit" value="Show Single Unit" style="width:120px; height:30px;float:right; display:none;"/>		
	<?php
}
?>
<div id="status"></div>

<br>
<br>
<!--<form name="EmailForm" id="EmailForm"  method="post" onSubmit="sendEmail();">-->
<table style="border: 1px solid black; visibility:hidden; padding:50px; vertical-align:middle;" id="EmailTable"  class='no-print'>
<input type="hidden" name="UnitID"  id="UnitID" value="<?php echo $_REQUEST["uid"];?>"/>
<?php 
$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
$unitNoForPdf = str_replace($specialChars,'',$show_owner_details[0]['unit_no']);
?>
<input type="hidden" name="UnitNoForPdf"  id="UnitNoForPdf" value="<?php echo $unitNoForPdf ;?>"/>
<input type="hidden" name="SocietyCode"  id="SocietyCode" value="<?php echo $objFetchData->objSocietyDetails->sSocietyCode;?>"/>
<input type="hidden" name="UnitNo"  id="UnitNo" value="<?php echo $show_owner_details[0]['unit_no'];?>"/>
<!--<input type="hidden" name="EmailID"  id="EmailID" value="<?php //echo $show_owner_details[0]['email'];?>"/>-->
<center>
<tr><td colspan="3">Please Fill Details For Sending Email</td></tr>
<tr><td colspan="3"><br/></td></tr>
<tr>
	<td valign="top" style="text-align:left;padding-left:10px">Email To</td>
 	<td>:</td>
  	<td>
		<?php if($_REQUEST["uid"] <> 0)
		{
			?>
			<input type="text" name="EmailID"  id="EmailID" value="<?php echo $show_owner_details[0]['email'];?>" style="width:100%;"  onChange="ValidateEmail(this)" onBlur="ValidateEmail(this)"/></td>
			<?php
		}
		else
		{
			?>
				<input type="text" name="EmailID"  id="EmailID" value="<?php echo $objFetchData->objSocietyDetails->sSocietyEmail;?>" style="width:100%;"  onChange="ValidateEmail(this)" onBlur="ValidateEmail(this)"/></td>
			<?php
		}
		?>
</tr>
<tr>
	<td></td><td></td><td style="color:#999">e.g.: sample1@example.com</td>
</tr>
<tr>
<td>
<br/>
</td>
</tr>

<tr>
	<td valign="top" style="text-align:left;padding-left:10px">CC To </td>
 	<td>:</td>
  	<td><input type="text" name="CC"  id="CC" value="<?php //echo $show_owner_details[0]['email'];?>" style="width:100%;"  onChange="ValidateEmail(this)"  onBlur="ValidateEmail(this)" /></td>
</tr>
<tr>
	<td></td><td></td><td style="color:#999">seperated by ; (Semicolon) e.g.: sample1@example.com;sample2@example.com</td>
</tr>
<tr>
<td>
<br/>
</td>
</tr>
<tr>
	<td valign="top" style="text-align:left;padding-left:10px">Email Subject</td>
 	<td>:</td>
  	<td><input type="text" name="SubjectHead" id="SubjectHead"  value="<?php echo 'Memeber Ledger Report For : ' . $show_owner_details[0]['unit_no']; ?>" style="width:100%;"  required/></td>
</tr>

<tr>
<td>
<br/>
</td>
</tr>
<tr>
    <td valign="top" style="text-align:left;padding-left:10px">Email Message</td>
    <td valign="top"> : </td>
    <td><textarea id="Message" style="width:400px;float:left" name="Message" cols="30" rows="10" type="text" required><?php echo 'Attached Memeber Ledger Report For Unit' . $show_owner_details[0]['unit_no'];?></textarea></td>
</tr>

<tr>
<td>
<br/>
</td>
</tr>
<tr>
	<td>Attached File:<a href="" target='_blank' id="EmailFile" name="EmailFile"><img src='images/pdficon.png' /></a></td>
</tr>
<tr><td colspan="3"><br/></td></tr>
<tr><td colspan="3"><input type="submit" id="SendEmail" name="Submit" value="Submit" onClick="sendEmail();"/></td></tr>
</center>
</table>
<!--</form>-->
</body>
<script>
if (unitstring != null || unitstring != '')
{
	ButtonStatus();
}
$("#btnExport").click(function(e) {
  window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#Exportdiv").html()));
  e.preventDefault();
         
});

</script>

</html>
  