<?php
include_once("includes/head_s.php");
include_once ("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/tenant.class.php");
include_once("classes/mem_other_family.class.php");
//nclude_once("classes/mem_other_family.class.php");
//$obj_mem_other_family = new mem_other_family($m_dbConn);
$obj_tenant = new tenant($m_dbConn);
$obj_mem_other_family = new mem_other_family($m_dbConn);

$unit_details = $obj_mem_other_family->unit_details($_REQUEST['mem_id']);
$society_dets = $obj_mem_other_family->get_society_details($_SESSION['society_id']);
$UnitBlock = $_SESSION["unit_blocked"];
//print_r($unit_details);

if(isset($_REQUEST['edit']))
{
	if($_REQUEST['edit']<>"")
	{ 
		$details = $obj_tenant->getViewDetails($_REQUEST['edit']);
		//print_r($details);
		$image=$details[0]['img'];
		$document=$details[0]['Document'];
		//print_r($image);
		//print_r($document);
		if($_SESSION['role'] == ROLE_MEMBER && $details[0]['active']==1 )
		{
			//echo "hi";
			?>
			<script>
				window.location.href = 'Dashboard.php';
			</script>

		<?php
		exit();
		}

	}
}?>

<?php
if(isset($_REQUEST['view']))
{
	if($_REQUEST['view']<>"")
	{ 
		$details = $obj_tenant->getViewDetails($_REQUEST['view']);
		//print_r($details);
		$image=$details[0]['img'];
		$document=$details[0]['Document'];
		//print_r($image);
		//print_r($document);
		if($_SESSION['role'] == ROLE_MEMBER && $details[0]['active']==1 )
		{
			//echo "hi";
			?>
			<script>
				window.location.href = 'Dashboard.php';
			</script>

		<?php
		exit();
		}

	}
}?>


