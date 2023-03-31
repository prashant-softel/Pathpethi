<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php include_once ("classes/dbconst.class.php"); ?>
<?php include_once("classes/dbmanager.class.php");


if(isset($_REQUEST['add']))
{
	$_SESSION['society_id'] = 0;
	$obj_dbManager = new dbManager();
	$dbName = $obj_dbManager->getEmptyDBName();
		
	if($dbName == '')
	{
		?>
			<script>
				alert('No Database Available To Import New Society.\n\nPlease Contact System Administrator.');
				window.location.href = "initialize.php";
			</script>
		<?php	
		exit();
	}
	else
	{
		$_SESSION['dbname'] = $dbName;
		?>
			<script>
				localStorage.setItem('dbname', "<?php echo $_SESSION['dbname']; ?>");
				window.location.href = "society.php?imp";
			</script>
		<?php
	}
	//include_once("includes/head.php");
}
/*else
{
	if(isset($_SESSION['admin']))
	{
		include_once("includes/header.php");
	}
	else
	{
		include_once("includes/head_s.php");
	}
}*/
include_once("includes/head_s.php");
//include_once("includes/menu.php");

include_once("classes/pp_loan.class.php");
$obj_loan = new pp_loan($m_dbConn, $m_dbConnRoot);

include_once("classes/utility.class.php");

$obj_utility = new utility($m_dbConn, $m_dbConnRoot);

error_reporting(1);
ini_set('display_error',E_ALL);
// echo "test";


?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<link href="css/popup.css" rel="stylesheet" type="text/css" />
	
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsloan.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script src="javascript/moment.min.js"></script>    
	<script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
   	<script type="text/javascript" src="js/ajax.js"></script>

    <script language="javascript" type="application/javascript">
	function show_error(msg) {
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = msg; 
	}
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }

	$(function()
        {	
			$.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true,
    })});

	function togglePopup(id)
	{
		var popup = document.getElementById(id);
    	popup.classList.toggle('show');
	}
	function LinkGDrive()
	{
		document.getElementById("frmGDriveLink").submit();
	}
	$(document).ready(function(){
		var iIsGDriveSetup = <?php if(isset($_REQUEST["GDriveFlag"])){echo $_REQUEST["GDriveFlag"];}else{echo "0";} ?>;
		if(iIsGDriveSetup == "1")
		{
			alert("Google Drive Setup completed Successfully");
		}
	});
	</script>    
</head>

