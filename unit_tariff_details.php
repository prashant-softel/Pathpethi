<?php include_once "classes/include/check_session.php";
include_once("classes/dbconst.class.php");
include "classes/include/fetch_data.php";
$objFetchData = new FetchData($m_dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
?>

<?php
include_once "classes/unit_tariff_details.class.php";
$obj_view_unit_report = new unit_tariff_details($m_dbConn);
//echo $_GET["sid"];
$show_owner_details=$obj_view_unit_report->show_owner_name($_REQUEST["uid"]);
//print_r($show_owner_details);

$bill_type = 0;
$sBillText = "(Regular Bill)";
if(isset($_REQUEST['bill']) && $_REQUEST['bill'] == 1)
{
	$bill_type = 1;
	$sBillText = "(Supplementary Bill)";
}
$show_due_details = $obj_view_unit_report->get_tariff_details($_REQUEST["uid"], $bill_type);

//print_r($show_due_details);
?>

<html>
<head>
<title>Unit Tariff Details</title>
<style>
	table {
    	border-collapse: collapse;
		text-align:center;
	}
	table, th, td {
   		border: 0px solid black;
		text-align:center;
	}	
</style>
	<!--<link rel="stylesheet" type="text/css" href="css/pagination.css" >-->
    <script type="text/javascript" src="js/jsReportPdfNEmail.js"></script>
    <!--<script type="text/javascript" src="js/ajax.js"></script>-->
	<script type="text/javascript" language="javascript" src="js/jquery-2.0.3.min.js"></script>
    <script language="javascript" type="application/javascript">
	//alert("test");
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
	
	function PrintPage() 
	{
		//Get the print button and put it into a variable
		var btnPrint = document.getElementById("Print");
		var btnExport = document.getElementById("btnExport");
		var btnExportToPDf = document.getElementById("btnExportPdf");
		var btnSendMail = document.getElementById("btnSendMail");
		
		btnPrint.style.visibility = 'hidden';
		btnExport.style.visibility = 'hidden';
		btnExportToPDf.style.visibility = 'hidden';
		btnSendMail.style.visibility = 'hidden';
		//Print the page content
        window.print();
        //Set the print button to 'visible' again 
        btnPrint.style.visibility = 'visible';
		btnExport.style.visibility = 'visible';
		btnExportToPDf.style.visibility = 'visible';
		btnSendMail.style.visibility = 'visible';
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


<div id="Exportdiv" style="border:1px solid black;">
<div style="border:0px solid black;width:100%;">
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
            <div style="font-weight:bold; font-size:16px;">Member Tariff Details <?php echo $sBillText; ?></div>
         </div>
    
<table style="width:100%;font-size:14px; border:1px solid black;border-left:none;border-right:none;"  id="tableMemberDetils">
        <tr align="center"><td colspan="5"><font  size="+1"><b><?php echo $show_owner_details[0]['owner_name'];?></b></font></td></tr>
        <tr>
            <td width="100"><b>Wing:</b><?php echo $show_owner_details[0]['wing'];?></td>
            <!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
            <td width="100"><b>Unit No:</b><?php echo $show_owner_details[0]['unit_no'];?></td>
            <td><b>Residence No:</b><?php echo $show_owner_details[0]['resd_no'];?></td>
            <!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
            <td><b>Mobile No:</b><?php echo $show_owner_details[0]['mob'];?></td>
            
            <!--<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>-->
            
            <td><b>Email ID:</b><?php echo $show_owner_details[0]['email'];?></td>
            
        </tr>
</table>


	<table style="border:0px solid black; border-collapse:collapse; width:100%;">
	<tr>
		<th>Account Head</th>
		<th>Begin Date</th>
		<th>End Date</th>
		<th>Amount</th>
	</tr>
<?php 
	$iPrevHead = '';
	for($iCnt = 0 ; $iCnt < sizeof($show_due_details) ; $iCnt++) 
	{
		$beginPeriod = getDisplayFormatDate($show_due_details[$iCnt]['BeginPeriod']);
		$endPeriod = $show_due_details[$iCnt]['EndPeriod'];
		
		$fromDate = date('F - Y', strtotime($show_due_details[$iCnt]['BeginPeriod']));
		$toDate = date('F - Y', strtotime($show_due_details[$iCnt]['EndPeriod']));
		
		if($endPeriod == PHP_MAX_DATE)
		{
			$endPeriod = "Lifetime";
			$toDate = $endPeriod;
		}
		else
		{
			$endPeriod = getDisplayFormatDate($endPeriod);
		}
					
		if($iPrevHead == '' || $iPrevHead <> $show_due_details[$iCnt]['AccountHeadID'])
		{
			$iPrevHead = $show_due_details[$iCnt]['AccountHeadID'];
					
			?>
				<tr>
					<td style="border-top:1px dotted black;"><?php echo $show_due_details[$iCnt]['ledger_name']; ?></th>
					<td style="border-top:1px dotted black;"><?php echo $fromDate; ?></td>
					<td style="border-top:1px dotted black;"><?php echo $toDate; ?></td>
					<td style="border-top:1px dotted black;"><?php echo $show_due_details[$iCnt]['AccountHeadAmount']; ?></td>
				</tr>
			<?php				
		}
		else
		{
			?>
				<tr>
					<td></td>
					<td><?php echo $fromDate; ?></td>
					<td><?php echo $toDate; ?></td>
					<td><?php echo $show_due_details[$iCnt]['AccountHeadAmount']; ?></td>
				</tr>
			<?php
		}
	}
?>
	</table>

</div>
</div>
<br>
<!--<input  type="button" id="btnSendMail" value="Email"  style="width:80px; height:30px; float:right;" onClick="sendEmail(<?php //echo $_REQUEST["uid"];?>,'<?php //echo $show_owner_details[0]['email'];?>');"/> -->
<!--<input  type="button" id="btnSendMail" value="Email"  style="width:80px; height:30px; float:right;" onClick="ShowTable()" /> 
<input  type="button" id="btnExport" value=" Export To Excel"  style="width:150px; height:30px; float:right;"/>
<input  type="button" id="btnExportPdf" value=" Export To Pdf"   onClick="ViewPDF('<?php //echo $objFetchData->objSocietyDetails->sSocietyCode;?>' , '<?php //echo $show_owner_details[0]['unit_no'];?>')" style="width:150px; height:30px; float:right;"/> 
<INPUT TYPE="button" id="Print" onClick="PrintPage()" name="Print!" value="Print!" style="width:100px; height:30px;float:right; "/>	-->
<div id="status"></div>

<br>
<br>
<!--<form name="EmailForm" id="EmailForm"  method="post" onSubmit="sendEmail();">-->
<table style="border: 1px solid black; visibility:hidden; padding:50px; vertical-align:middle;" id="EmailTable" >
<input type="hidden" name="UnitID"  id="UnitID" value="<?php echo $_REQUEST["uid"];?>"/>
<input type="hidden" name="SocietyCode"  id="SocietyCode" value="<?php echo $objFetchData->objSocietyDetails->sSocietyCode;?>"/>
<input type="hidden" name="UnitNo"  id="UnitNo" value="<?php echo $show_owner_details[0]['unit_no'];?>"/>
<!--<input type="hidden" name="EmailID"  id="EmailID" value="<?php //echo $show_owner_details[0]['email'];?>"/>-->
<center>
<tr><td colspan="3">Please Fill Details For Sending Email</td></tr>
<tr><td colspan="3"><br/></td></tr>
<tr>
	<td valign="top">Email To</td>
 	<td>:</td>
  	<td><input type="text" name="EmailID"  id="EmailID" value="<?php echo $show_owner_details[0]['email'];?>" style="width:100%;"  onChange="ValidateEmail(this)" onBlur="ValidateEmail(this)"/></td>
</tr>
<tr>
	<td valign="top">Email Subject</td>
 	<td>:</td>
  	<td><input type="text" name="SubjectHead" id="SubjectHead"  value="<?php echo 'Memeber Ledger Report For : ' . $show_owner_details[0]['unit_no']; ?>" style="width:100%;"  required/></td>
</tr>
<tr>
    <td valign="top">Email Message</td>
    <td valign="top"> : </td>
    <td><textarea id="Message"  name="Message" cols="30" rows="5" type="text" required><?php echo 'Attached Memeber Ledger Report For Unit' . $show_owner_details[0]['unit_no'];?></textarea></td>
</tr>
<tr>
	<td>Attached File:<a href="" target='_blank' id="EmailFile" name="EmailFile"><img src='images/pdficon.png' /></a></td>
</tr>
<tr><td colspan="3"><br/></td></tr>
<tr><td colspan="3"><input type="submit" name="Submit" value="Submit" onClick="sendEmail();"/></td></tr>
</center>
</table>
<!--</form>-->
</body>
<script>
$("#btnExport").click(function(e) {
  window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#Exportdiv").html()));
  e.preventDefault();
         
});
</script>

</html>
  