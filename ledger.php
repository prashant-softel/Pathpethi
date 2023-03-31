<?php //include_once "ses_set_s.php"; ?>
<?php
	include_once("includes/head_s.php");

?>
<?php
include_once("classes/account_subcategory.class.php");
$obj_account_subcategory = new account_subcategory($m_dbConn);
$startdate = $obj_account_subcategory->FetchDate($_SESSION['default_year']); 
include_once("classes/dbconst.class.php");
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/populateData.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/account_subcategory_08112018.js"></script>
     <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script language="javascript" type="application/javascript">
	minStartDate = '<?php echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>';
   $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
			defaultDate: new Date(),
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
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
	
	</script>
    <script>
	function addNew()
	{
		document.getElementById('new_entry').style.display = 'block';
		document.getElementById('btnAdd').style.display = 'none';
		document.getElementById("btnPrintAll").style.display = 'none';
		document.getElementById('brtag').style.display = "none";
	}
	
	function onCancel()
	{
		document.getElementById('new_entry').style.display = 'none';
		document.getElementById('btnAdd').style.display = 'block';		
	}
	
	var isSadmin = false; 
	<?php
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_SUPER_ADMIN))
		{ ?>
		isSadmin = true;
	<?php } ?>
</script>
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg'])){ ?>
<body onLoad="go_error();">
<?php } ?>

<body>
<br>
<center>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Ledger</div>
        <br>

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
<center>
<!--<button type="button" class="btn btn-primary" onclick="window.location.href='ledger_details.php?imp'">Ledger Details</button>-->
<?php if($_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1){ ?> <!--If user has manage master then only user can create ledger-->
<button type="button" class="btn btn-primary" onClick="addNew();" id="btnAdd" >Add New Ledger</button>
<?php }?>
<button type="button" class="btn btn-primary" onClick="window.open('multiple_ledger_print.php');"  id="btnPrintAll">Print Multiple Ledger</button>
<?php if($_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1){ ?>
<button type="button" class="btn btn-primary" onClick="window.open('account_category.php', '_blank')" id="">Manage Categories</button>
<?php }?>



<div id="brtag"><br/></div>
</center>
<div id="new_entry" style="display:none;">
<form name="account_subcategory" id="account_subcategory" method="post" action="process/account_subcategory.process.php" onSubmit="return val();">
 <input type="hidden" name="edit" id="edit" value="<?php echo $_REQUEST['edt']; ?>" />

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

<table style="width:80%;">
	<tr><td><br><br></td></tr>
  	<tr>
 	 <td>
<!-- First table of left side-->
	<table style="width:47%; float:left;">
		<tr><td><input type="hidden" name= "society_id" id="society_id" value="<?php echo DEFAULT_SOCIETY; ?>"/></td></tr>
        <tr>
			<td>Group<?php echo $star; ?></td>
			<td><select name="groupid" id="groupid" onChange="get_category(this.value);">		
				<?php echo $combo_society = $obj_account_subcategory->combobox("select `id`, `groupname` from `group`", $_REQUEST['groupid']); ?>
			</select>
			</td>
		</tr>
		<tr>
			<td>Category ID<?php echo $star; ?></td>
			<td><select name="categoryid" id="categoryid">		
