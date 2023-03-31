<?php include_once("includes/head_s.php");?>
<?php 
include_once "classes/include/dbop.class.php";
include_once "classes/pp_view_member_profile.class.php" ;
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
//print_r($show_mem_other_family);
$show_mem_car_parking    = $obj_view_member_profile->show_mem_car_parking();
$show_mem_bike_parking   = $obj_view_member_profile->show_mem_bike_parking();
$share_certificate_details = $obj_view_member_profile->show_share_certificate_details();
$show_share_certificate = $obj_view_member_profile->show_share_certificate();
$show_mem_lien=$obj_lien->getAllLienDetails($show_member_main[0]['unit']);
//print_r($share_certificate_details);
$UnitBlock = $_SESSION["unit_blocked"];
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

<form method="post" name="memberform" action="process/view_member_profile_mem_edit.process.php" onSubmit="return validate();">
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
    <input type="button" class="btn btn-primary "  value="Edit Unit Details" style=" height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;" onClick="window.location.href='unit.php?uid=<?php echo $show_member_main[0]['unit']?>'">
    <?php
	}
	?>
    </td>
    <td style="padding:5px">
	<?php 
	if($_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_EDIT_MEMBER] == '1' ))
	{
	?>
    <input type="button" class="btn btn-primary "  value="Transfer Ownership" style=" height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;" onClick="window.location.href='unit.php?mtfr&uid=<?php echo $show_member_main[0]['unit']?>'">
    <?php
	}
	?>
    </td>
    <td style="padding:5px">
    <input type="button" class="btn btn-primary "  value="Total Dues Rs.<?php echo $obj_utility->getDueAmount($show_member_main[0]['unit'])?>" style=" height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;background-color:#FFFFFF;color:#000;border-color:#FFFFFF;border-top-style:none;border-left-style:none;border-right-style:none;font-weight:bold" onClick="window.open('member_ledger_report.php?&uid=<?php echo $show_member_main[0]['unit'];?>', '_blank')">
    </td>
    <td style="padding:5px">
   	<?php
	if($show_mem_lien=="")
	{
	?>
    	<input type="button" class="btn btn-primary "  value="No Lien" style=" height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;background-color:#FFFFFF;color:#000;border-color:#FFFFFF;border-top-style:none;border-left-style:none;border-right-style:none;font-weight:bold" onClick="window.open('lien.php?type=open&unit_id=<?php echo $show_member_main[0]['unit'];?>', '_blank')">
    <?php
	}
	$openFlag=0;
	$closeFlag=0;
	for($i=0;$i<sizeof($show_mem_lien);$i++)
	{
		if($show_mem_lien[$i]['LienStatus'] == "Open")
		{
			$openFlag=1;
			break;
		}
		if($show_mem_lien[$i]['LienStatus']=="Closed")
		{
			$closeFlag=1;
		}
	}
	if($openFlag==1)
	{
	?>
    	<input type="button" class="btn btn-primary "  value="Open Lien" style=" height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;background-color:#FFFFFF;color:#F00;border-color:#FFFFFF;border-top-style:none;border-left-style:none;border-right-style:none;font-weight:bold" onClick="window.open('lien.php?type=open&unit_id=<?php echo $show_member_main[0]['unit'];?>', '_blank')">
    <?php
	}
	if($openFlag!=1)
	{
		if($closeFlag==1)
		{
		?>
    		<input type="button" class="btn btn-primary "  value="Closed Lien" style=" height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;background-color:#FFFFFF;color:#000;border-color:#FFFFFF;border-top-style:none;border-left-style:none;border-right-style:none;font-weight:bold" onClick="window.open('lien.php?type=closed&unit_id=<?php echo $show_member_main[0]['unit'];?>', '_blank')">
    	<?php
		}
	}
	?>
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

<table align="center" border="0" style="width: 100%;">
<tr>
    <td colspan="6" style="font-weight: bold;text-align: center;">
        <i class="fa fa-home" style="font-size: 14px;"></i>&nbsp;<u>UNIT DETAILS</u>&nbsp;<i class="fa fa-home" style="font-size: 14px;"></i><?php if($_SESSION['is_year_freeze'] == 0)
	{?>
    <!--<button type="button" class="btn btn-primary btn-xs" onClick="window.location.href='unit.php?uid=<?php echo $show_member_main[0]['unit']?>'" style="margin-left:10px"><i class="fa fa-edit fa-large"></i></button>-->
    <!--<a href="mem_other_family_new.php?prf&mkm&mem_id=<?php echo $_GET['id'];?>&tik_id=<?php echo time();?>" style="color:#00F; text-decoration:none;"><b>Add New</b></a></b>-->
    <?php }?>&nbsp;<br/><br/>
    </td>
    
</tr>
<tr>
    <td colspan="6"></td>
</tr>
<tr>
    <td colspan="6"></td>
</tr>
<tr align="left">
	<td width="130"><b>Wing</b></td>
    <td width="10">:</td>
    <td width="250"><?php echo $show_member_main[0]['wing'];?></td>
    
    <td width="150"><b>Flat No.</b></td>
    <td width="10">:</td>
    <td><?php echo $show_member_main[0]['unit_no'];?></td>
</tr>
<tr align="left">
	
    <td><b>Area</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['area']. ' Sq.Ft';?></td>

    <td><b>Parking No.</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['parking_no'];?></td>
</tr>
<tr align="left">
    <td><b>Owner Name(s)</b></td>
    <td>:</td>
    <td align="left" colspan="4">
        <input type="text" name="owner_name" id="owner_name" class="field_input" value="<?php echo $show_member_main[0]['owner_name'];?>" style="width:550px;" <?php if($_SESSION['profile'][PROFILE_EDIT_MEMBER] != 1 ) { echo 'readonly';} ?>/>
    </td>
</tr>
<tr align="left">
	<td width="150"><b>Landline Number</b></td>
    <td>:</td>
    <td align="left"><?php echo $show_member_main[0]['resd_no'] ?></td>
    <td><b>Intercom Number</b></td>
    <td>:</td>
    <td><?php echo $show_member_main[0]['intercom_no'];?></td>
</tr>
<tr align="left">
	<td width="150"><b>Permanant Address</b></td>
    <td>:</td>
    <td align="left"><?php echo $show_member_main[0]['alt_address'];  ?></td>
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
</tr>
<tr align="left">
	<td width="150"><b>Nomination Form Submitted</b></td>
    <td>:</td>
    <td align="left"><?php if($share_certificate_details[0]['nomination'] == 1) { echo "Yes"; } else { echo "No"; } ?></td>
    <?php if($share_certificate_details[0]['nomination'] == 1)
	{ ?>
    <td><b>Nominee Name</b></td>
    <td>:</td>
    <td align="left"><?php echo $share_certificate_details[0]['nominee_name']?></td>
    <?php }?>
