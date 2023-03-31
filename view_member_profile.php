<?php include_once("includes/head_s.php");?>
<?php 
include_once "classes/include/dbop.class.php";
include_once "classes/view_member_profile.class.php" ;
include_once "classes/dbconst.class.php";
include_once "classes/tenant.class.php" ;
include_once "classes/utility.class.php" ;
include_once "classes/lien.class.php";
$obj_tenant = new tenant($m_dbConn);
$m_dbConnRoot = new dbop(true);
$obj_lien=new lien($m_dbConn,$m_dbConnRoot);
$obj_utility = new utility($m_dbConn,$m_dbConnRoot);
//print_r($TenantDetails);
$obj_view_member_profile = new view_member_profile($m_dbConn);

$show_member_main 		 = $obj_view_member_profile->show_member_main();
//print_r($show_member_main[0]); 
$TenantDetails= $obj_tenant->getTenantRecords($show_member_main[0]['unit']);


$hasAccess = true;

if($_SESSION['role'] == ROLE_MEMBER && $_SESSION['unit_id'] <> $show_member_main[0]['unit'])
{
    $hasAccess = false;
}
/*else if($_SESSION['role'] == ROLE_ADMIN_MEMBER)
{
    if($_SESSION['profile'][PROFILE_EDIT_MEMBER] != 1 && $_SESSION['unit_id'] <> $show_member_main[0]['unit'])
    {
        $hasAccess = false;
    }
}*/

if($hasAccess == false)
{
	?>
		<script>
			window.location.href = 'Dashboard.php';

		</script>

	<?php
	exit();
}

$show_mem_other_family   = $obj_view_member_profile->show_mem_other_family();
$show_mem_car_parking    = $obj_view_member_profile->show_mem_car_parking();
$show_mem_bike_parking   = $obj_view_member_profile->show_mem_bike_parking();
$share_certificate_details = $obj_view_member_profile->show_share_certificate_details();
$show_share_certificate = $obj_view_member_profile->show_share_certificate();
$show_ledgers = $obj_view_member_profile->show_account_details();
$show_mem_lien=$obj_lien->getAllLienDetails($show_member_main[0]['unit']);
$UnitBlock = $_SESSION["unit_blocked"];
$balance_amount = $obj_utility->getDueAmount($show_member_main[0]['unit']);
$balance_amount = ($balance_amount < 0) ? abs($balance_amount).' (Cr)' : $balance_amount.' (Dr)';
?>
<head>
<style>
#errorBox
{
    color:hsla(0,100%,50%,1);
    font-weight: bold;
}
.table_format
{
	text-align: center;
    vertical-align: middle;
}
.table_format td, th
{
    text-align: center;
    vertical-align: middle;
}

.table_format_left
{
    text-align: left;
    vertical-align: middle;
}
.table_format_left td, .table_format_left td th
{
    text-align: left;
    vertical-align: middle;
}
</style>
<script language="application/javascript" type="text/javascript" src="js/validate.js"></script> 
<script type="text/javascript" src="js/jsview_member_profile_mem_edit.js"></script>
<script type="application/javascript" language="javascript"></script>
<script type="text/javascript" src="js/OpenDocumentViewer.js">

function go_error()
{
	setTimeout('hide_error()',10000);	
}
function hide_error()
{
	document.getElementById('error').style.display = 'none';	
}
	 
	
	//$( document ).ready(function() {
		 
		  <?php
		if(isset($_GET['edt']))
		{  
			?>
		var isblocked = '<?php echo $UnitBlock ?>';
		if(isblocked==1)
		{
			//alert("We are sorry,but your access has been blocked for this feature . Please contact your Managing Committee for resolution .");
			window.location.href='suspend.php';	
			
			
		}
    
	<?php 
		}
	?>
//});
$(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0',
            buttonImageOnly: true ,
            defaultDate: '01-01-1980'
        })});

</script>