<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/tenant.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script language="javascript" type="application/javascript">
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
	
	function for_print()
	{
		document.getElementById('print').style.display = "none";
		var html = document.getElementById('tenant').innerHTML;
		var print_div = document.getElementById('for_printing');
		print_div.innerHTML = html;
				
		var mywindow = window.open('', 'PRINT', 'height=600,width=800');

	    mywindow.document.write('<html><head><title></title>');
    	mywindow.document.write('</head><body>');
		mywindow.document.write(document.getElementById('head_for_printing').innerHTML);
    	mywindow.document.write(document.getElementById('for_printing').innerHTML);
	    mywindow.document.write('</body></html>');

    	mywindow.document.close(); // necessary for IE >= 10
	    mywindow.focus(); // necessary for IE >= 10*/

    	mywindow.print();
	    mywindow.close();

		document.getElementById('print').style.display = "block";

		return false;
	}
	//$( document ).ready(function() {
		 
		var isblocked = '<?php echo $UnitBlock ?>';
		if(isblocked==1)
		{
			//alert("We are sorry,but your access has been blocked for this feature . Please contact your Managing Committee for resolution .");
			
            window.location.href='suspend.php';
			
			
		}
	//});
	
	</script>
   
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
		$(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics_Dob").datepicker(datePickerOptions)});

	</script>
  
<script type="text/javascript">
		var datePickerOptions={ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+0',
            buttonImageOnly: true ,
            defaultDate: '01-01-1980'
        };
		var FieldCount=1;
		var MaxInputs=10;
		$(function () {
		$("#btnAdd").bind("click", function () {
		//	alert("Add");
		if(FieldCount <= MaxInputs) //max file box allowed
                  {
					   FieldCount++; 
					  document.getElementById('count').value=FieldCount;
					 
				  }
	    var div = $("<tr />");
        div.html(GetDynamicTextBox(""));
        $("#mem_table").append(div);
		$(".basics_Dob").datepicker(datePickerOptions);
	});

    $("#btnGet").bind("click", function () {
        var values = "";
        $("input[name=members]").each(function () {
            values += $(this).val() + "\n";
        });
        alert(values);
    });
    $("body").on("click", ".remove", function () {
        $(this).closest("div").remove();
    });
});
//}
function GetDynamicTextBox(value) {
    return '<td id="members_td_'+FieldCount+'"><input name = "members_'+FieldCount+'" id = "members_'+FieldCount+'" type="text" value = "' + value + '"   style="width:140px;" /></td>&nbsp;<td id="relation_td_'+FieldCount+'"><input name = "relation_'+FieldCount+'" id = "relation_'+FieldCount+'" type="text" value = "' + value + '"  style="width:80px;"  /></td>&nbsp;&nbsp;'+'<td id="mem_dob_td_'+FieldCount+'"><input name = "mem_dob_'+FieldCount+'" id = "mem_dob_'+FieldCount+'"  class="basics_Dob" type="text" value = "' + value + '" size="10"   style="width:80px;" /></td><td id="contact_td_'+FieldCount+'">&nbsp;&nbsp;&nbsp;&nbsp;<input name = "contact_'+FieldCount+'" id = "contact_'+FieldCount+'" type="text" value = "' + value + '"  style="width:80px;"  /></td><td id="email_td_'+FieldCount+'"><input name = "email_'+FieldCount+'" id = "email_'+FieldCount+'" type="text" value = "' + value + '"  style="width:140px;"  />&nbsp;</td><td></td><td></td>';
}
</script>

<script type="text/javascript">
		var DocCount=1;
		var MaxInputs=10;
		$(function () {
		$("#btnAddDoc").bind("click", function () {
		//	alert("Add");
		if(FieldCount <= MaxInputs) //max file box allowed
                  {
					   DocCount++; 
					  document.getElementById('doc_count').value=DocCount;
					 
				  }
	    var div = $("<tr />");
        div.html(GetDynamicFileBox(""));
        $("#doc_Id").append(div);
		//$(".basics_Dob").datepicker(datePickerOptions);
	});

    $("#btnGet").bind("click", function () {
        var values = "";
        $("input[name=upload]").each(function () {
            values += $(this).val() + "\n";
        });
        alert(values);
    });
    $("body").on("click", ".remove", function () {
        $(this).closest("div").remove();
    });
});
//}
function GetDynamicFileBox(value) {
    return '<td><input name = "userfile'+DocCount+'" id = "userfile'+DocCount+'" type="file" value = "' + value + '" /></td>'+'<td><input name = "doc_name_'+DocCount+'" id = "doc_name_'+DocCount+'" type="text" value = "' + value + '" /></td>'+
            '<!--<input type="button" value="Remove" class="remove" />-->'
}
</script>

</head>
<body>


<div id="middle">
<div class="panel panel-info" id="panel" style="display:block; margin-top:6%;width:77%;">
      <?php if(isset($_REQUEST['edit']))
	  {?>
      <div class="panel-heading" id="pageheader">Edit Lease</div>
      <?php 
	  }else if(isset($_REQUEST['view']))
	  {?>
      	<div class="panel-heading" id="pageheader">View Lease</div>
        <?php
	  }
	  else if(isset($_REQUEST['ter']))
	  {?>
		  <div class="panel-heading" id="pageheader">Terminate Lease</div>
		<?php  }
	  else{?>
        <div class="panel-heading" id="pageheader">Add Lease</div>
        <?php }?>
<br>
<button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;margin-left:10%" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
<center>
<center>
<?php if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_MEMBER){?>
<input type="button" class="btn btn-primary" onClick="window.location.href='view_member_profile.php?prf&id=<?php echo $_GET['mem_id'];?>'"  style="float:left;" value="Go to profile view">

<?php }else{ ?>
<input type="button" class="btn btn-primary" onClick="window.location.href='view_member_profile.php?scm&id=<?php echo $_GET['mem_id'];?>&tik_id=<?php echo time();?>&m'"  style="" value="Go to profile view">
<?php } ?>

</center>
<br><center>
<?php if(isset($_REQUEST['ter']))
{?>
<p style="font-size:12px; color:red; font-weight:bold;">Please update Lease end date to new date on which you want to terminate the lease.</p>
<?php }?>
</center>
<!-- <br />
   <button type="button" class="btn btn-primary" onClick="window.location.href='view_member_profile.php'">Go to profile view</button>
	<br />-->
    
    <?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg'])){ ?>