</tr>
<tr>
<tr align="left">
	<td width="150"><b>GSTIN No</b></td>
    <td>:</td>
    <td align="left">
    <input type="text" name="owner_gstin_no" id="owner_gstin_no" class="field_input" value="<?php echo $show_member_main[0]['owner_gstin_no'];?>" style="width:150px;" <?php if($_SESSION['profile'][PROFILE_EDIT_MEMBER] != 1 ) { echo 'readonly';} ?>/>
    </td>

</tr>

<!--<tr align="left"></span>
    <td><b>Show In Directory</b></td>
    <td>:</td>
    <td><input type="checkbox" name="publish_contact" id="publish_contact" value="1" <?php //if($show_member_main[0]['publish_contact']==1){echo 'checked';}else{echo '';}?> <?php //if(!isset($_GET[edt])){ echo 'disabled';} ?> <?php //if($_SESSION['role'] == ROLE_ADMIN) { echo 'onclick="return false;"';} ?>></td>
    <td></td>
    <td></td>
    <td></td>
</tr>-->
<!--<tr><td><br/></td></tr>
<tr align="left">
    <td width="130"><b>Name Displayed In Bill</b></td>
    <td width="10">:</td>
    <td colspan="4"><?php //echo $show_member_main[0]['owner_name']; ?></td>
</tr>-->
<!--<tr><td><br/></td></tr>
<tr>
    <td colspan="6" style="font-weight: bold;text-align: center;">
        <br><i class="glyphicon glyphicon-phone-alt" style="font-size: 12px;"></i>&nbsp;<u>CONTACT DETAILS</u>&nbsp;<i class="glyphicon glyphicon-phone-alt" style="font-size: 12px;"></i><br/><br/>
    </td>
</tr>
<tr align="left">
	<td><b>Residence Number</b></td>
    <td>:</td>
    <td><input type="text" name="resd_no" id="resd_no" class="field_input" value="<?php //echo $show_member_main[0]['resd_no'];?>" style="width:150px;" /></td>
    
    
</tr>
<tr align="left">
    <td><b>Mobile No.</b></td>
    <td>:</td>
    <td><input type="text" name="mob" id="mob" class="field_input" value="<?php //echo $show_member_main[0]['mob'];?>" style="width:150px;" /></td>
    
    <td><b>Alternate Mobile No.</b></td>
    <td>:</td>
    <td><input type="text" name="alt_mob" id="alt_mob" class="field_input" value="<?php //if($show_member_main[0]['alt_mob']==''){echo 'Not Mentioned';}else{echo $show_member_main[0]['alt_mob'];}?>" style="width:150px;"/></td>
</tr>
<tr align="left">
    <td><b>Email ID</b></td>
    <td>:</td>
    <td><input type="text" name="email" id="email" class="field_input" value="<?php echo $show_member_main[0]['email'];?>" style="width:150px;" /></td>
    <td><b>Alternate Email ID</b></td>
    <td>:</td>
    <td><input type="text" name="alt_email" id="alt_email" class="field_input" value="<?php if($show_member_main[0]['alt_email']==''){echo 'Not Mentioned';}else{echo $show_member_main[0]['alt_email'];}?>" style="width:150px;"/></td>
</tr>-->


<!--<tr>
    <td colspan="6" style="font-weight: bold;text-align: center;">
        <br><i class="fa fa-user" style="font-size: 14px;">&nbsp;</i><u>PERSONAL DETAILS</u>&nbsp;<i class="fa fa-user" style="font-size: 14px;"><br/><br/>
    </td>
</tr>
<tr align="left">
    <td><b>Date Of Birth</b></td>
    <td>:</td>
    <td><input type="text" name="dob" id="dob" class="basics field_date" value="<?php //echo getDisplayFormatDate($show_member_main[0]['dob']);?>" readonly style="width:70px;"/></td>
    
    <td><b>Designation</b></td>
    <td>:</td>
    <td colspan="5" align="left"><select name="desg" id="desg" class="field_select" style="width:142px;">
        <?php //echo $combo_desg = $obj_view_member_profile->combobox11("select desg_id,desg from desg where status='Y' order by desg" , $show_member_main[0]['desg_id']); ?>
    </select></td>
</tr>
<tr align="left">  
    <td><b>Wedding Anniversary</b></td>
    <td>:</td>
    <td><input type="text" name="wed_any" id="wed_any" class="basics field_date" readonly value="<?php //if($show_member_main[0]['wed_any']==''){echo 'Not Mentioned';}else{echo getDisplayFormatDate($show_member_main[0]['wed_any']);}?>" style="width:70px;"/></td>

    <td><b>Office Number</b></td>
    <td>:</td>
    <td><input type="text" name="off_no" id="off_no" class="field_input" value="<?php //echo $show_member_main[0]['off_no'];?>" style="width:150px;"/></td>
</tr>

<tr align="left">
    <td><b>Blood Group</b></td>
    <td>:</td>
    <td align="left">
        <select name="bg" id="bg" style="width:100px;" class="field_select">
        <?php //echo $combo_desg = $obj_view_member_profile->combobox11("select bg_id,`bg` from bg where status='Y'" , $show_member_main[0]['bg_id']); ?>
        </select>
    </td>
    
    <td><b>Office Location</b></td>
    <td>:</td>
    <td colspan="5" align="left"><input type="text" name="off_add" id="off_add" class="field_input" value="<?php //echo $show_member_main[0]['off_add'];?>" style="width:150px;"/></td>
</tr>
<tr>
    <td colspan="6"></td>
</tr>
<tr>
    <td colspan="6"></td>
</tr>
<tr align="left"></span>
    <td><b>Professional Profile<br/>(To show in Professional Directory)</b></td>
    <td>:</td>
    <td colspan="4">
        <textarea maxlength="500" style="width: 75%;" rows="3" name="profile" id="profile" class="field_input" placeholder="Enter your profession profile here ...."><?php //if(strlen($show_member_main[0]['profile']) == 0){if(!isset($_GET['edt'])){echo '<font color="red">No profile has been set</font>';}else{echo "";}} else{echo $show_member_main[0]['profile'];}?></textarea>
    </td>
</tr>
<tr align="left"></span>
    <td><b>Show In Directory</b></td>
    <td>:</td>
    <td><input type="checkbox" name="publish_profile" id="publish_profile" value="1" <?php //if($show_member_main[0]['publish_profile']==1){echo 'checked';}else{echo '';}?> <?php //if(!isset($_GET[edt])){ echo 'disabled';} ?> <?php //if($_SESSION['role'] == ROLE_ADMIN) { echo 'onclick="return false;"';} ?>></td>
    <td></td>
    <td></td>
    <td></td>
