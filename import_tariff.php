<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php
include_once("includes/head_s.php");
include_once("classes/include/dbop.class.php");	
include_once("classes/tarrif_import.class.php");	
//echo "Tracing";	

if(isset($_REQUEST['type']))
{
	//echo $_REQUEST['type'];
}

//print_r($_SESSION);
$dbConnRoot = new dbop(true);
$obj_import_tariff = new tarrif_import($dbConnRoot,$m_dbConn);
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
				//if((filename == 'Tariff.csv') || (filename == 'Tariff.txt'))
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
					//alert('File Name Should be "Tariff.csv / Tariff.txt"');
				//}
			}
			else 
			{
				alert('Please Select "Tariff.csv" File');
				$('input:submit').attr('disabled',true);
			}			
		});		
});

$(document).ready(

	function(){
		
		<?php $pid = $_REQUEST['periodid'];?>
		if(<?php echo $pid ?> != 0)
		{
			document.getElementById('bill_for').value = <?php echo $pid ?>;
		}
});

$(document).ready(

	function(){
		
		document.getElementById('Download').disabled = false;
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

function showhide()
{
	if(document.getElementById('methodofimportC').checked == true)
	{
		document.getElementById('tohide1').hidden = true;
		document.getElementById('tohide2').hidden = true;
		document.getElementById('tohide3').hidden = true;
		document.getElementById('downloadform').hidden = true;
		document.getElementById('create').hidden = true;
		document.getElementById('overwrite').hidden = true;
	}
	else if(document.getElementById('methodofimportW2S').checked == true)
	{
		document.getElementById('tohide1').hidden = false;
		document.getElementById('tohide2').hidden = false;
		document.getElementById('tohide3').hidden = false;
		document.getElementById('downloadform').hidden = false;
		document.getElementById('create').hidden = false;
		document.getElementById('overwrite').hidden = false;
	}
}
			

</script>
</head>




<body onLoad="go_error();">

<center>

<form id="importtariffform" name="importtariffform" action="process/import_tariff.process.php" method="post" enctype="multipart/form-data" >

<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Import Tariff</div>
        <div style="text-align:right;padding-right: 50px;padding-top: 10px;">
        
        </div>
       
<div id="right_menu">
<table style="margin-top:-200px">
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
      <td valign="middle" style="color:#FF0000"><?php echo "*";?></td>
      <td>Source</td>
      <td>&nbsp; : &nbsp;</td>
      <td><input type="radio" name="methodofimport" id="methodofimportW2S" value="Way2Society" onClick="showhide()" checked>&nbsp; &nbsp;Way2Society
      &nbsp; &nbsp;
      <input type="radio" name="methodofimport" id="methodofimportC" value="Custom" onClick="showhide()" >&nbsp; &nbsp;Custom
      </td>
      <!--<td><input type="radio" name="methodofimport" id="methodofimportC" value="Custom" onClick="showhide()" >&nbsp; &nbsp;Custom</td>-->
      </tr>
      <tr><br></tr>
      <tr><br></tr>
      <?php $qry1 = "select `YearID`, `YearDescription` from `year` where `status`='Y' and `YearID` = '".$_SESSION['default_year']."' ORDER BY `YearID` DESC";
	  //echo $qry;
	  ?>
            
      <tr><br></tr>
      <tr><br></tr>
      <tr id="tohide2" >
        	<td style="color:#FF0000"><?php echo "*"?></td>
			<td>Bill Year </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="year_id" id="year_id" style="width:142px;" ><?php /*?>onChange="get_period(this.value, <?php echo DEFAULT_PERIOD; ?>, 'period_id');"<?php */?>
                	<?php echo $obj_import_tariff->combobox($qry1,0); ?>
				</select>
            </td>
		</tr>    
         <tr><br></tr>
      <tr><br></tr>
        <?php $qry2 = "select ID, Type from period  where  status='Y' and YearID = '".DEFAULT_YEAR."'";	 
	  ?>
        <tr id="tohide3" >
        	<td style="color:#FF0000"><?php echo "*" ?></td>
            <td>Bill For </td>
            <td>&nbsp; : &nbsp;</td>
            <td>
            	<select name="bill_for" id="bill_for" style="width:142px;" >
                	<?php echo $obj_import_tariff->combobox($qry2,0); ?>
				</select>
            </td>
        </tr>
         <tr><br></tr>
      <tr><br></tr>
      
      <tr id="tohide1" >
      	<td></td>
        <td>Period</td>
        <td>&nbsp; : &nbsp;</td>
        <td><input type="radio" id="period" name="period" value="Current Period">&nbsp; Current Period
        	&nbsp; &nbsp; &nbsp;<input type="radio" id="period" name="period" value="Lifetime">&nbsp; Lifetime</td>
      </tr>
      
      <tr><br></tr>
      <tr><br></tr>
      
      <tr id="create" >
      	<td></td>
        <td>Do you want to <br>
        create new ledger?</td>
        <td>&nbsp; : &nbsp;</td>
        <td><input type="checkbox" id="create_l" name="create_l"></td>       
      </tr>
      
      <!--<tr><br></tr>
      <tr><br></tr>
      
      <tr id="overwrite" >
      	<td></td>
        <td>Do you want to overwrite <br>
        already imported tariffs?</td>
        <td>&nbsp; : &nbsp;</td>
        <td><input type="checkbox" id="overwrite_t" name="overwrite_t"></td>
      </tr>-->
      
      <tr><br></tr>
      <tr><br></tr>
      
<tr align="left">
        	<td valign="middle"></td>
			<td>Browse File To Import</td>
            <td>&nbsp; : &nbsp;</td>
			<td id="browse" colspan="2"><input type="file" name="upload_files[]" id="file" multiple /><br/></td>
            
</tr>   

<tr align="center">
 <td colspan="5" align="center"><input type="submit" id="Import" name="Import" value="Import"  disabled  class="btn btn-primary" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;color: #fff;background-color: #337ab7;border-color: #2e6da4;"/></td>
 
 </tr>

<input type="hidden" name="sid" value="<?php echo $_SESSION['society_id'];?>">
</table>
<div style="color:#FF0000">* File Name Should be "Tariff.csv/"Tariff.txt(comma delimited file)"</div>

</form>
<br>
<form id="downloadform" name="downloadform" <?php /*?>action="../trialdownload.php"<?php */?> action="process/download_tariff.process.php" method="post" style:"padding-bottom:50px;"><input type="submit" id="Download" name="Download" value="Download Sample" class="btn btn-primary" style="width:200px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal; color: #fff; background-color: #337ab7; border-color: #2e6da4;"/>
<br>&nbsp; &nbsp;</form>
</center>

</div>

</div>
<?php include_once "includes/foot.php"; ?>