</head>
<?php if(isset($_REQUEST['up'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>
<br>
<?php
    if($_SESSION['role'] == ROLE_MEMBER || $_SESSION['role'] == ROLE_ADMIN_MEMBER)
    {
        ?>
        <div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:3.5%;width:70%">
        <?php
    }
    else
    {
        ?>
        <div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:3.5%;width:75%">
        <?php
    }
?>
    <div class="panel-heading" id="pageheader">Profile View</div>
<!--<center><font color="#43729F" size="+1"><b>Profile View</b></font></center>-->
<br><br>

<center>

<form method="post" name="memberform" action="#" onSubmit="return validate();">
<input type="hidden" name="id" value="<?php echo $_GET['id'];?>" />
<div>
<center>
<table style="display:none">

<tr>
	<td>
<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
    </td>
</tr>
</table>
<table style="padding-bottom:10px">

<tr>
	<td style="padding:5px">
<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
    </td>
    <?php
	if($_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_EDIT_MEMBER] == '1' || $_SESSION['owner_id']==$_GET['id']))
	{
	?>
    
        <?php
		if(!isset($_GET['edt']))
		{  
			?>
            <td style="padding:5px">
				<input type="button"  class="btn btn-primary"  value="Edit Profile"  id="Edit" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;"onClick="window.location.href='view_member_profile.php?edt&prf&mkm&tik_id=<?php echo time();?>&id=<?php echo $_GET['id'];?>'">
                </td>
			<?php
		}
		else
		{
			?>
            <td style="padding:5px">
				<input type="submit"  class="btn btn-primary"  value="Update Profile"  id="insert" name="update" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal; background-color: #337ab7;color: #fff; border-color: #2e6da4;">
                </td>
                <td style="padding:5px">
				<input type="button"  class="btn btn-primary"  value="Cancel" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;"onClick="window.location.href='view_member_profile.php?prf&mkm&tik_id=<?php echo time();?>&id=<?php echo $_GET['id'];?>'">
                </td>
			<?php
		}
	}
	?>
	
	<td style="padding:5px">
	<?php 
	if($_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_EDIT_MEMBER] == '1' || $_SESSION['owner_id']==$_GET['id']))
	{
	?>
    <!-- <input type="button" class="btn btn-primary "  value="Edit Unit Details" style=" height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;" onClick="window.location.href='unit.php?uid=<?php echo $show_member_main[0]['unit']?>'"> -->
    <?php
	}
	?>
    </td>
    <td style="padding:5px">
    <input type="button" class="btn btn-primary "  value="Total Balance Rs.<?=$balance_amount?>" style=" height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;background-color:#FFFFFF;color:#000;border-color:#FFFFFF;border-top-style:none;border-left-style:none;border-right-style:none;font-weight:bold" onClick="window.open('view_ledger_details.php?lid=<?=$show_member_main[0]['unit'];?>&gid=<?=ASSET?>', '_blank')">
    </td>
    </tr>
    </table>
    </center>
</div>
<div id="errorBox"></div>
<table align="center" border="0"> <!-- class="profile_table" -->
<tr>
	<td valign="top" align="center"><font color="red"><?php if(isset($_GET['up'])){echo "<b id=error>Record Updated Successfully</b>";}else{echo '<b id=error></b>';} ?></font></td>
</tr>
<tr><td>
<p class="text-center">
    <i class="fa fa-home" style="font-size: 14px;"></i>&nbsp;<u>UNIT DETAILS</u>&nbsp;<i class="fa fa-home" style="font-size: 14px;"></i>
</p>
<table align="left" class="text-center" border="0" style="width: 100%;">
<tr align="left">
    <td><b>Owner Name(s)</b></td>
    <td>:</td>
    <td align="left" colspan="4">
        <input type="text" name="owner_name" id="owner_name" class="field_input" value="<?php echo $show_member_main[0]['owner_name'];?>" style="width:550px;" <?php if($_SESSION['profile'][PROFILE_EDIT_MEMBER] != 1 ) { echo 'readonly';} ?>/>
    </td>
    <td colspan="12" class="text-right" style="margin-right: 20px;">
        <!-- <input type="button" class="btn btn-primary" value="Accept Deposits" style="width:110px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;"onClick="window.location.href='pp_deposits.php?prf&mkm&tik_id=<?php echo time();?>&id=<?php echo $_GET['id'];?>&show&imp'"> -->
    </td>