</tr>-->
</table>
<table class="table_format">
<tr height="25" valign="bottom">
    <td colspan="6" style="font-weight: bold;text-align: center;">
        <br/>
        <i class="fa fa-group" style="font-size: 14px;">&nbsp;</i><b><u>DETAILS OF MEMBERS ASSOCIATED WITH UNIT/FLAT </u></b>&nbsp;<i class="fa fa-group" style="font-size: 14px;"></i>
       <?php if($_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_EDIT_MEMBER] == '1' || $_SESSION['owner_id']==$_GET['id']))
	{?>
    <br/>
    <button type="button" class="btn btn-primary btn-xs" onClick="window.location.href='mem_other_family_new.php?prf&mkm&mem_id=<?php echo $_GET['id'];?>&tik_id=<?php echo time();?>'" style="float: right;"> Add Member<!--<i class="fa fa-plus fa-small">--></i></button>
    <!--<a href="mem_other_family_new.php?prf&mkm&mem_id=<?php echo $_GET['id'];?>&tik_id=<?php echo time();?>" style="color:#00F; text-decoration:none;"><b>Add New</b></a></b>-->
    <?php }?>
    </td>
</tr>
<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#E8E8E8">
                        <th width="180">Name</th>
                        <th width="80">Relation with<br/>primary owner</th>
                        <th width="80">Mobile</th>
                        <th width="150">E-Mail</th>
                        <th width="50">Publish Contact</th>
                        <th width="50">Publish Profile</th>
                        <th width="50">Ownership</th>
                        
                        <th width="100">Login Account</th>
                        <th width="100">Subscribed to Society Email</th>
                        <?php
                        if(!isset($_GET['edt']))
                        {
                            ?>
                                <th width="50">View</th>
                            <?php
                        }
                        else
                        {
                            ?>
                                <th width="50">Delete</th>
                            <?php
                        }
                        ?>
        </tr>
       
        <?php
		if($show_mem_other_family<>"")
		{
            $ii1 = 1;
            foreach($show_mem_other_family as $k2 => $v2)
    		{
               ?> 
                <input type="hidden" name="mem_other_family_id<?php echo $ii1;?>" value="<?php echo $show_mem_other_family[$k2]['mem_other_family_id'];?>" />
                
                <tr height="25" bgcolor="#BDD8F4">
                    <td align="center">
                    <input type="text" name="other_name<?php echo $ii1;?>" id="other_name<?php echo $ii1;?>" value="<?php echo $show_mem_other_family[$k2]['other_name'];?>" style="width:120px;" class="field_input" <?php if((($_SESSION['role'] == ROLE_MEMBER) || ($_SESSION['role'] == ROLE_ADMIN_MEMBER || ($_SESSION['role'] == ROLE_ADMIN) && $show_mem_other_family[$k2]['coowner']==1)) && $_SESSION['profile'][PROFILE_EDIT_MEMBER] != 1) { echo 'readonly';} ?> />
                    </td>
                    <td align="center">
                    <input type="text" name="relation<?php echo $ii1;?>" id="relation<?php echo $ii1;?>" value="<?php echo $show_mem_other_family[$k2]['relation'];?>" style="width:80px;" class="field_input"/>
                    </td>
                    
                    <td align="center">
                    <input type="text" name="other_mobile<?php echo $ii1;?>" id="other_mobile<?php echo $ii1;?>" value="<?php echo $show_mem_other_family[$k2]['other_mobile'];?>" style="width:80px;" class="field_input" />
                    </td>
                    <td align="center">
                    <input type="text" name="other_email<?php echo $ii1;?>" id="other_email<?php echo $ii1;?>" value="<?php echo $show_mem_other_family[$k2]['other_email'];?>" style="width:150px;" class="field_input" />
                    </td>
                    
                    <td>
                        <input type="checkbox" name="other_publish_contact<?php echo $ii1;?>" id="other_publish_contact<?php echo $ii1;?>" value="1" <?php if($show_mem_other_family[$k2]['other_publish_contact']==1){echo 'checked';}else{echo '';}?> <?php if(!isset($_GET[edt])){ echo 'disabled';} ?> <?php if($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_EDIT_MEMBER] == '1' || $_SESSION['owner_id']==$_GET['id']) { echo 'onclick="return true;"';} ?>>
                    </td>
                    <td>
                        <input type="checkbox" name="other_publish_profile<?php echo $ii1;?>" id="other_publish_profile<?php echo $ii1;?>" value="1" <?php if($show_mem_other_family[$k2]['other_publish_profile']==1){echo 'checked';}else{echo '';}?> <?php if(!isset($_GET[edt])){ echo 'disabled';} ?> <?php if($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_EDIT_MEMBER] == '1' || $_SESSION['owner_id']==$_GET['id']) { echo 'onclick="return true;"';} ?>>
                    </td>
                    
                    <td>
                        <select name="coowner<?php echo $ii1;?>" id="coowner<?php echo $ii1;?>" style="width:70px;" class="field_select" <?php if(/*$_SESSION['role'] != ROLE_SUPER_ADMIN*/$_SESSION['profile'][PROFILE_EDIT_MEMBER] == 1 && $_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1) { }else{echo 'disabled';} ?>>
							<option value="0" <?php if($show_mem_other_family[$k2]['coowner'] == 0) { echo 'selected'; } ?> >None</option>
                            <option value="1" <?php if($show_mem_other_family[$k2]['coowner'] == 1) { echo 'selected'; } ?> >Owner</option>
                            <option value="2" <?php if($show_mem_other_family[$k2]['coowner'] == 2) { echo 'selected'; } ?> >Co-Owner</option>
		               	</select>
                    </td>
                    
                    <td>
                    <?php 
					if($show_mem_other_family[$k2]['other_email'] == "")
					{
						echo "Please update Email";
					}
					else
					{ 
						$RetStatus = $obj_view_member_profile->obj_activation->CheckIfMappingAlreadyExist($show_mem_other_family[$k2]['other_email'],$_SESSION['society_id'], $show_member_main[0]['unit']);
						if($RetStatus == ACCOUNT_EXIST_ACTIVE)
						{
							echo "Active";
						}
						else
						{
							echo "<input type='button' value='Send Email' name='Send_activation_email" . $ii1."' id='Send_activation_email". $ii1."' onClick=\"SendActEmail('Member','". $show_member_main[0]['unit'] ."','". $_SESSION['society_id'] ."','". getRandomUniqueCode()."','". $show_mem_other_family[$k2]['other_email'] ."','". $show_mem_other_family[$k2]['other_name'] ."')\"/>";
							
						}
					}
					?>
                    
                    </td>
                    <td>
                    <input type="checkbox" name="Send_commu_emails<?php echo $ii1;?>" id="Send_commu_emails<?php echo $ii1;?>" value="1" <?php if($show_mem_other_family[$k2]['send_commu_emails']==1){echo 'checked';}else{echo '';}?> <?php if(!isset($_GET[edt])){ echo 'disabled';} ?> <?php if($_SESSION['role']!=ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_EDIT_MEMBER] != '1' || $_SESSION['owner_id']==$_GET['id']) { echo 'onclick="return true;"';} ?>>
                    </td>
                    <td>
                     <?php
                        if(!isset($_GET['edt']))
                        {
                            ?>
                                <a id="exp_<?php echo $ii1; ?>" onClick="expandDetails(this);">More</a>
                            <?php
                        }
                        else
                        {
                            ?>
                                <input type="checkbox" name="delete<?php echo $ii1; ?>" id="delete<?php echo $ii1; ?>" value="1" <?php if(($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_EDIT_MEMBER] == '1' || $_SESSION['owner_id']==$_GET['id'])  && $show_mem_other_family[$k2]['coowner'] > 0) { echo 'onclick="return true;"';} ?>>
                            <?php
                        }
                    ?>
                    </td>
                </tr>
    <?php
        if(!isset($_GET['edt']))
        {
            ?>
                <tr id="extra_<?php echo $ii1; ?>" style="display: none;">
            <?php
        }
        else
        {
            ?>  
                <tr id="extra_<?php echo $ii1; ?>">
            <?php
        }
    ?>
                    <td colspan="8">
                        <table class="table_format_left" style="width: 100%;">
                            <tr>
                                <td width="80px"><b>Date Of Birth</b></td>
                                <td width="5px">:</td>
                                <td align="left" width="250px"><input type="text" name="other_dob<?php echo $ii1;?>" id="other_dob<?php echo $ii1;?>" class="basics field_date" size="9" readonly value="<?php echo getDisplayFormatDate($show_mem_other_family[$k2]['other_dob']);?>" style="width:70px;" class="field_input"/></td>
                                <td><b>Designation</b></td>
                                <td width="5px">:</td>
                                
                                <td align="left">
                                    <select name="other_desg<?php echo $ii1;?>" id="other_desg<?php echo $ii1;?>" style="width:112px;" class="field_select">
                                        <?php echo $combo_spouse_desg_o=$obj_view_member_profile->combobox11("select desg_id,desg from desg where status='Y'" , $show_mem_other_family[$k2]['desg_id']); ?>
                                    </select>
                                </td>
                                
                            </tr>
                            <tr>
                                <td><b>Blood Group</b></td>
                                <td width="5px">:</td>
                                <td>
                                    <select name="other_bg<?php echo $ii1;?>" id="other_bg" style="width:100px;" class="field_select">
                                <?php echo $combo_desg_child = $obj_view_member_profile->combobox11("select bg_id,`bg` from bg where status='Y'" , $show_mem_other_family[$k2]['bg_id']); ?>
                                    </select>
                                </td>
                                
                                <td width="120px"><b>Wedding Anniversary</b></td>
                                <td width="5px">:</td>
                                <td align="left"><input type="text" name="other_wed<?php echo $ii1;?>" id="other_wed<?php echo $ii1;?>" class="basics field_date" size="9" readonly value="<?php echo getDisplayFormatDate($show_mem_other_family[$k2]['other_wed']);?>" style="width:70px;" class="field_input"/></td>
                            </tr>
                            <tr>
                                <td><b>Profile</b></td>
                                <td width="5px">:</td>
                                <td colspan="4"><textarea maxlength="500" style="width: 80%;" rows="3" name="other_profile<?php echo $ii1;?>" id="other_profile<?php echo $ii1;?>" class="field_input" placeholder="Enter your profession profile here ...."><?php if(strlen($show_mem_other_family[$k2]['other_profile']) == 0){if(!isset($_GET['edt'])){echo '<font color="red">No profile has been set</font>';}else{echo "";}} else{echo $show_mem_other_family[$k2]['other_profile'];}?></textarea></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php
                $ii1++;
    		}
		}
		else
		{
			?>
            <tr height="25"><td colspan="9" align="center"><font color="#FF0000"><b>No Records Found</b></font></td></tr>
            <?php	
		}
		?>
        <input type="hidden" name="tot_other" id="tot_other" value="<?php echo $ii1-1;?>" />
        </table>
    </td>
