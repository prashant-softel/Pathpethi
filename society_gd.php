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

include_once("classes/society.class.php");
$obj_society = new society($m_dbConn, $m_dbConnRoot);
$res = $m_dbConn->select("select GDrive_W2S_ID from society");
$GDriveID = $res[0]["GDrive_W2S_ID"];
if($GDriveID == "")
{
	$GDriveStatus= "0";
}
else
{
	$GDriveStatus = "1";
} 
/*function randomPassword() 
{
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789*^%$#@!+=";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) 
	{
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}*/
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jssociety.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <link href="css/popup.css" rel="stylesheet" type="text/css" />
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
	</script>
    
   <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
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
	}
    
    <script type="text/javascript" src="js/ajax.js"></script>
    
    
</head>

<?php if((isset($_POST['ShowData']) && $_POST['ShowData']<> '')  || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else if(isset($_REQUEST['edt']) || $_REQUEST['insert']=='Edit'){ ?>
<body onLoad="getsociety('edit-<?php echo $_SESSION['society_id'];?>');">
<?php }else if(isset($_REQUEST['show'])){ ?>
<body onLoad="getsociety('show-<?php echo $_REQUEST['id'];?>');">
<body>
<?php } ?>

<div id="middle">
<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">Society Master</div>
<br>
<center>
<!--<a href="wing.php?imp&ssid=<?php echo $_SESSION['society_id'];?>&s&idd=<?php echo time();?>"><b><input type="button" value="Add Wing"></b></a>-->
<button type="button" class="btn btn-primary" onClick="window.location.href='wing.php?imp&ssid=<?php echo $_SESSION['society_id'];?>&s&idd=<?php echo time();?>'">Add Wing</button>
</center>

<?php $val = 'onSubmit="return val();"';
?>

<center>
<form name="frmGDriveLink" id="frmGDriveLink" method="post" action="linkGDrive.php">

</form>	
<form name="society" id="society" method="post" action="process/society.process.php" onSubmit="return val();">
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
    	<th style="width:100%;">Basic Info</th>
        <!--<th style="width:1%; background-color:#FFF;"></th>-->
       <!-- <th style="width:40%;">Billing Information</th></tr>-->
        <tr><td><br></td></tr>
         <tr>
         
        	<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Enter Society Code</span><span  style="margin-left: 55px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="society_code" id="society_code" class="field_input" value="<?php echo $_REQUEST['society_code'];?>" /></span></td>
		</tr>
        <tr >
        	<td valign="left"><span style="margin-left: 4%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Enter Society Name</span><span  style="margin-left: 52px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><div style=" width: 50%;float: right;margin-right: 4%;"><input type="text" name="society_name" id="society_name"  class="field_input" value="<?php echo $_REQUEST['society_name'];?>"/></div></span></td>
		</tr>
        <tr >
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Circle</span><span  style="margin-left: 128px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="circle" id="circle"  class="field_input"  value="<?php echo $_REQUEST['circle'];?>"/></span></td>
		</tr>
	<tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Society Registration Date</span><span  style="margin-left: 22px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="registration_date" id="registration_date"  class="basics field_date" size="10" readonly  style="width:80px;" value="<?php echo $_REQUEST['registration_date'];?>"/></span></td>
		</tr>
        <tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Society Registration No.</span><span  style="margin-left: 29px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><div style=" width: 50%;float: right;margin-right: 4%;"><input type="text" name="registration_no" id="registration_no" class="field_input" value="<?php echo $_REQUEST['registration_no'];?>"/></span></div></td>
		</tr>
        <tr >
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Society Address</span><span  style="margin-left: 72px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><div style=" width: 50%;float: right;margin-right: 4%;"><textarea name="society_add" id="society_add" class="field_input" rows="6" cols="32"  value="<?php echo $_REQUEST['society_add'];?>"></textarea></div></span></td>
		</tr>
        <tr >
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Address as Email Footer</span><span  style="margin-left: 27px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="checkbox" name="show_address_in_email" id="show_address_in_email"   value="1"></span></td>
		</tr>
        <tr><td><br></td></tr>
        <tr >
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">City</span><span  style="margin-left: 139px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="city" id="city" class="field_input"  value="<?php echo $_REQUEST['city'];?>"></span></td>
		</tr>
        <tr >
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">landmark</span><span  style="margin-left: 109px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="landmark" id="landmark"  class="field_input"  value="<?php echo $_REQUEST['landmark'];?>"/></span></td>
		</tr>
        
        <tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">State</span><span  style="margin-left: 132px;">&nbsp; : &nbsp;</span>
            	<span  style="margin-left: 4%;"><select name="state_id" id="state_id" style="width:200px;" class="field_select" value="<?php echo $_REQUEST['state_id'];?>">
                <?php echo $combo_state = $obj_society->combobox("select state_id,state from state_master where status='Y'",'15',true); ?>
                </select></span>
            </td>
		</tr>
        
		<tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Region</span><span  style="margin-left: 120px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="region" id="region"  class="field_input"  value="<?php echo $_REQUEST['region'];?>"/></span></td>
		</tr>
        
		<tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Postal Code</span><span  style="margin-left: 93px;">&nbsp; : &nbsp;
            </span>
			<span  style="margin-left: 4%;"><input type="text" name="postal_code" id="postal_code"  class="field_input" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" size="6"  value="<?php echo $_REQUEST['postal_code'];?>"/></span></td>
		</tr>
        
		<tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Country</span><span  style="margin-left: 117px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="country" id="country" class="field_input" value="India" /></span></td>
		</tr>
        
        <tr>
        <td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Landline No.</span><span  style="margin-left: 90px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="phone_code" id="phone_code"  class="field_input" onBlur="extractNumber(this,0,false);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true);" maxlength="5" style="width:40px;" value="0222"/> - <input type="text" name="phone" id="phone" class="field_input" onBlur="extractNumber(this,0,false);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true);" maxlength="12" style="width:100px;"/></span></td>
		</tr>
        
        <tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Mobile No.</span><span  style="margin-left: 101px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="phone2" id="phone2" class="field_input" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)"  value="<?php echo $_REQUEST['phone2'];?>"/></span></td>
		</tr>
        
        <tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Fax No.</span><span  style="margin-left: 118px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="fax_number" id="fax_number" class="field_input"  value="<?php echo $_REQUEST['fax_number'];?>"/></span></td>
		</tr>
		<tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">PAN Card No.</span><span  style="margin-left: 84px;">&nbsp; : &nbsp;
            </span>
			<span  style="margin-left: 4%;"><input type="text" name="pan_no" id="pan_no"  class="field_input" value="<?php echo $_REQUEST['pan_no'];?>" /></span></td>
		</tr>
        
		<tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">TAN Card No.</span><span  style="margin-left: 85px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="tan_no" id="tan_no" class="field_input" value="<?php echo $_REQUEST['tan_no'];?>"/> </span></td>
		</tr>
        <tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Service Tax No</span><span  style="margin-left: 79px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="service_tax_no" id="service_tax_no" class="field_input"  value="<?php echo $_REQUEST['service_tax_no'];?>"/></span></td>
		</tr>
        <tr >
        <td valign="left"><span style="margin-left: 4%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Email id </span><span  style="margin-left: 113px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="email" id="email" class="field_input" value="<?php echo $_REQUEST['email'];?>"/></span></td>
		</tr>
		<tr >
        	<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 3%;">Email id [CC]</span><span  style="margin-left: 89px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="cc_email" id="cc_email" class="field_input"  value="<?php echo $_REQUEST['cc_email'];?>"/></span></td>
		</tr>
        <tr>
        	<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 3%;">Contact Number<br></span><span style="margin-left:8%;"> (for email notifcation)</span><span  style="margin-left: 45px;">&nbsp; : &nbsp;</span>
			<span  style="margin-left: 4%;"><input type="text" name="email_contactno" id="email_contactno" class="field_input"  value="<?php echo $_REQUEST['email_contactno'];?>"/></span></td>
		</tr>
        <tr>
        	<td valign="left"><span style="margin-left: 4%;">&nbsp;</span><span  style="margin-left: 3%;">Website Url</span><span  style="margin-left: 98px;">&nbsp; : &nbsp;</span>
		<span  style="margin-left: 4%;"><input type="text" name="url" id="url" class="field_input" value="<?php echo $_REQUEST['url'];?>" /></span></td>
		</tr>
        <tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Society formed</span><span  style="margin-left: 80px;">&nbsp; : &nbsp;
            </span>
			<span  style="margin-left: 4%;"><input type="text" name="member_since" id="member_since"  class="basics field_date" size="10" readonly  style="width:80px;" value="<?php echo $_REQUEST['member_since'];?>"/></span></td>
		</tr>
		<tr>
        	<td valign="left"><span style="margin-left: 4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Google Drive Account </span><span  style="margin-left: 80px;">: &nbsp;
            </span>
			<span  style="margin-left: 4%;"><?php if( $GDriveStatus == "0"){echo "Not Linked <input type='button' value='Link Now' onClick='LinkGDrive()'/>";}else{ echo "Linked";}?></span></td>
		</tr>

         </table>
         
         
        <table width="50%" style="font-size:12px; float:left" id="PrintableTable">
        <tr style="background-color:#bce8f1;font-size:14px;" height="25">
        <th style="width:100%;">Billing Information</th></tr>
         <tr><td><br></td></tr>
        <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Billing Cycle</span><span style="margin-left:91px;">&nbsp; : &nbsp;</span>
			<span style="margin-left:4%;"><select name="bill_cycle" id="bill_cycle" class="field_select" value="<?php echo $_REQUEST['bill_cycle'];?>">
					<?php echo $combo_bill_cycle = $obj_society->combobox("select `ID`,`Description` from `billing_cycle_master`",0,true); ?>
                    <?php// print_r($combo_bill_cycle);?>
				</select></span>
            </td>
		</tr>
        <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Interest Rate</span><span style="margin-left:90px;">&nbsp; : &nbsp;</span>
			<span style="margin-left:4%;"><input type="text" name="int_rate" id="int_rate" class="field_input" value="<?php echo $_REQUEST['int_rate'];?>" /></span></td>
		</tr>
         <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Interest Method </span><span style="margin-left:72px;">&nbsp; : &nbsp;
            </span>
		<span style="margin-left:4%;">	<select name="int_method" id="int_method" class="field_select" value="<?php echo $_REQUEST['int_method'];?>">
            		<OPTION VALUE="<?php echo INTEREST_METHOD_DELAY_DUE; ?>">Delay After Due Days</OPTION>
                    <OPTION VALUE="<?php echo INTEREST_METHOD_FULL_MONTH; ?>" >Full Month</OPTION>
                </select> 
                
                 <button type="button"  style="border-radius:50px; width:15px; color:#009; vertical-align:middle;"  class="popup"  onMouseOver="togglePopup('demo2_tip');" onMouseOut="togglePopup('demo2_tip');"><i class="fa   fa-info-circle "  ></i>
                <div id="demo2_tip" class="popuptext" style="text-align:left; width:21vw;">
                      <dl style="margin-left:10px;">
                          <dt>If payment is not made by due date,</dt>
                          <dt><br/></dt> 
                          <dt>1. Delay After Due Days</dt>
                          <dd>- Interest will be calculated on number of days after due date.</dd>
                          <dt><br/></dt> 
                          <dt>2. Full Month</dt>
                          <dd>- Interest will be calculated from first day of the month.</dd>
                    </dl>
                </div>
             </button>
             </span>
            </td>
		</tr>
        <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;"><!--Rebate Method--> </span><span style="margin-left:160px;">&nbsp; &nbsp;</span>
			<span style="margin-left:4%;"><input type="hidden" name="int_tri_amt" id="int_tri_amt" class="field_input" value="<?php echo $_REQUEST['int_tri_amt'];?>" ></span></td>
		</tr>
        
          <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Rebate Method </span><span style="margin-left:72px;">&nbsp; : &nbsp;
            </span>
			<span style="margin-left:4%;"><select name="rebate_method" id="rebate_method" class="field_select" value="<?php echo $_REQUEST['rebate_method'];?>" >
            		<OPTION VALUE="<?php echo REBATE_METHOD_NONE; ?>">None</OPTION>
                    <OPTION VALUE="<?php echo REBATE_METHOD_FLAT; ?>">Flat Amount</OPTION>
                    <!--<OPTION VALUE="<?php //echo REBATE_DUE_WAIVER; ?>">Due Amount Waiver</OPTION>-->
               </select>
               </span>
            </td>
		</tr>
        
         <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Rebate Amount </span><span style="margin-left:71px;">&nbsp; : &nbsp;
            </span>
			<span style="margin-left:4%;"><input type="text" name="rebate" id="rebate" class="field_input"  value="<?php echo $_REQUEST['rebate'];?>"/></span></td>
		</tr>
        <tr>
        	<td valign="left"><span style="margin-left:4%;">&nbsp;</span><span  style="margin-left: 3%;">Cheque Bounce Charges</span><span style="margin-left:23px;">&nbsp; : &nbsp;</span>
            <span style="margin-left:4%;"><input type="text" name="chq_bounce_charge" id="chq_bounce_charge" class="field_input" value="<?php echo $_REQUEST['chq_bounce_charge'];?>"/></span></td>
		</tr>
        
        <tr >
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Show In Bill </span><span style="margin-left:92px;">&nbsp; : &nbsp;</span>
			<span style="margin-left:4%;"><label><input type="checkbox" name="show_wing" id="wing" value="1" />&nbsp;&nbsp;Wing</label></span>
              <span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">  </span><span >&nbsp;  &nbsp;</span>
			 <span style="margin-left:8%;"><label><input type="checkbox" name="show_floor" id="show_floor" value="1" />&nbsp;&nbsp;Floor</label></span>
            </td>
		</tr>
        
        <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;"> </span><span style="margin-left:163px;">&nbsp;  &nbsp;</span>
             <span style="margin-left:4%;"><label><input type="checkbox" name="show_area" id="area" value="1" />&nbsp;&nbsp;Area</label></span>
               <span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">  </span><span >&nbsp;  &nbsp;</span>
		    <span style="margin-left:8%;"><label><input type="checkbox" name="show_parking" id="parking" value="1" />&nbsp;&nbsp;Parking</label></span></td>
		</tr>
		<tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?> &nbsp;</span><span  style="margin-left: 3%;"> </span><span style="margin-left:163px;">&nbsp;  &nbsp;</span>
			 <span style="margin-left:4%;"><label><input type="checkbox" name="show_share" id="share" value="1" />&nbsp;&nbsp;Share Certificate No</label></span>
               <span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 0%;">  </span><span >&nbsp;  &nbsp;</span>
             <span style="margin-left:-6%;"><label><input type="checkbox" name="bill_due_date" id="bill_due_date" value="1" />&nbsp;&nbsp;Due Date</label></span>
             </td>
		</tr>
        <tr><td><br></td></tr>
        <tr>
        	<td valign="left">
        		<span style="margin-left:4%;">&nbsp;</span>
        		<span  style="margin-left: 3%;">GSTIN Number </span>
        		<span style="margin-left:70px;">&nbsp; : &nbsp;</span>
				<span style="margin-left:4%;">
						<input type="text" name="gstin_no" id="gstin_no" class="field_input" value="<?php echo $_REQUEST['gstin_no'];?>" />&nbsp;&nbsp;
					
				</span>
			</td>
		</tr>
		<tr>
        	<td valign="left">
        		<span style="margin-left:4%;">&nbsp;</span>
        		<span  style="margin-left: 3%;">Apply GST</span>
        		<span style="margin-left:95px;">&nbsp; : &nbsp;</span>
				<span style="margin-left:4%;">
						<input type="checkbox" name="apply_service_tax" id="apply_service_tax" value="1" />&nbsp;&nbsp;
				</span>
			</td>
		</tr>
        <tr>
        	<td valign="left">
        		<span style="margin-left:4%;">&nbsp;</span>
        		<span  style="margin-left: 3%;vertical-align: middle;">GST Start Date</span>
        		<span style="margin-left:78px;">: &nbsp;</span>
				<span style="margin-left:4%;">
						<input type="text"  class="basics field_date" size="10" readonly  style="width:80px;" name="gst_start_date" id="gst_start_date" value="<?php echo $_REQUEST['gst_start_date'];?>" />&nbsp;&nbsp;
				</span>
			</td>
		</tr>
        <tr>
        	<td valign="left">
        		<span style="margin-left:4%;">&nbsp;</span>
        		<span  style="margin-left: 3%;">Apply GST on Interest</span>
        		<span style="margin-left:35px;">&nbsp; : &nbsp;</span>
				<span style="margin-left:4%;">
						<input type="checkbox" name="apply_GST_On_Interest" id="apply_GST_On_Interest" value="1" />&nbsp;&nbsp;
				</span>
			</td>
		</tr>
        <tr>
        	<td valign="left">
        		<span style="margin-left:4%;">&nbsp;</span>
        		<span  style="margin-left: 3%;">Apply GST Above Threshold</span>
        		<span style="margin-left:1px;">&nbsp; : &nbsp;</span>
				<span style="margin-left:4%;">
						<input type="checkbox" name="apply_GST_above_Threshold" id="apply_GST_above_Threshold" value="1" />&nbsp;&nbsp;
				</span>
			</td>
		</tr>
        <tr><td><br></td></tr>
		<tr>
        	<td valign="left">
        		<span style="margin-left:4%;">&nbsp;</span>
        		<span  style="margin-left: 3%;">GST Threshold (Rs.)</span>
        		<span style="margin-left:41px;">&nbsp; : &nbsp;</span>
				<span style="margin-left:4%;">
                
					<input type="text" name="service_tax_threshold" id="service_tax_threshold" class="field_input" value="<?php echo $_REQUEST['service_tax_threshold'];?>" />&nbsp;&nbsp;
                    
				</span>
			</td>
		</tr>
		<tr>
        	<td valign="left">
        		<span style="margin-left:4%;">&nbsp;</span>
        		<span  style="margin-left: 3%;">IGST Rate (%)</span>
        		<span style="margin-left:74px;">&nbsp; : &nbsp;</span>
				<span style="margin-left:4%;">
						<input type="text" name="igst_tax_rate" id="igst_tax_rate" class="field_input" value="<?php echo $_REQUEST['igst_tax_rate'];?>" />&nbsp;&nbsp;
				</span>
			</td>
		</tr>
        <td valign="left">
        		<span style="margin-left:4%;">&nbsp;</span>
        		<span  style="margin-left: 3%;">CGST Rate (%)</span>
        		<span style="margin-left:69px;">&nbsp; : &nbsp;</span>
				<span style="margin-left:4%;">
						<input type="text" name="cgst_tax_rate" id="cgst_tax_rate" class="field_input" value="<?php echo $_REQUEST['cgst_tax_rate'];?>" />&nbsp;&nbsp;
				</span>
			</td>
		</tr>
        <td valign="left">
        		<span style="margin-left:4%;">&nbsp;</span>
        		<span  style="margin-left: 3%;">SGST Rate (%)</span>
        		<span style="margin-left:71px;">&nbsp; : &nbsp;</span>
				<span style="margin-left:4%;">
						<input type="text" name="sgst_tax_rate" id="sgst_tax_rate" class="field_input" value="<?php echo $_REQUEST['sgst_tax_rate'];?>" />&nbsp;&nbsp;
				</span>
			</td>
		</tr>
		<td valign="left">
        		<span style="margin-left:4%;">&nbsp;</span>
        		<span  style="margin-left: 3%;">CESS Rate (%)</span>
        		<span style="margin-left:71px;">&nbsp; : &nbsp;</span>
				<span style="margin-left:4%;">
						<input type="text" name="cess_tax_rate" id="cess_tax_rate" class="field_input" value="<?php echo $_REQUEST['cess_tax_rate'];?>" />&nbsp;&nbsp;
				</span>
			</td>
		</tr>


        <tr><td><br></td></tr>
        <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php echo $star;?></span><span  style="margin-left: 2%;">Unit Presentation </span><span style="margin-left:68px;">&nbsp; : &nbsp;
            </span>
            	 <span style="margin-left:4%;"><select name="unit_presentation" id="unit_presentation" class="field_select" >
					<?php echo $unit_type = $obj_society->combobox("select `id`,`Description` from `unit_type`",$_REQUEST['unit_presentation'],false); ?>
                </select>
                </span>
            </td>
		</tr>
         <tr  style="visibility:hidden">
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;"></span><span style="margin-left:57px;">&nbsp;  &nbsp;</span>
			 <span style="margin-left:24%0px;"><label><input type="checkbox" name="calc_int" id="calc_int" value="1" />&nbsp;&nbsp;Apply Interest</label></span></td>
		</tr>
        <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Bill Format Method</span><span style="margin-left:62px;">&nbsp; : &nbsp;</span>
           <span style="margin-left:4%;"> <select name="bill_method" id="bill_method" class="field_select" value="<?php echo $_REQUEST['bill_method'];?>">
            		<OPTION VALUE="<?php echo BILL_FORMAT_WITH_RECEIPT; ?>">Bill With Receipt</OPTION>
                    <OPTION VALUE="<?php echo BILL_FORMAT_WITHOUT_RECEIPT; ?>">Bill Without Receipt</OPTION>
                </select>
                </span>
            </td>
		</tr>
        
         <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Property Tax No.</span><span style="margin-left:73px;">&nbsp; : &nbsp;
            </span>
             <span style="margin-left:4%;">  <input type="text" name="property_tax_no" id="property_tax_no" class="field_input" value="<?php echo $_REQUEST['property_tax_no'];?>"/></span></td>
		</tr>
        <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Water Tax No</span><span style="margin-left:90px;">&nbsp; : &nbsp;</span>
			<span style="margin-left:4%;"><input type="text" name="water_tax_no" id="water_tax_no" class="field_input"  value="<?php echo $_REQUEST['water_tax_no'];?>"/></span></td>
		</tr>
        
        
         <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Bill Footer</span><span style="margin-left:108px;">&nbsp; : &nbsp;</span>
            
            <div  id="bill_footer_view" style=" width: 50%;float: right;margin-right: 4%;"></div></td>
        </tr>
        <tr id="bill_footer_edit">
        	<td style="padding-left:6%;">
            <span style="margin-left:3%">
			&nbsp;&nbsp;&nbsp;<textarea name="bill_footer" id="bill_footer" class="field_input"><?php echo $_REQUEST['bill_footer'];?></textarea>
            <script>
				CKEDITOR.config.height = 50;
				CKEDITOR.config.width = 425;
				CKEDITOR.replace('bill_footer', {toolbar: [
									{ name: 'clipboard', items: ['Undo', 'Redo']},
									{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
									 ]});
			</script>
            </span>
            </td>
		</tr>
        <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">SMS Start Text</span><span style="margin-left:90px;">&nbsp; : &nbsp;</span>
			<span style="margin-left:4%;"><input type="text" name="sms_start_text" id="sms_start_text" class="field_input" /></span></td>
		</tr>
         <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">SMS End Text</span><span style="margin-left:94px;">&nbsp; : &nbsp;</span>
			<span style="margin-left:4%;"><input type="text" name="sms_end_text" id="sms_end_text" class="field_input"/></span></td>
		</tr>
        <tr><td><br></td></tr>
         <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Send Reminder</span><span style="margin-left:86px;">&nbsp; : &nbsp;</span>
			<span style="margin-left:4%;"><label><input type="checkbox" name="send_reminder" id="send_reminder" value="1" />&nbsp;&nbsp;</label>
            <button type="button" style="border-radius:50px; width:15px; color:#009; vertical-align:middle;"  class="popup"  onMouseOver="togglePopup('demo5_tip');" onMouseOut="togglePopup('demo5_tip');"><i class="fa   fa-info-circle "  ></i>
                <div id="demo5_tip" class="popuptext" style="text-align:left; width:21vw;">
                      <dl style="margin-left:10px;">
                          <dt>[Send Reminder SMS]</dt>
                     </dl>
                </div>
             </button></span></td>
		</tr>
        
         <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Send Bill As Link</span><span style="margin-left:80px;">&nbsp; : &nbsp;</span>
			<span style="margin-left:4%;"><label><input type="checkbox" name="bill_as_link" id="bill_as_link" value="1" />&nbsp;&nbsp;</label>
            <button type="button" style="border-radius:50px; width:15px; color:#009; vertical-align:middle;"  class="popup"  onMouseOver="togglePopup('demo3_tip');" onMouseOut="togglePopup('demo3_tip');"><i class="fa   fa-info-circle "  ></i>
                <div id="demo3_tip" class="popuptext" style="text-align:left; width:21vw;">
                      <dl style="margin-left:10px;">
                          <dt>[Uncheck to send bill as attachment]</dt>
                     </dl>
                </div>
             </button></span></td>
		</tr>
        
          <tr>
        	<td valign="left"><span style="margin-left:4%;"><?php //echo $star;?>&nbsp;</span><span  style="margin-left: 3%;">Send Payment Received<br></span><span style="margin-left:8%;">Notification To Members </span><span style="margin-left: 7%;">&nbsp; : &nbsp;</span>
			<span style="margin-left:4%;"><label><input type="checkbox" name="neft_notify_by_email" id="neft_notify_by_email" value="1" />&nbsp;&nbsp;</label>
            <button type="button" style="border-radius:50px; width:15px; color:#009; vertical-align:middle;"  class="popup"  onMouseOver="togglePopup('demo4_tip');" onMouseOut="togglePopup('demo4_tip');"><i class="fa   fa-info-circle "  ></i>
                <div id="demo4_tip" class="popuptext" style="text-align:left; width:21vw;">
                      <dl style="margin-left:10px;">
                          <dt>[Uncheck to disbale  neft notification on email to members feature]</dt>
                     </dl>
                </div>
             </button>
             </span>
            </td>
		</tr>
        </table>
        <table width="100%"> 
         <tr><td colspan="4">&nbsp;</td></tr>
        <tr>
			<td colspan="4" align="center">
            <input type="hidden" name="login_id" id="login_id">
            <input type="hidden" name="id" id="id">
             <input type="hidden" name="unit_presentation_previous_value" id="unit_presentation_previous_value">
            <input type="submit" name="insert" id="insert" value="Submit"  class="btn btn-primary"  style="padding: 6px 12px; color:#fff;background-color: #2e6da4;">
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

</div>
<br><br>

</center>
</div>
</div>

<?php include_once "includes/foot.php"; ?>
