<?php include_once "ses_set_as.php"; ?>
<?php
include_once("includes/head_s.php");
include_once ("classes/dbconst.class.php");

include_once("classes/events.class.php");
include_once("classes/doc.class.php");
include_once( "classes/include/fetch_data.php");
$objFetchData = new FetchData($m_dbConn);
$Mobile =$objFetchData->getMobileNumber($_SESSION['unit_id']);
$objdoc = new document($m_dbConn);
$obj_events = new events($m_dbConn,$m_dbConnRoot);
if(isset($_REQUEST['id']))
{
	//print_r($_REQUEST['id']);
	if($_REQUEST['id']<>"")
	{
		$edit = $obj_events->Event_edit($_REQUEST['id']);
		
			$uploadFile=$edit[0]['Uploaded_file'];
			//$image_collection = explode(',', $image);	
		
	}
}
$UnitBlock = $_SESSION["unit_blocked"];
?>

 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jsevents20190504.js"></script>
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
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
    
    <!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
    <script type="text/javascript" src="javascript/jquery.clockpick.1.2.4.js"></script>
    <script type="text/javascript" src="javascript/ui.core.js"></script>
    <script type="text/javascript" src="javascript/ui.datepicker_event.js"></script>-->
    <script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
    <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true, 
			yearRange: '-0:+10', // Range of years to display in drop-down,
        })});
            
    </script>
     <script language="javascript" type="text/javascript">
     	$(document).ready(function()
     	{
     		document.getElementById("Document_type").selectedIndex = "1";
     	})
		function EnableEventType(value)
		{								
			if (value == 1) 
			{				
				$('#upload').hide();
				//$('#create').show();
				$('#desc').show();								
			}            
       		else if(value == 2)
			{				
				//$('#create').hide();
				$('#desc').show();
				$('#upload').show();				
				//CKEDITOR.replace( 'description', {toolbarStartupExpanded : false} );            	
			}
			else if(value == 0)
			{									
				$('#upload').hide();
				//$('#create').hide();
				$('#desc').hide();				
			}
		}
		
	document.body.onload =	function()
		{			
			go_error();
			<?php 
			if(!isset($_REQUEST['id'])  <> '')
			{
			?>
			
			EnableEventType(0);
			
			<?php
			 }?>
		}
	</script>	
 
</head>

<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body>
<?php }else{ ?>
<body>
<?php } ?>

<div id="middle">

<br>
<div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:3.5%;width:70%;">
<div class="panel-heading" id="pageheader">Add New Event</div>
<br>
<?php if(!($_SESSION['role']==ROLE_SUPER_ADMIN)){?>
<br>
<center>
<!--<a href="events_view_as.php?ev" style="color:#00F; text-decoration:none;"><b>Group Event</b></a>-->
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<!--<a href="events_view_as_self.php?ev" style="color:#00F; text-decoration:none;"><b>My Society Event</b></a>-->
<button type="button" class="btn btn-primary" onClick="window.location.href='events_view.php?ev=0'">My Society Events</button>


<br>
<?php } ?>