</tr>
<table class="table_format">
<tr height="25" valign="bottom" >
    
   
    <td colspan="6" style="font-weight: bold;text-align: center;">
        <br/>
        <i class="fa fa-group" style="font-size: 14px;">&nbsp;</i><b><u>LEASE DETAILS WITH UNIT/FLAT </u></b>&nbsp;<i class="fa fa-group" style="font-size: 14px;"></i>
       <?php if($_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_APPROVALS_LEASE] == '1' || $_SESSION['owner_id']==$_GET['id']))
	{?>
    <br/>
     
     <?php if($TenantDetails[0]['tenant_id']=='')
	 {?>
     <!--<span style="float: left;margin-left: 3px;"> Number of Previous Lease Entries : <?php  //echo $TenantDetails[0]['Count']?></span>-->
    <button type="button" class="btn btn-primary btn-xs" value="Add Tenant" onClick="window.location.href='tenant.php?prf&mem_id=<?php echo $_GET['id'];?>&tik_id=<?php echo time();?>'" style="float: right; "><!--<i class="fa fa-plus fa-small"></i>-->Add Lease</button>
    <!--<a href="mem_other_family_new.php?prf&mkm&mem_id=<?php echo $_GET['id'];?>&tik_id=<?php echo time();?>" style="color:#00F; text-decoration:none;"><b>Add New</b></a></b>-->
    <?php }else
	{?>
  <!--  <span style="float: left;margin-left: 3px;"><a href="tenant_list.php?u_id=<?php echo $TenantDetails[0]['unit_id']?>" target="_blank"> Number of Previous Lease Entries : <?php  //echo $TenantDetails[0]['Count']?></a></span>-->
		<!-- <button type="button" class="btn btn-primary btn-xs" onClick="window.location.href='mem_other_family_new.php?prf&mkm&mem_id=<?php// echo $_GET['id'];?>&tik_id=<?php// echo time();?>'" style="float: right; display:none;"><i class="fa fa-plus fa-small"></i></button>-->
          <!--<button type="button" class="btn btn-primary btn-xs" value="view_lease" onClick="window.location.href='tenant.php?mem_id=<?php //echo $_GET['id'];?>&tik_id=<?php //echo time();?>&view=<?php //echo $TenantDetails[0]['tenant_id'];?>'" style="float: right; "><!--<i class="fa fa-plus fa-small"></i>View Lease</button>-->
          <button type="button" class="btn btn-primary btn-xs" value="terminate_lease" onClick="window.location.href='tenant.php?mem_id=<?php echo $_GET['id'];?>&tik_id=<?php echo time();?>&edit=<?php echo $TenantDetails[0]['tenant_id'];?>&ter'" style="float: right; "><!--<i class="fa fa-plus fa-small"></i>-->Terminate Lease</button>
	<?php }
	/*if($TenantDetails[0]['active']==0)
	{?>
		 <button type="button" class="btn btn-primary btn-xs" onClick="window.location.href='tenant.php?prf&mkm&mem_id=<?php echo $_GET['id'];?>&tik_id=<?php echo time();?>'" style="float: right; display:block;">Edit Tenant</button>
		<?php }*/
	}?>
    </td>