<body onLoad="go_error();">
<?php } ?>

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

<form name="tenant" id="tenant" method="post" action="process/tenant.process.php" enctype="multipart/form-data"  onSubmit="return val();">

<table align='center' id="data_table" >
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

		<tr  align="left">
        <td valign="middle"><?php echo $star;?></td>
			<td><b>Name on the Lease Document </b></td>
            <td>&nbsp; : &nbsp;</td>
			<td id="td_1"><input type="text" name="t_name" id="t_name"  onBlur="document.getElementById('members_1').value=this.value;"/></td>
		</tr>
        <!--<tr  align="left">
        <td valign="middle"><?php// echo $star;?></td>
        	<td><b>Date Of Birth</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="dob" id="dob" class="basics_Dob" size="10" readonly  style="width:80px;" /></td>
		</tr>
-->
		<tr  align="left">
        <td valign="middle"><?php echo $star;?></td>
			<td><b>Lease Start Date</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td id="td_2"><input type="text" name="start_date" id="start_date" class="basics" size="10" readonly  style="width:80px;" /></td>
		</tr>
		<tr  align="left">
        <td valign="middle"><?php echo $star;?></td>
        	<td><b>Lease End Date</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td id="td_3"><input type="text" name="end_date" id="end_date" class="basics" size="10" readonly  style="width:80px;" /></td>
		</tr>

		<!--<tr  align="left">
        <td valign="middle"><?php //echo $star;?></td>
			<td><b>Contact No</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="mob" id="mob" onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" size="30"/></td>
		</tr>
				<tr  align="left">
        <td valign="middle"><?php //echo $star;?></td>
			<td><b>Email Address</b></td>
             <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="email" id="email" /></td>
		</tr>-->
        <tr  align="left">
        <td valign="middle"><?php //echo $star;?></td>
			<td><b>Agent Name</b></td>
             <td>&nbsp; : &nbsp;</td>
			<td id="td_4"><input type="text" name="agent" id="agent" /></td>
		</tr>
<tr  align="left">
        <td valign="middle"><?php //echo $star;?></td>
			<td><b>Agent  Contact No</b></td>
             <td>&nbsp; : &nbsp;</td>
			<td id="td_5"><input type="text" name="agent_no" id="agent_no"  onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" size="30"  /></td>
		</tr>

		<!--<tr  align="left">
        <td valign="middle"><?php// echo $star;?></td>
			<td><b>Additional Member</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="members" id="members" /></td>
            </tr>
            <tr  align="left">
      		  <td valign="middle"><?php// echo $star;?></td>
			<td><b>Relashion</b></td>
            <td>&nbsp; : &nbsp;</td>
            <td><input type="text" name="members" id="members" /><input type=button value='Add' onClick='add_element()';></td>
            </tr>
            <tr><td></td><th></th><td></td><td><div id="disp" name="disp"></div><input type="hidden" name="mem_list" id="mem_list" value=""></td>
		</tr>
