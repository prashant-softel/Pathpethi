<?php include_once("includes/head_s.php");
include_once("classes/client.class.php");
include_once("classes/dbconst.class.php");

$clientID = base64_decode($_GET['client']);
$obj_client = new client($m_dbConnRoot, $clientID);


$client_details = $obj_client->getClientDetails($clientID);
if($_SESSION['client_id'] == $clientID || $_SESSION['role'] == ROLE_MASTER_ADMIN || $_SESSION['role'] == ROLE_SUPER_ADMIN)
{
}
else
{
	?>
	<script>
	alert("Invalid link");		
	window.location.href = 'logout.php';
	</script>
    <?php	
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

	<script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/populateData.js"></script>
   	<script tytpe="text/javascript" src="js/ajax_new.js"></script>
    <script type="text/javascript" src="js/client_details.js"></script>
	<link rel="stylesheet" type="text/css" href="css/tabs.css">    
	<script language="javascript" type="application/javascript">
	
	var iEncrClientID = '<?php echo $_GET['client'] ?>';
	var iClientID = <?php echo $clientID; ?>;	
	/*$(function()
	{
		$.datepicker.setDefaults($.datepicker.regional['']);
		$(".basics").datepicker({ 
		dateFormat: "yy-mm-dd", 
		showOn: "both", 
		buttonImage: "images/calendar.gif", 
		buttonImageOnly: true 
	})});*/
	
	jQuery(document).ready(function() {
    	jQuery('.tabctrls .tabctrl-links a').on('click', function(e)  {
        	var currentAttrValue = jQuery(this).attr('href');
 			
			// Show/Hide tabctrls
        	jQuery('.tabctrls ' + currentAttrValue).show().siblings().hide();
 
        	// Change/remove current tabctrl to active
        	jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
 
        	e.preventDefault();
    	});
	});
	
	function go_error()
    {
		//$(document).ready(function()
		//{
			//$("#error").show();
		//});
		document.getElementById('error').style.display = 'block';
        setTimeout('hide_error()',5000);	
    }
	function gotoClent()
	{
		window.location.href = "client.php";
	}
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	
	</script>
</head>

<body>
<br>	
<div class="panel panel-info" id="panel" style="display:none">
    <div class="panel-heading" id="pageheader"><?php echo $client_details[0]['client_name']; ?></div>
	<center>
		<!--<br />
		<div id="client_name" style="font-size:24px;font-weight:bold;"><?php //echo $client_details[0]['client_name']; ?></div>-->
		<br /><br />
		<div id="main_list">
			<table style="width:100%">
            <tr>
            <td style="width:33%">
            </td>
            <td  style="width:33%;">
            <div style="font-size:22px;width:100%">List of Societies</div>
            </td>
            <td  style="width:33%">
            <?php 
			if($_SESSION['login_id'] == 4)
			{?>
            <div><input type="button" class="btn btn-outline btn-primary btn-lg btn-block"  id="btnHeaderFooter" name="btnHeaderFooter" style="float:right;width:280px;margin-bottom:50px" onClick="gotoClent()" value="Client Header Footer Settings" /></div>
           <?php }?>
            
            </td>
            </tr>
            </table>
			<br />
			<div id="list_society"></div>
		</div>
		<div id="user_list"> 
        	<div style="padding-left:10px;">        	    
        		<button type="button" class="btn btn-primary btn-circle" onClick="backToSocietyList()" style="float:left;"><i class="fa  fa-arrow-left"></i></button>
            </div>			
			<div id="society_name" style="font-size:18px;"></div>
			<br />
			<div style="font-size:18px;">List of Users</div>
			<br />
			<div class="">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tabctrl1" data-toggle="tab">Super Admin</a></li>
					<li><a href="#tabctrl2" data-toggle="tab">Admin</a></li>
					<li><a href="#tabctrl3" data-toggle="tab">Admin Member</a></li>
					<li><a href="#tabctrl4" data-toggle="tab">Member</a></li>
				</ul>
			 
				<div class="tabctrl-content">
					<div id="tabctrl1" class="tabctrl active">
						<div id="user_super_admin"></div>
					</div>
			 		<div id="tabctrl2" class="tabctrl">
						<div id="user_admin"></div>
					</div>
			 		<div id="tabctrl3" class="tabctrl">
						<div id="user_admin_member"></div>
					</div>
			 		<div id="tabctrl4" class="tabctrl">
						<div id="user_member"></div>
					</div>
				</div>
			</div>
			<!--<div id="user_super_admin"></div>
			<div id="user_admin"></div>
			<div id="user_member_admin"></div>
			<div id="user_member"></div>-->
		</div>
		<div id="user_details" style="padding-left:10px;padding-right:10px;"> 
        	&nbsp;&nbsp;
            <div id="member_list">       	
                <button type="button" class="btn btn-primary btn-circle" onClick="backToMembersList()" style="float:left;"><i class="fa  fa-arrow-left"></i>
                </button>
            </div>        	            
            <div id="assigned_societies">
            	<button type="button" class="btn btn-primary" onClick="viewAssienedSocieties()" style="float:right;">View Assigned Societies</button>            	
            </div>
            <div id="login_list">            	
                <button type="button" class="btn btn-primary btn-circle" onClick="backToLoginList()" style="float:left;"><i class="fa  fa-arrow-left"></i>
            	</button>
            </div>                      
            <div id="assing_new_society">            	
                <a id="addnewsociety" href="" onClick="AddNewSociety('')"><button type="button" class="btn btn-primary" style="float:right;">
                Assign New Society</button></a> 
            </div>                                                                                          			
        	<span style="font-size:22px;" id="loginDetails">Login History For </span>
            <span id="unit" style="font-size:22px;"></span>
            <br /> <br /> <br /> <br />           
            <div id="member"></div>            
            <br /> <br />            
		</div>
        
	</center>
</div>	

<?php include_once "includes/foot.php"; ?>

<script>
	fetchSocietyList(iClientID);
</script>	