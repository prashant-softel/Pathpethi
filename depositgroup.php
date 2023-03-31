<?php 
include_once("includes/head_s.php");
include_once("classes/depositgroup.class.php"); 
include_once("classes/utility.class.php"); 

$obj_depositgroup = new depositgroup($m_dbConn);

$obj_Utility = new utility($m_dbConn,$m_dbConnRoot);

$CurrentYearBeginingDate = $obj_Utility->getCurrentYearBeginingDate($_SESSION['default_year']);
$CurrentYearBeginingDate = getDisplayFormatDate($CurrentYearBeginingDate);
$slipNumbers  = $obj_depositgroup->getSlipNumbers();
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >  
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/depositgroup.js"></script>
	<script language="javascript" type="application/javascript">

	minStartDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>';
	maxEndDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_end_date']);?>';
	$(function()
	{
		$.datepicker.setDefaults($.datepicker.regional['']);
		$(".basics").datepicker({ 
		dateFormat: "dd-mm-yy", 
		showOn: "both", 
		buttonImage: "images/calendar.gif", 
		buttonImageOnly: true ,
		minDate: minStartDate,
		maxDate: maxEndDate
	})});

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
	function DepositIDChanged()
	{
		var dopid = document.getElementById("depositid2").value;
		//var dopid2 = "<?php //echo $_REQUEST["bankname"] ?>";
		var bankID = "<?php echo $_REQUEST["bankid"] ?>";
		//alert(bankID);
		//alert(dopid);
		 window.location.href='depositgroup.php?bankid='+bankID+'&depositgroupId='+dopid;
	}
	
	function gotocheque()
	{
		var dopid = document.getElementById("depositid2").value;
		//var dopid2 = "<?php //echo $_REQUEST["bankname"] ?>";
		var dopid3 = "<?php echo $_REQUEST["bankid"] ?>";
		//alert('bankid');
		//alert(dopid3);
		 window.location.href='ChequeDetails.php?depositid='+dopid+'&bankid='+dopid3;
	}
	function PrintSlip()
	{
		var dopid = document.getElementById("depositid2").value;
		var CanCloseSlip = document.getElementById("CloseSlip").checked;
		var dopid3 = "<?php echo $_REQUEST["bankid"] ?>";
		var from_date = document.getElementById('from_date').value;
		var to_date = document.getElementById('to_date').value;
		
		document.getElementById('CanClose').value = CanCloseSlip;
		document.getElementById('depositslipid').value = dopid;
		document.getElementById('from').value = from_date;
		document.getElementById('to').value = to_date;

		document.depositslip.submit();
		//window.open('GenerateDepositSlip.php?depositid='+dopid +'&CanClose='+CanCloseSlip+'&from_date='+from_date+'&to_date='+to_date, '_blank');
	}
	</script>
</head>
<body>
<center>
<br>	
<?php
		$sqlBankName = "select BankName from bank_master where BankID = '" . $_REQUEST['bankid'] . "'";
		$sqlResult = $m_dbConn->select($sqlBankName);
?>
<div class="panel panel-info" id="panel" style="display:none">
    <div class="panel-heading" id="pageheader">Deposit Slip <?php echo ' [ '.$sqlResult[0]['BankName'].' ] ';?></div>

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

