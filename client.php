<?php include_once("includes/head_s.php");
include_once("classes/client.class.php");
$obj_client = new client($m_dbConnRoot);
?>
 

<html>
<head>
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- MetisMenu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="dist/css/sb-admin-2.css" rel="stylesheet">

<!-- Custom Fonts -->
<link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- MetisMenu CSS -->
    <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">



	<style>
	.arrow_box
	 {
		position: absolute;
		border: 2px solid #888;
		border-radius:5px;
		height:20px;
		width:25px;
		padding:2px 29px;
		text-align:center;
		font-weight:bold;
		color:#888;
	}

	</style>
	<script type="text/javascript" src="js/ajax.js"></script>
   	<script tytpe="text/javascript" src="js/ajax_new.js"></script>
    <script type="text/javascript" src="js/client.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <link href="css/popup.css" rel="stylesheet" type="text/css" />
	<script language="javascript" type="text/javascript">
	$(function()
	{
		$.datepicker.setDefaults($.datepicker.regional['']);
		$(".basics").datepicker({ 
		dateFormat: "yy-mm-dd", 
		showOn: "both", 
		buttonImage: "images/calendar.gif", 
		buttonImageOnly: true 
	})});
		
	function go_error()
    {
		//$(document).ready(function()
		//{
			//$("#error").show();
		//});
		document.getElementById('error').style.display = 'block';
        setTimeout('hide_error()',5000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	
	function togglePopup()
	{
		var popup = document.getElementById('demo2_tip');
    	popup.classList.toggle('show');
	}
	</script>
</head>
 
<?php if(isset($_REQUEST['ShowData'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<?php } ?>
<body>
<!--<div class="navbar-default sidebar" role="navigation"  style="font-size:10px;font-size:1.00vw;float: left;margin-left: -15%;margin-top: -55px;">
 <div class="sidebar-nav navbar-collapse" style="line-height:4px;line-height:1.3vw;vertical-align:middle; float: left; width:100%;">
  <ul class="nav" id="side-menu">
  <li style="text-align:left">
      <a href="view_tips.php"><i class="fa fa-lightbulb-o" style="font-size: 1.28571429em;"></i> Add Tips</a>
  </li>
  </ul>
 	</div></div>
--><br>

<div class="panel panel-info" id="panel" style="display:none">
    <div class="panel-heading" id="pageheader">Client List</div>
<br>

<?php
$star = "<font color='#FF0000'>*</font>";

?>
<center>
<?php //echo $_SESSION['role'];
if($_SESSION['role'] == 'Master Admin')
{
echo '<input type="button" value="Add New" onClick="addNew();" id="btnAdd" class="btn btn-primary" style="width:100px; height:30px; font-family:Times New Roman, Times, serif; font-style:normal;"/>';
}
?>
<div id="new_entry" style="display:none;">
	<h2>Add New Client</h2>
	<form name="newclient" id="newclient" method="post" action="ajax/client.ajax.php">
	
	<table align='center'>
	<?php
		if(isset($_POST['ShowData']))
		{
	?>
			<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
	<?php
		}
	?>
			<tr align="left">
				<td><?php echo $star;?>Client Name : &nbsp;</td>
				<td>
					<input type="text" id="client_name" name="client_name" />
				</td>
			</tr>
			
			<tr align="left">
				<td><?php echo $star;?>Mobile : &nbsp;</td>
				<td>
					<input type="text" id="mobile" name="mobile" />
				</td>
			</tr>
			
			<tr>
				<td>Landline : &nbsp;</td>
				<td><input type="text" name="landline" id="landline" /></td>
			</tr>
            
            <tr>
				<td>Address : &nbsp;</td>
				<td><textarea name="address" id="address" rows="3" cols="40"></textarea></td>
			</tr>
			<tr><td><br/></td></tr>
            <tr>
                <td colspan="2" style="font-weight: bold;text-align: center;">
                    <i class="fa fa-envelope fa-fw" style="font-size: 14px;"></i>
                    &nbsp;<u>EMAIL CREDENTIALS DETAILS</u>&nbsp;
                    <i class="fa fa-envelope fa-fw" style="font-size: 14px;"></i>&nbsp;<br/><br/>
                </td>
			</tr>

			<tr>
				<td>EMail : &nbsp;</td>
				<td><input type="text" name="email" id="email" /></td>
			</tr>
					
			
            <tr>
				<td>Email Header : &nbsp;</td>
				<td><textarea name="email_header" id="email_header" rows="6" cols="60"></textarea></td>
                <script>
			//CKEDITOR.config.height = 100;
			//CKEDITOR.config.width = 500;
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('email_header', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ],
								 height: 100,
        						 width: 700,
								 uiColor: '#14B8C4'
								 });
		</script>
			</tr>
			<tr>
				<td>Email Footer : &nbsp;</td>
				<td><textarea name="email_footer" id="email_footer" rows="6" cols="60"></textarea>
                <script>
			//CKEDITOR.config.height = 100;
			//CKEDITOR.config.width = 500;
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('email_footer', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ],
								 height: 100,
        						 width: 700,
								 uiColor: '#14B8C4'
								 });
		</script></td>
			</tr>
            
            <tr><td><br/></td></tr>
            <tr>
                <td colspan="2" style="font-weight: bold;text-align: center;">
                    <i class="fa fa-envelope fa-fw" style="font-size: 14px;"></i>
                    &nbsp;<u>SMS CREDENTIALS DETAILS</u>&nbsp;
                    <i class="fa fa-envelope fa-fw" style="font-size: 14px;"></i>&nbsp;<br/><br/>
                </td>
			</tr>
            
            <tr>
				<td>Sms Userid : &nbsp;</td>
				<td><input type="text" name="sms_userid" id="sms_userid" />
                <button type="button" id= "popup" style="border-radius:50px; width:15px; color:#009; vertical-align:middle;"  class="popup"  onMouseOver="togglePopup();" onMouseOut="togglePopup();"><i class="fa   fa-info-circle "  ></i>
               <div id="demo2_tip" class="popuptext" style="text-align:left; width:21vw;">
                      <dl style="margin-left:10px;">
                          <dt>Sms Details will be used for sending,</dt>
                          <dt><br/></dt> 
                          <dt>1. General Sms</dt>
                          <dt>2. Bill Sms</dt>
                    </dl>
                </div>
             </button>
                </td>
			</tr>
            
            <tr>
				<td>Sms Key : &nbsp;</td>
				<td><input type="text" name="sms_key" id="sms_key" /></td>
			</tr>
            
            <!--<tr>
				<td>Sms Buy : &nbsp;</td>
				<td><input type="text" name="sms_buy" id="sms_buy" /></td>
			</tr>
            
            <tr>
            	<td>Sms Sent/Used : &nbsp;</td>
				<td>
                <div >    
                   <span id="sms_counter" class="arrow_box">0</span>
               </div>
    			</td>
    		</tr>-->
            <tr>
				<td>Sms Domain : &nbsp;</td>
				<td><input type="text" name="sms_domain" id="sms_domain" /></td>
			</tr>
            
            <tr>
				<td>Sms Sender ID : &nbsp;</td>
				<td><input type="text" name="sms_senderid" id="sms_senderid" /></td>
			</tr>
             
           
            
            <tr><td><br/></td></tr>
            <tr>
                <td colspan="2" style="font-weight: bold;text-align: center;">
                    <i class="fa fa-file-text-o fa-5x" style="font-size: 14px;"></i>
                    &nbsp;<u>MAINTENANCE BILL DETAILS</u>&nbsp;
                    <i class="fa fa-file-text-o fa-5x" style="font-size: 14px;"></i>&nbsp;<br/><br/>
                </td>
			</tr>


            <tr>
				<td>Bill Footer : &nbsp;</td>
				<td><textarea name="bill_footer" id="bill_footer" rows="6" cols="60"></textarea>
                <script>
			//CKEDITOR.config.height = 100;
			//CKEDITOR.config.width = 500;
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('bill_footer', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ],
								 height: 100,
        						 width: 700,
								 uiColor: '#14B8C4'
								 });
		</script></td>
			</tr>
			<tr>
				<td>Details : &nbsp;</td>
				<td><textarea name="details" id="details" rows="3" cols="40"></textarea></td>
			</tr>
			
			<tr><td></td></tr><tr><td></td></tr><tr><td></td></tr>
			<tr>
				<td colspan="2" align="center">
					<input type="hidden" name="id" id="id">
					<input type="submit" name="insert" id="insert" value="Insert" class="btn btn-primary" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;color: #fff;background-color: #337ab7;border-color: #2e6da4;">
					<input type="button" value="Cancel" onClick="onCancel();" class="btn btn-primary" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;">
				</td>
			</tr>
	</table>
	</form>
</div>
</center>
<br />
<div id="view_entry">
<center>
<table align="center">
<tr>
<td>
<?php
echo "<br>";
echo $str1 = $obj_client->pgnation();
?>
</td>
</tr>
</table>
</center>
</div>
</div>
<?php include_once "includes/foot.php"; ?>
<script>
	function addNew()
	{
		document.getElementById('new_entry').style.display = 'block';
		document.getElementById('btnAdd').style.display = 'none';
	}
	
	function onCancel()
	{
		document.getElementById('new_entry').style.display = 'none';
		document.getElementById('btnAdd').style.display = 'block';		
	}
	//getClient('edit-<?php //echo $_SESSION['society_client_id'] ?>');
</script>