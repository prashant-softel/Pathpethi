<?php
include_once("includes/head_s.php");
include_once("classes/tips.class.php");
include_once ("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
$obj_tips = new tips($m_dbConnRoot,$m_dbConn);
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
  <!--  <script type="text/javascript" src="multipleUpload/scripts/jquery.min.js"></script>
<script type="text/javascript" src="multipleUpload/scripts/jquery.wallform.js"></script>-->
<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/tips.js"></script>
     <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
     <script type="text/javascript" src="multipleUpload/scripts/jquery.min.js"></script>
<script type="text/javascript" src="multipleUpload/scripts/jquery.wallform.js"></script>

<script type="text/javascript" >
 $(document).ready(function() { 
		
            $('#photoimg').die('click').live('change', function()			{ 
			           //$("#preview").html('');
			    
				$("#imageform").ajaxForm({target: '#preview', 
				     beforeSubmit:function(){ 
					
					console.log('v');
					$("#imageloadstatus").show();
					 $("#imageloadbutton").hide();
					 }, 
					success:function(){ 
					console.log('z');
					 $("#imageloadstatus").hide();
					 $("#imageloadbutton").show();
					}, 
					error:function(){ 
							console.log('d');
					 $("#imageloadstatus").hide();
					$("#imageloadbutton").show();
					} }).submit();
					
		
			});
        }); 
</script>

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
		
  </script>
  <style>
  
  .preview
{
width:70px;
border:solid 1px #dedede;
padding:0px;
float: left;
margin-left: 37%;
clear: both;
}
#preview
{
color:#cc0000;
font-size:12px
}
.link
{
	float: left;
    margin-left: 20px;
    margin-top: 33px;
    color: blue;
}
  </style>
  
</head>
<body>
<div class="panel panel-info" style="margin-top:2%;margin-left:3.5%; border:none;width:85%">
    <div class="panel-heading" id="pageheader">Add Tips</div>
<br />
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

<br>
<center><button type="button" class="btn btn-primary" onclick="window.location.href='view_tips.php'">Go Back</button></center>
<br>
<center>
<form name="tips" id="tips" method="post" action="process/tips.process.php" enctype="multipart/form-data" onSubmit="return val();">
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


		<tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Tips Title</b></th>
        <td>&nbsp; : &nbsp;</td>			
        <td><input type="text" name="subject" id="subject"  /></td>
		</tr>
        <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Dashboard Type</b></th>
        <td>&nbsp; : &nbsp;</td>			
       	<td>
         <select name="type" id="type" style="width:200px;" >
        <option value="0">Don't Show in Dashboard</option>
        <option value="1">Show in All</option>
         <option value="2">My Society</option>
        <option value="3">Accounting / Admin</option>
       <!-- <option value="2">Both</option>-->
	  </select>
         </td>
		</tr>
        <!--<tr align="left">
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>With Videos</b></th>
        <td>&nbsp; : &nbsp;</td>			
       	<td>
        
      <input type="checkbox" id="training_type" name="training_type" value="1">
         </td>
		</tr>-->
		<tr align="left">
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>Date</b></th>
        <td>&nbsp; : &nbsp;</td>			
       	<td><input type="text" name="date" id="date" class="basics" size="10"  value="<?php echo date('d-m-Y');?>" readonly  style="width:80px;" /></td>
		</tr>
        <tr align="left">
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>Enter Url (Embeded link) </b></th>
        <td>&nbsp; : &nbsp;</td>			
       	<td><textarea id="url" name="url" cols="30" rows="3"></textarea></td>
		</tr>
			<!--<tr align="left">
        <td valign="middle"><?php //echo $star;?></td>
        <th><b>Uplade Image</b></th>
        <td>&nbsp; : &nbsp;</td>			
       	<td><input type="file" name="photo" id="photo" accept=".jpg, .png, .jpeg, .gif" multiple  style=" width: 49%;"/>
     
        </td>
        
		</tr>-->
        
		
		<tr align="left">
        <td valign="middle"><?php echo $star;?></td>
        <th><b>Description</b></th>
        <td colspan="2">&nbsp; : &nbsp;</td>	
        </tr>
        <tr>
		<td colspan="4"><textarea name="desc" id="desc" cols="30" rows="5"></textarea></td>
		</tr>
        <script>			
			
			//CKEDITOR.config.extraPlugins = 'iframe,youtube,justify';
			CKEDITOR.config.extraPlugins = 'justify';
			//CKEDITOR.config.youtube_width = '450';
			//CKEDITOR.config.youtube_height = '350';
			CKEDITOR.replace('desc', {toolbar: [
         						{ name: 'clipboard', items: ['Undo', 'Redo']},
								 { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align'], items: [ 'NumberedList', 'BulletedList','JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        						{name: 'editing', items: ['Format', 'Bold', 'Italic', 'Underline', 'Strike']},
								{ name: 'insert', items: [ 'Image','Youtube', 'Table', 'HorizontalRule', 'SpecialChar', 'Iframe'  ] },
								
   								 ],
								 height: 150,
        						 width: 700,
								 uiColor: '#14B8C4'
								 });
		</script>

        <tr><td><br><input type="hidden" name="tips_id" id="tips_id"></td></tr>
		<center><tr>
			<td colspan="4" align="center"><input type="hidden" name="id" id="id">
            <input type="submit" name="insert" id="insert" value="Submit"  class="btn btn-primary"  style="background-color:#337AB7;width:80px; height:32px;color:#FFF;border-color:#2E6DA4;"></td>
		</tr></center>
        <tr><td><br></td></tr>
</table>
</form>
   <!--<form id="imageform" method="post" enctype="multipart/form-data" action='ajaximage.php'>
Upload your image <input type="file" name="photoimg" id="photoimg" />
</form>
<div id='preview'>
</div>
<div id='link'></div>
-->
<form id="imageform" method="post" enctype="multipart/form-data" action="ajaximage.php">
Upload your image 
<div id='imageloadstatus' style='display:none'><img src="multipleUpload/loader.gif" alt="Uploading...."/></div>
<div id='imageloadbutton'>
<input type="file" name="photoimg" id="photoimg" />
</div>
</form>
<br>
<div id='preview'>
	</div>
	<br><br>
</center>
</div>
<br />
<table align="center">
<tr>
<td>
<?php
//echo "<br>";
//$str1 = $obj_tips->pgnation();
//echo "<br>";
//echo $str = $obj_tips->display1($str1);
//echo "<br>";
//$str1 = $obj_tips->pgnation();
//echo "<br>";
?>
</td>
</tr>
</table>
<br><br>
</body>
</html>

<?php
	if(isset($_REQUEST['edit']) && $_REQUEST['edit'] <> '')
	{
		?>
			<script>
				getNewTips('edit-' + <?php echo $_REQUEST['edit'];?>);				
			</script>
		<?php
	}
	
	if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{
		?>
			<script>
				getNewTips('delete-' + <?php echo $_REQUEST['deleteid'];?>);				
			</script>
		<?php
	}
?>

<?php include_once "includes/foot.php"; ?>