-->				<!--<tr  align="left">
        <td valign="middle"><?php //echo $star;?></td>
			<td><b>Policy Varification</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="checkbox" name="p_varification" id="p_varification" value="1" >	</td>
		</tr>-->
        <?php
	    if(($_SESSION['role'] == ROLE_SUPER_ADMIN)||($_SESSION['role'] == ROLE_ADMIN_MEMBER )|| ($_SESSION['role'] == ROLE_ADMIN))
	   {?>
        <tr  align="left">
        <td valign="middle"><?php //echo $star;?></td>
			<td><b>Accepted by society</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td id="td_6"><input type="checkbox" name="varified" id="varified" value="1" >	</td>
		</tr>
<?php }?>
<tr><td><br></td></tr>
        <tr  align="left">
        <td valign="middle"><?php //echo $star;?></td>
			<td><b>Lease Documents</b></td>
            <td>&nbsp; : &nbsp;</td></tr>
            <tr><td><br></td></tr>
            <tr align="left"><td colspan="4"><div id="doc" style="margin-left: 73px;font-weight: bold;text-transform: capitalize;"></div></td></tr>
            <tr align="left">
			<td colspan="6">
            <table id="doc_Id" style="margin-top:-5px;"><tr align="left"><td><b>&nbsp;&nbsp;Select file to upload</b></td>
            <td><b>Enter document name</b></td>
            </tr>
            <tr align="left">
            <td align="left"><input type="file" name="userfile1" id="userfile1" /></td>
            <td><input type="text" id="doc_name_1" name="doc_name_1"></td>
             <td><input id="btnAddDoc" type="button" value="Add More" /></td><!--<td><div id="doc" style="margin-left: 73px;font-weight: bold;text-transform: capitalize;"></div></td>-->
            </tr>
            <!--<tr><td   valign="middle"><div id="FileContainer" >-->
            <input type="hidden" name="doc_count" id="doc_count" value="1">
            <!--</div>-->
            <!--</td></tr>-->
            </table>
            </td>
		</tr>
       
    <tr><td><br></td></tr>
    <tr  align="left">
        <td valign="left"><?php //echo $star;?></td>
			<td><b>Tenant occupying the unit</b></td>
            <td>&nbsp; : &nbsp;</td></tr>
            <tr align="left" >
			<td colspan="8">
            <table id="mem_table" style="margin-top:-10px;" width="100%"><tr align="left" id="mem_table_tr"><td width="20%"><b>&nbsp;&nbsp;Name</b></td>
            <td width="20%"><b>Relation</b></td><td width="20%"><b>Date Of Birth<br/>(DD-MM-YYYY)</b></td>
          	<td width="20%">&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $star;?>&nbsp;&nbsp;Contact No.</b></td>
            <td width="20%"><b><?php echo $star;?>&nbsp;&nbsp;Email Address</b></td>
            <td id="create_login">Create Login</td>
            <td id="send_emails">Send E-Mails ?</td>
            </tr>
            <tr align="left">
            <td align="left" id="members_td_1"><input type="text" name="members_1" id="members_1" style="width:140px;" /></td>
            <td id="relation_td_1"><input type="text" name="relation_1" id="relation_1"  style="width:80px;" /></td>
            <td id="mem_dob_td_1"><input type="text" name="mem_dob_1" id="mem_dob_1"   class="basics_Dob" size="10" style="width:80px;" /></td>
            <td id="contact_td_1">&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="contact_1" id="contact_1"  style="width:80px;"  onBlur="extractNumber(this,0,true);" onKeyUp="extractNumber(this,0,true);" onKeyPress="return blockNonNumbers(this, event, true, true)" size="30" /></td>
            <td id="email_td_1"><input type="text" name="email_1" id="email_1" style="width:140px;" /></td>            
			<td><input type="checkbox"  name="chkCreateLogin" id="chkCreateLogin" value="1" /></td>
			<td><input type="checkbox" name="other_send_commu_emails" id="other_send_commu_emails" value="1" /></td>
		</tr>
            <td id="add_button"><input id="btnAdd" type="button" value="Add" /></td>
            </tr>
            <!--<tr><td   valign="left"><div id="TextBoxContainer" >-->
    <!--Textboxes will be added here -->
    <input type="hidden" name="count" id="count" value="1">
<!--</div></td></tr>-->
<br />
            
            </table>
        </td></tr>
   <!-- <tr  align="middle">
    <td valign="middle"><?php// echo $star;?></td>
    <td colspan="3" valign="middle"><b style="    font-size: 12px; margin-left:-120px;"><?php echo $star;?>&nbsp;&nbsp;Additional Members</b></td><tr>
    <tr><td><br></td></tr>
    <tr  align="middle"><td></td><td valign="middle" colspan="1" ><b style="margin-left:10px;"">Name :</b></td>
    <td valign="left" colspan="0">&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td valign="left"><b style="margin-left:-290px;">Relation :</b></td>
    <td valign="left"><b style="margin-left:-320px;">Date Of Bitrh :</b></td></tr>
    <tr align="left">
   <td colspan="6" valign="middle"><span style="margin-left:105px;">
   <input type="text" name="members_1" id="members_1" />
   &nbsp;<input type="text" name="relation_1" id="relation_1"  />
   &nbsp;<input type="text" name="mem_dob_1" id="mem_dob_1"   class="basics_Dob" size="10" readonly  style="width:80px;" />
   <!--&nbsp;<input type="text" name="mobile_1" id="mobile_1"  style="width:150px;"/>
   &nbsp;<input type="text" name="email_1" id="email_1" style="width:150px;" />-->
 <!--  &nbsp;&nbsp;&nbsp;<input id="btnAdd" type="button" value="Add" /></span></td></tr>