</tr>
<tr align="left">
	<td width="150"><b>Permanant Address</b></td>
    <td>:</td>
    <td align="left"><?php echo $show_member_main[0]['alt_address'];  ?></td>
    <td colspan="12" class="text-right">
        <input type="button" class="btn btn-primary" value="New Loan" style="width:110px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;"onClick="window.location.href='pp_loan.php?prf&mkm&tik_id=<?php echo time();?>&id=<?php echo $_GET['id'];?>&show&imp'">
    </td>
</tr>

<tr align="left">
    <td><b>Share Certificate No.</b></td>
    <td>:</td>
    <td align="left" colspan="4">
        <?php 
            if($share_certificate_details[0]['share_certificate'] <> '')
            {
                echo $share_certificate_details[0]['share_certificate'] . ', distinctive no. from ' . $share_certificate_details[0]['share_certificate_from'] . ' to ' . $share_certificate_details[0]['share_certificate_to'];
                if($share_certificate_details[0]['share_certificate_from'] > 0 || $share_certificate_details[0]['share_certificate_to'] > 0)
                {
                    echo ' (allotted  ' . ($share_certificate_details[0]['share_certificate_to'] - $share_certificate_details[0]['share_certificate_from'] + 1) . ' shares)' ;
                }
            } 
        ?>   
    </td>
    <td colspan="12" class="text-right">
       <input type="button" class="btn btn-primary" value="New Deposits" style="width:110px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;"onClick="window.location.href='pp_deposits.php?prf&mkm&tik_id=<?php echo time();?>&id=<?php echo $_GET['id'];?>&show&imp'">
    </td>
</tr>
</table>


<table align="left" class="text-center" border="0" style="width: 100%;">
    <tr height="25">
        <td class="text-center mb-2" style="font-weight: bold;text-align: center;">
            <br/>
            <i class="fa fa-group" style="font-size: 14px;">&nbsp;</i><b><u>ACCOUNT DETAILS</u></b>&nbsp;<i class="fa fa-group" style="font-size: 14px;"></i>
        </td>
    </tr>
</table>
<table align="center" class="text-center">
    <tr>
    	<td class="text-center">
        	<table border="0" class="text-center">
            <tr height="30" bgcolor="#E8E8E8" class="text-center">
                <th width="180">ACCOUNT TYPE</th>
                <th width="80">Amount</th>
            </tr>
           
            <?php
    		if($show_ledgers<>"")
    		{
                $ii1 = 1;
                foreach($show_ledgers as $k2 => $v2)
        		{
                   $LedgerID =  $v2['ledgerID'];
                   $GroupID =  $v2['group_id'];
                   $loan_id = $v2['id'];
                   $account_type = $v2['account_type'];

                   ?> 
                   
                    

                    <tr height="25" bgcolor="#BDD8F4" class="text-center">
                        <td align="center">
                            <input type="text" name="account_type_<?php echo $ii1;?>" id="account_type_<?php echo $ii1;?>" value="<?php echo $show_ledgers[$k2]['ledger_name'];?>" style="width:120px;" class="field_input" <?php if((($_SESSION['role'] == ROLE_MEMBER) || ($_SESSION['role'] == ROLE_ADMIN_MEMBER || ($_SESSION['role'] == ROLE_ADMIN) && $show_mem_other_family[$k2]['coowner']==1)) && $_SESSION['profile'][PROFILE_EDIT_MEMBER] != 1) { echo 'readonly';} ?> />
                        </td>
                        <td align="center" class="text-center">
                            <input type="text" name="account_amt_<?php echo $ii1;?>" id="account_amt_<?php echo $ii1;?>" value="<?php echo $show_ledgers[$k2]['Dues'];?>" style="width:80px;" class="field_input"/>
                        </td>
                        
                        <td align="center" class="text-center">
                            <a href="view_ledger_details.php?lid=<?php echo $LedgerID; ?>&gid=<?php echo $GroupID; ?>" target="_blank"><input type="button" name="view_ledger_<?php echo $ii1;?>" id="view_ledger_<?php echo $ii1;?>" value="View Ledger" class="btn btn-primary btn-sm"  /></a>
                        </td>
                        <?php

                            if($account_type == 'loan'){ ?>
                                <td align="center" class="text-center">
                                    <a href="pp_receipts.php?loan_id=<?php echo $loan_id; ?>" target="_blank"><input type="button" name="receipt_<?php echo $ii1;?>" id="receipt_<?php echo $ii1;?>" value="Receipt" class="btn btn-primary btn-sm"  /></a>
                                </td>
                            <?php }
                        ?>
                        
                    </tr>

                    </tr>           
                    <?php
                    $ii1++;
        		}
    		}
    		else
    		{
    			?>

                <?php	
    		}
    		?>
            </table>
        </td>
    </tr>
