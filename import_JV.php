<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php
include_once("includes/head_s.php");
include_once("classes/include/dbop.class.php");	
include_once("classes/JV_import.class.php");	
//echo "Tracing";	

if(isset($_REQUEST['type']))
{
	//echo $_REQUEST['type'];
}

//print_r($_SESSION);
$dbConnRoot = new dbop(true);
//$obj_import_JV = new JV_import($dbConnRoot,$m_dbConn);
//echo "Tracing 2";
?>


<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/populateData.js"></script>
<script type="text/javascript">

$(document).ready(
//alert("trying 0");
	function(){		
		$('input:submit').attr('disabled',true);
		$('input:file').change(
		function(){
			if($(this).val() != '')
			{
				var filename = $('input[type=file]').val().split('\\').pop();
				//alert("trying 1");
				//if((filename == 'JV.csv') || (filename == 'JV.txt'))
				//{
					var fileExtension = ['csv', 'txt'];
					//alert("trying 2");
					if($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) 
					{
						$('input:submit').attr('disabled',true);
						alert("Only formats are allowed : "+ fileExtension.join(', '));
					}
					else
					{
						$('input:submit').removeAttr('disabled');
						//alert("trying 3");
					}
				//}
				//else
				//{
					//$('input:submit').attr('disabled',true);
					//alert('File Name Should be "JV.csv / JV.txt"');
				//}
			}
			else 
			{
				alert('Please Select "JV.csv" File');
				$('input:submit').attr('disabled',true);
			}			
		});		
});
   
</script>

<script language="javascript" type="text/javascript">
//function forsubmit()
//{
//	var ext = document.getElementById('file').value;
//	alert(ext);
//}
//window.onload = forsubmit();

function go_error()
{
	setTimeout('hide_error()',10000);	
}
function hide_error()
{
	document.getElementById('error').style.display = 'none';	
}

</script>
</head>

<body onLoad="go_error();">

<center>

<form id="importJVform" name="importJVform" action="process/import_JV.process.php" method="post" enctype="multipart/form-data" >

<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Import Journal Voucher</div>
       
<div id="right_menu">
<table style="margin-top:-50px">

<?php
if(isset($_POST["ShowData"]))
{
	?>
    <tr height="30"><td colspan=5 style="text-align:center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
<?php } ?> 
<strong><div id="show" style="text-align:center; width:100%; color:#FF0000"><?php //echo $show_op; ?></div></strong>
<!--<tr height="50" align="center"><td>&nbsp;</td><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="180">For Society Admin Login</th></tr></table></th></tr>-->
<BR/>
<BR/>

</tr>        
      <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      </tr>
<tr align="left">
        	<td valign="middle"></td>
			<td>Browse File To Import</td>
            <td>&nbsp; : &nbsp;</td>
			<td id="browse"><input type="file" name="upload_files[]" id="file" multiple /><br/></td>
            
</tr>   

<!--<tr align="left">
        	<td valign="middle"></td>
			<td>Do you want to<br>create new ledger?</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="checkbox" name="new_ledger" id="new_ledger" /><br/></td>
            
</tr>  -->
<br>
<br>

<tr align="center">
 <td colspan="4" align="center"><input type="submit" name="Import" value="Import"  disabled  class="btn btn-primary" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;color: #fff;background-color: #337ab7;border-color: #2e6da4;"/></td>
</tr>

<input type="hidden" name="sid" value="<?php echo $_SESSION['society_id'];?>">
</table>

<div style="color:#FF0000">* File Name Should be "*.csv/*.txt(comma delimited file)"</div>
<div style="color:#FF0000">* Date format in the file should be "dd-mm-yyyy"</div>
<span>Journal Voucher Sample File : <a href="samplefile/Journal_Voucher.csv" download>Click here to Download</a> </span>

</form>
<br>
<!--<form id="downloadform" name="downloadform" <?php /*?>action="../trialdownload.php"<?php */?> action="process/download_tariff.process.php" method="post" style:"padding-bottom:50px;"><input type="submit" id="Download" name="Download" value="Download Sample" class="btn btn-primary" style="width:200px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal; color: #fff; background-color: #337ab7; border-color: #2e6da4;"/>
<br>&nbsp; &nbsp;</form>-->
</center>

</div>

</div>
<?php include_once "includes/foot.php"; ?>