</tr>
<!--<tr align="left">
<td> Number of Tenant Entries : <?php // echo $TenantDetails[0]['counttotal']?></td></tr>
--><tr>
	<td colspan="6">
    <?php if( $TenantDetails[0]['tenant_id']=='')
	 {?>
     <table border="0" >
     <tr height="30" bgcolor="#E8E8E8">
      <th width="280">Name on the Lease Document</th>
                        <th width="150">Start Date</th>
                        <th width="150">End Date</th>
                      <!--  <th width="100">Mobile</th>
                        <th width="180">E-Mail</th>-->
                        <th width="150">Member(s)</th>
               <!-- <th width="80">Document</th>-->
                        </tr>
                        <tr><td><br></td></tr>
                        <tr><td colspan="6" style="color: red;font-size: 11px; font-weight: bold;">No Active Tenant Record Found</td></tr>
                        
     <?php }
	 else{?>
    	<table border="0">
        <tr height="30" bgcolor="#E8E8E8">
                        <th width="280">Name on the Lease Document</th>
                        <th width="100">Start Date</th>
                        <th width="100">End Date</th>
                       <!-- <th width="100">Mobile</th>
                        <th width="180">E-Mail</th>-->
                        <th width="100">Member(s)</th>
                      <!--  <th width="80">Document</th>-->
                        <?php
                        if(!isset($_GET['edt']))
                        {
                            ?>
                                <th width="100">View</th>
                            <?php
                        }
                        else
                        {
                            ?>
                                <th width="100">Delete</th>
                            <?php
                        }
                        if($TenantDetails[0]['active']==0)
						{?>
                        <th width="100">Edit</th>
                        <?php 
						}
						else
						{?>
						<th width="100" style="display:none">Edit</th>
						<?php }
						if($TenantDetails[0]['active']==0)
						{?>
                        <th width="100"  style="display:none" >Status</th>
                        <?php }
						else{?>
                        <th width="100">Status</th>
                        <?php }?>
						
        </tr>
        
        <tr height="25" bgcolor="#BDD8F4">
        <td style="text-transform: capitalize;"><a id="mem_" onClick="memexpandDetails(this);"><?php echo $TenantDetails[0]['tenant_name']?> <?php echo $TenantDetails[0]['tenant_MName']?>  <?php echo $TenantDetails[0]['tenant_LName']?></a></td>
        <td><?php echo getDisplayFormatDate($TenantDetails[0]['start_date']);?></td>
        <td><?php echo getDisplayFormatDate($TenantDetails[0]['end_date']);?></td>
         <!--<td><?php// echo $TenantDetails[0]['mobile_no']?></td>
        <td><?php //echo $TenantDetails[0]['email']?></td>-->
        <td><?php echo $TenantDetails[0]['members']?></td>
      	<!--<td>  <?php //echo "<a href='http://localhost/onedrive/sujit/beta_aws/Uploaded_Documents/".$TenantDetails[0]['Document']. "'download>"?> <?php // "<img src='images/download1.ico'  width='20'>";?></a></td>-->
       <!--<td> <?php //echo  " <a href='http://localhost/onedrive/sujit/beta_aws/Uploaded_Documents/".$TenantDetails[0]['Document']. "'download>"?>Download </a></td>-->
       <td> <a id="mem_" onClick="window.location.href='tenant.php?mem_id=<?php echo $_GET['id'];?>&tik_id=<?php echo time();?>&view=<?php echo $TenantDetails[0]['tenant_id'];?>'">More</a></td>
       <?php
       if($TenantDetails[0]['active']==0)
	   {?><!--need this line -->
		   <td><a href="tenant.php?mem_id=<?php echo $_GET['id'];?>&tik_id=<?php echo time();?>&edit=<?php echo $TenantDetails[0]['tenant_id'];?>"><img src="images/edit.gif" /></a></td>
		<?php }
	      else
		  {?>
       <td><span>Active</span>
       <a href="tenant.php?mem_id=<?php echo $_GET['id'];?>?edit=<?php echo $TenantDetails[0]['tenant_id'];?>"><img src="images/edit.gif"  style="display:none;"/></a></td>
        <?php }?>
        
                   
        </tr>
         <?php
        if(!isset($_GET['edt']))
        {
            ?>
                <tr id="memdetail_" style="display: none;">
            <?php
        }
        else
        {
            ?>  
                <tr id="memdetail_">
            <?php
        }
    ?>
       <!-- <tr align="left" id="memdetail_" style="display: none;">-->
        <td  valign="left" colspan="8">
        <table class="table_format_left" style="width: 100%; float:left">
        <tr><td width="200%">
        					<table width="100%"  style="background-color:#f9f9f9;">
                         <tr height="25" align="left"  style="background-color:#f9f9f9;">
                         <th width="80px"style="text-align:left" >Agent Name :</th><th style="text-transform: capitalize;text-align:left; width:180px;"><?php echo $TenantDetails[0]['agent_name']?></th>
                         <th width="80px"style="text-align:left" >Contact No :</th><th><?php echo $TenantDetails[0]['agent_no']?></th>
                         </tr>
                         </table>
                         </td></tr>
                          <tr><td>
                         <table  width="100%"  >
                         <tr style="background-color:#bce8f1;font-size:14px;"  height="25">
    	<th colspan="8" align="center" style="text-align:center;">Tenant Occupying in the Flat </th>
        <th style="background-color:#FFF; width:50px;"></th>
      <th style="width:150px; text-align:center;" align="center">Document </th>

                            <tr height="25" align="left" style="background-color:#f9f9f9;">
                            <td  colspan="8"><table><tr>
                                <th  width="180" style="text-align:left" >Name</th>
                        		<th width="110" style="text-align:left" >Relation</th>
                                <th width="100"style="text-align:left" >Date Of Birth</th>
                                <th width="100"style="text-align:left" >Contact No</th>
                                <th width="100"style="text-align:left" >Email Address</th>
                                <th style="width:50px;">Send Communcation Emails ?</th>
                        </tr>
                       
                        <?php 
						 $mem_List=$TenantDetails[0]['Allmembers'];
						// $doc_List=$TenantDetails[0]['Alldocuments'];
						for($i=0;$i<sizeof($mem_List);$i++)
						{  //for($j=1;$j<=sizeof($mem_List);$j++)
							//{
							$member=$mem_List[$i]['mem_name'];
							$Relation=$mem_List[$i]['relation'];
							$MemberDob=getDisplayFormatDate($mem_List[$i]['mem_dob']);
							$number=$mem_List[$i]['contact_no'];
							$email=$mem_List[$i]['email'];
							?>
                            <tr align="left">
                             <td style="text-transform: capitalize;"><?php //echo $j?><?php echo $member?></td>
                            <td style="text-transform: capitalize;"><?php echo $Relation?></td>
                            <td><?php echo $MemberDob?></td>
                             <td><?php echo $number?></td>
                              <td><?php echo $email?></td>
                              <td style="text-align:center"> <input type="checkbox" name="mem_Send_commu_emails<?php echo $ii1;?>" id="mem_Send_commu_emails<?php echo $ii1;?>" value="1" <?php if($mem_List[$i]['send_commu_emails']==1){echo 'checked';}else{echo '';}?> <?php  echo 'disabled'; ?> <?php if($_SESSION['role'] == ROLE_ADMIN) { echo 'onclick="return false;"';} ?>></td>

                              </tr>
                             
						<?php }?>
                        </table></td> 
						 <td style="width:50px; background-color:#FFF"></td>
                         <td colspan="2"><table  width="150px">
						<?php $doc_List=$TenantDetails[0]['Alldocuments'];
						for($i=0;$i<sizeof($doc_List);$i++)
						{ 
							$docName=$doc_List[$i]['Name'];
							$doc_Link=$doc_List[$i]['Document'];
                            $doc_version=$doc_List[$i]['doc_version'];

                            $gdrive_id = $doc_List[$i]['attachment_gdrive_id'];
                            if($doc_version == "1")
                            {
                            $URL = "Uploaded_Documents/". $doc_Link;
                            }
                            else if($doc_version == "2")
                            {
                                if($gdrive_id == "" || $gdrive_id == "-")
                                {
                                       $URL = "Uploaded_Documents/". $doc_Link;
                                }
                                else
                                {
                                    $URL = "https://drive.google.com/file/d/". $gdrive_id."/view";
                                    $URL = "https://docs.google.com/viewer?srcid=".$gdrive_id ."&pid=explorer&efh=false&a=v&chrome=false&embedded=true";
                                }
                            }
                            $doc_id=$doc_List[$i]['doc_id'];
							?>
                            <tr align="center">
                            <td style="text-transform: capitalize; text-align:center;"><span style="text-align:center"><a onClick="OpenDocument('<?php echo $URL ?>')" target="_blank" style="cursor: pointer;text-decoration:none;"><?php echo $docName?></a></span></td>
                            <td><input style="color: red" onClick="delete_doc(this.id)" type="button" id="<?php echo $doc_id;?>"  name="<?php echo $doc_id;?>"  value="<?php echo "X";?>"></td>
                           </tr>
						<?php } //}?>
                        </table></td>
                       
                      <!--<table width="20%"> 
                        <tr height="25">
                         <th width="85" >Alternate no.</th></tr>
					<tr>  <td><?php //echo $TenantDetails[0]['alter_no']?></td></tr>
                       </table>
                    --></table></td></tr>
                   
                        <!--<?php 
						 //$doc_List=$TenantDetails[0]['Alldocuments'];
						//$j=20;
						//for($i=0;$i<sizeof($doc_List);$i++)
						//{ 
							//$docName=$doc_List[$i]['Name'];
							//$doc_Link=$doc_List[$i]['Document'];
							?>
                            <tr align="left">
                             <td style="text-transform: capitalize;"><a href="Uploaded_Documents/<?php// echo $doc_Link?> " target="_blank"><?php //echo $docName?></a></td>
                            
                           </tr>
						<?php //} //}?>
                    
                    </table>-->
                        </table><?php }?>
                        </td></tr>
                        <tr><td><br></td></tr>
                        
                  <?php if( $TenantDetails[0]['Count']==0 && $TenantDetails[0]['tenant_id']<>'')
	 			{?><tr>
			 <td colspan="6"> <span style="float: left;margin-left: 3px;"><a href="tenant_list.php?u_id=<?php echo $show_member_main[0]['unit']?>" target="_blank"><b> Number of Previous Lease Entries : <?php  echo $TenantDetails[0]['Count']?></b></a></span></td></tr>
             <?php 
			 }
			  else if( $TenantDetails[0]['Count']==0)
	 			{?><tr>
			 <td colspan="6"> <span style="float: left;margin-left: 3px;"><b> Number of Previous Lease Entries : <?php  echo $TenantDetails[0]['Count']?></b></span></td></tr>
             <?php 
			 }
			 else
			 {
				 //var_dump($TenantDetails);?>
          <tr><td  colspan="6"><span style="float: left;margin-left: 3px;"><a href="tenant_list.php?u_id=<?php echo $show_member_main[0]['unit']?>" target="_blank"><b> Number of Previous Lease Entries : <?php  echo $TenantDetails[0]['Count']?></b></a></span></td></tr>
             <?php }?>
             
    </table>
    
    
    
    
    
    
    