<?php //echo $combo_society = $obj_account_subcategory->combobox("select `category_id`, `category_name` from `account_category` where group_id='" .  $_REQUEST['groupid']. "' ", $_REQUEST['categoryid'], 'Primary', '1');
		//echo $combo_society = $obj_account_subcategory->combobox("select `category_id`, `category_name` from `account_category`",0);
	 ?>
		</select>
			</td>
		</tr>
		<tr>
			<td>Ledger<?php echo $star; ?></td>
			<td><input type="text" name="ledger_name" id="ledger_name"  value="<?php echo $_REQUEST['ledger_name']?>"/></td>
		</tr>	
        	  <script>
      $('#ledger_name').bind('keypress', function(e) {

			    if($('#ledger_name').val().length >= 0)
			    {
			        var k = e.keyCode;
			        var ok = k >= 65 && k <= 90 || // A-Z
			            k >= 97 && k <= 122 || // a-z
			            k >= 48 && k <= 57 || // 0-9
			            k == 32; // {space}

			        if (!ok){
			            e.preventDefault();
			        }
			    }
			}); 
		
			</script>
		<tr id="opening">
			<td >Opening Type <?php echo $star; ?></td>
			<td><select name="opening_type" id="opening_type" >
				<option value="0"> Please Select </option>
            	<option value="1"> Credit </option>
           	 	<option value="2"> Debit </option>
			</select>
			</td>
		</tr>
       	<tr id="opening_Balance">
			<td>Opening Balance<?php //echo $star; ?></td>
			<td><input type="text" name="opening_balance" id="opening_balance"  value="<?php echo $_REQUEST['opening_balance']?>"/></td>
		</tr>
        
	</table>
	<!-- Second  table of right side-->
	<table style="width:48%;float:left;">
		<tr>
			<td><!--Date--><?php //echo $star; ?></td>
			<td><input type="hidden" name="balance_date" id="balance_date"  value="<?php if($_REQUEST['balance_date'] <> ""){echo $_REQUEST['balance_date'];}else{echo $startdate; }?>"   readonly /></td>
		</tr>
        <tr id="GSTIN_Details">
			<td>GSTIN No.<?php //echo $star; ?></td>
			<td><input type="text" name="GSTIN_No" id="GSTIN_No"  value="<?php echo $_REQUEST['GSTIN_No']?>"/></td>
		</tr>
        <tr id="pan_Details">
			<td>PAN No<?php //echo $star; ?></td>
			<td><input type="text" name="Pan_no" id="Pan_no"  value="<?php echo $_REQUEST['PAN_No']?>"/></td>
		</tr>
        
         <tr id="nature_Details">
			<td >Nature of Payment</td>
			<td><select name="natureOfPayment" id="natureOfPayment" value="<?php echo $_REQUEST['nature_of_payId']?>">
            	<?php
					echo '<option value="">Please Select</option>'; 
					for($i = 0; $i < sizeof($NatureOfTDS);  $i++)
					{
						echo '<option value="' . $NatureOfTDS[$i]["id"] . '">' . $NatureOfTDS[$i]["id"] . ' - ' .  $NatureOfTDS[$i]["description"] . '</option>';
					}
				?>	
				<?php //echo $natureofpayment = $obj_account_subcategory->combobox1("select `tds_id`,concat_ws(' - ', `nature_name`, `desc`) from `nature_of_tds`",0); ?>
			</select></td>
		</tr>
        <tr id="nature_detail_rate">
			<td>TDS Rate (%) </td>
			<td><input type="text" name="nature_rate" id="nature_rate"  value="<?php echo $_REQUEST['nature_deduction_rate']?>" onKeyUp='extractNumber(this,2,true);'/></td>
		</tr>
        <tr>
			<td>Note</td>
			<td><input type="text" name="note" id="note"  value="<?php echo $_REQUEST['note']?>"/></td>
		</tr>
	</table>
</td></tr>
</table>
 <tr><td colspan="2"> <br /></td></tr>
        <tr>
        <td colspan="2">
		<!--<tr><td><input type="hidden" name= "society_id" id="society_id" value="<?php echo DEFAULT_SOCIETY; ?>"/></td></tr>
        <tr>
			<td>Group<?php// echo $star; ?></td>
			<td>
			<select name="groupid" id="groupid" onChange="get_category(this.value);">		
				<?php //echo $combo_society = $obj_account_subcategory->combobox("select `id`, `groupname` from `group`", $_REQUEST['groupid']); ?>
			</select>
			</td>
		</tr>-->
		<!--<tr>
			<td>Category ID<?php //echo $star; ?></td>
			<td>
		<select name="categoryid" id="categoryid">		