<form name="depositgroup" id="depositgroup" method="post" action="process/depositgroup.process.php">
<table align='center'>
<?php
if(isset($msg))
{
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}
	else
	{
	?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
	<?php
	}
}
else
{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
}
?>
<tr>
</tr>
</table>
<?php  $deposit = $obj_depositgroup->combobox("select dp.id,dp.desc from depositgroup as dp JOIN ledger as led ON led.id = dp.bankid where dp.status=0 and dp.bankid = '" . $_REQUEST['bankid'] . "' and led.society_id='".$_SESSION['society_id']."' and (dp.id IN(".$slipNumbers.") OR  `DepositSlipCreatedYearID` =  '".$_SESSION['default_year']."') "); 
if($deposit <> "")
{
?>
<div style="width:45%; float:left;border:1px solid #03F;background:#E2E2E2;" id="select_slip">
<table>	
 	
	<tr><td colspan="2" style="text-align:center;"><h2>Select Deposit Slip</h2><br></td></tr>
	<tr>
			<td align="center" colspan="2">
            	<select name="depositid2" id="depositid2" value="depositid2"><?php echo $deposit ?>
            	</select>
            </td>
	</tr>
	<tr>
		<td></td><td></td>
	</tr>
	<tr>
			<td valign="middle">&nbsp;<b>Print Entries From : </b></td>
			<td>
            	<input type="text" name="from_date" id="from_date"   class="basics" size="10" readonly value="<?php echo $CurrentYearBeginingDate?>" style="width:80px;"/></td>
	</tr>
	<tr>
			<td valign="middle">&nbsp;<b>Print Entries To : </b></td>
			<td>
				<input type="text" name="to_date" id="to_date"  class="basics" size="10" readonly value=<?php echo date("d-m-Y") ?>  style="width:80px;"/></td>
	</tr>
    <tr>
    	<td></td><td></td>
    </tr>
    <tr>
        <td style="text-align:center;" colspan="2">
        <input type="button" name="select" id="select" value="select" style="background-color:#E8E8E8;width:80px;height:30px" onClick="gotocheque()" >
        </input>&nbsp;&nbsp;
        <input type="button" name="Print" id="Print" value="Print" style="background-color:#E8E8E8;width:80px;height:30px" onClick="PrintSlip()">
        </input>
        
        </td>
     </tr>
     <tr>
     <td>
     <br>
     <input type="checkbox" id="CloseSlip">Close slip after print</input>
     </td>
     </tr>
 </table>
 <br><br><br>
 </div>
 <?php
if(IsReadonlyPage() == false)
{?>
 <div style="width:9%; float:left;font-size:18px;text-align:center;"><br><br><br><br><br><br><--OR--></div>
 <?php
}
}
else if($_SESSION['is_year_freeze'] == 1)
{
	echo '<center><div style="padding-top: 150px;"><font  color="#FF0000" style="font-weight:bold;font-size:16px;">No deposit slips are available for selected bank.</font></div></center>';
}

 
if($deposit == "" || sizeof($deposit) == 0)
{?>
 <div style="width:45%;border:1px solid #03F;background:#E2E2E2;" id="new_slip">
 <?php } 
 else
 {?> 
  <div style="width:45%;float:right;border:1px solid #03F;background:#E2E2E2;" id="new_slip">
  <?php }?>
 <table>       
	    <tr>
  <td colspan="2" style="text-align:center;"><h2>Create New Deposit Slip</h2><br></td>
  </tr>
        
        <tr>
			<td>Description <?php echo $star; ?> : &nbsp;</td>
			<td>
		<input type="text" name="desc" id="desc" value="" required >	</td>
		</tr>
		<tr>
			<td>Bank : &nbsp;</td>
			<?php  $BankName =  $obj_depositgroup->m_dbConn->select("select ledger_name from ledger where id=".$_REQUEST["bankid"]." and society_id=".$_SESSION['society_id']) ?>
           <td><input type="text" name="bankname" value="<?php echo $BankName[0]['ledger_name'];?>" readonly ></td>
		<input type="hidden" name="bankid" id="bankid" value=<?php echo $_REQUEST["bankid"] ?> >	</td>
		</tr>
		<tr>
			<td>Created By : &nbsp;</td>
            <?php $name =  $obj_depositgroup->m_dbConn->select("select name from login where login_id=". $_SESSION['login_id']) ?>
			<td><input type="text" name="createdby" id="createdby" value='<?php  echo $name[1]['name'] ?>'  readonly /></td>
		</tr>
        <tr>
			<td>Deposited By : &nbsp;</td>
            <?php //$name =  $obj_depositgroup->m_dbConn->select("select name from login where login_id=". $_SESSION['login_id']) ?>
			<td><input type="text" name="depositedby" id="depositedby"/></td>
		</tr>
		<tr>
			<td>Status : &nbsp;</td>
			<td>
		<select type="number" name="status" id="status">
        <option value="0">Open</option>
        <option value="1">Close</option>
        </select>	</td>
		</tr>
        <tr><td><br></td></tr>
       <tr>
			<td colspan="2" align="center"><input type="hidden" name="id" id="id"><input type="submit" name="insert" id="insert" value="Create" style="background-color:#E8E8E8;width:140px"></td>
		</tr>
        <tr><td><br></td></tr>
</table>
</div>
</form>
<form name="depositslip" id="depositslip" method="post" action="GenerateDepositSlip.php" target="_blank">
	<input type="hidden" name="CanClose" id="CanClose" value="" />
	<input type="hidden" name="depositslipid" id="depositslipid" value="" />
	<input type="hidden" name="from" id="from" value="" />
	<input type="hidden" name="to" id="to" value="" />
</form>
<!--<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>-->
<div style="width:100%;">
<?php 
echo "<br><br><br><br><br><br><br>";
$str1 = $obj_depositgroup->pgnation();
echo "<br>";
//echo $str = $obj_depositgroup->display1($str1);
echo "<br>";
//$str1 = $obj_depositgroup->pgnation();
echo "<br>";
?>
</div>
</center>
<?php
if(IsReadonlyPage() == true)
{?>
<script>document.getElementById('new_slip').style.display = 'none';
				$("#select_slip").css("float", "none");
</script>
<?php }?>
</div>
<?php include_once "includes/foot.php"; ?>