<tr><td><br/><br></td></tr>
<tr height="25" valign="bottom">
    <td colspan="6" style="font-weight: bold;text-align: center;"><i class="fa fa-car" style="font-size: 14px;">&nbsp;</i><b><u>VEHICLE DETAILS</u></b>&nbsp;<i class="fa fa-car" style="font-size: 14px;"></i>
       <?php if($_SESSION['is_year_freeze'] == 0 && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_EDIT_MEMBER] == '1' || $_SESSION['owner_id']==$_GET['id']))
	{?>
    <br/>
    <button type="button"  class="btn btn-primary btn-xs" onClick="window.location.href='mem_vehicle_new.php?prf&mkm&mem_id=<?php echo $_GET['id'];?>&tik_id=<?php echo time();?>'" style="float: right;">Add Vehicle<!--<i class="fa fa-plus fa-small">--></i></button>
    <!--<a href="mem_vehicle_new.php?prf&mkm&mem_id=<?php //echo $_GET['id'];?>&tik_id=<?php //echo time();?>" style="color:#00F; text-decoration:none;"><b>Add New</b></a></b>-->
    <?php }?>
    </td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#E8E8E8">
            <th width="120">Car Owner</th>
            <th width="150">Car Registration No.</th>
            <th width="85">Parking Slot No.</th>
            <th width="85">Parking Sticker No.</th>
            <th width="80">Parking Type</th>
            <th width="80">Car Make</th>
            <th width="80">Car Model</th>
            <th width="80">Car Colour</th>
            <?php
                if(isset($_GET['edt']))
                {
					if($_SESSION['role'] == ROLE_ADMIN || $_SESSION['role'] == ROLE_SUPER_ADMIN)
					{
                    ?>
                        <th width="50">Delete</th>
                    <?php
					}
                }
            ?>
        </tr>
        <?php
		if($show_mem_car_parking<>"")
        {
            $ii2 = 1;
        foreach($show_mem_car_parking as $k3 => $v3)
        {
        ?>       
            <input type="hidden" name="mem_car_parking_id<?php echo $ii2;?>" value="<?php echo $show_mem_car_parking[$k3]['mem_car_parking_id'];?>" />
            <tr height="25" bgcolor="#BDD8F4">
            <td align="center">
                <input type="text" name="car_owner<?php echo $ii2;?>" id="car_owner" value="<?php echo $show_mem_car_parking[$k3]['car_owner'];?>" style="width:150px;" class = "field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
                <input type="text" name="car_reg_no<?php echo $ii2;?>" id="car_reg_no" value="<?php echo $show_mem_car_parking[$k3]['car_reg_no'];?>" style="width:120px;" class = "field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
                <input type="text" name="parking_slot<?php echo $ii2;?>" id="parking_slot<?php echo $ii2;?>" value="<?php echo $show_mem_car_parking[$k3]['parking_slot'];?>" style="width:70px;" size="13" class = "field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
                <input type="text" name="parking_sticker<?php echo $ii2;?>" id="parking_sticker<?php echo $ii2;?>" value="<?php echo $show_mem_car_parking[$k3]['parking_sticker'];?>" style="width:70px;" size="13" class = "field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
             <td align="center">
             	
				<?php
				if(isset($_GET['edt']))
				{
				 	if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN)
					{
						
				?>
                		<select name="car_parking_type<?php echo $ii2;?>" id="parking_type<?php echo $ii2;?>" style="width:70px;" class="field_select">
				<?php
						echo $obj_view_member_profile->ComboboxWithDefaultSelect("Select `Id`,`ParkingType` from `parking_type` where Status = 'Y'",$show_mem_car_parking[$k3]['ParkingType']);
				?>
                		</select>    
                <?php
					} 
					else
					{ 
				?> 
                		<select name="car_parking_type<?php echo $ii2;?>" id="parking_type<?php echo $ii2;?>" style="width:70px;" class="field_select" disabled>
                <?php
						echo $obj_view_member_profile->ComboboxWithDefaultSelect("Select `Id`,`ParkingType` from `parking_type` where Status = 'Y'",$show_mem_car_parking[$k3]['ParkingType']); ?></select>
                <?php
					}
				}
				else
				{
					?> 
                		<select name="car_parking_type<?php echo $ii2;?>" id="parking_type<?php echo $ii2;?>" style="width:70px;" class="field_select" disabled>
                <?php
						echo $obj_view_member_profile->ComboboxWithDefaultSelect("Select `Id`,`ParkingType` from `parking_type` where Status = 'Y'",$show_mem_car_parking[$k3]['ParkingType']); ?> </select>
                <?php
				}
				?>
            </td>
            <td align="center">
                <input type="text" name="car_make<?php echo $ii2;?>" id="car_make" value="<?php echo $show_mem_car_parking[$k3]['car_make'];?>" style="width:80px;" class = "field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
                <input type="text" name="car_model<?php echo $ii2;?>" id="car_model" value="<?php echo $show_mem_car_parking[$k3]['car_model'];?>" style="width:80px;" class = "field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
                <input type="text" name="car_color<?php echo $ii2;?>" id="car_color" value="<?php echo $show_mem_car_parking[$k3]['car_color'];?>" style="width:80px;" class = "field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td>
                 <?php
                    if(isset($_GET['edt']))
                    {
						if($_SESSION['role'] == ROLE_ADMIN || $_SESSION['role'] == ROLE_SUPER_ADMIN)
						{
                        ?>
                            <input type="checkbox" name="car_delete<?php echo $ii2; ?>" id="car_delete<?php echo $ii2; ?>" value="1">
                        <?php
                    	}
					}
                ?>
            </td>
        </tr>
        <?php
            $ii2++;
        }
        }
        else
        {
            ?>
            <tr height="25"><td colspan="8" align="center"><font color="#FF0000"><b>No Records Found<!--  by admin --></b></font></td></tr>
            <?php   
        }
        ?>
        <input type="hidden" name="tot_car" value="<?php echo $ii2-1;?>" />
        </table>
    </td>
