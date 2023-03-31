<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php
 	//Turn off all error reporting
    //error_reporting(0);
	include_once ("classes/include/dbop.class.php");
	$dbConn = new dbop();
	
	include_once("includes/head_s.php");
	

if(isset($_REQUEST['type']))
{
	//echo $_REQUEST['type'];
}
?>


<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/populateData.js"></script>
<script type="text/javascript">

$(document).ready(

	function(){
		$('input:submit').attr('disabled',true);
		$('input:file').change(
		function(){
			if ($(this).val())
			{
				$('input:submit').removeAttr('disabled'); 
			}
			else 
			{
				$('input:submit').attr('disabled',true);
			}
		});
});
    
</script>

<script language="javascript" type="text/javascript">
function go_error()
{
	setTimeout('hide_error()',10000);	
}
function hide_error()
{
	document.getElementById('error').style.display = 'none';	
}
			
function chgAction( action_name )
{
    if( action_name=="aaa" ) {
    		document.payment_n_receipt_form.action="";
	    }
    else{
        document.payment_n_receipt_form.action="";
    }    
}
</script>
</head>




<body onLoad="go_error();">


<form name="reco_import_form" action="process/import_reco_report.process.php" method="post" enctype="multipart/form-data" >

<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
   
        <div class="panel-heading" id="pageheader">Import Receipt Register / Payment Register reconcile Report</div>
<div id="right_menu">
<table>

<?php
if(isset($_POST["ShowData"]))
{
	?>
    <tr height="30"><td colspan=5 style="text-align:center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
<?php } ?> 
<strong><div id="show" style="text-align:center; width:100%; color:#FF0000"><?php //echo $show_op; ?></div></strong>
<!--<tr height="50" align="center"><td>&nbsp;</td><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="180">For Society Admin Login</th></tr></table></th></tr>-->
<BR/>
<BR/>
<?php
$BankDetails = $dbConn->select("select BankID,BankName from bank_master");
?>
</tr>        
      <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      </tr>
      <tr>
      <td>Selct Bank</td>
      <td><select id="cbBank" name="cbBank" >
      <?php 
	  	foreach($BankDetails as $arBank)
		{
			echo "<option id=".$arBank['BankID']." value=".$arBank['BankID'].">".$arBank['BankName']."</option>";
		}
		?>
      </select></td>
      </tr>
      <tr>
      <td>Select Transaction Type</td>
      <td><select id="TranxType" name="TranxType">
      <option id="1" value="Receipts">Receipts / Deposits</option>
      <option id="2" value="Payments">Payments</option>
      </select></td>
      </tr>
<tr align="left">
        	
			
            <td>Browse File To Import</td>
            
			<td id="browse"><input type="file" name="upload_files[]" id="file" multiple /></td>
            
</tr>   

<tr><td colspan="4">&nbsp;</td></tr>
<tr height="50" align="center">
 <td colspan="4" align="center"><input type="submit" name="Import" value="Import"  disabled /></td>
</tr>
<input type="hidden" name="type" value="<?php echo $_REQUEST['type'];?>">
<input type="hidden" name="sid" value="<?php echo $_SESSION['society_id'];?>">
</table>


        <div style="color:#FF0000">* File Name Should be "RECN_payments.csv" for Payment transactions and "RECN_receipts.csv" for Receipts transactions.</div>
<div style="color:#FF0000">* Please make sure Dates in csv file are with dd-mm-yyyy format to avoid incorrect Reconcile date in reco statement.</div>
        <div style="color:#FF0000">*</div>

</center>
</form>
</div>

</div>
<?php include_once "includes/foot.php"; ?>