<?php if((isset($_POST['ShowData']) && $_POST['ShowData']<> '')  || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else if(isset($_REQUEST['edt']) || $_REQUEST['insert']=='Edit'){ ?>
<body>
<?php }else if(isset($_REQUEST['show'])){ ?>
<body>
<body>
<?php } ?>

<div id="middle">
<center>
<br>
<div class="panel panel-info" id="panel" style="display: block;" >
<?php if(isset($_GET['id']) && !empty($_GET['id'])){?>
<div class="panel-heading" id="pageheader" class="hidden">Loan Details</div>
<br>
<?php $val = 'onSubmit="return val();"';
?>

<center>

<form method="POST" action="process/pp_loan.process.php" onSubmit="return val();">
	<?php
		$star = "<font color='#FF0000'>*&nbsp;</font>";
		if(isset($_REQUEST['msg']))
		{
			$msg = "Sorry !!! You can't delete it. ( Dependency )";
		}
		else if(isset($_REQUEST['msg1']))
		{
			$msg = "Deleted Successfully.";
		}
		else
		{
			//$msg = '';	
		}
	?>
    <table align='center' style=" width:100%;">
		<?php
		if(isset($msg))
		{
			if(isset($_POST["ShowData"]))
			{
		?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
			}
			else
			{
			?>
            	<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $msg; ?></b></font></td></tr>	   
            <?php		
			}
		}
		else
		{
		?>	
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>
		<!--</table>-->
             
       <tr>
       <td colspan="2"> 
      <!--  <div style="width:100%;">-->
       <!-- <table  style="width:100%;">-->
       <table width="50%" style="font-size:12px; float:left;" id="PrintableTable">
	<tr style="background-color:#bce8f1;font-size:14px;" height="25">
        <!--<th style="width:1%; background-color:#FFF;"></th>-->
       <!-- <th style="width:40%;">Billing Information</th></tr>-->
        
        <!-- Loan Type -->
        <tr>
         
        	<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Select Loan Type</span><span  style="margin-left: 55px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;">
				<select name="loan_type" id="loan_type" tabindex="1">
					<?php echo $obj_loan->comboboxLoanType("SELECT A.category_name, A.category_id FROM account_category A, account_category B WHERE A.parentcategory_id = B.category_id AND B.category_name = '".LOAN."'");
					 ?>
				</select>
			</span></td>
		</tr>

		<!-- Loan Period -->
		<tr>
        	<td valign="left"><span style="margin-left:3%;"><?php echo $star;?>&nbsp;</span>
        		<span  style="margin-left: 2%;">Loan Period</span><span style="margin-left:82px;">&nbsp; : &nbsp;
            	</span>
				<span style="margin-left:6%;">	
					<input type="text" name="loan_period" id="loan_period" placeholder="Enter days" tabindex="3" class="field_input"/>
	             </span>
	        </td>
		</tr>
        <tr>


		<!-- Loan Date -->
		<tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 8px;">Loan Date</span><span  style="margin-left: 92px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 30px;"><input type="text" name="loan_date" id="loan_date" tabindex="4" class="basics field_date" size="10" readonly  style="width:80px;" /></span></td>
		</tr>

        
        
        <!-- <tr><td><br></td></tr>  Line Break -->
        
        <!-- Interest Rate -->
        <tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 8px;">Interest Rate</span><span  style="margin-left: 78px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 30px;"><input type="text" name="int_rate" id="int_rate" tabindex="6" class="field_input"  ></span></td>
		</tr>

		<!-- Installment Amount -->
        <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 1%;">Installment Amount </span><span style="margin-left:44px;">&nbsp; : &nbsp;
            </span>
			<span style="margin-left:6%;"><input type="text" name="installment_amt" id="installment_amt" tabindex="8" class="field_input" readonly /></span></td>
		</tr>
	
		<!-- Loan Charges -->
        <tr >
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Loan Charges</span><span  style="margin-left: 72px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;"><input type="text" name="loan_chrgs" id="loan_chrgs" tabindex="10" class="field_input"/></span></td>
		</tr>
	
		<tr>
			<td><br></td>
		</tr>
		

		<tr>
			<td><br></td>
		</tr>

		<!--Bank Accounts-->
		 
		<tr>
         <td valign="left"><span style="margin-left: 3%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Select Bank</span><span  style="margin-left: 90px;">&nbsp; : &nbsp;</span>
		 <span  style="margin-left: 6%;">
			 <select name="bank_id" id="bank_id" tabindex="12" onBlur="getBankLeafs();">
				 <?php echo $obj_utility->BankComboBox();
				  ?>
			 </select>
		 </span></td>
	 	</tr>

		 <!--Cheque Number-->
		 
		 <tr id="cheque_no_tr" style="display:none">
         <td valign="left"><span style="margin-left: 3%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Cheque Number</span><span  style="margin-left: 66px;">&nbsp; : &nbsp;</span>
		 <span  style="margin-left: 6%;" name="cheque_no_span" id="cheque_no_span">
		</span></td>
	 	</tr>


		 <tr>
			<td><br></td>
		</tr>
		

		<tr>
			<td><br></td>
		</tr>
        
        <!-- Referred By 1 -->
        <tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Referred By</span><span  style="margin-left: 83px;">&nbsp; : &nbsp;</span>
            	<span  style="margin-left: 6%;"><select name="referred_1" id="referred_1" style="width:200px;" tabindex="16" class="field_select">
            		<?php echo $member_combo = $obj_loan->comboboxName("SELECT DISTINCT member_id,owner_name FROM member_main WHERE status = 'Y'");
					 ?>
                </select></span>
            </td>
		</tr>
        
        <!-- Referred By 2 -->
        <tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Referred By</span><span  style="margin-left: 83px;">&nbsp; : &nbsp;</span>
            	<span  style="margin-left: 6%;"><select name="referred_2" id="referred_2" style="width:200px;" tabindex="17" class="field_select">
            		<?php echo $member_combo; ?>
                </select></span>
            </td>
		</tr>

		<!-- Joint Name -->
		<tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Joint Name</span><span  style="margin-left: 86px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;"><input type="text" name="joint_name" id="joint_name" tabindex="18" class="field_input" /></span></td>
		</tr>
		
		 </table>
         
         
        <table width="50%" style="font-size:12px; float:left" id="PrintableTable">
        <tr style="background-color:#bce8f1;font-size:14px;" height="25">
        
		<!-- Loan Amount -->
        <tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Loan Amount</span><span  style="margin-left: 76px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><div style=" width: 50%;float: right;margin-right: 4%;"><input type="text" name="loan_amt" id="loan_amt" tabindex="2" class="field_input" /></div></span></td>
		</tr>			

		<tr><td><br></td></tr>

		<!-- Loan Maturity Date -->
        <tr>
			<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 8px;">Loan Maturity Date</span><span  style="margin-left: 45px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 30px;"><input type="text" name="maturity_date" id="maturity_date" tabindex="5" class="basics field_date" size="10" readonly  style="width:80px;" /></span></td>
		</tr>	

		<!-- Interest Amount -->
        <tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 8px;">Interest Amount</span><span  style="margin-left: 62px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 30px;"><input type="text" name="interest_amt" id="interest_amt" tabindex="7" class="field_input"  ></span></td>
		</tr>

		<!-- Maturity Amount -->
        <tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?>&nbsp;</span><span  style="margin-left: 8px;">Maturity Amount</span><span  style="margin-left: 60px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 31px;"><input type="text" name="maturity_amt" id="maturity_amt" tabindex="9" class="field_input"  ></span></td>
		</tr>

		<!-- Mortgage Type-->
		<tr>
			<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Select Mortgage Type</span><span  style="margin-left: 31px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;">
				<select name="mortgage" id="mortgage" tabindex="11" onchange="getAccountNumbers()">
					<option value="0">Please select</option>
					<option value="<?=SAVING_ACCOUNT?>">Saving Account</option>
					<option value="<?=DAILY_DEPOSIT_ACCOUNT?>">Daily Deposit Account</option>
					<option value="<?=MONTHLY_DEPOSIT_ACCOUNT?>">Monthly Deposit Account</option>
					<option value="<?=FIXED_DEPOSIT_ACCOUNT?>">Fixed Deposit Account</option>
				</select>
			</span></td>
		</tr>
		
		<!-- Mortgage Type-->
		<tr>
			<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Select Mortgage Account</span><span  style="margin-left: 15px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 6%;">
				<select name="mortgage_account" id="mortgage_account" tabindex="11"></select>
			</span></td>
		</tr>


		
        	<td><br></td>
		</tr>
        
        <tr>
        	<td><br></td>	
		</tr>
        
        
		
		 
		
		 <!--Bank Leaf-->
		 
		 <tr>
         <td valign="left"><span style="margin-left: 3%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Select Leaf</span><span  style="margin-left: 93px;">&nbsp; : &nbsp;</span>
		 <span  style="margin-left: 6%;">
			 <select name="bank_leaf" id="bank_leaf" tabindex="13" onBlur="getChequeNoList();" >
			</select>
		 </span></td>
	 	</tr>
		

        <tr><td><br></td></tr>

        <tr><td><br></td></tr>

		<!-- Note -->
		<tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Note</span><span  style="margin-left: 123px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"></span></td>
		</tr>

		<tr id="note">
        	<td style="padding-left:6%;">
            <span style="margin-left:3%">
			&nbsp;&nbsp;&nbsp;<textarea name="note_footer" id="note_footer" tabindex="18" class="field_input"></textarea>
            <script>
				CKEDITOR.config.height = 50;
				CKEDITOR.config.width = 425;
				CKEDITOR.replace('note_footer', {toolbar: [
									{ name: 'clipboard', items: ['Undo', 'Redo']},
									{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
									 ]});
			</script>
            </span>
            </td>
		</tr>
        
        <tr><td><br></td></tr>
         
         
        </table>
        <table width="100%"> 
         <tr><td colspan="4">&nbsp;</td></tr>
        <tr>
			<td colspan="4" align="right" style="margin-right: 20px;">
            <input type="hidden" name="login_id" id="login_id">
            <input type="hidden" name="id" id="id" value="<?php echo $_GET['id']; ?>">
             <input type="hidden" name="unit_presentation_previous_value" id="unit_presentation_previous_value">
            <input type="submit" name="insert" id="insert" value="Submit" tabindex="19" class="btn btn-primary"  style="padding: 6px 12px; color:#fff;background-color: #2e6da4;margin-right: 20px;">
            </td>

            <td colspan="4" align="left">
			<input type="reset" name="reset" id="reset" value="Reset" tabindex="20" class="btn btn-danger" style="width: 75px;box-shadow: 1px 1px 4px #666;">
            </td>
		</tr>
        <tr><td><br></td></tr>
        <br> 
<br>
    </table>
 </td>
</tr>
</table>

</form>
</center>
<br>
<?php }?>

<div class="panel-heading" id="pageheader">List of Loans</div> <br>
<?php
	$obj_loan->list_loan_show();
 ?>

</div>
<br><br>

</center>
</div>
</div>

<?php include_once "includes/foot.php"; ?>