</tr>

<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#E8E8E8">
            <th width="120">Bike Owner</th>
            <th width="150">Bike Registration No.</th>
            <th width="85">Parking Slot No.</th>
            <th width="85">Parking Sticker No.</th>
            <th width="80">Parking Type</th>
            <th width="80">Bike Make</th>
            <th width="80">Bike Model</th>
            <th width="80">Bike Colour</th>
            <?php
                if(isset($_GET['edt']))
                {
					if($_SESSION['role'] == ROLE_ADMIN || $_SESSION['role'] == ROLE_SUPER_ADMIN)
					{
                    ?>
                        <th width="50">Delete</th>
                    <?php
					}
                }
            ?>
        </tr>    
        <?php
		if($show_mem_bike_parking<>"")
        {
            $ii3 = 1;
        foreach($show_mem_bike_parking as $k4 => $v4)
        {
        ?> 
        <input type="hidden" name="mem_bike_parking_id<?php echo $ii3;?>" value="<?php echo $show_mem_bike_parking[$k4]['mem_bike_parking_id'];?>" />      
        <tr height="25" bgcolor="#BDD8F4">
           	<td align="center">
                <input type="text" name="bike_owner<?php echo $ii3;?>" id="bike_owner" value="<?php echo $show_mem_bike_parking[$k4]['bike_owner'];?>" style="width:150px;" class="field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
                <input type="text" name="bike_reg_no<?php echo $ii3;?>" id="bike_reg_no" value="<?php echo $show_mem_bike_parking[$k4]['bike_reg_no'];?>" style="width:120px;" class="field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
                <input type="text" name="bike_parking_slot<?php echo $ii3;?>" id="bike_parking_slot" value="<?php echo $show_mem_bike_parking[$k4]['parking_slot'];?>" style="width:70px;" size="13" class="field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
                <input type="text" name="bike_parking_sticker<?php echo $ii3;?>" id="bike_parking_sticker<?php echo $ii3;?>" value="<?php echo $show_mem_bike_parking[$k4]['parking_sticker'];?>" style="width:70px;" size="13" class = "field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
 	<?php
              if(isset($_GET['edt']))
				{
				 	if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN)
					{
						
				?>
                		<select name="bike_parking_type<?php echo $ii3;?>" id="parking_type<?php echo $ii3;?>" style="width:70px;" class="field_select">
				<?php
						echo $obj_view_member_profile->ComboboxWithDefaultSelect("Select `Id`,`ParkingType` from `parking_type` where Status = 'Y'",$show_mem_bike_parking[$k4]['ParkingType']);
				?>
                		</select>    
                <?php
					} 
					else
					{ 
				?> 
                		<select name="bike_parking_type<?php echo $ii3;?>" id="parking_type<?php echo $ii3;?>" style="width:70px;" class="field_select" disabled>
                <?php
						echo $obj_view_member_profile->ComboboxWithDefaultSelect("Select `Id`,`ParkingType` from `parking_type` where Status = 'Y'",$show_mem_bike_parking[$k4]['ParkingType']); ?></select>
                <?php
					}
				}
				else
				{
					?> 
                		<select name="bike_parking_type<?php echo $ii3;?>" id="parking_type<?php echo $ii3;?>" style="width:70px;" class="field_select" disabled>
                <?php
						echo $obj_view_member_profile->ComboboxWithDefaultSelect("Select `Id`,`ParkingType` from `parking_type` where Status = 'Y'",$show_mem_bike_parking[$k4]['ParkingType']); ?> </select>
                <?php
				}
				?>
            </td>
            <td align="center">
            <input type="text" name="bike_make<?php echo $ii3;?>" id="bike_make" value="<?php echo $show_mem_bike_parking[$k4]['bike_make'];?>" style="width:80px;" class="field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
            <input type="text" name="bike_model<?php echo $ii3;?>" id="bike_model" value="<?php echo $show_mem_bike_parking[$k4]['bike_model'];?>" style="width:80px;" class="field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
            <input type="text" name="bike_color<?php echo $ii3;?>" id="bike_color" value="<?php echo $show_mem_bike_parking[$k4]['bike_color'];?>" style="width:80px;" class="field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td>
                 <?php
                    if(isset($_GET['edt']))
                    {
						if($_SESSION['role'] == ROLE_ADMIN || $_SESSION['role'] == ROLE_SUPER_ADMIN)
						{
                        ?>
                            <input type="checkbox" name="bike_delete<?php echo $ii3; ?>" id="bike_delete<?php echo $ii3; ?>" value="1">
                        <?php
						}
                    }
                ?>
            </td>
        </tr>
        <?php
            $ii3++;
        }
        }
        else
        {
            ?>
            <tr height="25"><td colspan="8" align="center"><font color="#FF0000"><b>No Records Found<!--  by admin --></b></font></td></tr>
            <?php   
        }
        ?>
        <input type="hidden" name="tot_bike" value="<?php echo $ii3-1;?>" />
        </table>
    </td>