<?php //echo $combo_society = $obj_account_subcategory->combobox("select `category_id`, `category_name` from `account_category` where group_id='" .  $_REQUEST['groupid']. "' ", $_REQUEST['categoryid'], 'Primary', '1');
		//echo $combo_society = $obj_account_subcategory->combobox("select `category_id`, `category_name` from `account_category`",0);
	 ?>
		</select>
			</td>
		</tr>-->
		<!--<tr>
			<td>Ledger<?php //echo $star; ?></td>
			<td><input type="text" name="ledger_name" id="ledger_name"  value="<?php //echo $_REQUEST['ledger_name']?>"/></td>
		</tr>	
        	  <script>
      $('#ledger_name').bind('keypress', function(e) {

			    if($('#ledger_name').val().length >= 0)
			    {
			        var k = e.keyCode;
			        var ok = k >= 65 && k <= 90 || // A-Z
			            k >= 97 && k <= 122 || // a-z
			            k >= 48 && k <= 57 || // 0-9
			            k == 32; // {space}

			        if (!ok){
			            e.preventDefault();
			        }
			    }
			}); 
		
</script>-->
       <!--<tr id="opening">
			<td >Opening Type <?php //echo $star; ?></td>
			<td>
		<select name="opening_type" id="opening_type" >
			<option value="0"> Please Select </option>
            <option value="1"> Credit </option>
            <option value="2"> Debit </option>
		</select>
			</td>
		</tr>-->
       <!--	<tr id="opening_Balance">
			<td>Opening Balance<?php //echo $star; ?></td>
			<td><input type="text" name="opening_balance" id="opening_balance"  value="<?php //echo $_REQUEST['opening_balance']?>"/></td>
		</tr>-->
       <!-- <tr>
			<td><!--Date--><?php //echo $star; ?><!--</td>
			<td><input type="hidden" name="balance_date" id="balance_date"  value="<?php //if($_REQUEST['balance_date'] <> ""){echo $_REQUEST['balance_date'];}else{echo $startdate; }?>"   readonly /></td>
		</tr>-->
       <!-- <tr id="GSTIN_Details">
			<td>GSTIN No.<?php //echo $star; ?></td>
			<td><input type="text" name="GSTIN_No" id="GSTIN_No"  value="<?php //echo $_REQUEST['GSTIN_No']?>"/></td>
		</tr>
        <tr id="pan_Details">
			<td>PAN No<?php //echo $star; ?></td>
			<td><input type="text" name="Pan_no" id="Pan_no"  value="<?php //echo $_REQUEST['PAN_No']?>"/></td>
		</tr>-->
       <!-- <tr>
			<td>Note</td>
			<td><input type="text" name="note" id="note"  value="<?php //echo $_REQUEST['note']?>"/></td>
		</tr>-->
       <!-- <tr><td colspan="2"> <br /></td></tr>
        <tr>
        <td colspan="2">-->
            <!--<table style="width:100%;" cellpadding="5" cellspacing="5">
            <tr>
                <td>Show In Bill : </td><td><input type="checkbox" name="show_in_bill" id="show_in_bill" value="1" />&nbsp;&nbsp;&nbsp;</td>
                <td>Taxable : </td><td><input type="checkbox" name="taxable" id="taxable" value="1" />&nbsp;&nbsp;&nbsp;</td>
                <td>Sale : </td><td><input type="checkbox" name="sale" id="sale" value="1" />&nbsp;&nbsp;&nbsp;</td>
                <td>Purchase : </td><td><input type="checkbox" name="purchase" id="purchase" value="1" />&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr>
            	<td>Income : </td><td><input type="checkbox" name="income" id="income" value="1" />&nbsp;&nbsp;&nbsp;</td>
                <td>Expense : </td><td><input type="checkbox" name="expense" id="expense" value="1" />&nbsp;&nbsp;&nbsp;</td>
                <td>Payment : </td><td><input type="checkbox" name="payment" id="payment" value="1" />&nbsp;&nbsp;&nbsp;</td>
                <td>Receipt : </td><td><input type="checkbox" name="receipt" id="receipt" value="1" />&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr <?php //if ($_SESSION['society_id'] <> '32' && $_SESSION['society_id'] <> '59'){ echo 'style="visibility:hidden;"'; } ?>><td>Supplementary Bill : </td><td><input type="checkbox" name="supplementary_bill" id="supplementary_bill" value="1"  /></td></tr>
            </table>-->
            <!--cellpadding="5" cellspacing="5"-->
            <table style="width:70%;margin-left: 12%;" >
            	<tr>
                	<td><input type="checkbox" name="show_in_bill" id="show_in_bill" value="1"/></td><td  style="width:20%;">Show In Maintence Bill</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><input type="checkbox" name="income" id="income" value="1" /></td><td>Income</td>
                    <td><input type="checkbox" name="sale" id="sale" value="1" /></td><td>Sale</td>
                </tr>
                <tr>                   
                	<td><input type="checkbox" name="supplementary_bill" id="supplementary_bill" value="1"  /></td><td>Supplementary Bill</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><input type="checkbox" name="expense" id="expense" value="1" /></td><td>Expense</td>
                    <td><input type="checkbox" name="purchase" id="purchase" value="1" /></td><td>Purchase</td>
                </tr>
                <tr>
                	<td><input type="checkbox" name="taxable" id="taxable" value="1" /></td><td>Taxable</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><input type="checkbox" name="payment" id="payment" value="1" /></td><td>Payment</td>
                </tr>
                <tr>
					<td><input type="checkbox" name="nothreshold" id="nothreshold" value="1" /></td><td>Taxable Without GST Threshold</td>					
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td><input type="checkbox" name="receipt" id="receipt" value="1" /></td><td>Receipt</td>
            	</tr>	            
            </table>
        </td>
        </tr>
        <tr><td colspan="2"> <br /></td></tr>
        <tr>
			<td colspan="2" align="center"><input type="hidden" name="id" id="id"><input type="submit" class="btn btn-primary" name="insert" id="insert" value="<?php if(isset($_REQUEST['insert'] ) && $_REQUEST['insert'] <> ""){echo $_REQUEST['insert'];}else {echo "Insert";} ?>" style="padding: 6px 12px; color:#fff;background-color: #2e6da4;" >
            &nbsp; <input type="button" name="cancel" class="btn btn-primary" id="cancel" value="Cancel" onClick="window.location.href='ledger.php'" style="padding: 6px 12px; color:#fff;background-color: #2e6da4;" /></td>
		</tr>
<script>
		var group_id=document.getElementById('groupid').value;
		
		get_category(group_id,'<?php echo $_REQUEST['categoryid'];?>');
		
         
        </script>
</table>
</form>
</div>

<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_account_subcategory->pgnation();
echo "<br>";
//$str = $obj_account_subcategory->display1($str1);
echo "<br>";
//$str1 = $obj_account_subcategory->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>
</center>
</div>

<?php
if(isset($_REQUEST['edt']))
{
	 
?>
<script>	
	//alert("Bingo");
	getaccount_subcategory('edit-' + <?php echo  $_REQUEST['edt'] ?>); 
</script>

<?php    
}

if(isset($_REQUEST['opening_type']))
{
?>
<script>
	document.getElementById('opening_type').value = '<?php echo $_REQUEST['opening_type']?>';
</script>

<?php    
}

if(isset($_REQUEST['type'])  && $_REQUEST['type'] == 1)
{
?>
<script>
	document.getElementById("btnAdd").click(); 
</script>

<?php    
}
?>
<?php include_once "includes/foot.php"; ?>