</table>

<br>
<br>

</td></tr>

<tr>
<td><input type="hidden" name="test" id="test"/></td>
</tr>

</table>
</form>
<center>
<?php
    if(!isset($_GET['edt']))
    {
        ?>
            <script>
                $('.field_input').replaceWith(function(){
                    return '<span class='+this.className+'>'+this.value+'</span>'
                });
                $('.field_select').replaceWith(function(){
                    return '<span class='+this.className+'>' + this.options[this.selectedIndex].text + '</span>'
                });
                $('.field_date').replaceWith(function(){
                    return '<span class="">'+this.value+'</span>'
                });
            </script>
        <?php
    }
?>
<script>
function expandDetails(obj)
{
    var id = obj.id.split('_')[1]; 
    document.getElementById("exp_" + id).innerHTML = "Less";
    document.getElementById("exp_" + id).onclick = function(){ collapseDetails(this); } ;
    document.getElementById("extra_" + id).style.display = "table-row"; 
}
function collapseDetails(obj)
{
    var id = obj.id.split('_')[1]; 
    document.getElementById("exp_" + id).innerHTML = "More";
    document.getElementById("exp_" + id).onclick = function(){ expandDetails(this); } ;
    document.getElementById("extra_" + id).style.display = "none"; 
}
function memexpandDetails(obj)
{
    var mem = obj.id.split('_')[1]; 
    document.getElementById("mem_" + mem).innerHTML = "Less";
    document.getElementById("mem_" + mem).onclick = function(){ memcollapseDetails(this); } ;
    document.getElementById("memdetail_" + mem).style.display = "table-row"; 
}
function memcollapseDetails(obj)
{
    var mem = obj.id.split('_')[1]; 
    document.getElementById("mem_" + mem).innerHTML = "More";
    document.getElementById("mem_" + mem).onclick = function(){  memexpandDetails(this); } ;
    document.getElementById("memdetail_" + mem).style.display = "none"; 
}
function SendActEmail(role,unit_id,society_id,code,email,name)
{

	$.ajax({
		url : "ajax/ajax_email.php",
		type : "POST",
		data: {"mode" : "email","role" : role,"unit_id" : unit_id,"society_id" : society_id,"code" : code,"email" : email,"name" : name} ,
		success : function(data)
		{	
			
			if(data != '') 
			{
				var sIndex = data.indexOf("Success");
				if(parseInt(sIndex) > 0)
				{
					alert("Email Send Successfully");
				}
				else
				{
					alert("Error while sending Email. Please retry.");
				}

			}
			else
			{
			}
		}
	});	
}
function delete_doc(DocumentID)
{
    if(confirm("Are you sure you want to delete this attachment ?"))
    {
        $.ajax({
        url : "ajax/documents.ajax.php",
        type : "POST",
        data: {"method" : "delete","ID" : DocumentID} ,
        success : function(data)
        {   
           // alert(data);
           var sData = data.trim();
            if(sData == "1") 
            {
                alert("Document deleted Successfully");
                window.location.reload();
            }
            else
            {
                alert("Document not deleted");
            }
        }
        /*fail: function()
        {
            alert("Failed ! unable to delete selected document");
        },
        
        error: function(XMLHttpRequest, textStatus, errorThrown) 
        {
            alert("Unexpected error while deleting selected document");
        }*/
    }); 
    }
}
</script>
<?php include_once "includes/foot.php"; ?>