</tr>
<tr><td><br><br></td></tr>
<tr height="25" valign="bottom">
    <td colspan="6" style="font-weight: bold;text-align: center;"><i class="fas fa-hand-holding-usd" style="font-size: 14px;">&nbsp;</i><b><u>LIEN  / MORTGAGE DETAILS</u></b>&nbsp;<i class="fas fa-hand-holding-usd" style="font-size: 14px;"></i>
       <?php if( $_SESSION['profile'][PROFILE_MANAGE_LIEN] == 1)
		{
		?>
    		<br/>
    <button type="button"  class="btn btn-primary btn-xs" onClick="window.location.href='addLien.php?unit_id=<?php echo $show_member_main[0]['unit'];?>'" style="float: right;">Add Lien<!--<i class="fa fa-plus fa-small">--></i></button>
    <?php 
	}
	?>
    </td>
</tr>
<tr>
	<td colspan="6">
    	<table border="0">
        <tr height="30" bgcolor="#E8E8E8">
            <th width="150">Bank Name</th>
            <th width="150">Loan Amount</th>
            <th width="150">Society NOC Date</th>
            <th width="150">Loan Status</th>
            <th width="150">Close Date</th>
            
        </tr>
        <?php
		if($show_mem_lien<>"")
        {
            $j = 1;
        	foreach($show_mem_lien as $k5 => $v5)
        	{
        ?> 
        <input type="hidden" name="lienId<?php echo $j;?>" id="lienId<?php echo $j;?>" value="<?php echo $show_mem_lien[$k5]['Id'];?>" />      
       <tr height="25" bgcolor="#BDD8F4">
        	<td align="center">
                <input type="text" name="bankName<?php echo $j;?>" id="bankName<?php echo $j;?>" value="<?php echo $show_mem_lien[$k5]['BankName'];?>" style="width:150px;" class="field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
                <input type="text" name="amount<?php echo $j;?>" id="amount<?php echo $j;?>" value="<?php echo $show_mem_lien[$k5]['Amount'];?>" style="width:120px;" class="field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
                <input type="text" name="societyNOCDate<?php echo $j;?>" id="societyNOCDate<?php echo $j;?>" value="<?php echo getDisplayFormatDate($show_mem_lien[$k5]['SocietyNOCDate']);?>" style="width:70px;" size="13" class="field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
                <input type="text" name="lienStatus<?php echo $ii3;?>" id="lienStatus<?php echo $j;?>" value="<?php echo $show_mem_lien[$k5]['LienStatus'];?>" style="width:70px;" size="13" class = "field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
            <td align="center">
            <input type="text" name="closeDate<?php echo $j;?>" id="closeDate<?php echo $j;?>" value="<?php echo getDisplayFormatDate($show_mem_lien[$k5]['CloseDate']);?>" style="width:80px;" class="field_input" <?php if($_SESSION['role'] != ROLE_SUPER_ADMIN && $_SESSION['role'] != ROLE_ADMIN) { echo 'readonly';} ?> />
            </td>
           </tr>
          <?php
            $j++;
        	}
        }
		else
        {
            ?>
            <tr height="25"><td colspan="8" align="center"><font color="#FF0000"><b>No Records Found<!--  by admin --></b></font></td></tr>
            <?php   
        }
        ?>
        <input type="hidden" name="tot_lien" value="<?php echo $j-1;?>" />
     	</table>
     </td>
</table>
<table class="table_format" style="width: 100%;">
<tr height="25" valign="bottom">
    <td colspan="6" style="font-weight: bold;text-align: center;">
        <br/><i class="fa fa-medkit" style="font-size: 14px;"></i>&nbsp;<u><b>EMERGENCY CONTACT DETAILS</b></u>&nbsp;<i class="fa fa-medkit" style="font-size: 14px;">&nbsp;</i>
    </td>
</tr>
<tr height="25" valign="center" align="left">
    <td style="text-align: left;" width="100px"><b>Relatives Name</b></td>
    <td>:</td>
    <td colspan="5" align="left" style="text-align: left;">
    <input type="text" name="eme_rel_name" id="eme_rel_name" class="field_input" value="<?php echo $show_member_main[0]['eme_rel_name'];?>" style="width:180px;"/>
    </td>
</tr>

<tr align="left">
    <td style="text-align: left;"><b>Contact No.</b></td>
    <td>:</td>
    <td style="text-align: left;">
    <input type="text" name="eme_contact_1" id="eme_contact_1" class="field_input" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $show_member_main[0]['eme_contact_1'];?>" style="width:120px;"/>
    </td>
    
    <td width="120px"><b>Alternate Contact No.</b></td>
    <td>:</td>
    <td style="text-align: left;">
    <input type="text" name="eme_contact_2" id="eme_contact_2" class="field_input" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" value="<?php echo $show_member_main[0]['eme_contact_2'];?>" style="width:120px;"/>
    </td>
</tr>
</table>
<br>
<br>
<?php if($_SESSION['is_year_freeze'] == 0 &&($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['profile'][PROFILE_EDIT_MEMBER] == '1' ))
{?>
		<center>
            <?php
                if(!isset($_GET['edt']))
                {  
                    ?>
                        <input type="button"  class="btn btn-primary"  value="Edit Profile"  id="Edit" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;"onClick="window.location.href='view_member_profile.php?edt&prf&mkm&tik_id=<?php echo time();?>&id=<?php echo $_GET['id'];?>'">
                    <?php
                }
                else
                {
                    ?>
                        <input type="submit"  class="btn btn-primary"  value="Update Profile"  id="insert" name="update" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal; background-color: #337ab7;color: #fff; border-color: #2e6da4;">
                        <input type="button"  class="btn btn-primary"  value="Cancel" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;"onClick="window.location.href='view_member_profile.php?prf&mkm&tik_id=<?php echo time();?>&id=<?php echo $_GET['id'];?>'">
                    <?php
                }
            ?>
        </center>
<?php } ?>
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