<center>
<form name="events" id="events" method="post" action="process/events.process.php" enctype="multipart/form-data" onSubmit="return val();">
<input type="hidden" name="cur_date" id="cur_date" value="<?php echo date('Y-m-d');?>"/>
<input type="hidden" id="event_id" name="event_id" />
<input type="hidden" id="society_id" name="society_id" />
	<?php
		$star = "<font color='#FF0000'>* &nbsp;</font>";
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
    <table align='center'>
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
	 	<tr align="left">
     		<td valign="middle"><?php echo $star;?></td>
   			<th><b>Issued By</b></th>
	    	<td>&nbsp; : &nbsp;</td>
        	<td><input type="text" name="issueby" id="issueby" value="<?php echo $_SESSION['name']; ?>" /></td>        	
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td><b>Event Date</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="events_date" id="events_date" class="basics" readonly style="width:80px;"/></td>
            <td>&nbsp;  &nbsp;  &nbsp;  &nbsp;</td>	
            <td valign="middle"><?php echo $star;?></td>
			<td><b>Event End Date</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="end_date" id="end_date" class="basics" readonly style="width:80px;"/></td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <!--<tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td><b>Event End Date</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="end_date" id="end_date" class="basics" readonly style="width:80px;"/></td>
		</tr>-->
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td><b>Event Time</b></td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            <select name="hr" id="hr" style="width:50px;">
            <option value="">HH</option>
            <?php for($i=1;$i<=12;$i++)
			{
				if(strlen($i)==1)
				{
					echo "<option value=0".$i.">0".$i."</option>";
				}
				else
				{
					echo "<option value=".$i.">".$i."</option>";
				}
			}
			?>
            </select>
           
            <select name="mn" id="mn" style="width:50px;">
            <option value="">MM</option>
            <?php for($ii=0;$ii<=59;$ii++)
			{
				if(strlen($ii)==1)
				{
					echo "<option value=0".$ii.">0".$ii."</option>";
				}
				else
				{
					echo "<option value=".$ii.">".$ii."</option>";
				}
			}
			?>
            </select>
            
            <select name="ampm" id="ampm" style="width:50px;">
            <option value="AM">AM</option>
            <option value="PM">PM</option>
            </select>
            </td>
            <td>&nbsp;  &nbsp;  &nbsp;  &nbsp;</td>           
        	<td valign="top"><?php //echo $star;?></td>
			<td valign="top"><b>Applicable To</b></td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><select name="event_type" style="width:100px;" id="event_type">
            <option value="1" selected>All</option>
            <option value="2">Society Members</option>
            <!-- <option value="3">Tenants</option> -->
            </select>
            </td>            
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr align="left">
        	<td valign="top"><?php //echo $star;?></td>
			<td valign="top"><b>Participation Charges</b></td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><input type="text" name="event_charges" id="event_charges" size="38" value="0" >
            <input type="hidden" name="event_creation_type" id="event_creation_type" value="2" ></td>
           
                <td colspan="4"><select name="Document_type" style="visibility: hidden;" id="Document_type" style="width: 100px">
                    <?php echo $combo_doc = $objdoc->combobox("select ID, doc_type from `document_type` where `doc_type`='Events'",'0');
                    ?>
                  </select>
                </td>
          
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr align="left">
            <td valign="left"></td> 
            <td><b> Notify Member By Email </b></td>  
            <td>&nbsp;:&nbsp;</td>     
            <td><input type="checkbox" name="notify" id="notify" value="1">   </td>
            <td>&nbsp;  &nbsp;  &nbsp;  &nbsp;</td>
            <td valign="left"></td> 
            <td><b> Send Mobile Notification </b></td>  
            <td>&nbsp;:&nbsp;</td>     
            <td><input type="checkbox" name="mobile_notify" id="mobile_notify" value="1" checked>   </td>
    	</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
         <tr align="left">
        	<td valign="left"></td>
            <td><b>Send SMS to Members</b></td>	
            <td>&nbsp;:&nbsp;</td>     
            <td><input type="checkbox" name="smsnotify" id="smsnotify" value="1" onChange="SetSMS();">&nbsp;&nbsp;&nbsp;&nbsp;<b>( Charges Apply )</b><div id="smsTest" name="smsTest" style="display:none;"><br/><textarea name="SMSTemplate" id="SMSTemplate" rows="4" cols="50" style="resize::none;float:left;font:bold;"></textarea><br><input type="button" onClick="showTestSMS()" value="Send Test SMS" style="float: right;margin-right: 40%;margin-top: 3%;background-color: cornflowerblue;color: black;border: none;"></div></td>
       	</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <!--<tr align="left">
            <td valign="left"></td> 
            <td><b> Send Mobile Notification </b></td>  
            <td>&nbsp;:&nbsp;</td>     
            <td><input type="checkbox" name="mobile_notify" id="mobile_notify" value="1">   </td>
    	</tr>-->
        
        <tr align="left">
        	<td valign="top"><?php echo $star;?></td>
			<td valign="top"><b>Event Title</b></td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><input type="text" name="events_title" id="events_title" size="38" onBlur="SetSMS();"></td>
            <td>&nbsp;  &nbsp;  &nbsp;  &nbsp;</td>
            <td valign="top"></td>
			<td valign="top"><b>Url</b></td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><input type="text" name="events_url" id="events_url" size="38" placeholder="IF ANY"></td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <!--<tr align="left">
        	<td valign="top"></td>
			<td valign="top"><b>Url</b></td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><input type="text" name="events_url" id="events_url" size="38" placeholder="IF ANY"></td>
		</tr>-->
        
        <!--<tr align="left">
        	<td valign="top"><?php //echo $star;?></td>
			<td valign="top"><b>Applicable To</b></td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><select name="event_type" style="width:100px;" id="event_type">
            <option value="1" selected>All</option>
            <option value="2">Society Members</option>
            <!-- <option value="3">Tenants</option> -->
            <!--</select>
            </td>
		</tr>-->
        
        <!--<tr align="left">
        	<td valign="top"><?php //echo $star;?></td>
			<td valign="top"><b>Participation Charges</b></td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td><input type="text" name="event_charges" id="event_charges" size="38" placeholder="If event is free then enter 0"></td>
		</tr>-->
        
       <!-- <tr align="left">
        	<td valign="top"><?php //echo $star;?></td>
			<td valign="top">Select group</td>
            <td valign="top">&nbsp; : &nbsp;</td>
			<td>
            	<div style="overflow-y:scroll;overflow-x:hidden;width:280px; height:100px; border:solid #CCCCCC 2px;">
					<?php //echo $combo_society_id = $obj_events->combobox11("select society_grp_id,grp_name,society_id from society_group where status='Y' and society_id='".$_SESSION['society_id']."'","society_grp_id[]","society_grp_id"); ?>
                </div>
            </td>
	  </tr>-->
        <!-- <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Event Creation Type</b></th>
        <td>&nbsp; : &nbsp;</td>
        <td><select name="event_creation_type" id="event_creation_type" style=" width:135px; height:20;" onChange="EnableEventType(this.value);">  
        		<OPTION VALUE="0" selected>Please Select</OPTION>
                <OPTION VALUE="1">Written Event</OPTION>
                <OPTION VALUE="2">Upload Event</OPTION>                
             </select></td>
		</tr> -->
        <tr id=""> 
            <td valign="middle"></td>
            <td><b>Attachments </b></td>   
            <td>&nbsp;:&nbsp;</td>               
            <td> <input name="userfile" id="userfile" type="file"  /></td>
            </tr>
            <tr id = "filenametd">
            <td valign="middle"></td>
            <td></td>   
            <td></td>    
          <td><Label id="filename" style="padding-top:5px;padding-left:5px"></Label></td></tr>
             <!--<table><tr><td>
             <input name="userfile" id="userfile" type="file" />
              </td>
              <td>
              <?php //if($uploadFile <>'')
			 //{?>
            	<b><?php //echo substr(($uploadFile),0,20)?></b></td><td>&nbsp;&nbsp;</td><td><a href="javascript:void(0);" onClick="del_file('<?php //echo $uploadFile;?>',<?php //echo $_REQUEST['id']?>);">Remove File</a>
                <?php //}?> 
              </td></tr></table>-->
            
    	</tr>   
    	<tr><td colspan="4">&nbsp;</td></tr>
		<tr align="left" id="">
        	<td valign="top"><?php echo $star;?></td>
			<td valign="top" align="left"><b>Event Description</b></td>
            <td valign="top" align="left">&nbsp; : &nbsp;</td>
			<td colspan="6" align="left"><textarea name="events_desc" id="events_desc" rows="5" cols="50"></textarea></td>
		</tr>
        <script>
			//CKEDITOR.config.height = 100;
			//CKEDITOR.config.width = 500;
			CKEDITOR.config.extraPlugins = 'justify';
			CKEDITOR.replace('events_desc', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike'] }
   								 ],
								 height: 250,
        						 width: 500,
								 uiColor: '#14B8C4'
								 });
		</script>
		
        
        
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="9" align="center">
            <input type="hidden" name="id" id="id">
            <input type="hidden" id="userMobileNo" name="userMobileNo" value="<?php echo $Mobile[0]['mob'] ?>">
 			<?php if($_REQUEST['id'] <> 0)
			{?><input type="hidden" id="updateid" name="updateid" value="<?php echo $_REQUEST['id']; ?>">
               <input type="hidden" id="OgEventTitle" name="OgEventTitle">
			<?php }else
			{?>
            <input type="hidden" id="updateid" name="updateid" value="0">
			<?php 	}?>
            <input type="submit" name="insert" id="insert" value="Create" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;">
            </td>
		</tr>
</table>
<br>
</form>
<script>
document.getElementById("filenametd").style.display = "none";
</script>
</center>

<?php
	if(isset($_REQUEST['id']) && $_REQUEST['id'] <> '')
	{
		?>
			<script>
				getEvents('edit-' + <?php echo $_REQUEST['id'];?>);				
			</script>
		<?php
	}
	
	if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{
		?>
			<script>
				getEvents('delete-' + <?php echo $_REQUEST['deleteid'];?>);				
			</script>
		<?php
	}
?>
<?php include_once "includes/foot.php"; ?>