<tr><td><br /></td></tr>-->
<!--<br />
<tr><td  colspan="6" valign="middle"><div id="TextBoxContainer"  style="margin-left:105px;">-->
    <!--Textboxes will be added here -->
    <!--<input type="hidden" name="count" id="count" value="1">
    <input type="hidden" name="doc_count" id="doc_count" value="1">
</div>
<br /></td></tr>-->
<tr><td><br></td></tr>
<tr align="left">
    	<td valign="middle"><?php //echo $star;?></td>
    	<td style="text-align:left;"><b>Note</b></td>
   		<td>&nbsp; : &nbsp;</td>
        <td id="to_show_note"></td>
    </tr>
    <tr><td colspan="4" id="textarea"><textarea name="note" id="note" rows="5" cols="50"></textarea></td></tr>
       	<script>			
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('note', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ],
								 height: 100,
        						 width: 740,
								 uiColor: '#14B8C4'
								 });
		</script>
        
        <tr><td><input type="hidden" name="tenant_id" id="tenant_id" value=<?php  echo $_GET['edit']?>></td></tr>
         <tr><td><input type="hidden" name="mem_id" id="mem_id" value="<?php  echo $_GET['mem_id']?>"></td></tr>
         <tr><td><input type="hidden" name="unit_id" id="unit_id" value="<?php  echo $unit_details['unit_id']?>"></td></tr>
        <tr><td><input type="hidden" id="doc_id" name="doc_id" value="<?php echo $details[0]['doc_id']?>"></td></tr>
        <tr><td><input type="hidden" value="<?php echo getRandomUniqueCode(); ?>" name="Code" id=="Code" /></td></tr>
		<td colspan="4" align="center">
            <!--<input type="hidden" name="id" id="id">-->
            <input type="submit" name="insert" id="insert" value="Submit" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;" >
            <input type="button" name="print" id="print" value="Print" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7; display:none" onClick="for_print();" >
            </td>
        
         <tr><td><br><br></td></tr>
</table>
<div id="head_for_printing" style="display:none"><center><table><tr><td style="text-align:center"><?php echo $society_dets[0]['society_name']; ?></td></tr><tr><td style="text-align:center"><?php echo $society_dets[0]['society_add']; ?></td></tr></table></center></div>
<div id="for_printing" style="display:none"></div>
</form>
</center>

<table align="center">
<tr>
<td>
<?php
/*echo "<br>";
$str1 = $obj_tenant->pgnation();
echo "<br>";
echo $str = $obj_tenant->display1($str1);
echo "<br>";
$str1 = $obj_tenant->pgnation();
echo "<br>";*/
?>
</td>
</tr>
</table>
</div>
</div>
</body>
</html>
<?php
	if(isset($_REQUEST['edit']) && $_REQUEST['edit'] <> '')
	{
		?>
			<script>
				getTenant('edit-' + <?php echo $_REQUEST['edit'];?>);				
			</script>
		<?php
	}
	
	if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{
		?>
			<script>
				getTenant('delete-' + <?php echo $_REQUEST['deleteid'];?>);				
			</script>
		<?php
	}
	
	if(isset($_REQUEST['view']) && $_REQUEST['view'] <> '')
	{
		?>
        	<script>
				getTenant('view-' + <?php echo $_REQUEST['view']; ?> );
			</script>
        <?php
	}
?>

<?php
	if(isset($_REQUEST['ter']))
	{
		?>
		<script>
			bTerminate = true;
		</script>
		<?php
	}
?>

<?php include_once "includes/foot.php"; ?>